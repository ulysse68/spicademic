<?php
/**
 * eGroupware - SpiCademic - 
 * SpiCademic : Modules to manage academic files & presentations
 *
 * @link http://www.spirea.fr
 * @package spicademic
 * @author Spirea SARL <contact@spirea.fr>
 * @copyright (c) 2012-10 by Spirea
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
  */

class file_type_so extends so_sql{
	
	var $spicademic_file_type = 'spicademic_ref_file';
	
	var $so_file_type;
	
	/**
	 * Constructeur
	 *
	 */
	function file_type_so(){
		$this->so_file_type = new so_sql('spicademic',$this->spicademic_file_type);
	}
	
	function construct_search($search){
	/**
	 * Crée une recherche. Le tableau de retour contiendra toutes les colonnes de la table en cours, en leur faisant correspondre la valeur $search 
	 *
	 * La requête ainsi crée est prête à être utilisée comme filtre
	 *
	 * @param int $search tableau des critères de recherche
	 * @return array
	 */
		$tab_search=array();
		foreach((array)$this->so_file_type->db_data_cols as $id=>$value){
			$tab_search[$id]=$search;
		}
		return $tab_search;
	}

	function add_update_file_type($info){
	/**
	 * Crée ou met à jour un type de fichier
	 *
	 * @param $info : information concernant le type de fichier
	 */
		$msg='';
		if(is_array($info)){
			unset($info['button']);
			unset($info['nm']);
			unset($info['msg']);
			$this->so_file_type->data = $info;
			if(isset($this->so_file_type->data['file_type_id'])){
				$this->so_file_type->data['file_type_modified']=time();
				$this->so_file_type->data['file_type_modifier']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_file_type->update($this->so_file_type->data,true);
				
				$msg .= lang('file type updated');
			}else{
				$this->so_file_type->data['file_type_id'] = '';
				$this->so_file_type->data['file_type_creation']=time();
				$this->so_file_type->data['file_type_creator']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_file_type->save();
				
				$msg .= lang('file type created');
			}
		}
		return $msg;
	}
}
?>