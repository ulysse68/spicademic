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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.searchproject_bo.inc.php');
require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.acl_spicademic.inc.php');

class searchproject_ui extends searchproject_bo{
	
	var $public_functions = array(
		'index'	=> true,
		'edit' 	=> true,
	);
	
	/**
	 * Constructeur
	 *
	 */
	function searchproject_ui(){
		parent::searchproject_bo();

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
		// Message de retour
		if(isset($_GET['msg'])){
			$msg = $_GET['msg'];
		}

		// Suppression
		if(isset($content['nm']['rows']['delete'])){
			list($id) = @each($content['nm']['rows']['delete']);

			if($this->so_searchproject->delete($id)){
				$msg = lang('Status deleted');
			}
			unset($content['nm']['rows']['delete']);
		}
		
		// Premier passage sur la vue index
		if (!is_array($content['nm']))
		{
			$default_cols='searchproject_id,searchproject_label,searchproject_description,searchproject_active,searchproject_order';
			$content['nm'] = array(                           // I = value set by the app, 0 = value on return / output
				'get_rows'       	=>	'spicademic.searchproject_bo.get_rows',	// I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
				'bottom_too'     	=> false,		// I  show the nextmatch-line (arrows, filters, search, ...) again after the rows
				'never_hide'     	=> true,		// I  never hide the nextmatch-line if less then maxmatch entrie
				'no_cat'         	=> true,
				'filter_no_lang' 	=> false,		// I  set no_lang for filter (=dont translate the options)
				'filter2_no_lang'	=> false,		// I  set no_lang for filter2 (=dont translate the options)
				'lettersearch'   	=> false,
				'no_filter2'		=> true,
				'options-cat_id' => false,
				'start'          	=>	0,			// IO position in list
				'cat_id'         	=>	'',			// IO category, if not 'no_cat' => True
				'search'         	=>	'',// IO search pattern
				'order'          	=>	'proj_id',	// IO name of the column to sort after (optional for the sortheaders)
				'sort'           	=>	'ASC',		// IO direction of the sort: 'ASC' or 'DESC'
				'col_filter'     	=>	array(),	// IO array of column-name value pairs (optional for the filterheaders)
				'filter_label'   	=>	lang('Status'),	// I  label for filter    (optional)
				'filter'         	=>	'',	// =All	// IO filter, if not 'no_filter' => True
				'default_cols'   	=> $default_cols,
				'filter_onchange' 	=> "this.form.submit();",
				'filter2_onchange' 	=> "this.form.submit();",
				'no_csv_export'		=> true,
				'no_columnselection'=> true,
				'csv_fields'		=>false,
				//'manual'         => $do_email ? ' ' : false,	// space for the manual icon
			);
		}
		
		$content['msg'] = $msg;
		
		// Listes
		$sel_options = array(
			'filter' => array(''=>lang('All status'),'1'=>lang('Active'),'0'=>lang('Inactive')),
		);
		
		$tpl = new etemplate('spicademic.searchproject.index');
		$content['nm']['header_right'] = 'spicademic.searchproject.index.right';
		$GLOBALS['egw_info']['flags']['app_header'] = lang('searchproject management');
		$tpl->read('spicademic.searchproject.index');
		$tpl->exec('spicademic.searchproject_ui.index', $content, $sel_options, $readonlys, array('nm' => $content['nm']));
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
					$msg = $this->add_update_searchproject($content);
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
			$id = $this->so_searchproject->data['proj_id'];
			
			$content['msg']=$msg;
		}else{
			// récupération de l'identifiant
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
				// Nouveau
				$GLOBALS['egw_info']['flags']['app_header'] = lang('Add searchproject');
				$content['proj_active'] = true;

				$content['proj_parent'] = $_GET['parent_id'];
			}else{
				// Existant
				$content += $this->get_info($id);
				$GLOBALS['egw_info']['flags']['app_header'] = lang('Edit searchproject');	
			}
		}

		// Listes
		$sel_options = array(
			'proj_parent' => $this->get_parents($content['proj_id']),
		);
		
		$tpl = new etemplate('spicademic.searchproject.edit');
		$tpl->read('spicademic.searchproject.edit');
		$tpl->exec('spicademic.searchproject_ui.edit', $content, $sel_options, $readonlys, $content,2);
	}
}
?>