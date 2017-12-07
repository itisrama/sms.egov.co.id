<?php

defined('_JEXEC') or die('Restricted access');

// -PC- J3.0 fix
if( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

class pkg_jcksuiteInstallerScript
{
	
	function 	preflight( $type, $parent ) 
	{
		$jversion = new JVersion();
		 // Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
  
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) 
		{
			Jerror::raiseWarning(null, 'Cannot install this version of the JCK Suite package in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
	}
		
	function postflight($parent)
	{
		?>
		<a id="jckmodal-install" href="../plugins/editors/jckeditor/install/index.php" rel="{handler: 'iframe' , size: {x:571, y:400}}" title="test" style="visibility:hidden">test</a>
		<style type="text/css">
			#sbox-btn-close { display:none;}
			#sbox-window{ background-color : #000000;}
		</style>
		<script type="text/javascript">
		if(typeof Hash == "undefined")
		{
			(function(){if(this.Hash)return;var e=this.Hash=new Type("Hash",function(e){if(typeOf(e)=="hash")e=Object.clone(e.getClean());for(var t in e)this[t]=e[t];return this});this.$H=function(t){return new e(t)};e.implement({forEach:function(e,t){Object.forEach(this,e,t)},getClean:function(){var e={};for(var t in this){if(this.hasOwnProperty(t))e[t]=this[t]}return e},getLength:function(){var e=0;for(var t in this){if(this.hasOwnProperty(t))e++}return e}});e.alias("each","forEach");e.implement({has:Object.prototype.hasOwnProperty,keyOf:function(e){return Object.keyOf(this,e)},hasValue:function(e){return Object.contains(this,e)},extend:function(t){e.each(t||{},function(t,n){e.set(this,n,t)},this);return this},combine:function(t){e.each(t||{},function(t,n){e.include(this,n,t)},this);return this},erase:function(e){if(this.hasOwnProperty(e))delete this[e];return this},get:function(e){return this.hasOwnProperty(e)?this[e]:null},set:function(e,t){if(!this[e]||this.hasOwnProperty(e))this[e]=t;return this},empty:function(){e.each(this,function(e,t){delete this[t]},this);return this},include:function(e,t){if(this[e]==undefined)this[e]=t;return this},map:function(t,n){return new e(Object.map(this,t,n))},filter:function(t,n){return new e(Object.filter(this,t,n))},every:function(e,t){return Object.every(this,e,t)},some:function(e,t){return Object.some(this,e,t)},getKeys:function(){return Object.keys(this)},getValues:function(){return Object.values(this)},toQueryString:function(e){return Object.toQueryString(this,e)}});e.alias({indexOf:"keyOf",contains:"hasValue"})})();Hash.implement({getFromPath:function(e){return Object.getFromPath(this,e)},cleanValues:function(e){return new Hash(Object.cleanValues(this,e))},run:function(){Object.run(arguments)}})
			
		}
		
		if (typeof SqueezeBox == "undefined") 
		{
				 var head = document.getElementsByTagName('head')[0];
				 var link = document.createElement('link');
			 	 link.type = 'text/css';
				 link.rel = 'stylesheet';
				 link.href = '../media/system/css/modal.css';
				 head.appendChild(link);
				
				var script = document.createElement('script');
				script.type= 'text/javascript';
				script.src= '../media/system/js/modal.js';
				head.appendChild(script);
			
			if(script && script.onreadystatechange)
			{
				script.onreadystatechange = function() 
				{
				   if (this.readyState == 'complete')
				   {
						if($$('#system-message dd.error ul').length < 1) //check to see if there are no errors reported
						{
							var wizard = document.getElementById("jckmodal-install");
							SqueezeBox.fromElement(wizard,	{ parse: 'rel'});
							(function()
							{
								SqueezeBox.bound  &&  SqueezeBox.overlay.removeEvent('click',SqueezeBox.bound.close) || SqueezeBox.overlay.removeEvent('click',SqueezeBox.listeners.close);
							}).delay(250);	
						}	
				   }	
				};
			}
			else
			{
				if(script)
				{		
					script.onload =  function()
					{
						
						if($$('#system-message dd.error ul').length < 1) //check to see if there are no errors reported
						{
							var wizard = document.getElementById("jckmodal-install");
							SqueezeBox.fromElement(wizard,	{ parse: 'rel'});
							(function()
							{
								SqueezeBox.bound  &&  SqueezeBox.overlay.removeEvent('click',SqueezeBox.bound.close) || SqueezeBox.overlay.removeEvent('click',SqueezeBox.listeners.close);
							}).delay(250);	
						}
					}
				}
			}
		}
		else
		{
			if($$('#system-message dd.error ul').length < 1) //check to see if there are no errors reported
				{
					var wizard = document.getElementById("jckmodal-install");
					SqueezeBox.fromElement(wizard,	{ parse: 'rel'});
					(function()
					{
						SqueezeBox.bound  &&  SqueezeBox.overlay.removeEvent('click',SqueezeBox.bound.close) || SqueezeBox.overlay.removeEvent('click',SqueezeBox.listeners.close);
					}).delay(250);	
				}
		}	
		</script>
		<?php
	}
}