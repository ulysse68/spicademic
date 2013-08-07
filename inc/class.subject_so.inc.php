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

class subject_so extends so_sql{
	
	var $spicademic_subject = 'spicademic_ref_subject';
	
	var $so_subject;
	
	/**
	 * Constructeur
	 *
	 */
	function subject_so(){
		$this->so_subject = new so_sql('spicademic',$this->spicademic_subject);
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
		foreach((array)$this->so_subject->db_data_cols as $id=>$value){
			$tab_search[$id]=$search;
		}
		return $tab_search;
	}

	function add_update_subject($info){
	/**
	 * Crée ou met à jour un sujet
	 *
	 * @param $info : information concernant le sujet
	 */
		$msg='';
		if(is_array($info)){
			unset($info['button']);
			unset($info['nm']);
			unset($info['msg']);
			$this->so_subject->data = $info;
			if(isset($this->so_subject->data['subject_id'])){
				// Existant
				$this->so_subject->data['subject_modified']=time();
				$this->so_subject->data['subject_modifier']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_subject->update($this->so_subject->data,true);
				
				$msg .= ' '.lang('subject updated');
			}else{
				// Nouveau
				$this->so_subject->data['subject_id'] = '';
				$this->so_subject->data['subject_created']=time();
				$this->so_subject->data['subject_creator']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_subject->save();
				
				$msg .= ' '.lang('subject created');
			}
		}
		return $msg;
	}
}
?>