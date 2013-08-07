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

class publication_type_so extends so_sql{
	
	var $spicademic_publication_type = 'spicademic_ref_pub_type';
	var $spicademic_ref_field = 'spicademic_ref_field';
	var $spicademic_ref_type_field = 'spicademic_ref_type_field';
	
	var $so_publication_type;
	var $so_field;
	var $so_type_field;
	
	/**
	 * Constructeur
	 *
	 */
	function publication_type_so(){
		$this->so_publication_type = new so_sql('spicademic',$this->spicademic_publication_type);
		$this->so_field = new so_sql('spicademic',$this->spicademic_ref_field);
		$this->so_type_field = new so_sql('spicademic',$this->spicademic_ref_type_field);
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
		foreach((array)$this->so_publication_type->db_data_cols as $id=>$value){
			$tab_search[$id]=$search;
		}
		return $tab_search;
	}

	function add_update_publication_type($info){
	/**
	 * Crée ou met à jour un type de publication
	 *
	 * @param $info : information concernant le type de publication
	 */
		$msg='';
		if(is_array($info)){

			$this->add_update_type_field($info['field']);
			unset($info['field']);
			
			$this->so_publication_type->data = $info;
			if(isset($this->so_publication_type->data['type_id'])){
				// Existant
				$this->so_publication_type->data['type_modified']=time();
				$this->so_publication_type->data['type_modifier']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_publication_type->update($this->so_publication_type->data,true);
				
				$msg .= lang('publication type updated');
			}else{
				// Nouveau
				$this->so_publication_type->data['type_id'] = '';
				$this->so_publication_type->data['type_created']=time();
				$this->so_publication_type->data['type_creator']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_publication_type->save();
				
				$msg .= lang('publication type created');
			}
		}
		return $msg;
	}

	function add_update_type_field($info){
	/**
	 * Mets a jour un association entre type de publication et champs
	 *
	 * @param $info : information about the date to updates
	 * @return string
	 */
		foreach((array)$info as $key => $data){
			if(is_numeric($key)){
				$this->so_type_field->data = $data;
				$this->so_type_field->save();
			}
		}
	}
}
?>