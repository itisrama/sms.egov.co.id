<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// get params
$sitename  = $this->params->get('sitename');
$slogan    = $this->params->get('slogan', '');
$logotype  = $this->params->get('logotype', 'text');
$logoimage = $logotype == 'image' ? $this->params->get('logoimage') : '';
$logoimgsm = ($logotype == 'image' && $this->params->get('enable_logoimage_sm', 0)) ? $this->params->get('logoimage_sm') : false;

if (!$sitename) {
	$sitename = JFactory::getConfig()->get('sitename');
}

$document	= JFactory::getDocument();

$logoimageurl = ($logotype == 'image' && $logoimage) ? JURI::base(false) . '/' . $logoimage : null;
$logoimagesmurl = $logoimgsm ? JURI::base(false) . '/' . $logoimgsm : null;

if($logotype == 'image' && $logoimage) {
	list($imgwidth, $imgheight) = getimagesize($logoimageurl);
	$document->addStyleDeclaration(sprintf("
		.logo-image h1 {
			background-image: url(%s);
			width: %spx;
			height: %spx;
		}
	", $logoimageurl, $imgwidth, $imgheight));
}
?>

<header id="t3-header" class="t3-header clearfix">
	<?php $this->addScript(T3_URL.'/js/nav-collapse.js'); ?>
	

	<div class="logo-<?php echo $logotype, ($logoimgsm ? ' logo-control' : '') ?>">
		<a href="<?php echo JURI::base(true) ?>">
			<h1><?php echo $sitename ?></h1>
		</a>
		<small class="site-slogan"><?php echo $slogan ?></small>
	</div>

	<ul class="navbar-right list-inline">
		<?php /*
		<li class="dropdown" href="#" data-toggle="dropdown">
			<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
			</a>
			<div class="dropdown-menu dropdown-user">
				<jdoc:include type="modules" name="<?php $this->_p('head-user') ?>" style="raw" />
			</div>
		</li>*/?>
		<li>
			<jdoc:include type="modules" name="<?php $this->_p('head-user') ?>" style="raw" />
		</li>
		<li>
			<button class="btn-collapse btn btn-default" data-target=".t3-mainnav">
				<i class="fa fa-bars"></i>
			</button>
		</li>
	</ul>
</header>