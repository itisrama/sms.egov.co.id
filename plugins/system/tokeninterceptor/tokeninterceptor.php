<?php
/** 
 * Token interceptor system plugin
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Sponsorship Reward
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 * 
 */

// no direct access
defined('_JEXEC') or die;


class plgSystemTokeninterceptor extends JPlugin
{
    
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
        $app = JFactory::getApplication();

        if (($app->isSite() && $this->params->get('use_frontend')) || ($app->isAdmin() && $this->params->get('use_backend'))) {
         register_shutdown_function(array($this,'killdie'));
        }
        
    }
    
    public function killdie(){
        $content = ob_get_contents();
        if($content==JText::_('JINVALID_TOKEN')){
            $message = $this->params->get('message',JText::_('PLG_SYSTEM_TOKENINTERCEPTOR_MESSAGE'));
            Jerror::raiseNotice(null, $message);
            if(class_exists('JController')){
                $controller = new JController();
            }elseif(class_exists('JControllerLegacy')){
                $controller = new JControllerLegacy();
            }else{
                return;
            }
            $controller->setRedirect(JURI::current());
            $controller->redirect();
            return false;   
        }
      }
  }
