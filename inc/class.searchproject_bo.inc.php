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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.searchproject_so.inc.php');	

class searchproject_bo extends searchproject_so{
	
	/**
	 * Constructeur
	 *
	 */
	function searchproject_bo(){
		parent::searchproject_so();
	}
	
	function get_info($id){
	/**
	 * Retourne les information d'un projet de recherche
	 *
	 * @param $id : identifiant du projet de recherche
	 * @return array
	 */
		return $this->so_searchproject->read($id);
	}
	
	function get_rows($query,&$rows,&$readonlys){
	/**
	 * Récupère et filtre les projets de recherche
	 *
	 * @param array $query avec des clefs comme 'start', 'search', 'order', 'sort', 'col_filter'. Pour définir d'autres clefs comme 'filter', 'cat_id', vous devez créer une classe fille
	 * @param array &$rows lignes complétés
	 * @param array &$readonlys pour mettre les lignes en read only en fonction des ACL, non utilisé ici (à utiliser dans une classe fille)
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
			$query['col_filter']['proj_active'] = $query['filter'];
		}

		// Filtre champ recherche
		if(!is_array($query['search'])){
			$search = $this->construct_search($query['search']);
		}else{
			$search=$query['search'];
		}

		$rows = $this->so_searchproject->search($search,false,$order,'',$wildcard,false,$op,$start,$query['col_filter']);
		if(!$rows){
			$rows = array();
		}

		// Filtre les parents et enfants dans des tableaux différents.
		foreach((array)$rows as $row){
			if($row['proj_parent'] > 0){
				$childs[$row['proj_parent']][] = $row;
			}else{
				$parents[] = $row;
			}
		}
		
		// On récupère les parents puis les enfants pour ces parents
		foreach((array)$parents as $parent){
			$temp_rows[] = $parent;
			$temp_rows[count($temp_rows)-1]['class'] = 'bold';

			foreach((array)$childs[$parent['proj_id']] as $child){
				$temp_rows[] = $child;
				$temp_rows[count($temp_rows)-1]['class'] = 'sub';
				$readonlys['add['.$child['proj_id'].']'] = true;
			}
		}
		$rows = $temp_rows;
		
		$GLOBALS['egw_info']['flags']['app_header'] = lang('searchproject Management');
		
		return $this->so_searchproject->total;	
    }

    function get_parents($id){
    /**
     * Retourne la liste des parents possible pour un type
     *
     * @param $id : identifiant du type en cours de traitement
     * @return array
     */
   		$return = array();
   		$info = $this->so_searchproject->search(array('proj_active'=>'1'),false);
   		foreach((array)$info as $id => $data){
   			if(empty($data['proj_parent']) && $data['proj_id'] != $id){
   				$return[$data['proj_id']] = $data['proj_title'];
   			}
   		}
   		return $return;
    }
}
?>