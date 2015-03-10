<?php
/**
 * eGroupware - SpiCademic - Hooks
 * SpiCademic : Modules to manage academic publications & presentations
 *
 * @link http://www.spirea.fr
 * @package spicademic
 * @author Spirea SARL <contact@spirea.fr>
 * @copyright (c) 2012-10 by Spirea
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
  */

 /**
 * diverse static hooks
 */
 

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.acl_spicademic.inc.php');	
require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.spicademic_so.inc.php');	

class spicademic_hooks
{

	static function search_link($location)
	{
	/**
	* Méthode initialisant les variables globales
	* 
	* @param int $location paramètres locaux à charger
	* @return array
	*/
		$appname = 'spicademic';
		/* Récupération des droits d'accès ACL */
		$acl = CreateObject($appname.'.acl_'.$appname);
		
		return array(
			'query' => 'spicademic.spicademic_bo.link_query',
			'title' => 'spicademic.spicademic_bo.link_title',
			'titles' => 'spicademic.spicademic_bo.link_titles',
			'view'  => array(
				'menuaction' => 'spicademic.spicademic_ui.edit',
			),
			'view_id' => 'id',
			'view_popup'  => '930x700',
			'add' => array(
				'menuaction' => 'spicademic.spicademic_ui.edit',
			),
			'add_app'    => 'link_app',
			'add_id'     => 'link_id',
			'add_popup'  => '930x700',
		);
	}

	static function all_hooks($args){
	/**
	* Méthode initialisant les variables globales des tickets et chargeant les préférences paramétrées.
	* Permet aussi d'afficher le menu et de créer des liens dirigés vers son contenu
	*
	* \version 
	*
	* @param array $args tableau contenant l'index location définissant l'endroit où l'utilisateur se trouve : spicademic menu,spicademic,admin,... (on en déduit ainsi les paramètres à afficher)
	*/
		$appname = 'spicademic';
		$location = is_array($args) ? $args['location'] : $args;
		
		$config = CreateObject('phpgwapi.config');
		$obj_config = $config->read('spicademic');
		
		// Récupération des groupes de l'utilisateur
		$groupeUser = array_keys($GLOBALS['egw']->accounts->memberships($GLOBALS['egw_info']['user']['account_id']));
		
		// Construction des droits - une seule fonction - dans class.acl_so.inc.php 
		$GLOBALS['egw_info']['user']['SpicademicLevel'] = acl_spicademic::get_spicademic_level();


		if ($location == 'sidebox_menu'){
			$file = array();
			display_sidebox($appname,lang('Publications'),$file);
		}
		/***** Menu Publications & présentations *****/
		if ($GLOBALS['egw_info']['user']['apps']['spicademic'] && $location != 'admin' && $location != 'preferences'){
			$file = array();
			
			if(spicademic_so::is_manager()){
				// $file[]=array(
				// 	'text' => '<a class="textSidebox" href="'.$GLOBALS['egw']->link('/index.php',array('menuaction' => 'spicademic.spicademic_ui.edit')).
				// 	'" onclick="window.open(this.href,\'_blank\',\'dependent=yes,width=990,height=600,scrollbars=yes,status=yes\');
				// 	return false;">'.lang('New publication').'</a>',
				// 	'no_lang' => true,
				// 	'link' => false,
				// );
				$file['New publication'] = "javascript:egw_openWindowCentered2('".egw::link('/index.php',array('menuaction' => 'spicademic.spicademic_ui.edit'),false)."','_blank',990,600,'yes')";
			}
			
			$file['Pending publications']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.index&view=pending');
			$file['Archived publications']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.index&view=archived');
			$file['Validated publications']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.index&view=validated');
			$file['All publications']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.index&view=all');

			$file['Advanced search']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.index&adv_search='.true);

			if ($location == 'publications'){
				display_section($appname,$file);
			}else{
				display_sidebox($appname,lang('Publications & presentations'),$file);
				// display_sidebox($appname,lang('SpiCademic'),$file);
			}
		}

		/***** Menu Imports *****/
		if ($GLOBALS['egw_info']['user']['apps']['spicademic'] && $location != 'admin' && $location != 'preferences'){
			$file = array();
			
			$file['Import publication']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.import');
			/* Masqué: activer ces 3 lignes plutôt que la précédente si on veut que seuls les admins puissent importer
			if(spicademic_so::is_manager()){
				$file['Import publication']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.import');
			}*/
			
			$file['Imported publications']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.index&view=import');

			if ($location == 'publications'){
				display_section($appname,$file);
			}else{
				display_sidebox($appname,lang('Imports'),$file);
				// display_sidebox($appname,lang('SpiCademic'),$file);
			}
		}
		
		
		/***** Menu Référentiels *****/
		if (($GLOBALS['egw_info']['user']['SpicademicLevel'] >= 20) && $location != 'admin' && $location != 'referentiel'){
			$file = array();
			
			$file['Subjects']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.subject_ui.index');
			$file['Search project']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.searchproject_ui.index');
			$file['Status']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.publication_status_ui.index');
			$file['Types']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.publication_type_ui.index');
			$file['Fields']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.fields_ui.index');
			$file['File status']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.file_status_ui.index');
			$file['File types']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.file_type_ui.index');
			$file['Roles']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.role_ui.index');
			
			$file['Authorized file extension']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.extension_ui.index');
			
			if ($location == 'referentiel'){
				display_section($appname,$file);
			}else{
				display_sidebox($appname,lang('Repository'),$file);
			}
		}

		/***** Menu Admin *****/
		if ($GLOBALS['egw_info']['user']['apps']['admin'] && $location != 'preferences' && $location != 'spicademic'){
			$file = array();
			$file['General']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.admin');

			if ($location == 'admin'){
				display_section($appname,$file);
			}else{
				display_sidebox($appname,lang('Admin'),$file);
			}
		}

		/***** Menu About *****/
		if ($location != 'admin' && $location != 'preferences' && $location != 'spicademic'){
			$file = array();
			$file[lang('About').' SpiCademic']=$GLOBALS['egw']->link('/index.php','menuaction=spicademic.spicademic_ui.about');
			$file[lang('User Manual')]=$obj_config['manualUrl'];
			// $file[lang('License').' SpiCademic']=$GLOBALS['egw']->link('/spicademic/about/Licence_spicademic_fr.pdf');
			display_sidebox($appname,lang('About'.' spicademic'),$file);
		}
		
	}
	
	static function home(){
	/**
	 * Crée l'écran d'accueil avec les paramètres par défaut
	 */
		if($GLOBALS['egw_info']['user']['preferences']['spicademic']['mainscreen_show_spicademic'])
		{
			$content =& ExecMethod('spicademic.spicademic_ui.home');
			$title="Tickets spicademic";
			$portalbox =& CreateObject('phpgwapi.listbox',array(
				'title'	=> $title,
				'primary'	=> $GLOBALS['egw_info']['theme']['navbar_bg'],
				'secondary'	=> $GLOBALS['egw_info']['theme']['navbar_bg'],
				'tertiary'	=> $GLOBALS['egw_info']['theme']['navbar_bg'],
				'width'	=> '100%',
				'outerborderwidth'	=> '0',
				'header_background_image'	=> $GLOBALS['egw']->common->image('phpgwapi/templates/default','bg_filler')
			));
			$GLOBALS['egw_info']['flags']['app_header'] = $save_app_header;
			unset($save_app_header);

			$GLOBALS['portal_order'][] = $app_id = $GLOBALS['egw']->applications->name2id('spicademic');
			foreach(array('up','down','close','question','edit') as $key)
			{
				$portalbox->set_controls($key,Array('url' => '/set_box.php', 'app' => $app_id));
			}
			$portalbox->data = Array();
			echo '<!-- BEGIN spicademic info -->'."\n".$portalbox->draw($content)."\n".'<!-- END spicademic info -->'."\n";
		}
		else
		{
			echo '<!-- BEGIN spicademic info -->'."\nTU AS CHOISI DE NE RIEN AFFICHER\n".'<!-- END spicademic info -->'."\n";
		}
	}

	static function settings(){
	 /**
	 * NOTE : Fonction obligatoire pour la version EGW 1.9 
	 *
	 * @return boolean
	 */
		$settings = array(
			// array(
			// 	'type'  => 'section',
			// 	'title' => lang('Main settings'),
			// 	'no_lang'=> true,
			// 	'xmlrpc' => False,
			// 	'admin'  => False
			// ),
			'setting_code' => array(
				'type'   => 'select',
				'label'  => 'Information for this setting',
				'name'   => 'setting_code',
				'help'   => 'Additional information for the setting',
				'values' => $list,
				'xmlrpc' => True,
				'admin'  => False,
			)
		);
		return $settings;	// otherwise prefs say it cant find the file ;-)
	}
}
?>
