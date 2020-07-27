<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/system.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/general.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="templates/misesorgua/css/template.css" />
		<!--[if lte IE 6]>
		<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<meta name="google-site-verification" content="_xQIBxUJH1uTBjVCF4C4bOaJ4JPVRHzPMzdVo-_2dv0" />
		<meta property="og:site_name" content="mises.org.ua"/>
		<meta property="og:image" content="http://i1.minus.com/jbfL3TWN2HdtV1.jpg"/>
		<meta property="og:image:type" content="image/png"/>
		<meta property="og:image:width" content="760"/>
		<meta property="og:image:height" content="399"/>
		<meta property="fb:admins" content="1764499006, 100000618003384"/>
	</head>
	<body>
		<div id="all">
			<div id="head">
				<jdoc:include type="modules" name="header" style="xhtml" />
			</div>
			<div id="tommenu">
				<jdoc:include type="modules" name="search" />
				<div id="user3">
					<jdoc:include type="modules" name="user3" />
				</div>
			</div>
			<div id="content">
				<div id="rightsidebar">
					<jdoc:include type="modules" name="left" style="xhtml" />
					<br />
					<iframe src="http://www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2Fmisesorgua%2F170417736303563&amp;width=204&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=false&amp;height=270" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:204px; height:270px;" allowTransparency="true"></iframe>
				</div>
				<div id="component">
					<jdoc:include type="component" />
				</div>
				<div id="endcontent"></div>
			</div>
			<div id="footer">
				<jdoc:include type="modules" name="footer" style="xhtml" />
			</div>			
		</div>
		<script type="text/javascript">

			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-19280831-1']);
			_gaq.push(['_trackPageview']);
		      
			(function() {
			  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();		      
		</script>		
	</body>
</html>