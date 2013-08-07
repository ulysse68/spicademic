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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.publication_type_so.inc.php');	

class publication_type_bo extends publication_type_so{
	
	/**
	 * Constructeur
	 *
	 */
	function publication_type_bo(){
		parent::publication_type_so();
	}
	
	function get_info($id){
	/**
	 * Retourne la liste des types de publication avec les infos les concernant
	 *
	 * @return array
	 */
		return $this->so_publication_type->read($id);
	}
	
	function get_rows($query,&$rows,&$readonlys){
	/**
	 * Récupère et filtre les types de publication
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
			$query['col_filter']['type_active'] = $query['filter'];
		}
		
		// Filtre champ recherche
		if(!is_array($query['search'])){
			$search = $this->construct_search($query['search']);
		}else{
			$search=$query['search'];
		}

		$rows = $this->so_publication_type->search($search,false,$order,'',$wildcard,false,$op,$start,$query['col_filter']);
		if(!$rows){
			$rows = array();
		}
		
		$GLOBALS['egw_info']['flags']['app_header'] = lang('Publication type Management');

		return $this->so_publication_type->total;	
    }

    function get_parents($id){
    /**
     * Retourne la liste des parents possible pour un type
     *
     * @param $id : identifiant du type en cours de traitement
     * @return array
     */
   		$return = array();
   		$info = $this->so_publication_type->search(array('type_active'=>'1'),false);
   		foreach((array)$info as $id => $data){
   			if(empty($data['type_parent']) && $data['type_id'] != $id){
   				$return[$data['type_id']] = $data['type_title'];
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
    		'0' => 'mandatory',
    		'1' => 'optional', 
    		'2' => 'readonly', 
    		'3' => 'hidden',
    	);

    	return $return;
    }

    function get_fields(){
    /**
     * Retourne la liste des champs disponible (champs actifs)
     *
     * @return array
     */
    	$return = array();
   		$info = $this->so_field->search(array('field_active'=>'1'),false);
   		foreach((array)$info as $id => $data){
   			$return[$data['field_id']] = $data['field_label'];
   		}
   		return $return;
    }

    function get_type_field($type_id){
    /**
     * Retourne la liste des champs associé au type choisit
     *
     * @param $type_id : identifiant du type
     * @return array
     */
    	$type_fields = $this->so_type_field->search(array('type_id' => $type_id),false);

    	$i = 1;
    	foreach((array)$type_fields as $type_field){
    		$return[$i] = $type_field;
    		++$i;
    	}

    	return $return;
    }
}
?>