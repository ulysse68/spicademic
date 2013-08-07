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

function spicademic_upgrade1_000()
{
	$GLOBALS['egw_setup']->oProc->AddColumn('spicademic_ref_pub_status','status_responsible',array(
		'type' => 'int',
		'precision' => '4'
	));

	return $GLOBALS['setup_info']['spicademic']['currentver'] = '1.001';
}


function spicademic_upgrade1_001()
{
	/* done by RefreshTable() anyway
	$GLOBALS['egw_setup']->oProc->AlterColumn('spicademic_publi_contact','contact_add_id',array(
		'type' => 'varchar',
		'precision' => '255'
	));*/
	/* done by RefreshTable() anyway
	$GLOBALS['egw_setup']->oProc->AddColumn('spicademic_publi_contact','contact_account_id',array(
		'type' => 'int',
		'precision' => '4'
	));*/
	$GLOBALS['egw_setup']->oProc->RefreshTable('spicademic_publi_contact',array(
		'fd' => array(
			'contact_id' => array('type' => 'auto','nullable' => False),
			'contact_publi' => array('type' => 'int','precision' => '4','nullable' => False),
			'contact_add_id' => array('type' => 'varchar','precision' => '255'),
			'contact_link' => array('type' => 'int','precision' => '4'),
			'contact_role' => array('type' => 'int','precision' => '4'),
			'contact_creator' => array('type' => 'int','precision' => '4'),
			'contact_created' => array('type' => 'int','precision' => '20'),
			'contact_modifier' => array('type' => 'int','precision' => '4'),
			'contact_modified' => array('type' => 'int','precision' => '20'),
			'contact_order' => array('type' => 'int','precision' => '4'),
			'contact_account_id' => array('type' => 'int','precision' => '4')
		),
		'pk' => array('contact_id'),
		'fk' => array('contact_publi' => 'spicademic_publi','contact_link' => 'egw_links','contact_role' => 'spicademic_ref_role'),
		'ix' => array(),
		'uc' => array()
	));

	return $GLOBALS['setup_info']['spicademic']['currentver'] = '1.002';
}

