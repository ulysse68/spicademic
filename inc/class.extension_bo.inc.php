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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.extension_so.inc.php');	

class extension_bo extends extension_so{
	
	/**
	 * Constructeur
	 *
	 */
	function extension_bo(){
		parent::extension_so();
	}
	
	function get_info($id){
	/**
	 * Retourne les information d'une extension
	 *
	 * @param $id : identifiant de l'extension
	 * @return array
	 */
		return $this->so_extension->read($id);
	}

	function get_rows($query,&$rows,&$readonlys){
	/**
	 * Récupère et filtre les extensions
	 *
	 * @param array $query avec des clefs comme 'start', 'search', 'order', 'sort', 'col_filter'. Pour définir d'autres clefs comme 'filter', 'cat_id', vous devez créer une classe fille
	 * @param array &$rows lignes complétés
	 * @param array &$readonlys pour mettre les lignes en read only en fonction des ACL
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

		// Filtre champs recherche
		if(!is_array($query['search'])){
			$search = $this->construct_search($query['search']);
		}else{
			$search=$query['search'];
		}

		// Filtre sur les actifs/inactifs
		if(!empty($query['filter']) or ($query['filter']==0)){
			$query['col_filter']['extension_active'] = $query['filter'];
		}

		// Récupération des lignes de données
		$rows = $this->so_extension->search($search,false,$order,'',$wildcard,false,$op,$start,$query['col_filter']);
		if(!$rows){
			$rows = array();
		}

		$GLOBALS['egw_info']['flags']['app_header'] = lang('Extension Management');

		return $this->so_extension->total;	
    }
}
?>