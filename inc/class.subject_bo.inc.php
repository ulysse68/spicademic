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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.subject_so.inc.php');	

class subject_bo extends subject_so{
	
	/**
	 * Constructeur
	 *
	 */
	function subject_bo(){
		parent::subject_so();
	}
	
	function get_info($id){
	/**
	 * Retourne les informations d'un sujet
	 *
	 * @param $id : identifiant du sujet
	 * @return array
	 */
		return $this->so_subject->read($id);
	}
	
	function get_rows($query,&$rows,&$readonlys){
	/**
	 * Récupère et filtre les statuts
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
			$query['col_filter']['subject_active'] = $query['filter'];
		}
		
		// Filtre champ recherche
		if(!is_array($query['search'])){
			$search = $this->construct_search($query['search']);
		}else{
			$search=$query['search'];
		}

		$rows = $this->so_subject->search($search,false,$order,'',$wildcard,false,$op,$start,$query['col_filter']);
		if(!$rows){
			$rows = array();
		}
		
		// Filtre les parents et enfants dans des tableaux différents.
		foreach((array)$rows as $row){
			if($row['subject_parent'] > 0){
				$childs[$row['subject_parent']][] = $row;
			}else{
				$parents[] = $row;
			}
		}
		
		// On récupère les parents puis les enfants pour ces parents
		foreach((array)$parents as $parent){
			$temp_rows[] = $parent;
			$temp_rows[count($temp_rows)-1]['class'] = 'bold';

			foreach((array)$childs[$parent['subject_id']] as $child){
				$temp_rows[] = $child;
				$temp_rows[count($temp_rows)-1]['class'] = 'sub';
				$readonlys['add['.$child['subject_id'].']'] = true;
			}
		}
		$rows = $temp_rows;

		$GLOBALS['egw_info']['flags']['app_header'] = lang('subject Management');
		
		return $this->so_subject->total;	
    }

    function get_parents($id=''){
    /**
     * Retourne la liste des parents possible pour un sujet
     *
     * @param $id : identifiant du sujet en cours de traitement
     * @return array
     */
   		$return = array();
   		$info = $this->so_subject->search(array('subject_active'=>'1'),false);
   		foreach((array)$info as $key => $data){
   			if(empty($data['subject_parent']) && $data['subject_id'] != $id){
   				$return[$data['subject_id']] = $data['subject_title'];
   			}
   		}
   		return $return;
    }

    function get_access(){
    /**
     * Retourne les acces possible sur le sujet
     *
     * @return array
     */
    	$return = array(
    		'public' => 'public',
    		'readonly' => 'readonly', 
    		'adminonly' => 'adminonly', 
    		'hidden' => 'hidden',
    	);

    	return $return;
    }
}
?>