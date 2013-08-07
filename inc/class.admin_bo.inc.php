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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.admin_so.inc.php');	

class admin_bo extends admin_so{
	
		
	function admin_bo(){
	/**
	 * Constructeur
	 *
	 */
		parent::admin_so();
	}
	
	function add_update_config($info){
	/**
	 * Routine permettant de créer/modifier la config
	 *
	 * @param array $content=null
	 * @return string
	 */
		$obj = CreateObject('phpgwapi.config');
		foreach((array)$info as $id => $value){
			$obj->save_value($id,$value,'spicademic');
		}
		$this->config=$obj->read('spicademic');
		return lang('Configuration updated');
	}
	
	function get_publi_status(){
    /**
     * Retourne la liste des statuts de publication
     *
     * @return array
     */
    	$return = array();
		$info = $this->so_ref_publi_status->search(array('status_active'=>'1'),false);
    	foreach((array)$info as $data){
    		$return[$data['status_id']] = $data['status_label'];
    	}
		
		return $return;
    }

    function get_file_status(){
    /**
     * Retourne la liste des statuts de fichier
     *
     * @return array
     */
    	$return = array();
		$info = $this->so_file_status->search(array('status_active'=>'1'),false);
    	foreach((array)$info as $data){
    		$return[$data['status_id']] = $data['status_label'];
    	}
		
		return $return;
    }

    function get_role(){
    /**
     * Retourne la liste des roles
     *
     * @return array
     */
    	$return = array();
		$info = $this->so_role->search(array('role_active'=>'1'),false);
    	foreach((array)$info as $data){
    		$return[$data['role_id']] = $data['role_label'];
    	}
		
		return $return;
    }

    function get_fields(){
    /**
     * Retourne la liste des champs
     *
     * @return array
     */
    	$return = array();
		$info = $this->so_field->search(array('field_active'=>'1'),false);
    	foreach((array)$info as $data){
    		$return[$data['field_id']] = $data['field_label'];
    	}
		
		return $return;
    }
}
?>