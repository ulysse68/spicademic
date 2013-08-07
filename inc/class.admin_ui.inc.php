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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.admin_bo.inc.php');
require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.acl_spicademic.inc.php');

class admin_ui extends admin_bo{
	
	// Function appelable par l'url	
	var $public_functions = array(
		'index'	=> true,
		'edit' 	=> true,
	);
	
	
	function admin_ui(){
	/**
	 * Constructeur
	 *
	 */
		parent::admin_bo();

		// Construction des droits - une seule fonction - dans class.acl_spicademic.inc.php 
		$GLOBALS['egw_info']['user']['SpicademicLevel'] = acl_spicademic::get_spicademic_level();
		// Gestion ACL - Simple utilisateur = Pas d'accès
		if ($GLOBALS['egw_info']['user']['SpicademicLevel'] <= 10){
			$GLOBALS['egw']->framework->render('<h1 style="color: red;">'.lang('Permission denied, please contact your administrator!!!')."</h1>\n",null,true);
			exit;
		}
		// Fin blocage au niveau du constructeur
	}
	
	function index($content = null){
	/**
	 * Charge le template index
	 *
	 */ 
		$msg='';
		if(is_array($content)){
			list($button) = @each($content['button']);
			// Clic sur un bouton (save/apply/cancel)
			switch($button){
				case 'save':
				case 'apply':
					$msg=$this->add_update_config($content);
					$GLOBALS['egw_info']['flags']['java_script'] .= "<script language=\"JavaScript\">
						var referer = opener.location;
						opener.location.href = referer+(referer.search?'&':'?')+'msg=".addslashes(urlencode($msg))."';</script>";
					break;
				default:
				case 'cancel':
					echo "<html><body><script>window.close();</script></body></html>\n";
					$GLOBALS['egw']->common->egw_exit();
					break;
			}
		}
		
		// Récupération des données
		$content = $this->config;
		
		// Remplissage des listes
		$sel_options = array(
			'default_pub_status' => $this->get_publi_status(),
			'validated_pub_status' => $this->get_publi_status(),
			'archived_pub_status' => $this->get_publi_status(),
			'pending_pub_status' => $this->get_publi_status(),
			
			'default_file_status' => $this->get_file_status(),

			'author_role' => $this->get_role(),
			'translator_role' => $this->get_role(),
			'editor_role' => $this->get_role(),

			'xml_date' => $this->get_fields(),
		);		
		
		$tpl = new etemplate('spicademic.admin.general');
		$tpl->exec('spicademic.admin_ui.index', $content,$sel_options,$no_button, $content);
	}
}
?>