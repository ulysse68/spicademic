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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.extension_bo.inc.php');
require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.acl_spicademic.inc.php');

class extension_ui extends extension_bo{
	
	var $public_functions = array(
		'index'	=> true,
		'edit' 	=> true,
	);
	
	/**
	 * Constructeur
	 *
	 */
	function extension_ui(){
		parent::extension_bo();

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
	 */
		// Récupération d'un éventuel retour
		if(isset($_GET['msg'])){
			$msg = $_GET['msg'];
		}

		// Suppression d'une extension
		if(isset($content['nm']['rows']['delete'])){
			list($id) = @each($content['nm']['rows']['delete']);

			if($this->so_extension->delete($id)){
				$msg = lang('extension deleted');
			}
			unset($content['nm']['rows']['delete']);
		}
		
		// Premier passage sur la page
		if (!is_array($content['nm']))
		{
			$default_cols='extension_id,extension_label,extension_actif,extension_icone';
			$content['nm'] = array(                           // I = value set by the app, 0 = value on return / output
				'get_rows'       	=>	'spicademic.extension_bo.get_rows',	// I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
				'bottom_too'     	=> false,		// I  show the nextmatch-line (arrows, filters, search, ...) again after the rows
				'never_hide'     	=> true,		// I  never hide the nextmatch-line if less then maxmatch entrie
				'no_cat'         	=> true,
				'filter_no_lang' 	=> false,		// I  set no_lang for filter (=dont translate the options)
				'filter2_no_lang'	=> false,		// I  set no_lang for filter2 (=dont translate the options)
				'lettersearch'   	=> false,
				'no_filter2'		=> true,
				'options-cat_id' 	=> false,
				'start'          	=>	0,			// IO position in list
				'cat_id'         	=>	'',			// IO category, if not 'no_cat' => True
				'search'         	=>	'',// IO search pattern
				'order'          	=>	'extension_id',	// IO name of the column to sort after (optional for the sortheaders)
				'sort'           	=>	'ASC',		// IO direction of the sort: 'ASC' or 'DESC'
				'col_filter'     	=>	array(),	// IO array of column-name value pairs (optional for the filterheaders)
				'filter_label'   	=>	'',	// I  label for filter    (optional)
				'filter'         	=>	'',	// =All	// IO filter, if not 'no_filter' => True
				'default_cols'   	=> $default_cols,
				'filter_onchange' 	=> "this.form.submit();",
				'filter2_onchange' 	=> "this.form.submit();",
				'no_csv_export'		=> true,
				'csv_fields'		=> false,
				//'manual'         => $do_email ? ' ' : false,	// space for the manual icon
			);
		}
		
		// Message de retour
		$content['msg'] = $msg;

		// Listes
		$sel_options = array(
			'filter' => array(''=>lang('All status'),'1'=>lang('Active'),'0'=>lang('Inactive')),
		);
		
		$tpl = new etemplate('spicademic.extension.index');
		$content['nm']['header_right'] = 'spicademic.extension.index.right';
		$GLOBALS['egw_info']['flags']['app_header'] = lang('extension management');
		$tpl->read('spicademic.extension.index');
		$tpl->exec('spicademic.extension_ui.index', $content, $sel_options, $readonlys, array('nm' => $content['nm']));
	}
	
	function edit($content = null){
	/**
	 * Charge le template edit
	 */
		$msg='';
	
		if(is_array($content)){
			// Clic sur un bouton (save/apply/cancel)
			list($button) = @each($content['button']);
			switch($button){
				case 'save':
				case 'apply':
					$msg = $this->add_update_extension($content);
					if($button=='save'){
						echo "<html><body><script>var referer = opener.location;opener.location.href = referer+(referer.search?'&':'?')+'msg=".
							addslashes(urlencode($msg))."'; window.close();</script></body></html>\n";
						$GLOBALS['egw']->common->egw_exit();
					}
					$GLOBALS['egw_info']['flags']['java_script'] .= "<script language=\"JavaScript\">
						var referer = opener.location;
						opener.location.href = referer+(referer.search?'&':'?')+'msg=".addslashes(urlencode($msg))."';</script>";
					break;
				case 'cancel':
					echo "<html><body><script>window.close();</script></body></html>\n";
					$GLOBALS['egw']->common->egw_exit();
					break;
			}
			$id = $this->so_extension->data['extension_id'];
			
			$content['msg']=$msg;
		}else{
			// Récupération de l'identifiant de l'extansion
			if(isset($_GET['id'])){
				$id=$_GET['id'];
			}else{
				$id='';
			}
		}

		if(isset($id)){
			$content = array(
				'msg'         => $msg,
			);
			if(empty($id)){
				// Nouvelle extension
				$GLOBALS['egw_info']['flags']['app_header'] = lang('Add extension');
				$content['extension_active'] = true;
			}else{
				// Modification d'une extension
				$content += $this->get_info($id);
				$GLOBALS['egw_info']['flags']['app_header'] = lang('Edit extension');	
			}
		}
		
		$tpl = new etemplate('spicademic.extension.edit');
		$tpl->read('spicademic.extension.edit');
		$tpl->exec('spicademic.extension_ui.edit', $content, $sel_options, $readonlys, $content,2);
	}
}
?>