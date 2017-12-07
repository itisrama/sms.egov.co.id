<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<div class="row-fluid">
	<div class="col-md-6 col-md-offset-3">
		<div class="guest-wrap">
			<div class="row-fluid">
				<div class="display col-md-7">
					<div><img src="templates/eburo/images/overrides/login/display.jpg"></div>
				</div>
				<div class="loginarea col-md-5">
					<div class="login <?php echo $this->pageclass_sfx?>">
						<h1 class="text-hide">
				  			<?php echo $this->escape($this->params->get('page_heading')); ?>
				  		</h1>

						<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon btn-default"><i class="fa fa-user"></i></span>
									<input type="text" aria-required="true" required="" class="validate-username form-control" value="" id="username" name="username">
								</div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon btn-default"><i class="fa fa-key"></i></span>
									<input type="password" aria-required="true" required="" maxlength="99" size="25" class="validate-password form-control" value="" id="password" name="password">
								</div>
							</div>
							
							<?php $tfa = JPluginHelper::getPlugin('twofactorauth'); ?>

							<?php if (!is_null($tfa) && $tfa != array()): ?>
								<div class="form-group">
									<?php echo $this->form->getField('secretkey')->input; ?>
								</div>
							<?php endif; ?>
							
							<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
								<div class="checkbox pull-left">
									<label>
										<input id="remember" type="checkbox" name="remember" value="yes"/> 
										<?php echo JText::_(version_compare(JVERSION, '3.0', 'ge') ? 'COM_USERS_LOGIN_REMEMBER_ME' : 'JGLOBAL_REMEMBER_ME') ?>
									</label>
								</div>
							<?php endif; ?>
							
							<div class="clearfix">	
								<button type="submit" class="btn btn-blue pull-right"><?php echo JText::_('JLOGIN'); ?></button>
									
								<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
								<?php echo JHtml::_('form.token'); ?>
							</div>
							
							<?php /*
							<ul>
								<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
									<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a></li>
								<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
									<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a></li>
								<?php
								$usersConfig = JComponentHelper::getParams('com_users');
								if ($usersConfig->get('allowUserRegistration')) : ?>
								<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
										<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a></li>
								<?php endif; ?>
							</ul>*/?>
						</form>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>