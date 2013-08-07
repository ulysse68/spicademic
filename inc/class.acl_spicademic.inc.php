<?php
/**
 * eGroupware - SpiCademic - 
 * SpiCademic : Modules to manage academic publications & presentations
 *
 * @link http://www.spirea.fr
 * @package spicademic
 * @author Spirea SARL <contact@spirea.fr>
 * @copyright (c) 2012-10 by Spirea
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
  */

class acl_spicademic {
	function get_spicademic_level(){
	/**
	 * Constructeu²r
	 */
		$config = CreateObject('phpgwapi.config');
		$obj_config = $config->read('spicademic');
				
		$managers = array();
		
		// Récupération des groupes de l'utilisateur
		$groupeUser = array_keys($GLOBALS['egw']->accounts->memberships($GLOBALS['egw_info']['user']['account_id']));

		if($GLOBALS['egw_info']['user']['apps']['admin']){
			// Admin
			$GLOBALS['egw_info']['user']['SpicademicLevel'] = 99;
		}elseif(in_array($obj_config['ManagementGroup'],$groupeUser)){
			// Groupe de gestion manager
			$GLOBALS['egw_info']['user']['SpicademicLevel'] = 59;
		}elseif(in_array($GLOBALS['egw_info']['user']['account_id'],$managers)){
			// Manager
			$GLOBALS['egw_info']['user']['SpicademicLevel'] = 19;
		}else{
			// Utilisateur
			$GLOBALS['egw_info']['user']['SpicademicLevel'] = 1;
		}
		
		return $GLOBALS['egw_info']['user']['SpicademicLevel'];
	}
}

?>