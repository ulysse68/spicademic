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

$setup_info['spicademic']['name'] = 'spicademic';
$setup_info['spicademic']['title'] = 'Modules to manage academic publications & presentations';
$setup_info['spicademic']['version'] = '1.003';
$setup_info['spicademic']['app_order'] = 0;
$setup_info['spicademic']['tables'] = array('spicademic_ref_pub_status','spicademic_ref_pub_status_transition','spicademic_ref_file_status','spicademic_ref_file_status_transition','spicademic_ref_pub_type','spicademic_ref_role','spicademic_ref_file','spicademic_ref_extension','spicademic_ref_subject','spicademic_ref_project','spicademic_ref_field','spicademic_ref_type_field','spicademic_publi','spicademic_publi_extra','spicademic_publi_file','spicademic_publi_comment','spicademic_publi_subject','spicademic_publi_contact');
$setup_info['spicademic']['enable'] = 1;

$setup_info['spicademic']['author'][] = array(
	'name'  => 'Spirea',
	'email' => 'contact@spirea.fr',
	'url'	=> 'http://www.spirea.fr',
);

$setup_info['spicademic']['maintainer'][] = array(
	'name'  => 'Spirea',
	'email' => 'contact@spirea.fr',
	'url'   => 'http://www.spirea.fr'
);

$setup_info['spicademic']['license'] = 'Copyright 2012 - Spirea';
$setup_info['spicademic']['description'] = 'Modules to manage academic publications & presentations';

$setup_info['spicademic']['depends'][] = array(
	'appname' => 'phpgwapi',
	'versions' => array('1.8')
);
$setup_info['spicademic']['depends'][] = array(
	'appname' => 'etemplate',
	'versions' => array('1.8')
);

/* The hooks this app includes, needed for hooks registration */
/* note spirea : doit être nickel : pas de ligne vide, vérifier les applications et chemins */
$setup_info['spicademic']['hooks']['preferences'] = 'spicademic_hooks::all_hooks';  // affiche les liens dans le menu des préférences
$setup_info['spicademic']['hooks']['settings'] = 'spicademic_hooks::settings';  // affiche les liens dans le menu des préférences
$setup_info['spicademic']['hooks']['admin'] = 'spicademic_hooks::all_hooks'; // affiche les liens dans le menu d'administration
$setup_info['spicademic']['hooks']['spicademic menu'] = 'spicademic_hooks::all_hooks'; // affiche les liens dans le menu spiclient menu
$setup_info['spicademic']['hooks']['sidebox_menu'] = 'spicademic_hooks::all_hooks'; // affiche le menu sur la gauche de l'appli
$setup_info['spicademic']['hooks']['search_link'] = 'spicademic_hooks::search_link'; // note : il y avait une faute de frappe !
$setup_info['spicademic']['hooks']['home'] = 'spicademic_hooks::home'; //Permet d'afficher un hook sur la page d'accueil












































