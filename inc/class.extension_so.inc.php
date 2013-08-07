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

class extension_so extends so_sql{
	
	var $spicademic_extension = 'spicademic_ref_extension';
	
	var $so_extension;
	
	/**
	 * Constructeur
	 *
	 */
	function extension_so(){
		$this->so_extension = new so_sql('spicademic',$this->spicademic_extension);
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
		$tab_search = array();
		foreach((array)$this->so_extension->db_data_cols as $id=>$value){
			$tab_search[$id]=$search;
		}
		return $tab_search;
	}

	function add_update_extension($info){
	/**
	 * Crée ou met à jour une extension
	 *
	 * @param $info : information concernant l'extansion
	 */
		$msg='';
		if(is_array($info)){
			unset($info['button']);
			unset($info['nm']);
			unset($info['msg']);
			$this->so_extension->data = $info;
			if(isset($this->so_extension->data['extension_id'])){
				$this->so_extension->data['extension_modified']=time();
				$this->so_extension->data['extension_modifier']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_extension->update($this->so_extension->data,true);
				
				$msg .= lang('Extension updated');
			}else{
				$this->so_extension->data['extension_id'] = '';
				$this->so_extension->data['extension_created']=time();
				$this->so_extension->data['extension_creator']=$GLOBALS['egw_info']['user']['account_id'];
				$this->so_extension->save();
				
				$msg .= lang('Extension created');
			}
		}
		return $msg;
	}
}
?>