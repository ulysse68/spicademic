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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.publication_status_so.inc.php');	

class publication_status_bo extends publication_status_so{
	
	/**
	 * Constructeur
	 *
	 */
	function publication_status_bo(){
		parent::publication_status_so();
	}
	
	function get_info($id){
	/**
	 * Retourne les informations concernant un statut
	 *
	 * @param $id : identifiant du statut  
	 * @return array
	 */
		$info = $this->so_publication_status->read($id);

		$transition = $this->so_transition_status->search(array('status_source' => $id),false);
		foreach((array)$transition as $keyTransition => $dataTransition){
			$info['status_childs'][$keyTransition] = $dataTransition['status_target'];
		}

		return $info;
	}
	
	function get_rows($query,&$rows,&$readonlys){
	/**
	 * R�cup�re et filtre les statuts
	 *
	 * @param array $query avec des clefs comme 'start', 'search', 'order', 'sort', 'col_filter'. Pour d�finir d'autres clefs comme 'filter', 'cat_id', vous devez cr�er une classe fille
	 * @param array &$rows lignes compl�t�s
	 * @param array &$readonlys pour mettre les lignes en read only en fonction des ACL, non utilis� ici (� utiliser dans une classe fille)
	 * @return int
	 */
		if(!is_array($query['col_filter']) && empty($query['col_filter'])){
			$query['col_filter']=array();
		}
		
		$order=$query['order'].' '.$query['sort'];
		$id_only=false;
		$start=array(
			(int)$query['start'],
			(int) $query['num_rows']
		);
		$wildcard = '%';
		$op = 'OR';
		
		// Filtre sur les actifs/inactifs
		if(!empty($query['filter']) or ($query['filter']==0)){
			$query['col_filter']['status_active'] = $query['filter'];
		}
		
		// Filtre champ recherche
		if(!is_array($query['search'])){
			$search = $this->construct_search($query['search']);
		}else{
			$search=$query['search'];
		}

		$rows = $this->so_publication_status->search($search,false,$order,'',$wildcard,false,$op,$start,$query['col_filter']);
		if(!$rows){
			$rows = array();
		}
		foreach($rows as $id=>$value){
			$transition = $this->so_transition_status->search(array('status_source' => $value['status_id']),false);
			foreach((array)$transition as $keyTransition => $dataTransition){
				$rows[$id]['status_childs'][$keyTransition] = $dataTransition['status_target'];
			}
		}
		
		$GLOBALS['egw_info']['flags']['app_header'] = lang('Status Management');

		return $this->so_publication_status->total;	
    }

    function get_possible_child($id){
	/**
	 * Retourne la liste des enfants possible pour un statut
	 *
	 * @return array
	 */
		$retour = array();
		$info = $this->so_publication_status->search(array('status_active' => true),false,'status_label');
		$i = 0;
		foreach((array)$info as $key => $data){
			if($data['status_id'] != $id){
				$retour[$data['status_id']] = $data['status_label'];
			}
		}

		return $retour;
	}
}
?>