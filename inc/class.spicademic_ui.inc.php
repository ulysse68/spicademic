<?php
/**
 * eGroupware - SpiCademic - UI
 * SpiCademic : Modules to manage academic publications & presentations
 *
 * @link http://www.spirea.fr
 * @package spicademic
 * @author Spirea SARL <contact@spirea.fr>
 * @copyright (c) 2012-10 by Spirea
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
  */

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.spicademic_bo.inc.php');
require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.acl_spicademic.inc.php');

// require_once(EGW_INCLUDE_ROOT. '/spicademic/phprtflite/lib/PHPRtfLite.php');

class spicademic_ui extends spicademic_bo{
	/**
	 * Methods callable via menuaction
	 *
	 * @var array
	 */
	var $public_functions = array(
		'index' 	=> true,
		'edit' 		=> true,
		'about' 	=> true,
		'file_edit'	=> true,
		'list_type'	=> true,
		'import'	=> true,

		'get_addressbook' => true,
		
		'bibtex'	=> true,
		'ris'		=> true,
		'csv'		=> true,
		'xml'		=> true,
		'rtf'		=> true,
		'pdf' 		=> true,
		'pdf_list' 	=> true,

		'mail' 		=> true,
		'download' 	=> true,

		'admin'		=> true,
	);

	/**
	 * Constructeur
	 *
	 */
	function spicademic_ui()
	{
		// Récupération des groupes de l'utilisateur
		$groupeUser = array_keys($GLOBALS['egw']->accounts->memberships($GLOBALS['egw_info']['user']['account_id']));
		
		// Construction des droits - une seule fonction - dans class.acl_so.inc.php 
		$GLOBALS['egw_info']['user']['SpicademicLevel'] = acl_spicademic::get_spicademic_level();
		// Gestion ACL - Simple utilisateur = Pas d'accès
		// if ($GLOBALS['egw_info']['user']['SpicademicLevel'] <= 10){
		// 	$GLOBALS['egw']->framework->render('<h1 style="color: red;">'.lang('Permission denied, please contact your administrator!!!')."</h1>\n",null,true);
		// 	exit;
		// }
		// Fin blocage au niveau du constructeur
		parent::spicademic_bo();

		$GLOBALS['egw_info']['flags']['java_script'] = $this->search_write_js();

		//Spirea YLF - 14/02/2011 - Appel au script pour l'autocomplétion. Cacher l'élément org_name qui nous servira pour les update mais que l'utilisateur n'a pas besoin de voir.
		// $GLOBALS['egw_info']['flags']['java_script'] .= '<script type="text/javascript" src="/spicademic/inc/js/autocomplete.js"></script>
		// <script type="text/javascript">
		// window.onload = function(){document.getElementById(\'exec[contact][new_n_family]\').style.visibility="hidden";
		// initAutoComplete(document.getElementById(\'\'),document.getElementById(\'exec[contact][concat_n_family]\'),document.getElementById(\'bouton-submit\'));};
		// </script>';
	}

	function index($content=null){
	/**
	 * Charge le template index
	 */
		// Action (coche + liste)
		if(isset($content['action'])){
			if (!count($content['nm']['rows']['checked']) && !$content['use_all']){
				// Aucune coche ni requete entiere
				$msg = lang('You need to select some publications first');
			}else{
				// Si la case requete entiere est coché
				if($content['use_all']){
					$query = $content['nm'];
					@set_time_limit(0);
					$query['num_rows'] = -1;
					$this->get_rows($query,$temp,$readonlys);
					foreach((array)$temp as $publi){
						$content['nm']['rows']['checked'][] = $publi['publi_id'];
					}
				}
				
				if(is_array($content['nm']['rows']['checked']) && $content['action'] != 'delete'){
					$publi = implode(',',(array)$content['nm']['rows']['checked']);
					echo "<html><body><script>window.open('".egw::link('/index.php','menuaction=spicademic.spicademic_ui.'.$content['action'].'&id='.utf8_decode($publi))."','_blank','dependent=yes,width=750,height=600,scrollbars=yes,status=yes');</script></body></html>\n";
				}elseif($content['action'] == 'delete' && $GLOBALS['egw_info']['user']['SpicademicLevel'] > 19){
					// Suppression de(s) publication(s) (uniquement pour admin / gestionnaire)
					foreach((array)$content['nm']['rows']['checked'] as $id){
						$this->so_contact->delete(array('contact_publi' => $id));
						$this->so_comment->delete(array('comment_publi' => $id));
						$this->so_extra->delete(array('publi_id' => $id));
						$this->so_file->delete(array('file_publi' => $id));
						$this->so_publi_subject->delete(array('publi_id' => $id));

						egw_link::unlink2('','spicademic',$id);

						if($this->delete($id)){
							$msg = lang('Publication(s) deleted successfully');
			   			}
					}
				}
			}

			unset($content['action']);
			unset($content['nm']['rows']['checked']);
		}

		// Suppression d'une publication
		if(isset($content['nm']['rows']['delete'])){
			list($id) = @each($content['nm']['rows']['delete']);
		
			$this->so_contact->delete(array('contact_publi' => $id));
			$this->so_comment->delete(array('comment_publi' => $id));
			$this->so_extra->delete(array('publi_id' => $id));
			$this->so_file->delete(array('file_publi' => $id));
			$this->so_publi_subject->delete(array('publi_id' => $id));

			egw_link::unlink2('','spicademic',$id);

			if($this->delete($id)){
				$msg = lang('Publication deleted successfully');
   			}

   			unset($content['nm']['rows']['delete']);
		}

		// Si c'est vide on recupere le cache (permet de garder les filtres apres validation)
		if(empty($content['nm']))
			$content['nm'] = $GLOBALS['egw']->session->appsession('index','spicademic');

		if (!is_array($content['nm']))
		{
			$default_cols='publi_id,publi_title,authors,publi_responsible,publi_type,publi_status,publi_year';
			$content['nm'] = array(                           // I = value set by the app, 0 = value on return / output
				'get_rows'       	=>	'spicademic.spicademic_ui.get_rows',	// I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
				'bottom_too'     	=> false,		// I  show the nextmatch-line (arrows, filters, search, ...) again after the rows
				'never_hide'     	=> true,		// I  never hide the nextmatch-line if less then maxmatch entrie
				'no_cat'         	=> true,
				'filter_no_lang' 	=> true,		// I  set no_lang for filter (=dont translate the options)
				'filter2_no_lang'	=> true,		// I  set no_lang for filter2 (=dont translate the options)
				'lettersearch'   	=> false,
				'no_filter'			=> false,
				'no_filter2'		=> false,
				'options-cat_id' 	=> false,
				'start'          	=>	0,			// IO position in list
				'cat_id'         	=>	'',			// IO category, if not 'no_cat' => True
				'search'         	=>	'',// IO search pattern
				'order'          	=>	'publi_id',	// IO name of the column to sort after (optional for the sortheaders)
				'sort'           	=>	'DESC',		// IO direction of the sort: 'ASC' or 'DESC'
				'col_filter'     	=>	array(),	// IO array of column-name value pairs (optional for the filterheaders)
				'filter_label'   	=>	'',//lang('Status'),	// I  label for filter    (optional)
				'filter'         	=>	'',	// =All	// IO filter, if not 'no_filter' => True
				'default_cols'   	=> $default_cols,
				'filter_onchange' 	=> "this.form.submit();",
				'filter2_onchange' 	=> "this.form.submit();",
				'no_csv_export'		=> false,
				'csv_fields'		=> $this->export(),
				// 'no_columnselection'=> true,
				//'manual'         => $do_email ? ' ' : false,	// space for the manual icon
			);
		}

		// Bascule du filtre de vue dans la liste filter2
		if(isset($_GET['view'])){
			$content['nm']['filter2'] = $_GET['view'];
			unset($content['nm']['col_filter']);

			// On vide les champs de la recherche avancée
			unset($content['nm']['publi_project']);
			unset($content['nm']['publi_subject']);
			unset($content['nm']['publi_year']);
			unset($content['nm']['publi_lang']);
			unset($content['nm']['publi_responsible']);
			unset($content['nm']['authors']);
		}

		$content['hide_adv_search'] = !$_GET['adv_search'];
		$content['nm']['header_left'] = $_GET['adv_search'] ? '' : 'spicademic.index.publi';
		
		// Suppression en masse pour admin / gestionnaire
		$delete = array();
		if($GLOBALS['egw_info']['user']['SpicademicLevel'] > 19){
			$delete = array('delete' => lang('Delete'));
		}

		// Listes
		$sel_options = array(
			'filter' => array(''=>lang('All types')) + $this->get_publi_type(),
			'filter2' => array(
				'validated' => lang('Validated publications'),
				'pending' => lang('Pending publications'),
				'archived' => lang('Archived publications'),
				'all' => lang('All publications'),
				'import' => lang('Imported publications'),
			),
			'action' => $delete + array(
				lang('Downloads') => array(
					'rtf' => lang('RTF'),
					'bibtex' => lang('BibTeX'),
					'xml' => lang('XML'),
					'csv' => lang('CSV'),
					'ris' => lang('RIS'),
				),
			),
			'publi_status' => $this->get_publi_status(),
			'publi_type' => $this->get_publi_type(),
			'publi_project' => $this->get_projects(),
			'publi_subject' => $this->get_subjects(),
			'author_role' => $this->get_roles(),
			'authors' => $this->get_authors(),
		);

		$content['nm']['header_right'] = 'spicademic.index.right';
	
		$tpl = new etemplate('spicademic.index');
		$tpl->read('spicademic.index');
		$tpl->exec('spicademic.spicademic_ui.index', $content, $sel_options, $readonlys, array('nm' => $content['nm']));
	}
	
	function edit($content=null){
	/**
	 * Fonction de création/modification d'une note de frais
	 *
	 * @param $content
	 */
		$tabs = 'general|publication|contact|subject|files|comment|link|history';
		
		// récupération de l'onglet sur lequel on se trouvait
		$tab = $content[$tabs];

		// Clic sur un bouton
		if(is_array($content)){
			list($button) = @each($content['button']);
			switch($button){
				case 'save':
				case 'apply':
					// Application lors de la création (pas d'onglet détails) => retour sur l'onglet détails après la création
					if(empty($content['details'])){
						$tab = 'spicademic.edit.publication';
					}

					// Sauvegarde/appliquer
					foreach((array)$content['details'] as $key => $value){
						if($value['class'] == 'inputRequired' && empty($_POST['exec']['details'][$key][$value['field_id']])) {
							$msg .= lang('Error while saving').' : '.lang('%1 field must not be empty',$value['field_label'])."\n";
						}
					}

					if(empty($_REQUEST['exec']['publi_type'])){
						$msg .= lang('Error while saving').' : '.lang('Publication type must not be empty')."\n";
					}

					if(empty($msg)){
						// On reprend le type car il n'est pas dans $content
						$content['publi_type'] = $_REQUEST['exec']['publi_type'];

						$msg = $this->add_update_publi($content);
						if($button=='save'){
							echo "<html><body><script>var referer = opener.location;opener.location.href = referer+(referer.search?'&':'?')+'msg=".
								addslashes(urlencode($msg))."'; window.close();</script></body></html>\n";
							$GLOBALS['egw']->common->egw_exit();
						}
					}

					$GLOBALS['egw_info']['flags']['java_script'] .= "<script language=\"JavaScript\">
						var referer = opener.location;
						opener.location.href = referer+(referer.search?'&':'?')+'msg=".addslashes(urlencode($msg))."';</script>";
					break;
				case 'cancel':
					// Annuler
					echo "<html><body><script>window.close();</script></body></html>\n";
					$GLOBALS['egw']->common->egw_exit();
					break;
				case 'upload_file': 
					// On reprend le type car il n'est pas dans $content
					$content['publi_type'] = $_REQUEST['exec']['publi_type'];

					$msg = $this->add_update_publi($content);


					$GLOBALS['egw_info']['user']['spicademic_add'] = true;
					// Ajout d'un fichier
					$auth_extension = $this->get_auth_extension();
					if(!empty($content['upload_file'])){
						if(!empty($content['upload_name'])){
							if($content['upload_file']['size'] == 0){
								// Fichier vide (0 octet)
								$msg = lang('Error').' : '.lang('Selected file can\'t be empty');
							}else{
								$file_name = explode('.',$content['upload_file']['name']);
								$extension = $file_name[count($file_name)-1];

								if(in_array(strtolower($extension),$auth_extension)){
									foreach((array)$auth_extension as $key => $data){
										if(strtolower($data) == strtolower($extension)){
											$extension = $key;
											break;
										}
									}
									
									if(strlen($content['upload_file']['name']) < $this->obj_config['file_caracters'] || empty($this->obj_config['file_caracters'])){

										$fs_id = abs(egw_link::link('spicademic',$content['publi_id'],'file',$content['upload_file']));
										if($fs_id != 0){
											$exist = $this->so_file->read(array('file_fs_id' => $fs_id));
											$checksum = md5_file($content['upload_file']['tmp_name']);
											$existChecksum = $this->so_file->search(array('file_checksum' => $checksum, 'file_publi' => $content['publi_id']),false);
											$type = $content['upload_file']['type'];
											if(!is_array($exist) && !is_array($existChecksum)){
												$msg = $this->add_update_file(
													array('1' => array(
															'file_publi' => $content['publi_id'], 
															'file_fs_id' => $fs_id,
															'file_id' => '',
															'file_status' => $content['file_status'],
															'file_type' => $content['file_type'],
															'file_checksum' => $checksum,
															'file_extension' => $extension,
															'file_name' => $content['upload_name'],
														)
													));
												$msg = lang('File uploaded successfully')."\n".$msg;
											}else{
												if(is_array($exist)){
													// Fichier avec le meme nom deja existant sur le dossier
													$msg = lang('Error').' : '.lang('File "%1" already exists (two files can\'t have the same name)',$content['upload_file']['name']);
												}elseif($existeChecksum){
													$files = $this->get_files();
													// Fichier avec le meme checksum deja existant sur le dossier
													$msg = lang('Error').' : '.lang('Checksum already exist on this publication for file "%1"',$files[$existChecksum[0]['file_id']]);
													
													unset($files);
												}
											}
										}else{
											// Le fichier n'a pas pu etre uploader (probleme de droit soit sur le serveur soit sur le filemanager)
											$msg = lang('Error uploading file!')."\n".lang('Please contact your administrator');
										}
									}else{
										// Nom de fichier trop long
										$msg = lang('Error').' : '.lang('File name must not exceed %1 caracters',$this->obj_config['file_caracters']);
									}
								}else{
									// Extension non autorisé
									$msg = lang('Error').' : '.lang('Extension "%1" is not authorized !!!',$extension);
								}
							}
						}else{
							// Aucun intitulé sélectionné
							$msg = lang('Error').' : '.lang('Please enter a title');
						}
					}else{
						// Aucun fichier sélectionner
						$msg = lang('Error').' : '.lang('Please select a file');
					}
					unset($GLOBALS['egw_info']['user']['spicademic_add']);
					break;
			}
			$id = $this->data['publi_id'] ? $this->data['publi_id'] : $content['publi_id'];

			// Ajout d'un commentaire
			if(isset($content['comment']['add_comment'])){
				$msg_comment = $this->add_update_comment($content['publi_id'],$content['comment']);
				unset($content['comment']['add_comment']);
			}

			// Up d'un contact (changement ordre)
			if(isset($content['contact']['up'])){
				foreach((array)$content['contact']['up'] as $contact_id => $data){
					$contact = $this->so_contact->read($contact_id);
					// _debug_array($contact);exit;
					$contact['contact_order'] = $contact['contact_order'] >= 1 ? $contact['contact_order']-1 : $contact['contact_order'];
					$this->so_contact->data = $contact;
					$this->so_contact->update($contact,true);

					// Down des contact ayant le nouvel ordre
					$temp_contacts = $this->so_contact->search(array('contact_publi'=>$contact['contact_publi'], 'contact_order' => (string)$contact['contact_order']),false);
					foreach($temp_contacts as $temp_contact){
						if($temp_contact['contact_id'] != $contact['contact_id']){
							$temp_contact['contact_order'] = $temp_contact['contact_order'] + 1;
							$this->so_contact->data = $temp_contact;
							$this->so_contact->update($temp_contact,true);
						}
					}
				}
			}
			// Down d'un contact (changement ordre)
			if(isset($content['contact']['down'])){
				foreach((array)$content['contact']['down'] as $contact_id => $data){
					$contact = $this->so_contact->read($contact_id);
					$contact['contact_order'] = $contact['contact_order'] + 1;
					$this->so_contact->data = $contact;
					$this->so_contact->update($contact,true);

					// Up des contact ayant le nouvel ordre
					$temp_contacts = $this->so_contact->search(array('contact_publi'=>$contact['contact_publi'], 'contact_order' => (string)$contact['contact_order']),false);
					foreach($temp_contacts as $temp_contact){
						if($temp_contact['contact_id'] != $contact['contact_id']){
							$temp_contact['contact_order'] = $contact['contact_order'] >= 1 ? $contact['contact_order']-1 : $contact['contact_order'];
							$this->so_contact->data = $temp_contact;
							$this->so_contact->update($temp_contact,true);
						}
					}
				}
			}
			
		}else{

			if(isset($_GET['id'])){
				$id=$_GET['id'];
			}else{
				$id='';
				
			}
		}

		// Ajoute un contact sur la publication
		if($content['contact']['button']['add_contact']){
			// On reprend le type car il n'est pas dans $content
			$content['publi_type'] = $_REQUEST['exec']['publi_type'];

			$msg = $this->add_update_publi($content);

			// Si plusieurs champs sont renseigné (Contact et/ou Compte et/ou Nouveau contact)
			$check_add = !empty($content['contact']['contact_add_id']) || !empty($_REQUEST['contact_id']);
			$check_account = !empty($content['contact']['contact_account_id']);
			$check_new = empty($_REQUEST['contact_id']) && !empty($_REQUEST['new_n_family']) && !empty($_REQUEST['new_n_given']);
			$add = true;
			if(($check_add && $check_account) || ($check_add && $check_new) || ($check_account && $check_new) || ($check_add && $check_account && $check_new)){
				$add = false;
				$msg = lang('Error while saving').' : '.lang('Please select an account, a contact or enter new value but do not use more than one of these options at once');
			}

			$contact_add_id = '';
			if($add){
				$new = false;
				if($check_add){
					if(!empty($content['contact']['contact_add_id'])){
						$contact_add_id = $content['contact']['contact_add_id'];
					}else{
						$contact_add_id = $_REQUEST['contact_id'];
					}
					// Le champ contact est rempli
					$exist = $this->so_contact->search(array('contact_add_id'=>$contact_add_id,'contact_publi'=>$content['publi_id']),false);
				}else{
					if($check_account){
						// Le champ compte est rempli
						$exist = $this->so_contact->search(array('contact_account_id'=>$content['contact']['contact_account_id'],'contact_publi'=>$content['publi_id']),false);
					}else{
						if($check_new){
							// Nouveau contact (Au moins nom et prenom remplit)
							$so_addressbook = CreateObject('addressbook.addressbook_so');
							$contact = array(
								'tid' => 'n',
								'owner' => empty($this->obj_config['ManagementGroup']) ? $GLOBALS['egw_info']['user']['account_id'] : $this->obj_config['ManagementGroup'],
								'n_family' => $_REQUEST['new_n_family'],
								'n_given' => $_REQUEST['new_n_given'],
								'email' => $_REQUEST['new_contact_email'],
								'created' => time(),
								'creator' => $GLOBALS['egw_info']['user']['account_id'],
								'etag' => 0,
							);
							$so_addressbook->save($contact);

							// Récupération du contact_id
							$contact_id = $so_addressbook->somain->data['id'];
						}else{
							$exist = array();
							$new = true;
						}
					}
				}

				// Si le contact n'est pas lié a la publication
				if(!is_array($exist)){
					$link_app = 'addressbook';
					$link_id = empty($contact_add_id) ? $contact_id : $contact_add_id;
					if (preg_match('/^[a-z_0-9-]+:[:a-z_0-9-]+$/i',$link_app.':'.$link_id)){
						$link = egw_link::link('spicademic',$content['publi_id'],$link_app,$link_id,$content['contact']['contact_role']);
					}

					// Contrôle sur les inscriptions, rôles et statuts obligatoire...
					if ($content['contact']['contact_role'] > 0){
						if($check_account){
							$account = $GLOBALS['egw']->accounts->read($content['contact']['contact_account_id']);
							$content['contact']['contact_add_id'] = $account['person_id'];
						}

						$this->so_contact->data = array(
							'contact_id' => $exist[0]['contact_id'],
							'contact_publi' => $content['publi_id'],
							'contact_add_id' => empty($contact_add_id) ? $contact_id : $contact_add_id,
							'contact_account_id' => $content['contact']['contact_account_id'],
							'contact_link' => $link,
							'contact_role' => $content['contact']['contact_role'],
							'contact_order' => $this->get_max_contact_order($id),
							'contact_creator' => $GLOBALS['egw_info']['user']['account_id'],
							'contact_created' => time()
						);

						$this->so_contact->save();
						// $msg = $this->notify($this->so_contact->data);
					}else{
						$msg = lang('Error while saving').' : '.lang('The contact must have one role to be registered');
					}				
				}else{
					if($new){
						$msg = lang('Error while saving').' : '.lang('Please select an existing contact OR set a first name AND last name to create a new one');
					}else{
						if(!empty($content['contact']['contact_add_id']))
							$msg = lang('Error').' : '.lang('Contact already linked to this session');

						if(!empty($content['contact']['contact_account_id']))
							$msg = lang('Error').' : '.lang('User already linked to this session');
					}
				}
			}

			$id = $content['publi_id'];
			unset($content);
		}

		// Confirmer le contact choisi sur liste d'attente
		if(isset($content['contact']['delete'])){
			foreach((array)$content['contact']['delete'] as $contact_id => $value){
				$contact = $this->so_contact->read($contact_id);

				$this->so_contact->delete($contact_id);
				egw_link::unlink($contact['contact_link']);
			}

			$id = $content['publi_id'];
			unset($content);
		}
		
		// On actualise le $content uniquement s'il est vide ou si on vient d'appeler save/apply
		if(isset($id)){
			unset($content['button']);
			$content = array(
				'msg'         => $msg,
				'link_to' => array(
					'to_id' => $id,
					'to_app' => 'spicademic',
				),
			);
			if(empty($id)){
				// Nouveau
				$content['publi_status'] = $this->obj_config['default_pub_status'];
				$content['publi_responsible'] = $GLOBALS['egw_info']['user']['account_id'];

				// Masque les logos d'export
				$content['hide_export'] = true;

				// Onglets en lecture seul lors de la création
				$readonlys[$tabs]['contact'] = true;
				$readonlys[$tabs]['publication'] = true;
				$readonlys[$tabs]['files'] = true;
				$readonlys[$tabs]['comment'] = true;
				$readonlys[$tabs]['link'] = true;
				$readonlys[$tabs]['history'] = true;

				$readonlys['button[save]'] = true;
			}else{
				// Edition d'une publication existante
				$GLOBALS['egw_info']['flags']['app_header'] = lang('Edit publication');
				$content = array_merge($content,$this->get_info($id));

				$content['files'] = $this->get_publi_files($id, $readonlys);
				$content['comment'] = $this->get_comments($id);
				$content['details'] = $this->get_fields_values($content['publi_type'],$id);
				$content['contact'] = $this->get_contact($id);

				$content['history'] = array(
					'id'  => $id,
					'app' => 'spicademic',
				);

				// Autorisation des comptes comme contacts ??
				$content['contact']['hide_w_users'] = $this->obj_config['allow_users'] ? false : true;
				$content['contact']['hide_users'] = $this->obj_config['allow_users'] ? true : false;

				// Utilisateur simple OU statut archivé => Lecture seule
				if($GLOBALS['egw_info']['user']['account_id'] != $content['publi_responsible'] && ($GLOBALS['egw_info']['user']['SpicademicLevel'] < 20 || in_array($content['publi_status'],explode(',',$this->obj_config['archived_pub_status'])))){
					$readonlys = $this->set_readonlys();
					$readonlys['publi_subject'] = true;

					$content['contact']['no_add'] = true;
					$content['contact']['hide_contact_action'] = true;
					$content['comment']['no_add'] = true;
					$content['hideupload'] = true;
					$content['no_links'] = true;
					$content['status_only'] = true;

					$content['contact']['hide_users'] = true;
					$content['contact']['hide_w_users'] = true;

					// Si le statut est archivé mais que l'utilisateur n'est pas un simple utilisateur (admin) on autorise le changement de statut
					if($GLOBALS['egw_info']['user']['SpicademicLevel'] > 20){
						$readonlys['publi_status'] = false;
					}
				}
			}
		}

		// Menu des types
		$content['publi_type_html'] = $this->get_publi_type('html',$content['publi_type']);
		$content['help_type'] = $this->get_publi_type('help',$content['publi_type']);

		$content['file_status'] = $this->obj_config['default_file_status'];
		$content['comment']['msg'] = $msg_comment;
		
		// Listes
		$sel_options = array(
			'publi_status' => $this->get_publi_status($content['publi_status']),
			'publi_type' => $this->get_publi_type(),
			'publi_project' => $this->get_projects(),
			'publi_subject' => $this->get_subjects(),
			'publi_peer_review' => $this->get_peer_review(),
			'publi_scope' => $this->get_scope(),
			'contact_role' => $this->get_roles(),

			'comment_visa' => $this->get_visa(),
			'field_id' => $this->get_fields(),

			'file_type' => $this->get_file_type(),
			'file_status' => $this->get_file_status(),
		);
 
 		// !!!! NE MARCHE PAS !!!!
 		// CSS - Modification couleur onglet
		$css='<!--[if !IE]>--><STYLE type="text/css">
			.mandatory-tab { 
				background-color: '.$this->obj_config['mandatory_color'].'; 
				font-weight:bold;
				padding-top : 4px;
				padding-right : 10px;
				padding-bottom : 3px;
				padding-left : 9px;
				margin-left: -6px;
				margin-right: -11px;
				margin-top: 1px;
			}
		</STYLE><![endif]-->';

		$GLOBALS['egw_info']['flags']['java_script'].=$css."\n";

		// Coloration de l'onglet Détails si il contient des champs obligatoire (ou que le type est vide)
		if(!empty($content['publi_type'])){
			$type_fields = $this->so_type_field->search(array('type_id' => $content['publi_type'],'type_field_access' => '0'),false);
		}
		if(!empty($type_fields)){
			$content['tab_details'] = '<span class="mandatory-tab">'.lang('Details').'*'.'</span>';
		}else{
			$content['tab_details'] = lang('Details');
		}

		// Autocomplétion sur le nom des contacts
		$this->autocomplete_js();
		$this->autocomplete_html(&$content);

		// Retour sur l'onglet où l'utilisateur se trouvait
		$content[$tabs] = $tab;
		
		$tpl = new etemplate('spicademic.edit');
		$GLOBALS['egw_info']['flags']['app_header'] = lang('Edit publication');
		$tpl->read('spicademic.edit');
		$tpl->exec('spicademic.spicademic_ui.edit', $content, $sel_options, $readonlys, $content,2);
	}

	function autocomplete_html($content){
	/** 
	 * Code HTML pour l'autocomplétion
	 * /!\ LES NOMS AVEC DES [] NE SONT PAS ACCEPTE EN JS
	 *
	 * @param $content : tableau des valeurs de la publication
	 * @return array
	 */
		$content['contact']['new_n_family'] = '<input name="new_n_family" value="" id="new_n_family" size="30">';
		$content['contact']['new_n_given'] = '<input name="new_n_given" value="" id="new_n_given" size="30" onchange="emptyValues(this);">';
		$content['contact']['new_contact_email'] = '<input name="new_contact_email" value="" id="new_contact_email" size="30" onchange="emptyValues(this);">';
		$content['contact']['contact_id'] = '<input type="hidden" name="contact_id" value="" id="contact_id" size="1">';
	}

	function get_addressbook(){
	/**
	 * Fonction AJAX pour l'autocompletion
	 *
	 * @return JSON
	 */
		$so_addressbook = new so_sql('phpgwapi','egw_addressbook');

		// Récupération des contacts (filtre supplémentaire si on ne doit pas prendre les comptes)
		if($this->obj_config['allow_users']){
			$join = "WHERE LOWER(n_family) LIKE '%".strtolower($_GET['startWith'])."%' OR LOWER(n_given) LIKE '%".strtolower($_GET['startWith'])."%'";
		}else{
			$join = "WHERE (account_id = '' OR account_id IS NULL) AND (LOWER(n_family) LIKE '%".strtolower($_GET['startWith'])."%' OR LOWER(n_given) LIKE '%".strtolower($_GET['startWith'])."%')";
		}
		
		$addressbook = $so_addressbook->search('',false,$order,'',$wildcard,false,$op,array(0,$_GET['maxRows']),array(),$join);
		foreach($addressbook as $contact){
			$json[] = array(
				'contact_id' => $contact['contact_id'],
				'n_family' => $contact['n_family'],
				'n_given' => $contact['n_given'],
				'contact_email' => $contact['contact_email'],
				'label' => $contact['n_family'].' '.$contact['n_given'],
			);
		}

		echo json_encode($json);
		die();
	}

	function autocomplete_js(){
	/** 
	 * JS pour l'autocomplétion
	 */
		$protocole = 'https';
		if(empty($_SERVER['HTTPS']))
			$protocole = 'http';

		$autocomplete_js = "
			<link rel='stylesheet' href='http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' />
			<script src='http://code.jquery.com/jquery-1.9.1.js'></script>
			<script src='http://code.jquery.com/ui/1.10.3/jquery-ui.js'></script>
			<script type='text/javascript'>
				$(function() {
					$( '#new_n_family' ).autocomplete({
					minLength: 0,
					source: function( request, response ) {
					                $.ajax({
					                    url: '".$GLOBALS['egw_info']['server']['webserver_url']."/index.php?menuaction=spicademic.spicademic_ui.get_addressbook',
					                    dataType: 'json',
					                    // type : 'GET',
					                    data: {
					                        startWith : $('#new_n_family').val(),
					                        maxRows: 10,
					                        string : request.term
					                    },
					                    success: function( data ) {
					                        response( $.map(data, function( item ) {
					                            return {
					                                label: item.n_family+' '+item.n_given,
					                                n_given: item.n_given,
					                                n_family: item.n_family,
					                                contact_email: item.contact_email,
					                                contact_id: item.contact_id
					                            }
					                        }));
											
					                    }
					                });
					            },
					focus: function( event, ui ) {
						$( String('#new_n_family') ).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						$( '#contact_id' ).val( ui.item.contact_id );
						$( '#new_n_family' ).val( ui.item.n_family );
						$( '#new_n_given' ).val( ui.item.n_given );
						$( '#new_contact_email' ).val( ui.item.contact_email );
						return false;
					}
					})
					.data( 'ui-autocomplete' )._renderItem = function( ul, item ) {
						if(item.contact_email){
							return $( '<li>' )
							.append( '<a>' + item.label + ' (' + item.contact_email + ')</a>' )
							.appendTo( ul );
						}else{
							return $( '<li>' )
							.append( '<a>' + item.label + '</a>' )
							.appendTo( ul );
						}
					};
				});
			</script>
			<script>
			function emptyValues(obj_changed){
				if(document.getElementById('contact_id').value != ''){
					if(document.getElementById('new_n_family') != obj_changed)
						document.getElementById('new_n_family').value = '';

					if(document.getElementById('new_n_given') != obj_changed)
						document.getElementById('new_n_given').value = '';

					if(document.getElementById('new_contact_email') != obj_changed)
						document.getElementById('new_contact_email').value = '';
	
					document.getElementById('contact_id').value = '';
				}
			}
			</script>";
		$GLOBALS['egw_info']['flags']['java_script'] .= $autocomplete_js."\n";
	}

	function pdf($references = null,$path = ''){
	/**
	 * Export de la publication au format BibTeX
	 */
		if(isset($_GET['id'])){
			$pdf = CreateObject('spicademic.generate_pdf',$_GET['id']);
			$pdf->generate($path);
		}else{
			$ref_param = array();
			$references = explode(',',$_GET['ndf_id']);
			foreach((array)$references as $ndf_id){
				// $ref_param[] = $this->read($ndf_id);
			}
			$pdf = CreateObject('spicademic.generate_pdf',$ref_param);
			$pdf->generate($path,$_GET['header']);
		}
	}

	function bibtex($content=null){
	/**
	 * Génère le fichier bibtex correspondant à la publication
	 */
		$bibtex = '';
		foreach ((array)explode(',',$_GET['id']) as $publi_id) {
			$publi = $this->read($publi_id);
			$temp_file = fopen($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.bib', 'w+');
			
			/* @book{Ben62,
	    		title = "L'Oxydation des m\'etaux",
	    		author = "J. B{\'e}nard and J. Bardolle and F. Bouillon and M. Cagnet% and J. Moreau and G. Valensi",
	    		publisher = "Gauthier-Villars",
	    		year = "1962"
			} */

			$type = $this->so_ref_publi_type->read($publi['publi_type']);
			$bibtex .= '@'.$type['type_bibtex_code'].'{'.$publi['publi_id'].','."\n";

			$bibtex .= "\t".'title = {'.$publi['publi_title'].'},'."\n";
			$bibtex .= "\t".'year = {'.$publi['publi_year'].'},'."\n";
			$bibtex .= "\t".'abstract = {'.$publi['publi_abstract'].'},'."\n";
			$bibtex .= "\t".'keywords = {'.$publi['publi_keywords'].'},'."\n";
			// $bibtex .= "\t".'address = {'.$publi['publi_address'].'},'."\n";

			// Ancien code - 04/10/2013
			// $authors = $this->so_contact->search(array('contact_publi' => $publi['publi_id'],'contact_role' => $this->obj_config['author_role']),false,'contact_order');
			// $temp_author = array();
			// foreach((array)$authors as $author){
			// 	$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
			// 	$temp_author[] = $contact['n_family'].', '.$contact['n_given'];
			// }
			// $bibtex .= "\t".'author = {'.implode(' and ',(array)$temp_author)."},\n";

			$authors = $this->so_contact->search(array('contact_publi' => $publi['publi_id']),false,'contact_order, contact_role');
			$temp_author = array();
			foreach((array)$authors as $author){
				$role = $this->so_ref_roles->read($author['contact_role']);
				if($role['role_bibtex_export']){
					$code = $role['role_bibtex_code'];
				
					$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
					$temp_author[$code][] = $contact['n_family'].', '.$contact['n_given'];
				}
			}
			
			foreach($temp_author as $code => $data){
				$bibtex .= "\t".$code.' = {'.implode(' and ',(array)$data)."},\n";	
			}
			
			$extras = $this->so_extra->search(array('publi_id' => $publi['publi_id']),false);
			foreach((array)$extras as $extra){
				$field = $this->so_field->read($extra['field_id']);
				if($field['field_export_bibtex'] && !empty($field['field_bibtex_code'])){
					$bibtex .= "\t".$field['field_bibtex_code'].' = {'.$extra['extra_value']."},\n";
				}
			}
			$bibtex .= '}'."\n";
		}

		fputs($temp_file, $bibtex);
		fclose($temp_file);

		header('Content-type: application/force-download');
		header('Content-Disposition:inline;filename="'.lang('BibTeX').'.bib"');
		// readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.bib');

		ob_clean();
		flush();
		if (readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.bib')){
			unlink($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.bib');
		}
	}

	function ris($content=null){
	/**
	 * Génère le fichier ris correspondant à la publication
	 */
		$ris = '';
		foreach ((array)explode(',',$_GET['id']) as $publi_id) {
			$publi = $this->read($publi_id);
			$temp_file = fopen($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.ris', 'w+');

			$type = $this->so_ref_publi_type->read($publi['publi_type']);

			$ris .= 'TY  - '.$type['type_ris_code']."\n";
			$ris .= 'T1  - '.$publi['publi_title']."\n";
			$ris .= 'Y1  - '.$publi['publi_year']."\n";

			$ris .= 'AB  - '.$publi['publi_abstract']."\n";
			
			foreach((array)explode(',',$publi['publi_keywords']) as $keyword){
				$ris .= 'KW  - '.$keyword."\n";
			}
			// $ris .= 'CY  - '.$publi['publi_address']."\n";

			// Ancien code - 04/10/2013
			// $authors = $this->so_contact->search(array('contact_publi' => $publi['publi_id'],'contact_role' => $this->obj_config['author_role']),false,'contact_order');
			// foreach((array)$authors as $author){
			// 	$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
			// 	$ris .= 'A1  - '.$contact['n_family'].', '.$contact['n_given']."\n";
			// }

			$authors = $this->so_contact->search(array('contact_publi' => $publi['publi_id']),false,'contact_role, contact_order');
			$temp_author = array();
			foreach((array)$authors as $author){
				$role = $this->so_ref_roles->read($author['contact_role']);
				if($role['role_ris_export']){
					$code = $role['role_ris_code'];

					$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
					$ris .= $code.'  - '.$contact['n_family'].', '.$contact['n_given']."\n";
				}
				
			}

			$extras = $this->so_extra->search(array('publi_id' => $publi['publi_id']),false);
			foreach((array)$extras as $extra){
				$field = $this->so_field->read($extra['field_id']);
				if($field['field_export_ris'] && !empty($field['field_ris_code'])){
					$ris .= $field['field_ris_code'].'  - '.$extra['extra_value']."\n";
				}
			}

			$ris .= 'ER  -'."\n\n";
		}

		fputs($temp_file, $ris);
		fclose($temp_file);

		header('Content-type: application/force-download');
		header('Content-Disposition:inline;filename="'.lang('RIS').'.ris"');
		// readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.ris');

		ob_clean();
		flush();
		if (readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.ris')){
			unlink($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.ris');
		}
	}

	function csv($content=null){
	/**
	 * Génère le fichier csv correspondant à la publication
	 */
		// Correspondance pour libellé des colonnes
		$field2text = array(
			'type_code' => lang('Type'),
			'publi_title' => lang('Title'),
			'publi_year' => lang('Year'),
			'publi_abstract' => lang('Abstract'),
			'publi_keywords' => lang('Keywords'),
			'contacts' => lang('Contacts')
		);

		$csv = '';
		$i = 0;
		foreach ((array)explode(',',$_GET['id']) as $publi_id) {
			// Info de la publication
			$publi = $this->read($publi_id);

			// Fichier temporaire
			$temp_file = fopen($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.csv', 'w+');

			$type = $this->so_ref_publi_type->read($publi['publi_type']);

			// Champs interne
			$temp_csv[$field2text['type_code']][$i] = $type['type_code'];
			$temp_csv[$field2text['publi_title']][$i] = $publi['publi_title'];
			$temp_csv[$field2text['publi_year']][$i] = $publi['publi_year'];
			$temp_csv[$field2text['publi_abstract']][$i] = $publi['publi_abstract'];
			$temp_csv[$field2text['publi_keywords']][$i] = $publi['publi_keywords'];

			$contacts = $this->so_contact->search(array('contact_publi' => $publi['publi_id']),false,'contact_order');
		
			// Récupération des contacts (groupés par role)
			$current_role = '';
			$temp = array();
			foreach((array)$contacts as $contact){
				$role = $this->so_ref_roles->read($contact['contact_role']);
				$info_contact = $GLOBALS['egw']->contacts->read($contact['contact_add_id']);

				$temp[lang($role['role_label'])][] = $info_contact['n_family'].' '.$info_contact['n_given'];
			}
			// conversion pour correspondre aux formats avant passage en csv
			foreach($temp as $role => $data){
				$temp_csv[$field2text['contacts'].' '.$role][$i] = implode(',',(array)$data);
			}
			
			// Champs additionel
			$extras = $this->so_extra->search(array('publi_id' => $publi['publi_id']),false);
			foreach((array)$extras as $extra){
				$field = $this->so_field->read($extra['field_id']);
				if($field['field_export_csv']){
					$temp_csv[lang($field['field_label'])][$i] = $extra['extra_value'];
				}
			}

			++$i;
		}

		// Formation de chaque ligne (permet d'avoir des colonnes vides pour les valeurs manquantes)
		foreach($temp_csv as $title => $data){
			$lines[0][] = $title;

			for($k = 0 ; $k < $i ; $k++){
				$lines[$k+1][] = $data[$k];
			}
		}

		// Eclatement des lignes en CSV (séparateur => ';' // saut de ligne => "\n")
		foreach($lines as $line){
			$csv .= implode(';',$line);
			$csv .= "\n";
		}

		// Encodage
		$csv = utf8_decode($csv);

		// Envoi du fichier CSV au navigateur + téléchargment
		fputs($temp_file, $csv);
		fclose($temp_file);

		header('Content-type: application/force-download');
		header('Content-Disposition:inline;filename="'.lang('CSV').'.csv"');

		ob_clean();
		flush();
		
		// Force le téléchargement + Supprime le fichier temporaire
		if (readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.csv')){
			unlink($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.csv');
		}
	}

	function xml($content=null){
	/**
	 * Génère le fichier xml correspondant à l'affaire
	 */
		$xml = '<?xml version="1.0" encoding="UTF-8"?><xml><records>';
		foreach ((array)explode(',',$_GET['id']) as $publi_id) {
			$publi = $this->read($publi_id);
			$temp_file = fopen($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.xml', 'w+');

			$type = $this->so_ref_publi_type->read($publi['publi_type']);
			$xml .= '<record><source-app name="Spicademic" version="1.x">Spicademic</source-app>';

			$xml .= '<ref-type>'.$publi['publi_id'].'</ref-type>';

			// Ancien code - 04/10/2013		
			// $authors = $this->so_contact->search(array('contact_publi' => $publi['publi_id'],'contact_role' => $this->obj_config['author_role']),false,'contact_order');
			// if(is_array($authors)){
			// 	$xml .= '<contributors><authors>';
			// 	foreach((array)$authors as $author){
			// 		$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
			// 		$xml .= '<author>'.$contact['n_family'].', '.$contact['n_given'].'</author>';
			// 	}
			// 	$xml .= '</authors></contributors>';
			// }

			$authors = $this->so_contact->search(array('contact_publi' => $publi['publi_id']),false,'contact_role, contact_order');
			if(is_array($authors)){
				$xml .= '<contributors>';

				$current_role = '';
				foreach((array)$authors as $author){
					$author_role = $this->so_ref_roles->read($author['contact_role']);
					if($current_role != $author_role){
						if(!empty($current_role)){
							$xml .= '</'.$current_role['role_xml_code_parent'].'>';
						}

						if($author_role['role_xml_export']){
							$xml .= '<'.$author_role['role_xml_code_parent'].'>';
						}
					}
					$current_role = $author_role;

					$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);

					if($current_role['role_xml_export']){
						$code = $current_role['role_xml_code_child'];
						$xml .= '<'.$code.'>'.$contact['n_family'].', '.$contact['n_given'].'</'.$code.'>';
					}
				}

				if($current_role['role_xml_export']){
					$xml .= '</'.$current_role['role_xml_code_parent'].'>';
				}
				$xml .= '</contributors>';
			}

			$xml .= '<titles><title>'.$publi['publi_title'].'</title></titles>';
			
			if(!empty($publi['publi_keywords'])){
				$xml .= '<keywords>';
				foreach((array)explode(',',$publi['publi_keywords']) as $keyword){
					$xml .= '<keyword>'.$keyword.'</keyword>';
				}
				$xml .= '</keywords>';
			}

			// if(!empty($publi['publi_address']))
			// 	$xml .= '<pub-location>'.$publi['publi_address'].'</pub-location>';

			if(!empty($publi['publi_lang']))
				$xml .= '<language>'.$publi['publi_lang'].'</language>';

			if(!empty($publi['publi_abstract']))
				$xml .= '<abstract>'.$publi['publi_abstract'].'</abstract>';

			$extras = $this->so_extra->search(array('publi_id' => $publi['publi_id']),false);
			$temp_xml = $temp_date_xml = '';
			foreach((array)$extras as $extra){
				$field = $this->so_field->read($extra['field_id']);
				if($field['field_export_xml'] && !empty($field['field_xml_code']) && !empty($extra['extra_value']) ){
					if(in_array($field['field_id'],explode(',',$this->obj_config['xml_date']))){
						$temp_date_xml .= '<'.$field['field_xml_code'].'>'.$extra['extra_value'].'</'.$field['field_xml_code'].'>';
					}else{
						$temp_xml .= '<'.$field['field_xml_code'].'>'.$extra['extra_value'].'</'.$field['field_xml_code'].'>';
					}
				}
			}

			if(!empty($publi['publi_year']))
				$xml .= '<dates><year>'.$publi['publi_year'].'</year>'.$temp_date_xml.'</dates>';

			$xml .= $temp_xml;

			$xml .= '</record>';
		}
		$xml .= '</records></xml>';

		fputs($temp_file, $xml);
		fclose($temp_file);

		header('Content-type: application/force-download');
		header('Content-Disposition:inline;filename="'.lang('XML').'.xml"');
		// readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.xml');

		ob_clean();
		flush();
		if (readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.xml')){
			unlink($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.xml');
		}
	}

	function rtf($content=null){
	/**
	 * Génère le fichier rtf correspondant à l'affaire
	 */
		$rtf = '{\rtf1\ansi\deff0\deftab360
{\fonttbl
{\f0\fswiss\fcharset0 Arial}
{\f1\froman\fcharset0 Times New Roman}
{\f2\fswiss\fcharset0 Verdana}
{\f3\froman\fcharset2 Symbol}
}

{\colortbl;
\red0\green0\blue0;
}

{\info
{\author Spicademic}{\operator }{\title Spicademic RTF Export}}

\f1\fs24
\paperw11907\paperh16839
\pgncont\pgndec\pgnstarts1\pgnrestart ';

		foreach ((array)explode(',',$_GET['id']) as $publi_id) {
			$publi = $this->read($publi_id);
			$temp_file = fopen($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.rtf', 'w+');

			$authors = $this->so_contact->search(array('contact_publi' => $publi['publi_id'],'contact_role' => $this->obj_config['author_role']),false,'contact_order');
			$temp_author = array();
			foreach((array)$authors as $author){
				$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);

				// Récupération des initiales du prénom
				$final_n_given = '';
				$first = true;
				foreach(explode(' ', $contact['n_given']) as $temp_n_given){
					foreach(explode('-',$temp_n_given) as $n_given){
						if(!$first) $final_n_given .= '-';
						$final_n_given .= substr($n_given, 0, 1).'.';
						$first = false;
					}
				}

				$temp_author[] = $contact['n_family'].', '.$final_n_given;
			}

			if(!empty($temp_author))
				$rtf .= implode(' & ',(array)$temp_author).' ';

			if(!empty($publi['publi_year']))
				$rtf .= '('.$publi['publi_year'].'). ';

			if(!empty($publi['publi_title']))
				$rtf .= $publi['publi_title'].'. ';

			$extras = $this->so_extra->search(array('publi_id' => $publi['publi_id']),false);
			$temp_extra = array();
			foreach((array)$extras as $extra){
				$field = $this->so_field->read($extra['field_id']);
				if($field['field_export_rtf'] && !empty($extra['extra_value'])){
					$temp_extra[] = $extra['extra_value'];
				}
			}
			if(!empty($temp_extra))
				$rtf .= implode(', ',(array)$temp_extra).'. ';

			// if(!empty($publi['publi_year']))
			// 	$rtf .= $publi['publi_year'].'. ';

			$rtf .= '\par\par ';
		}

		$rtf .= '}';
		fputs($temp_file, utf8_decode($rtf));
		fclose($temp_file);
	
		header('Content-type: application/force-download');
		header('Content-Disposition:inline;filename="'.lang('RTF').'.rtf"');
		// readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.rtf');

		ob_clean();
		flush();
		if (readfile($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.rtf')){
			unlink($GLOBALS['egw_info']['server']['temp_dir'].'/'.lang('Publication').'_#'.$publi['publi_id'].'.rtf');
		}
	}
	
	function pdf_list($references = null){
	/**
	 * Génère le fichier pdf correspondant à l'affaire
	 */
		// ob_start permet de faire une temporisation de sortie (permet d'éviter l'erreur (FPDF error: Some data has already been output, can't send PDF file)
		ob_start();
		if(isset($_GET['dossier_id'])){
			$ref_param = array();
			$references = explode(',',$_GET['dossier_id']);
			foreach((array)$references as $dossier_id){
				$ref_param[] = $this->read($dossier_id);
			}
			$pdf = CreateObject('spicademic.generate_pdf',$ref_param);
			$pdf->generate_liste();
		}
		
		
	}
	
	function mail($content=null){
	/**
	* Charge l'e-template de mail, l'exécute avec les paramètres donnés.
	*
	* @param array $content = NULL
	*/
		if(is_array($content)){
			list($button) = @each($content['button']);
			switch ($button){
				case 'cancel' :
					echo "<html><body><script>window.close();</script></body></html>\n";
					$GLOBALS['egw']->common->egw_exit();
					break;
				case 'send' :
					$msg = $this->send_mail($content, true);
					unset($content['button']);
					break;
				default :
			}
		}
		if(($_GET['id'])){
			// Informations de la publication
			$publi = $this->read($_GET['id']);
			$content['title'] = $publi['publi_title'];
			
			// Sender
			$content['sendby'] = $GLOBALS['egw_info']['user']['email'];
			
			// Receivers
			// $ses_contact = $this->so_reg->search(array('reg_ses' => $session['ses_id']),false);
			
			// foreach($ses_contact as $reg_info){
			// 	$contact = $GLOBALS['egw']->contacts->read($reg_info['reg_contact']);
				
			// 	if(!empty($contact['email'])){
			// 		$receivers[$contact['email']] = $contact['email'];
			// 	}
			// }
			$content['sendto'] = implode(',',(array)$receivers);
			
			//sujet du mail
			$content['subject']= lang('Publication notification');
						
			// Contenu du message à envoyer
			$content['message'] = str_replace("\n","<br/>",$this->obj_config['mail_publi_notif']);
			
			$url = $GLOBALS['egw_info']['server']['webserver_url'].'/index.php?menuaction=spicademic.spicademic_ui.edit&id='.$publi['publi_id'];
			$content['message'] .= "<hr>".lang('Link to the publication').' : <a href="'.$url.'">'.lang('Click here').'</a>';
		}
		$content['msg'] = $msg;
		$tpl = new etemplate('spicademic.mail');
		$tpl->exec('spicademic.spicademic_ui.mail', $content,$sel_options,$readonlys,$content,2);
	}

	function download($content=null){
	/**
	 * Lance le téléchargement d'un fichier
	 */
		// Infos du fichier
		$file = $this->so_file->read($_GET['id']);
		$fs = $this->so_sqlfs->read($file['file_fs_id']);
		
		// Chemin réel et nom du fichier 
		$cheminReelDuFichier = sqlfs_stream_wrapper::_fs_path(abs($fs['fs_id']));
		$filename = $fs['fs_name'];

		// header permettant de lancer le téléchargement
		header("Content-disposition: attachment; filename=\"".$filename."\"");
		header("Content-type: application/octet-stream");
		readfile ($cheminReelDuFichier);
	}

	function about(){
	/**
	* Affiche le boite de dialogue 'A propos ...'
	*/
	
		$lg = 'en';
		if ($GLOBALS['egw_info']['user']['preferences']['common']['lang'] == 'fr'){
			$lg = 'fr';
		}
			
		$content=$sel_options=$readonlys=array();
		$lines=file(EGW_INCLUDE_ROOT.'/spicademic/about/about_'.$lg.'.txt');
		$content['about']="";
		
		foreach ((array)$lines as $line_num => $line) {
			$content['about'].=htmlspecialchars($line) . "<br />\n";
		}

		// Info de l'appli (Table / Version)
		$app = $GLOBALS['egw_info']['flags']['currentapp'];
		$infoApp = $GLOBALS['egw_info']['apps'][$app];

		$file = EGW_SERVER_ROOT."/$app/setup/tables_current.inc.php";
		$phpgw_baseline = array();
		if ($app != '' && file_exists($file)){
			include($file);
		}
		
		$content['about'] .= "<br/><hr/>".lang('Version').' : '.$infoApp['version']."<br/>";
		$content['about'] .= lang('Tables').' :<br/>'.implode("\t<br/>",array_keys($phpgw_baseline))."<br/>";
		
		$tpl = new etemplate('spicademic.about');
		$tpl->exec('spicademic.spicademic_ui.about', $content,$sel_options,$readonlys,$content,0);
	}

	function search_write_js(){
	/**
	* Génère le code javascript pour faire une recherche
	*
	* @return string
	*/
		$authors = $this->get_authors();
		foreach($authors as $id => $name){
			$js_authors .= 'document.getElementById("exec[nm][authors]['.$id.']").removeAttribute("checked");'."\n";
		}

		$subjects = $this->get_subjects();
		foreach($subjects as $id => $name){
			$js_subjects .= 'document.getElementById("exec[nm][publi_subject]['.$id.']").removeAttribute("checked");'."\n";
		}

		$langs = translation::list_langs();
		foreach($langs as $id => $name){
			$js_langs .= 'document.getElementById("exec[nm][publi_lang]['.$id.']").removeAttribute("checked");'."\n";
		}


		// remise a zero des parametres de recherche
		$javascript='
		<script type="text/javascript">
			function reset_form(form){
				document.getElementById("exec[nm][publi_project]").value="";
				document.getElementById("exec[nm][publi_year]").value="";
				document.getElementById("exec[nm][publi_lang]").value="";
				'.$js_langs.'
				'.$js_subjects.'
				'.$js_authors.'

				var select = document.getElementById("eT_accountsel_exec_nm_publi_responsible_"); // your form
				for (var loop=0; loop < select.options.length; loop++) {
					select.options[loop].removeAttribute("selected"); // remove the option
				}
			}
		</script>
		';
		return $javascript;
	}

	function file_edit($content = null){
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
					$msg = $this->add_update_file(array($content));
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
			$id = $this->so_file->data['file_id'];
			
			$content['msg']=$msg;
		}else{
			// Récupération de l'identifiant
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

			$content += $this->get_file_info($id);
			$GLOBALS['egw_info']['flags']['app_header'] = lang('Edit file');	
		}

		// Listes
		$sel_options = array(
			'file_type' => $this->get_file_type(),
			'file_status' => $this->get_file_status(),
		);
		
		$tpl = new etemplate('spicademic.file.edit');
		$tpl->read('spicademic.file.edit');
		$tpl->exec('spicademic.spicademic_ui.file_edit', $content, $sel_options, $readonlys, $content,2);
	}

	function list_type($content = null){
	/**
	 * Charge le template edit
	 */
		$content = $this->get_publi_type('rows');

		// Listes
		$sel_options = array(
			'type_id' => $this->get_publi_type(),
		);
		
		$tpl = new etemplate('spicademic.list_type');
		$tpl->read('spicademic.list_type');
		$tpl->exec('spicademic.spicademic_ui.list_type', $content, $sel_options, $readonlys, $content,2);
	}

	function import($content = null){
	/**
	 * Charge le template edit
	 */
		if(!empty($content['import'])){
			if(!empty($content['upload_file'])){
				$cross_reference = array(
					'title' 	=> 'publi_title',
					'year' 		=> 'publi_year',
					'abstract' 	=> 'publi_abstract',
					'keywords' 	=> 'publi_keywords',
				);
				$ignore = array('raw','linebegin','lineend','author');
				require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.bibtex_parser.inc.php');
				$bibtex = new BibTeX_Parser($content['upload_file']['tmp_name']);
	
				$success = 0;
				if($bibtex->count >= 0){
					for($i=0;$i <= $bibtex->count; ++$i){
						// Vérification de l'existance du type
						// Comparaison sans prise en compte de la casse
						$join = 'WHERE UPPER(type_bibtex_code) = "'.strtoupper($bibtex->types[$i]).'"';
						$type = $this->so_ref_publi_type->search('',false,$order,'',$wildcard,false,$op,$start,$col_filter,$join);
						
						// Si le type existe bien dans la base
						if(!empty($type)){
							// Valeurs par défaut
							$temp_publi[$i] = array(
								'publi_status' => $this->obj_config['imported_pub_status'],
								'publi_type' => $type[0]['type_id'],
								'publi_responsible' => $GLOBALS['egw_info']['user']['account_id'],
								'publi_creator' => $GLOBALS['egw_info']['user']['account_id'],
								'publi_created' => time(),
							);

							// Parcours des items
							foreach($bibtex->items as $field => $value){
								// On ignore certains champs inutile ET les champs vides
								if(!empty($value[$i]) && !in_array($field,$ignore)){
									if(array_key_exists($field, $cross_reference)){
										// Si le champs n'est pas un champ extra (titre/année/abstract/keywords)
										$temp_publi[$i][$cross_reference[$field]] = $value[$i];
									}else{
										// Si le champs est un champ extra : on verifie qu'il existe dans la base et est dispo pour ce type
										$field_id = '';
										$check_fields = $this->so_field->search(array('field_bibtex_code' => $field),false);
										if(!empty($check_fields)){
											foreach($check_fields as $check_field){
												$check_type_field = $this->so_type_field->read(array('type_id' => $type[0]['type_id'],'field_id' => $check_field['field_id']));
												if(!empty($check_type_field) && empty($field_id)){
													$field_id = $check_field['field_id'];
												}
											}
										}

										// Ajout dans un tableau temporaire
										if(!empty($field_id)){
											$temp_publi_extra[$i][] = array(
												'field_id' => $field_id,
												'extra_value' => $value[$i],
												'extra_creator' => $GLOBALS['egw_info']['user']['account_id'],
												'extra_created' => time(),
											);
										}
									}
								}
							}

							$success++;
						}else{
							// Type non existant dans la base
							$error[] = lang('The type "%1" does not match any available types', strtoupper($bibtex->types[$i]));
						}
					}

					// Mise en base
					foreach($temp_publi as $key => $data){
						// Mise en base de la publication
						$this->data = $data;
						$this->save();

						// Parcours des champs extra (mise en base avec reprise de l'id de publication obtenu juste avant)
						foreach($temp_publi_extra[$key] as $extra_data){
							$this->so_extra->data = $extra_data;
							$this->so_extra->data['publi_id'] = $this->data['publi_id'];
							$this->so_extra->save();
						}
					}
				}else{
					// Fichier ne contenant aucune publication (bib vide ou mal remplit)
					$error[] = lang('Selected file contains no publications');
				}
				
				
				// Message de retour
				$content['msg'] = lang('%1 publication successfully imported',$success)."\n";
				$content['msg'] .= lang('%1 publication failed to be imported',count($error))."\n";

				// Notice d'erreur (type inexistant)
				if(!empty($error))
					$content['msg'] .= lang('Notice').' : '.implode("\n",$error);
			}else{
				$content['msg'] = lang('Error').' : '.lang('No file uploaded');
			}
		}

		unset($content['upload_file']);
		unset($content['import']);

		$tpl = new etemplate('spicademic.import');
		$tpl->read('spicademic.import');
		$tpl->exec('spicademic.spicademic_ui.import', $content, $sel_options, $readonlys, $content,0);
	}

	function admin($content = null){
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
					// $GLOBALS['egw_info']['flags']['java_script'] .= "<script language=\"JavaScript\">
					// 	var referer = opener.location;
					// 	opener.location.href = referer+(referer.search?'&':'?')+'msg=".addslashes(urlencode($msg))."';</script>";
					break;
				default:
				case 'cancel':
					echo "<html><body><script>window.close();</script></body></html>\n";
					$GLOBALS['egw']->common->egw_exit();
					break;
			}
		}
		
		// Récupération des données
		$content = $this->obj_config;
		
		// Remplissage des listes
		$sel_options = array(
			'default_pub_status' => $this->get_all_publi_status(),
			'validated_pub_status' => $this->get_all_publi_status(),
			'archived_pub_status' => $this->get_all_publi_status(),
			'pending_pub_status' => $this->get_all_publi_status(),
			'imported_pub_status' => $this->get_all_publi_status(),
			
			'default_file_status' => $this->get_file_status(),

			'author_role' => $this->get_roles(),
			'translator_role' => $this->get_roles(),
			'editor_role' => $this->get_roles(),

			'xml_date' => $this->get_fields(),
		);		
		
		$tpl = new etemplate('spicademic.admin.general');
		$tpl->exec('spicademic.spicademic_ui.admin', $content,$sel_options,$no_button, $content);
	}
}
?>