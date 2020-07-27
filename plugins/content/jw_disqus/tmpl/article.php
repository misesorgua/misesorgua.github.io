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

<span id="startOfPage"></span>

<?php if($disqusArticleCounter): ?>
<!-- Disqus comments counter and anchor link -->
<div class="jwDisqusArticleCounter">
	<a class="jwDisqusArticleCounterLink" href="<?php echo $output->itemURL; ?>#disqus_thread"><?php echo JText::_('View Comments'); ?></a>
	<div class="clr"></div>
</div>
<?php endif; ?>

<?php echo $row->text; ?>

<hr />

<!-- Disqus Comments -->
<div class="jwDisqusForm">
	<?php echo $output->comments; ?>
</div>

<div class="jwDisqusBackToTop">
	<a href="<?php echo $output->itemURL; ?>#startOfPage"><?php echo JText::_("back to top"); ?></a>
	<div class="clr"></div>
</div>
	
<div class="clr"></div>
