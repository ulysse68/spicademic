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

class publication_status_so extends so_sql{
	
	var $spicademic_publication_status = 'spicademic_ref_pub_status';
	var $spicademic_transition_status = 'spicademic_ref_pub_status_transition';
	
	var $so_publication_status;
	var $so_transition_status;
	
	/**
	 * Constructeur
	 *
	 */
	function publication_status_so(){
		$this->so_publication_status = new so_sql('spicademic',$this->spicademic_publication_status);
		$this->so_transition_status = new so_sql('spicademic',$this->spicademic_transition_status);
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
		foreach((array)$this->so_publication_status->db_data_cols as $id=>$value){
			$tab_search[$id]=$search;
		}
		return $tab_search;
	}

	function add_update_publication_status($info){
	/**
	 * Crée ou met à jour un statut
	 *
	 * @param $info : information concernant le statut
	 */
		$msg='';
		if(is_array($info)){
			unset($info['button']);
			unset($info['nm']);
			unset($info['msg']);
			$this->so_publication_status->data = $info;

			if(isset($this->so_publication_status->data['status_id'])){
				// Existant
				$this->so_publication_status->data['status_modified']=time();
				$this->so_publication_status->data['status_modifier']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_publication_status->update($this->so_publication_status->data,true);

				$infoTransition['status_id'] = $this->so_publication_status->data['status_id'];
				$infoTransition['status_childs'] = explode(',',$info['status_childs']);
				$msg = $this->add_update_transition($infoTransition);
				
				$msg .= lang('status updated');
			}else{
				// Nouveau
				$this->so_publication_status->data['status_id'] = '';
				$this->so_publication_status->data['status_created']=time();
				$this->so_publication_status->data['status_creator']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_publication_status->save();

				$infoTransition['status_id'] = $this->so_publication_status->data['status_id'];
				$infoTransition['status_childs'] = explode(',',$info['status_childs']);
				$msg = $this->add_update_transition($infoTransition);
				
				$msg .= lang('status created');
			}
		}
		return $msg;
	}

	function add_update_transition($info){
	/**
	 * Crée ou met à jour les transitions d'un statut
	 *
	 * @param $info : information concernant la transition (statut_id, statut_enfants(array))
	 */
		$msg = '';
		if(is_array($info)){
			$this->so_transition_status->delete(array('status_source' => $info['status_id']));
			foreach((array)$info['status_childs'] as $key => $child){
				if(!empty($child)){
					$this->so_transition_status->data['status_source'] = $info['status_id'];
					$this->so_transition_status->data['status_target'] = $child;
					$this->so_transition_status->save();
				}
			}
		}
	}
	
	
}
?>