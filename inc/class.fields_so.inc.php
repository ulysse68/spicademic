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

class fields_so extends so_sql{
	
	var $spicademic_fields = 'spicademic_ref_field';
	
	var $so_fields;
	
	/**
	 * Constructeur
	 *
	 */
	function fields_so(){
		$this->so_fields = new so_sql('spicademic',$this->spicademic_fields);
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
		foreach((array)$this->so_fields->db_data_cols as $id=>$value){
			$tab_search[$id]=$search;
		}
		return $tab_search;
	}

	function add_update_fields($info){
	/**
	 * Crée ou met à jour un champ
	 *
	 * @param $info : information concernant le champ
	 */
		$msg='';
		if(is_array($info)){
			unset($info['button']);
			unset($info['nm']);
			unset($info['msg']);
			
			if(isset($info['field_id'])){
				// Nouveau
				$check_exist = $this->so_fields->search(array('field_label' => $info['field_label']),false,$order,'',$wildcard,false,$op,$start,$query['col_filter'],'WHERE field_id <> '.$info['field_id']);

				// Controle sur la presence d'un autre champs avec le meme label
				if(is_array($check_exist)){
					$msg = lang('Error while saving').' : '.lang('Label already exist for an existing field');
				}else{
					$this->so_fields->data = $info;
					$this->so_fields->data['field_modified']=time();
					$this->so_fields->data['field_modifier']=$GLOBALS['egw_info']['user']['account_id'];
					$this->so_fields->update($this->so_fields->data,true);
					
					$msg .= lang('field updated');
				}
			}else{
				// Edition d'un existant
				$check_exist = $this->so_fields->search(array('field_label' => $info['field_label']),false);

				// Controle sur la presence d'un autre champs avec le meme label
				if(is_array($check_exist)){
					$msg = lang('Error while saving').' : '.lang('Label already exist for an existing field');
				}else{
					$this->so_fields->data['field_id'] = '';
					$this->so_fields->data['field_creation']=time();
					$this->so_fields->data['field_creator']=$GLOBALS['egw_info']['user']['account_id'];
					$this->so_fields->save();
					
					$msg .= lang('field created');
				}
			}
		}
		return $msg;
	}
}
?>