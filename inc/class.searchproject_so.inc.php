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

class searchproject_so extends so_sql{
	
	var $spicademic_searchproject = 'spicademic_ref_project';
	
	var $so_searchproject;
	
	/**
	 * Constructeur
	 *
	 */
	function searchproject_so(){
		$this->so_searchproject = new so_sql('spicademic',$this->spicademic_searchproject);
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
		foreach((array)$this->so_searchproject->db_data_cols as $id=>$value){
			$tab_search[$id]=$search;
		}
		return $tab_search;
	}

	function add_update_searchproject($info){
	/**
	 * Crée ou met à jour un projet de recherche
	 *
	 * @param $info : information concernant le projet de recherche
	 */
		$msg='';
		if(is_array($info)){
			unset($info['button']);
			unset($info['nm']);
			unset($info['msg']);
			$this->so_searchproject->data = $info;
			if(isset($this->so_searchproject->data['proj_id'])){
				// Existant
				$this->so_searchproject->data['proj_modified']=time();
				$this->so_searchproject->data['proj_modifier']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_searchproject->update($this->so_searchproject->data,true);
				
				$msg .= lang('searchproject updated');
			}else{
				// Nouveau
				$this->so_searchproject->data['proj_id'] = '';
				$this->so_searchproject->data['proj_created']=time();
				$this->so_searchproject->data['proj_creator']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_searchproject->save();
				
				$msg .= lang('searchproject created');
			}
		}
		return $msg;
	}
}
?>