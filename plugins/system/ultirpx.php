<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.event.plugin');
jimport('joomla.user.helper');

class plgSystemUltiRPX extends JPlugin
{
  function plgSystemUltiRPX(&$subject, $config)
  {
       
	parent::__construct($subject,$config);

  }
     
  function onAfterInitialise()
  {
    // just startup
    global $mainframe;
    global $mybaseurl;          
    
    if((isset($_GET['token']))||(isset($_POST['token']))) {
      if (isset($_GET['token'])) {
        $token = $_GET['token'];
      } else {
        $token = $_POST['token'];
      }
      $db =& JFactory::getDBO();
      $query = "select * from #__rpx where propname='key'";
      $db->setquery($query);
      $row = $db->loadObject();
      $apiKey = $row->propvalue;;
      $post_data = array('token' => $token,
                         'apiKey' => $apiKey,
                         'format' => 'json');
      if ($this->params->get('usecurl') == 1) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info/?token='.$token.'&&apiKey='.$apiKey.'&&format=json');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $raw_json = curl_exec($curl);
        curl_close($curl);
      } else {
        $raw_json = file_get_contents("https://rpxnow.com/api/v2/auth_info/?token=".$token."&&apiKey=".$apiKey."&&format=json");
      }

      // parse the json response into an associative array
      $auth_info = json_decode($raw_json, true);

      // process the auth_info response
      if ($auth_info['stat'] == 'ok') {
        $db =& JFactory ::getDBO();
        $rpxid = 'rpx'.md5($auth_info['profile']['identifier']);
        $query = "SELECT userid FROM #__rpx_mapping WHERE rpxid='".$rpxid."'";
        $db->setQuery($query);
        $userid = $db->loadResult();

        $newuser = true;
        if (isset($userid)) {
          $user =& JFactory::getUser($userid);
          if ($user->id == $userid) {
            $newuser = false;
          } else {
            // possible if previous registered, but meanwhile removed
            // we have a userid without user...remove from the rpx_mapping
            $query = "DELETE FROM #__rpx_mapping WHERE userid='".$userid."'";
            $db->setQuery($query);
            $db->query();
          }
        }
        if ($newuser == true) {
           // save the user
          $user = new JUser();
          $authorize =& JFactory::getACL();
          $newUsertype = 'Registered';
          $user->set('id', 0);
          $user->set('usertype','');

          $user->set('gid', $authorize->get_group_id('',$newUsertype, 'ARO'));
          $date =& JFactory::getDate();
          $user->set('registerDate', $date->toMySQL());
          if (isset($auth_info['profile']['displayName'])) {
            $displayName = $auth_info['profile']['displayName'];
          } else if (isset($auth_info['profile']['name']['displayName'])) {
            $displayName = $auth_info['profile']['name']['displayName'];
          }
          if (isset($auth_info['profile']['preferredUsername'])) {
            $preferredUsername = $auth_info['profile']['preferredUsername'];
          } else if (isset($auth_info['profile']['name']['preferredUsername'])) {
            $preferredUsername = $auth_info['profile']['name']['preferredUsername'];
          }

          $user->set('name', $displayName);
          // if username already exists, just add an index to it
          $nameexists = true;
          $index = 0;
          $userName = $preferredUsername;
          while ($nameexists == true) {
            if (JUserHelper::getUserId($userName) != 0) {
              $index++;
              $userName = $preferredUsername.$index;
            } else {
              $nameexists = false;
            }
          }
          $user->set('username', $userName);
          
	  $host = JFactory::getURI()->getHost();
	  $domain = substr($host,4); // strips the www.
          if ($this->params->get('fakemail') == 0) {
	    if (isset($auth_info['profile']['email'])) {
              $user->set('email', $auth_info['profile']['email']);
            } else if (isset($auth_info['profile']['name']['email'])) {
              $user->set('email', $auth_info['profile']['email']);
            } else {
              $user->set('email',  str_replace(" ","_",$userName)."@".$domain);
            }
          } else {
	      $user->set('email',  str_replace(" ","_",$userName)."@".$domain);
	  }
          $pwd = JUserHelper::genRandomPassword();
          $user->set('password', $pwd);

          if (!$user->save()) {
            echo "ERROR: ";
            echo $user->getError();
          } else {
            $query = "INSERT INTO #__rpx_mapping (userid, rpxid) VALUES ('".$user->get('id')."','".$rpxid."')";
            $db->setQuery($query);
            if (!$db->query()) {
              JERROR::raiseError(500, $db->stderror());
            }
          }
          // check if the community builder tables are there
          $query = "SHOW TABLES LIKE '%__comprofiler'";
          $db->setQuery($query);
          $tableexists = $db->loadResult();

          if (isset($tableexists)) {
            $cbquery = "INSERT IGNORE INTO #__comprofiler(id,user_id) VALUES ('".$user->get('id')."','".$user->get('id')."')";
            $db->setQuery($cbquery);
            if (!$db->query()) {
              JERROR::raiseError(500, $db->stderror());
            }
          }
        }

        // Get an ACL object
        $acl =& JFactory::getACL();

        // Get the user group from the ACL
        if ($user->get('tmp_user') == 1) {
            $grp = new JObject;
            // This should be configurable at some point
            $grp->set('name', 'Registered');
         } else {
            $grp = $acl->getAroGroup($user->get('id'));
         }

         //Mark the user as logged in
         $user->set( 'guest', 0);
         $user->set('aid', 1);

         // Fudge Authors, Editors, Publishers and Super Administrators into the special access group
         if ($acl->is_group_child_of($grp->name, 'Registered')      ||
            $acl->is_group_child_of($grp->name, 'Public Backend'))    {
              $user->set('aid', 2);
         }

         //Set the usertype based on the ACL group name
         $user->set('usertype', $grp->name);

         // Register the needed session variables
         $session =& JFactory::getSession();
         $session->set('user', $user);

         // Get the session object
         $table = & JTable::getInstance('session');
         $table->load( $session->getId() );

         $table->guest           = $user->get('guest');
         $table->username        = $user->get('username');
         $table->userid          = intval($user->get('id'));
         $table->usertype        = $user->get('usertype');
         $table->gid             = intval($user->get('gid'));

         $table->update();

         // Hit the user last visit field
         $user->setLastVisit();
	 
	 // redirect
	 $returnURL = $this->getReturnURL();
	 $mainframe->redirect($returnURL);

      }
    }	  
  }
  function getReturnURL()
  {
    if($itemid =  $this->params->get('login'))
    {
        $menu =& JSite::getMenu();
        $item = $menu->getItem($itemid);
        $url = JRoute::_($item->link.'&Itemid='.$itemid, false);
    }
    else
    {
        // stay on the same page
        $uri = JFactory::getURI();
        $url = $uri->current();
        $url .= '?';
        $paramarray = $uri->getQuery(true);
        foreach ($paramarray as $paramname => $paramvalue) {
          if ($paramname != 'token') {
            $url .= $paramname;
            $url .='=';
            $url .= $paramvalue;
            $url .= '&&';
          }
        }
	
        //$url = $uri->toString(array('path', 'query', 'fragment'));
    }
    return $url;
  }
}

?>
