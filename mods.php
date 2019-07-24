<?php

//////////////////////////////////////////////////////////////
//===========================================================
// mods.php(For individual softwares)
//===========================================================
// SOFTACULOUS 
// Version : 1.0
// Inspired by the DESIRE to be the BEST OF ALL
// ----------------------------------------------------------
// Started by: Alons
// Date:       10th Jan 2009
// Time:       21:00 hrs
// Site:       http://www.softaculous.com/ (SOFTACULOUS)
// ----------------------------------------------------------
// Please Read the Terms of use at http://www.softaculous.com
// ----------------------------------------------------------
//===========================================================
// (c)Softaculous Inc.
//===========================================================
//////////////////////////////////////////////////////////////

if(!defined('SOFTACULOUS')){

	die('Hacking Attempt');

}


/**
 * This function will allow you to modify the XML that is being passed.
 * You can modify it here and Softaculous will parse it as it parses the XML of install.xml of packages.
 *
 * @package      softaculous
 * @subpackage   scripts
 * @author       Pulkit Gupta
 * @param        string $str The key of the Language string array.
 * @return       string The parsed string if there was a equivalent language key otherwise the key itself if no key was defined.
 * @since     	 1.0
 */
/*function __wp_mod_install_xml($xml){
	
	global $__settings, $settings, $error, $software, $globals, $softpanel, $notes, $adv_software;
	
}*/

/**
 * This function will parse your mod_install.xml and shows an option of choose plugin to users 
 *
 * @package      softaculous
 * @subpackage   scripts
 * @author       Pulkit Gupta
 * @param        string $str The key of the Language string array.
 * @return       string The parsed string if there was a equivalent language key otherwise the key itself if no key was defined.
 * @since     	 1.0
 */
function __wp_mod_settings(){
	
	global $__settings, $settings, $error, $software, $globals, $softpanel, $notes, $adv_software;
	
	$install = @implode(file($globals['path'].'/conf/mods/'.$software['softname'].'/mod_install.xml'));
	
	$install = parselanguages($install);
	
	$tmp_settings = array();
	
	if(preg_match('/<softinstall (.*?)>(.*?)<\/softinstall>/is', $install)){
		
		$tmp_settings = load_settings($install, $adv_software, 1);
	}
	
	$settings = array_merge($settings, $tmp_settings);
}

/**
 * If anything is needed to be execute before the installation procedure starts than it should be done here.
 *
 * @package      softaculous
 * @subpackage   scripts
 * @author       Pulkit Gupta
 * @param        string $str The key of the Language string array.
 * @return       string The parsed string if there was a equivalent language key otherwise the key itself if no key was defined.
 * @since     	 1.0
 */
function __pre_mod_install(){
	
	global $__settings, $settings, $error, $software, $globals, $softpanel, $notes, $adv_software;
	
}

/**
 * If anything is needed to be execute after the installation procedure starts than it should be done here.
 *
 * @package      softaculous
 * @subpackage   scripts
 * @author       Pulkit Gupta
 * @param        string $str The key of the Language string array.
 * @return       string The parsed string if there was a equivalent language key otherwise the key itself if no key was defined.
 * @since     	 1.0
 */
function __post_mod_install(){
	
	global $__settings, $settings, $error, $software, $globals, $softpanel, $notes, $adv_software;

	$scriptSettings = parse_ini_file($globals['path'].'/conf/mods/'.$software['softname'].'/config.ini' , true);

	if(!empty($scriptSettings['WPMU_DEV_Settings']['Dashboard_API_KEY'])){
		if(strlen($scriptSettings['WPMU_DEV_Settings']['Dashboard_API_KEY']) !== 40){
			$error[] = "invalid WPMU Dev API key!";
		}
	}else{
		$error[] = "WPMU Dev API key not found!";
	}
	// Check which plugin is checked for installation
	if(!empty($__settings['install_theme'])){

		$filename = '';
		$install_theme = true;
		
		//Get selected theme
		if($__settings['install_theme'] == "none"){
			$install_theme = false;
		}else{
			$filename = $__settings['install_theme'].'.zip';
		}

		if($install_theme){
			//Unzip Genesis parent theme, and selected child theme
			if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/themes/genesis.2.6.1.zip')){
				if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/themes/genesis.2.6.1.zip', $__settings['softpath'].'/wp-content/themes/')){
					$error[] = 'could not unzip the plugin files - '.'genesis.2.6.1.zip';
				}
			}else{
				$error[] = "File does not exist - genesis.2.6.1.zip";
			}

			if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/themes/'.$filename)){
				if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/themes/'.$filename, $__settings['softpath'].'/wp-content/themes/')){
					$error[] = 'could not unzip the plugin files - '.$filename;
				}
			}else{
				$error[] = 'File does not exist - '.$filename;
			}
		
			//Activate themes in WP database
			$query = "UPDATE ".$__settings['dbprefix']."options SET 
						option_value = 'genesis'
						WHERE option_name = 'template';";
									
			$result = sdb_query($query, $__settings['softdbhost'], $__settings['softdbuser'], $__settings['softdbpass'], $__settings['softdb']);
			
			$query = "UPDATE ".$__settings['dbprefix']."options SET 
						option_value = '".$__settings['install_theme']."'
						WHERE option_name = 'stylesheet';";

			$result = sdb_query($query, $__settings['softdbhost'], $__settings['softdbuser'], $__settings['softdbpass'], $__settings['softdb']);
		}
	}

	//Get plugins to be activated from previous steps in install process
	$__settings['active_plugins'] = array();

	$query = "SELECT option_value FROM ".$__settings['dbprefix']."options WHERE option_name = 'active_plugins';";
	$result = sdb_query($query, $__settings['softdbhost'], $__settings['softdbuser'], $__settings['softdbpass'], $__settings['softdb']);

	$__settings['active_plugins'] = _unserialize($result[0]['option_value']);
	
	//Unzip all WPMUDev Plugins
	if(!empty($__settings['WPMUDev_Plugins'])){
		//WPMUDev Dashboard
		if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/WPMUDev-dashboard.zip')){
			if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/WPMUDev-dashboard.zip', $__settings['softpath'].'/wp-content/plugins/')){
				$error[] = 'Could not unzip the plugin files - WPMUDev Dashboard';
			}
		}else{
			$error[] = 'File does not exixt - WPMUDev Dashboard';
		}
		//Defender
		if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Defender.zip')){
			if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Defender.zip', $__settings['softpath'].'/wp-content/plugins/')){
				$error[] = 'Could not unzip the plugin files - Defender';
			}
		}else{
			$error[] = 'File does not exixt - Defender';
		}
		//Hummingbird
		if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Hummingbird.zip')){
			if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Hummingbird.zip', $__settings['softpath'].'/wp-content/plugins/')){
				$error[] = 'Could not unzip the plugin files - Hummingbird';
			}
		}else{
			$error[] = 'File does not exixt - Hummingbird';
		}
		//Snapshot
		if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Snapshot-pro.zip')){
			if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Snapshot-pro.zip', $__settings['softpath'].'/wp-content/plugins/')){
				$error[] = 'Could not unzip the plugin files - Snapshot';
			}
		}else{
			$error[] = 'File does not exixt - Snapshot';
		}
		//Smush
		if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Smush-pro.zip')){
			if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Smush-pro.zip', $__settings['softpath'].'/wp-content/plugins/')){
				$error[] = 'Could not unzip the plugin files - Smush';
			}
		}else{
			$error[] = 'File does not exixt - Smush';
		}
		//SmartCrawl
		if(file_exists($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Smartcrawl.zip')){
			if(!sunzip($globals['path'].'/conf/mods/'.$software['softname'].'/plugins/Smartcrawl.zip', $__settings['softpath'].'/wp-content/plugins/')){
				$error[] = 'Could not unzip the plugin files - Smartcrawl';
			}
		}else{
			$error[] = 'File does not exixt - Smartcrawl';
		}
		//Add WPMUDev plugins to activation list
		$__settings['active_plugins'][] = 'wpmudev-updates/update-notifications.php';
		$__settings['active_plugins'][] = 'wp-defender/wp-defender.php';
		$__settings['active_plugins'][] = 'wp-hummingbird/wp-hummingbird.php';
		$__settings['active_plugins'][] = 'snapshot/snapshot.php';
		$__settings['active_plugins'][] = 'wp-smush-pro/wp-smush.php';
		$__settings['active_plugins'][] = 'wpmu-dev-seo/wpmu-dev-seo.php';
	}

	//Activate all plugins
	$__settings['active_plugins'] = serialize($__settings['active_plugins']);
	
	$query = "UPDATE ".$__settings['dbprefix']."options
			SET option_value = '".$__settings['active_plugins']."' 
			WHERE option_name = 'active_plugins';";
	$result = sdb_query($query, $__settings['softdbhost'], $__settings['softdbuser'], $__settings['softdbpass'], $__settings['softdb']);

	//WPMUDev Dashboard Database Entries
	$query = "INSERT INTO ".$__settings['dbprefix']."options (option_name, option_value) VALUES ('wpmudev_apikey', '".$scriptSettings['WPMU_DEV_Settings']['Dashboard_API_KEY']."'), ('wdp_un_limit_to_user', '1');";
	$result = sdb_query($query, $__settings['softdbhost'], $__settings['softdbuser'], $__settings['softdbpass'], $__settings['softdb']);

	//Hummingbird Database Entries
	$query = "INSERT INTO ".$__settings['dbprefix']."options (option_name, option_value) VALUES ('wphb-pro', 'yes');";
	$result = sdb_query($query, $__settings['softdbhost'], $__settings['softdbuser'], $__settings['softdbpass'], $__settings['softdb']);
}

?>