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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.file_type_so.inc.php');	

class file_type_bo extends file_type_so{
	
	/**
	 * Constructeur
	 *
	 */
	function file_type_bo(){
		parent::file_type_so();
	}
	
	function get_info($id){
	/**
	 * Retourne les informations d'un type de fichier
	 *
	 * @param $id : identifiant du type de fichier
	 * @return array
	 */
		return $this->so_file_type->read($id);
	}
	
	function get_rows($query,&$rows,&$readonlys){
	/**
	 * Récupère et filtre les types de fichier
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
			$query['col_filter']['file_type_active'] = $query['filter'];
		}
		
		// Filtre champ recherche
		if(!is_array($query['search'])){
			$search = $this->construct_search($query['search']);
		}else{
			$search=$query['search'];
		}

		$rows = $this->so_file_type->search($search,false,$order,'',$wildcard,false,$op,$start,$query['col_filter']);
		if(!$rows){
			$rows = array();
		}
	
		$GLOBALS['egw_info']['flags']['app_header'] = lang('file type Management');

		return $this->so_file_type->total;	
    }

    function get_parents($id){
    /**
     * Retourne la liste des parents possible pour un type de fichier
     *
     * @param $id : identifiant du type de fichier en cours de traitement
     * @return array
     */
   		$return = array();
   		$info = $this->so_file_type->search(array('file_type_active'=>'1'),false);
   		foreach((array)$info as $id => $data){
   			if(empty($data['file_type_parent']) && $data['file_type_id'] != $id){
   				$return[$data['file_type_id']] = $data['file_type_title'];
   			}
   		}
   		return $return;
    }

    function get_access(){
    /**
     * Retourne les acces possible sur le type
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