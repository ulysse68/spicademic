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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.fields_so.inc.php');	

class fields_bo extends fields_so{
	
	/**
	 * Constructeur
	 *
	 */
	function fields_bo(){
		parent::fields_so();
	}
	
	function get_info($id){
	/**
	 * Retourne les informations concernant un champ
	 *
	 * @param $id : identifiant du champ
	 * @return array
	 */
		return $this->so_fields->read($id);
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
			$query['col_filter']['field_active'] = $query['filter'];
		}

		// Filtre champ recherche		
		if(!is_array($query['search'])){
			$search = $this->construct_search($query['search']);
		}else{
			$search=$query['search'];
		}
		
		// Récupération des lignes
		$rows = $this->so_fields->search($search,false,$order,'',$wildcard,false,$op,$start,$query['col_filter']);
		if(!$rows){
			$rows = array();
		}

		
		$GLOBALS['egw_info']['flags']['app_header'] = lang('Fields');

		return $this->so_fields->total;	
    }

    function get_field_type(){
    /**
     * Retourne la liste des type de champs disponible
     *
     * @return array
     */
    	return array(
    		'txt' => lang('Text'),
    		'nbr' => lang('Number'),
    		'box' => lang('Select Box'),
    	);
    }
}
?>