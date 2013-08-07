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

	$sql_ref_extansion = "INSERT INTO `spicademic_ref_extension` (`extension_id`, `extension_label`, `extension_code`, `extension_icone`, `extension_active`, `extension_creator`, `extension_created`, `extension_modifier`, `extension_modified`) VALUES
	(1, 'jpg', 'Image jpg', 'jpg', 1, 5, 1351590897, NULL, NULL),
	(2, 'jpeg', 'Image jpeg', 'jpeg', 1, 5, 1351590897, NULL, NULL),
	(3, 'doc', 'Doc files', 'doc', 1, 5, 1352026313, NULL, NULL),
	(4, 'pdf', 'pdf', 'pdf', 1, 9, 1357890928, NULL, NULL);";

	$sql_ref_field="INSERT INTO `spicademic_ref_field` (`field_id`, `field_label`, `field_bibtex_code`, `field_desc`, `field_level`, `field_order`, `field_type`, `field_options`, `field_length`, `field_rows`, `field_value_min`, `field_value_max`, `field_length_min`, `field_length_max`, `field_color`, `field_active`, `field_export_bibtex`, `field_export_xml`, `field_export_rtf`, `field_export_csv`, `field_creator`, `field_created`, `field_modifier`, `field_modified`, `field_xml_code`, `field_ris_code`, `field_export_ris`) VALUES
	(1, 'address', 'address', 'Usually the address of the publisher or other type of institution. For major publishing houses, van Leunen recommends omitting the information entirely. For small publishers, on the other hand, you can help the reader by giving the complete address. ', 1, 1, 'txt', '', 20, 3, 10, 50, 0, 0, '', 1, 0, 1, 1, 1, NULL, NULL, 5, 1357205517, 'date', 'AD', 0),
	(2, 'annote', 'annote', 'An annotation. It is not used by the standard bibliography styles, but may be used by others that produce an annotated bibliography. ', 1, 2, 'txt', NULL, 20, 5, NULL, 255, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(3, 'booktitle', 'booktitle', 'Title of a book, part of which is being cited', 1, 2, 'txt', NULL, 50, 1, NULL, 255, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(4, 'chapter', 'chapter', 'A chapter (or section or whatever) number.', 1, 3, 'txt', NULL, 20, 1, NULL, 50, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(5, 'crossref', 'crossref', 'The database key of the entry being cross referenced. ', 1, 5, 'txt', NULL, 50, 1, NULL, 50, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(6, 'edition', 'edition', 'The edition of a book--for example, ``Second''''. This should be an ordinal, and should have the first letter capitalized, as shown here; the standard styles convert to lower case when necessary.', 1, 5, 'box', 'First=First\r\nSecond=Second\r\nThird=Third', NULL, 1, NULL, 20, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(7, 'editor', 'editor', 'Name(s) of editor(s), typed as indicated in the LATEX book. If there is also an author field, then the editor field gives the editor of the book or collection in which the reference appears', 1, 6, 'txt', NULL, 50, 1, NULL, 50, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(8, 'howpublished', 'howpublished', 'How something strange has been published. The first word should be capitalized.', 1, 7, 'txt', NULL, 50, 1, NULL, 50, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(9, 'institution', 'institution', 'The sponsoring institution of a technical report. ', 1, 8, 'txt', NULL, 100, 1, NULL, 100, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(10, 'journal', 'journal', NULL, 1, 9, 'txt', 'A journal name. Abbreviations are provided for many journals; see the Local Guide.', 100, 1, NULL, 100, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(11, 'key', 'key', 'Used for alphabetizing, cross referencing, and creating a label when the ``author'''' information (described in Section 4) is missing. This field should not be confused with the key that appears in the \\cite command and at the beginning of the database entry.', 1, 10, 'txt', NULL, 20, 1, NULL, 20, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(12, 'month', 'month', 'The month in which the work was published or, for an unpublished work, in which it was written. You should use the standard three-letter abbreviation, as described in Appendix B.1.3 of the LATEX book', 1, 11, 'box', 'jan=January\r\nfeb=February\r\nmar=March\r\napr=April\r\nmai=mai\r\njun=June\r\njul=July\r\naug=August\r\nsep=September\r\noct=October\r\nnov=November\r\ndec=December', 10, 1, 3, 3, 0, 0, '', 1, 1, 1, 1, 1, NULL, NULL, 5, 1358179873, 'date', '', 0),
	(13, 'note', 'note', 'Any additional information that can help the reader. The first word should be capitalized. ', 1, 12, 'txt', NULL, 20, 5, NULL, 255, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(14, 'number', 'number', 'The number of a journal, magazine, technical report, or of a work in a series. An issue of a journal or magazine is usually identified by its volume and number; the organization that issues a technical report usually gives it a number; and sometimes books are given numbers in a named series. ', 1, 13, 'nbr', NULL, 10, 1, 1, 7, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(15, 'organization', 'organization', 'The organization that sponsors a conference or that publishes a manual. ', 1, 13, 'txt', NULL, 20, 1, NULL, 255, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(16, 'pages', 'pages', 'One or more page numbers or range of numbers, such as 42-111 or 7,41,73-97 or 43+ (the `+'' in this last example indicates pages following that don''t form a simple range). To make it easier to maintain Scribe-compatible databases, the standard styles convert a single dash (as in 7-33) to the double dash used in TEX to denote number ranges (as in 7-33). ', 1, 15, 'txt', NULL, 10, 1, NULL, 20, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(17, 'publisher', 'publisher', 'The publisher''s name.', 1, 16, 'txt', NULL, 20, 1, NULL, 255, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(18, 'school', 'school', 'The name of the school where a thesis was written.', 1, 16, 'txt', NULL, 20, 1, NULL, 50, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(19, 'series', 'series', 'The name of a series or set of books. When citing an entire book, the the title field gives its title and an optional series field gives the name of a series or multi-volume set in which the book is published.', 1, 17, 'txt', NULL, 20, 1, NULL, 50, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(20, 'volume', 'volume', 'The volume of a journal or multivolume book. ', 1, 18, 'txt', NULL, 20, 1, NULL, 50, NULL, NULL, NULL, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
	(21, 'volume.', 'volume', 'volume', 0, 0, 'txt', '', 0, 0, 0, 0, 1, 20, '', 1, 0, 0, 0, 0, NULL, NULL, 5, 1357133256, 'volume', 'volume', 0);";

	$sql_ref_file = "INSERT INTO `spicademic_ref_file` (`file_type_id`, `file_type_label`, `file_type_description`, `file_type_order`, `file_type_active`, `file_type_creator`, `file_type_created`, `file_type_modifier`, `file_type_modified`) VALUES
	(1, 'Article', '...', 1, 1, 5, NULL, 5, 1356100115),
	(2, 'Reviews', '', 2, 1, 5, NULL, 5, 1354706388),
	(3, 'Video', '', 4, 1, 5, NULL, 5, 1354706407);";

	$sql_ref_file_status = "INSERT INTO `spicademic_ref_file_status` (`status_id`, `status_label`, `status_group`, `status_order`, `status_color`, `status_active`, `status_creator`, `status_created`, `status_modifier`, `status_modified`) VALUES
	(1, 'Published', -1, 1, NULL, 1, 5, 1354706327, NULL, NULL),
	(2, 'Archived', -1, 2, NULL, 1, 5, 1354706307, NULL, NULL),
	(3, 'Unvalidated', -1, 3, NULL, 1, 5, 1354706318, NULL, NULL);";

	$sql_ref_file_status_transition = "INSERT INTO `spicademic_ref_file_status_transition` (`status_source`, `status_target`) VALUES
	(2, 1),
	(1, 2),
	(1, 3);";

	$sql_ref_pub_status = "INSERT INTO `spicademic_ref_pub_status` (`status_id`, `status_label`, `status_group`, `status_order`, `status_color`, `status_active`, `status_creator`, `status_created`, `status_modifier`, `status_modified`) VALUES
	(1, 'New', -1, 1, NULL, 1, 5, 1351608520, 5, 1355824022),
	(2, 'To be validated', -1, 3, NULL, 1, 5, 1352025652, 5, 1355757708),
	(3, 'Published', -1, 3, NULL, 1, 5, 1354559296, 5, 1355757716),
	(4, 'Archived', -1, 10, NULL, 1, 5, 1354559308, 5, 1354705929);";

	$sql_ref_pub_status_transition = "INSERT INTO `spicademic_ref_pub_status_transition` (`status_source`, `status_target`) VALUES
	(2, 4),
	(1, 2),
	(1, 3),
	(1, 4),
	(3, 4),
	(2, 3),
	(3, 2);";

	$sql_ref_pub_type = "INSERT INTO `spicademic_ref_pub_type` (`type_id`, `type_title`, `type_code`, `type_bibtex_code`, `type_description`, `type_parent`, `type_level`, `type_access`, `type_group`, `type_responsible`, `type_order`, `type_color`, `type_active`, `type_creator`, `type_created`, `type_modifier`, `type_modified`, `type_ris_code`) VALUES
	(1, 'ARTICLE', 'ARTICLE', 'ARTICLE', 'An article from a journal or magazine. ', 0, NULL, 1, NULL, NULL, 1, '#336699', 1, NULL, NULL, 9, 1357891902, 'ARTICLE'),
	(2, 'BOOK', 'Book', 'BOOK', 'A book with an explicit publisher. ', 1, NULL, 0, NULL, NULL, 2, '#3300FF', 1, 5, NULL, 9, 1357891999, ''),
	(3, 'BOOKLET', 'BOOKLET', 'BOOKLET', 'A work that is printed and bound, but without a named publisher or sponsoring institution.', 0, NULL, 0, NULL, NULL, 3, '', 1, 5, NULL, 5, 1354559841, NULL),
	(4, 'CONFERENCE', 'CONFERENCE', 'CONFERENCE', 'An article in the proceedings of a conference. This entry is identical to the ''inproceedings'' entry and is included for compatibility with another text formatting system. ', 3, NULL, 0, NULL, NULL, 4, '#CC9900', 1, 5, 1352026100, 5, 1354559689, NULL),
	(5, 'INBOOK', 'INBOOK', 'INBOOK', 'A part of a book, which may be a chapter and/or a range of pages. ', 0, NULL, 0, NULL, NULL, 5, '', 1, 5, 1354559525, 9, 1357892085, ''),
	(6, 'INCOLLECTION', 'INCOLLECTION', 'INCOLLECTION', 'A part of a book with its own title. ', 0, NULL, 0, NULL, NULL, 6, '', 1, 5, 1354559536, 5, 1354559729, NULL),
	(7, 'INPROCEEDINGS', 'INPROCEEDINGS', 'INPROCEEDINGS', 'An article in the proceedings of a conference.', 0, NULL, 0, NULL, NULL, 7, '', 1, 5, 1354559546, 5, 1354559746, NULL),
	(8, 'MANUAL', 'MANUAL', 'MANUAL', 'Technical documentation.', 0, NULL, 0, NULL, NULL, 8, '', 1, 5, 1354559558, 5, 1354559755, NULL),
	(9, 'MASTERSTHESIS', 'MASTERSTHESIS', 'MASTERSTHESIS', 'A Master''s thesis. ', 0, NULL, 0, NULL, NULL, 9, '#3333CC', 1, 5, 1354559568, 5, 1354560736, NULL),
	(10, 'MISC', 'MISC', 'MISC', 'Use this type when nothing else seems appropriate. ', 0, NULL, 0, NULL, NULL, 10, '', 1, 5, 1354559577, 5, 1354559778, NULL),
	(11, 'PHDTHESIS', 'PHDTHESIS', 'PHDTHESIS', 'A PhD thesis.', 0, NULL, 0, NULL, NULL, 11, '', 1, 5, 1354559589, 5, 1354559791, NULL),
	(12, 'PROCEEDINGS', 'PROCEEDINGS', 'PROCEEDINGS', 'The proceedings of a conference.', 0, NULL, 0, NULL, NULL, 12, '', 1, 5, 1354559599, 5, 1354559804, NULL),
	(13, 'TECHREPORT', 'TECHREPORT', 'TECHREPORT', 'A report published by a school or other institution, usually numbered within a series.', 0, NULL, 0, NULL, NULL, 13, '', 1, 5, 1354559610, 5, 1354559621, NULL),
	(14, 'UNPUBLISHED', 'UNPUBLISHED', 'UNPUBLISHED', 'A document with an author and title, but not formally published.', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, 5, 1354559824, NULL, NULL, NULL),
	(15, 'DEACTIVATED', 'DEACTIVATED', '', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, 5, 1354706828, NULL, NULL, NULL);";

	$sql_ref_role = "INSERT INTO `spicademic_ref_role` (`role_id`, `role_label`, `role_description`, `role_order`, `role_active`, `role_creator`, `role_created`, `role_modifier`, `role_modified`) VALUES
	(1, 'Administrator', 'Administrator', 1, 0, NULL, NULL, 5, 1358411915),
	(2, 'Author', 'Author', 2, 1, 5, 1351588708, 5, 1358411928),
	(3, 'Reviewer', 'Reviewer', 1, 1, 5, 1351692852, 5, 1358411947),
	(4, 'Thesis supervisor', 'Thesis supervisor', 1, 1, 5, 1351692907, 5, 1358412009),
	(5, 'Speaker', 'Speaker', 5, 1, 5, 1354706649, 5, 1358412031),
	(6, 'Translator', 'Translator', 6, 1, 5, 1358412046, NULL, NULL);";

	$sql_ref_type_field = "INSERT INTO `spicademic_ref_type_field` (`type_id`, `field_id`, `type_field_access`, `type_field_creator`, `type_field_created`, `type_field_modifier`, `type_field_modified`) VALUES
	(1, 1, 1, 5, 1354791983, NULL, NULL),
	(1, 3, 3, 5, 1354793059, NULL, NULL),
	(1, 12, 1, 5, 1354793169, NULL, NULL),
	(1, 16, 0, 5, 1354793183, NULL, NULL),
	(2, 3, 0, 5, 1357131192, NULL, NULL),
	(2, 20, 1, 5, 1357134009, NULL, NULL),
	(2, 6, 1, 9, 1357891938, NULL, NULL),
	(2, 7, 1, 9, 1357891950, NULL, NULL),
	(2, 16, 1, 9, 1357891968, NULL, NULL),
	(2, 19, 1, 9, 1357891984, NULL, NULL),
	(5, 3, 0, 9, 1357892041, NULL, NULL),
	(5, 16, 1, 9, 1357892054, NULL, NULL);";


	$oProc->query ($sql_ref_extansion);
	$oProc->query ($sql_ref_field);
	$oProc->query ($sql_ref_file);
	$oProc->query ($sql_ref_file_status);
	$oProc->query ($sql_ref_file_status_transition);
	$oProc->query ($sql_ref_pub_status);
	$oProc->query ($sql_ref_pub_status_transition);
	$oProc->query ($sql_ref_pub_type);
	$oProc->query ($sql_ref_role);
	$oProc->query ($sql_ref_type_field);

	$oProc->query ("DELETE FROM {$GLOBALS['egw_setup']->config_table} WHERE config_app='spicademic'");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'archived_pub_status', '4')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'author_role', '2')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'default_file_status', '1')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'default_pub_status', '1')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'ManagementGroup', '-15')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'pending_pub_status', '1,2')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'translator_role', '6')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'validated_pub_status', '3')");
	$oProc->query ("INSERT INTO {$GLOBALS['egw_setup']->config_table} (config_app, config_name, config_value) VALUES ('spicademic', 'xml_date', '12')");
?>
