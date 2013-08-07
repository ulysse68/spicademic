<?php
/**
 * eGroupWare - Setup
 * http://www.egroupware.org
 * Created by eTemplates DB-Tools written by ralfbecker@outdoor-training.de
 *
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @package spicademic
 * @subpackage setup
 * @version $Id$
 */


$phpgw_baseline = array(
	'spicademic_ref_pub_status' => array(
		'fd' => array(
			'status_id' => array('type' => 'auto','nullable' => False),
			'status_label' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'status_group' => array('type' => 'int','precision' => '4'),
			'status_order' => array('type' => 'int','precision' => '4'),
			'status_color' => array('type' => 'varchar','precision' => '50'),
			'status_active' => array('type' => 'bool'),
			'status_creator' => array('type' => 'int','precision' => '4'),
			'status_created' => array('type' => 'int','precision' => '20'),
			'status_modifier' => array('type' => 'int','precision' => '4'),
			'status_modified' => array('type' => 'int','precision' => '20'),
			'status_responsible' => array('type' => 'int','precision' => '4')
		),
		'pk' => array('status_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_pub_status_transition' => array(
		'fd' => array(
			'status_source' => array('type' => 'int','precision' => '4','nullable' => False),
			'status_target' => array('type' => 'int','precision' => '4','nullable' => False)
		),
		'pk' => array(),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_file_status' => array(
		'fd' => array(
			'status_id' => array('type' => 'auto','nullable' => False),
			'status_label' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'status_group' => array('type' => 'int','precision' => '4'),
			'status_order' => array('type' => 'int','precision' => '4'),
			'status_color' => array('type' => 'varchar','precision' => '50'),
			'status_active' => array('type' => 'bool'),
			'status_creator' => array('type' => 'int','precision' => '4'),
			'status_created' => array('type' => 'int','precision' => '20'),
			'status_modifier' => array('type' => 'int','precision' => '4'),
			'status_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('status_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_file_status_transition' => array(
		'fd' => array(
			'status_source' => array('type' => 'int','precision' => '4','nullable' => False),
			'status_target' => array('type' => 'int','precision' => '4','nullable' => False)
		),
		'pk' => array(),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_pub_type' => array(
		'fd' => array(
			'type_id' => array('type' => 'auto','nullable' => False),
			'type_title' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'type_code' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'type_bibtex_code' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'type_description' => array('type' => 'longtext'),
			'type_parent' => array('type' => 'int','precision' => '4'),
			'type_level' => array('type' => 'int','precision' => '4'),
			'type_access' => array('type' => 'int','precision' => '4'),
			'type_group' => array('type' => 'int','precision' => '4'),
			'type_responsible' => array('type' => 'int','precision' => '4'),
			'type_order' => array('type' => 'int','precision' => '4'),
			'type_color' => array('type' => 'varchar','precision' => '50'),
			'type_active' => array('type' => 'bool'),
			'type_creator' => array('type' => 'int','precision' => '4'),
			'type_created' => array('type' => 'int','precision' => '20'),
			'type_modifier' => array('type' => 'int','precision' => '4'),
			'type_modified' => array('type' => 'int','precision' => '20'),
			'type_ris_code' => array('type' => 'varchar','precision' => '255')
		),
		'pk' => array('type_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_role' => array(
		'fd' => array(
			'role_id' => array('type' => 'auto','nullable' => False),
			'role_label' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'role_description' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'role_order' => array('type' => 'int','precision' => '4'),
			'role_active' => array('type' => 'bool'),
			'role_creator' => array('type' => 'int','precision' => '4'),
			'role_created' => array('type' => 'int','precision' => '20'),
			'role_modifier' => array('type' => 'int','precision' => '4'),
			'role_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('role_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_file' => array(
		'fd' => array(
			'file_type_id' => array('type' => 'auto','nullable' => False),
			'file_type_label' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'file_type_description' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'file_type_order' => array('type' => 'int','precision' => '4'),
			'file_type_active' => array('type' => 'bool'),
			'file_type_creator' => array('type' => 'int','precision' => '4'),
			'file_type_created' => array('type' => 'int','precision' => '20'),
			'file_type_modifier' => array('type' => 'int','precision' => '4'),
			'file_type_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('file_type_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_extension' => array(
		'fd' => array(
			'extension_id' => array('type' => 'auto','nullable' => False),
			'extension_label' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'extension_code' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'extension_icone' => array('type' => 'varchar','precision' => '50','nullable' => False),
			'extension_active' => array('type' => 'bool'),
			'extension_creator' => array('type' => 'int','precision' => '4'),
			'extension_created' => array('type' => 'int','precision' => '20'),
			'extension_modifier' => array('type' => 'int','precision' => '4'),
			'extension_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('extension_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_subject' => array(
		'fd' => array(
			'subject_id' => array('type' => 'auto','nullable' => False),
			'subject_title' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'subject_code' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'subject_bibtex_code' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'subject_description' => array('type' => 'longtext'),
			'subject_parent' => array('type' => 'int','precision' => '4'),
			'subject_level' => array('type' => 'int','precision' => '4'),
			'subject_access' => array('type' => 'int','precision' => '4'),
			'subject_group' => array('type' => 'int','precision' => '4'),
			'subject_responsible' => array('type' => 'int','precision' => '4'),
			'subject_order' => array('type' => 'int','precision' => '4'),
			'subject_color' => array('type' => 'varchar','precision' => '50'),
			'subject_active' => array('type' => 'bool'),
			'subject_creator' => array('type' => 'int','precision' => '4'),
			'subject_created' => array('type' => 'int','precision' => '20'),
			'subject_modifier' => array('type' => 'int','precision' => '4'),
			'subject_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('subject_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_project' => array(
		'fd' => array(
			'proj_id' => array('type' => 'auto','nullable' => False),
			'proj_title' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'proj_code' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'proj_description' => array('type' => 'longtext'),
			'proj_parent' => array('type' => 'int','precision' => '4'),
			'proj_level' => array('type' => 'int','precision' => '4'),
			'proj_group' => array('type' => 'int','precision' => '4'),
			'proj_responsible' => array('type' => 'int','precision' => '4'),
			'proj_order' => array('type' => 'int','precision' => '4'),
			'proj_color' => array('type' => 'varchar','precision' => '50'),
			'proj_active' => array('type' => 'bool'),
			'proj_creator' => array('type' => 'int','precision' => '4'),
			'proj_created' => array('type' => 'int','precision' => '20'),
			'proj_modifier' => array('type' => 'int','precision' => '4'),
			'proj_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('proj_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_field' => array(
		'fd' => array(
			'field_id' => array('type' => 'auto','nullable' => False),
			'field_label' => array('type' => 'varchar','precision' => '50'),
			'field_bibtex_code' => array('type' => 'varchar','precision' => '50'),
			'field_desc' => array('type' => 'text'),
			'field_level' => array('type' => 'int','precision' => '4'),
			'field_order' => array('type' => 'int','precision' => '4'),
			'field_type' => array('type' => 'varchar','precision' => '20'),
			'field_options' => array('type' => 'text'),
			'field_length' => array('type' => 'int','precision' => '4'),
			'field_rows' => array('type' => 'int','precision' => '4'),
			'field_value_min' => array('type' => 'int','precision' => '4'),
			'field_value_max' => array('type' => 'int','precision' => '4'),
			'field_length_min' => array('type' => 'int','precision' => '4'),
			'field_length_max' => array('type' => 'int','precision' => '4'),
			'field_color' => array('type' => 'varchar','precision' => '10'),
			'field_active' => array('type' => 'bool'),
			'field_export_bibtex' => array('type' => 'bool'),
			'field_export_xml' => array('type' => 'bool'),
			'field_export_rtf' => array('type' => 'bool'),
			'field_export_csv' => array('type' => 'bool'),
			'field_creator' => array('type' => 'int','precision' => '4'),
			'field_created' => array('type' => 'int','precision' => '20'),
			'field_modifier' => array('type' => 'int','precision' => '4'),
			'field_modified' => array('type' => 'int','precision' => '20'),
			'field_xml_code' => array('type' => 'varchar','precision' => '50'),
			'field_ris_code' => array('type' => 'varchar','precision' => '50'),
			'field_export_ris' => array('type' => 'bool')
		),
		'pk' => array('field_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_ref_type_field' => array(
		'fd' => array(
			'type_id' => array('type' => 'int','precision' => '4','nullable' => False),
			'field_id' => array('type' => 'int','precision' => '4','nullable' => False),
			'type_field_access' => array('type' => 'int','precision' => '4'),
			'type_field_creator' => array('type' => 'int','precision' => '4'),
			'type_field_created' => array('type' => 'int','precision' => '20'),
			'type_field_modifier' => array('type' => 'int','precision' => '4'),
			'type_field_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('type_id','field_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_publi' => array(
		'fd' => array(
			'publi_id' => array('type' => 'auto','nullable' => False),
			'publi_type' => array('type' => 'int','precision' => '4','nullable' => False),
			'publi_status' => array('type' => 'int','precision' => '4'),
			'publi_project' => array('type' => 'int','precision' => '4'),
			'publi_other' => array('type' => 'varchar','precision' => '255'),
			'publi_title' => array('type' => 'varchar','precision' => '255'),
			'publi_year' => array('type' => 'int','precision' => '4'),
			'publi_responsible' => array('type' => 'int','precision' => '4'),
			'publi_scope' => array('type' => 'bool'),
			'publi_peer_review' => array('type' => 'int','precision' => '2'),
			'publi_internal_url' => array('type' => 'varchar','precision' => '255'),
			'publi_external_url' => array('type' => 'varchar','precision' => '255'),
			'publi_doi' => array('type' => 'varchar','precision' => '255'),
			'publi_doi_url' => array('type' => 'varchar','precision' => '255'),
			'publi_pdf' => array('type' => 'varchar','precision' => '255'),
			'publi_desc' => array('type' => 'text'),
			'publi_keywords' => array('type' => 'text'),
			'publi_lang' => array('type' => 'varchar','precision' => '5'),
			'publi_creator' => array('type' => 'int','precision' => '4'),
			'publi_created' => array('type' => 'int','precision' => '20'),
			'publi_modifier' => array('type' => 'int','precision' => '4'),
			'publi_modified' => array('type' => 'int','precision' => '20'),
			'publi_address' => array('type' => 'varchar','precision' => '255'),
			'publi_abstract' => array('type' => 'text')
		),
		'pk' => array('publi_id'),
		'fk' => array('publi_type' => 'spicademic_ref_pub_type','publi_status' => 'spicademic_ref_pub_status','publi_project' => 'spicademic_ref_project','publi_responsible' => 'egw_accounts'),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_publi_extra' => array(
		'fd' => array(
			'publi_id' => array('type' => 'int','precision' => '4','nullable' => False),
			'field_id' => array('type' => 'int','precision' => '4','nullable' => False),
			'extra_value' => array('type' => 'varchar','precision' => '255'),
			'extra_creator' => array('type' => 'int','precision' => '4'),
			'extra_created' => array('type' => 'int','precision' => '20'),
			'extra_modifier' => array('type' => 'int','precision' => '4'),
			'extra_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('publi_id','field_id'),
		'fk' => array('publi_id' => 'spicademic_publi','field_id' => 'spicademic_ref_field'),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_publi_file' => array(
		'fd' => array(
			'file_id' => array('type' => 'auto','nullable' => False),
			'file_publi' => array('type' => 'int','precision' => '4','nullable' => False),
			'file_status' => array('type' => 'int','precision' => '4'),
			'file_type' => array('type' => 'int','precision' => '4'),
			'file_checksum' => array('type' => 'varchar','precision' => '255'),
			'file_fs_id' => array('type' => 'int','precision' => '4'),
			'file_extension' => array('type' => 'int','precision' => '4'),
			'file_creator' => array('type' => 'int','precision' => '4'),
			'file_created' => array('type' => 'int','precision' => '20'),
			'file_modifier' => array('type' => 'int','precision' => '4'),
			'file_modified' => array('type' => 'int','precision' => '20'),
			'file_name' => array('type' => 'varchar','precision' => '255')
		),
		'pk' => array('file_id'),
		'fk' => array('file_publi' => 'spicademic_publi','file_status' => 'spicademic_ref_file_status','file_type' => 'spicademic_ref_file','file_fs_id' => 'egw_sqlfs','file_extension' => 'spicademic_ref_extension'),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_publi_comment' => array(
		'fd' => array(
			'comment_id' => array('type' => 'auto','nullable' => False),
			'comment_publi' => array('type' => 'int','precision' => '4'),
			'comment_text' => array('type' => 'text'),
			'comment_visa' => array('type' => 'int','precision' => '4'),
			'comment_status' => array('type' => 'varchar','precision' => '255'),
			'comment_creator' => array('type' => 'int','precision' => '4'),
			'comment_created' => array('type' => 'int','precision' => '20'),
			'comment_modifier' => array('type' => 'int','precision' => '4'),
			'comment_modified' => array('type' => 'int','precision' => '20')
		),
		'pk' => array('comment_id'),
		'fk' => array('comment_publi' => 'spicademic_publi'),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_publi_subject' => array(
		'fd' => array(
			'publi_id' => array('type' => 'int','precision' => '4','nullable' => False),
			'subject_id' => array('type' => 'int','precision' => '4','nullable' => False)
		),
		'pk' => array('publi_id','subject_id'),
		'fk' => array('publi_id' => 'spicademic_publi','subject_id' => 'spicademic_ref_subject'),
		'ix' => array(),
		'uc' => array()
	),
	'spicademic_publi_contact' => array(
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
	)
);
