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

class admin_so extends so_sql{
	// Tables
	var $spicademic_ref_pub_status = 'spicademic_ref_pub_status';
	var $spicademic_ref_pub_status_transition = 'spicademic_ref_pub_status_transition';
	var $spicademic_ref_file_status = 'spicademic_ref_file_status';
	var $spicademic_ref_pub_type = 'spicademic_ref_pub_type';
	var $spicademic_ref_role = 'spicademic_ref_role';
	var $spicademic_ref_field = 'spicademic_ref_field';
	
	// Variable so_sql de chaque table
	var $so_ref_publi_status;
	var $so_ref_publi_status_transition;
	var $so_file_status;
	var $so_ref_publi_type;
	var $so_role;
	var $so_field;
	

	
	var $config;
	
	function admin_so(){
	/**
	 * Constructeur
	 *
	 */
		/* Récupération les infos de configurations */
		$config = CreateObject('phpgwapi.config');
		$this->config = $config->read('spicademic');	
		
		
		// Création des différents so_sql
		$this->so_ref_publi_status = new so_sql('spicademic',$this->spicademic_ref_pub_status);
		$this->so_ref_publi_status_transition = new so_sql('spicademic',$this->spicademic_ref_pub_status_transition);
		$this->so_file_status = new so_sql('spicademic',$this->spicademic_ref_file_status);
		$this->so_ref_publi_type = new so_sql('spicademic',$this->spicademic_ref_pub_type);
		$this->so_role = new so_sql('spicademic',$this->spicademic_ref_role);
		$this->so_field = new so_sql('spicademic',$this->spicademic_ref_field);

	}

}
?>