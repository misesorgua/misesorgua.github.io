<?php 
/*
// JoomlaWorks "Disqus Comment System" Plugin for Joomla! 1.5.x - Version 2.2
// Copyright (c) 2006 - 2009 JoomlaWorks Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www.joomlaworks.gr
// Designed and developed by the JoomlaWorks team
// ***Last update: November 14th, 2009***
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php echo $row->text; ?>

<!-- Disqus comments counter and anchor link -->
<a class="jwDisqusListingCounterLink" href="<?php echo $output->itemURL; ?>#disqus_thread" title="<?php echo JText::_('Add a comment'); ?>">
	<?php echo JText::_('Add a comment'); ?>
</a>
