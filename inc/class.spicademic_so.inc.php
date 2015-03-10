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

class spicademic_so extends so_sql{
	
	var $spicademic_publi = 'spicademic_publi';

	var $spicademic_ref_pub_status = 'spicademic_ref_pub_status';
	var $spicademic_ref_pub_status_transition = 'spicademic_ref_pub_status_transition';
	var $spicademic_ref_pub_type = 'spicademic_ref_pub_type';
	var $spicademic_ref_project = 'spicademic_ref_project';
	var $spicademic_ref_subject = 'spicademic_ref_subject';
	var $spicademic_ref_role = 'spicademic_ref_role';
	var $spicademic_publi_subject = 'spicademic_publi_subject';
	var $spicademic_ref_extension = 'spicademic_ref_extension';
	var $spicademic_publi_file = 'spicademic_publi_file';
	var $spicademic_publi_comment = 'spicademic_publi_comment';
	var $spicademic_ref_type_field = 'spicademic_ref_type_field';
	var $spicademic_ref_field = 'spicademic_ref_field';
	var $spicademic_publi_extra = 'spicademic_publi_extra';
	var $spicademic_publi_contact = 'spicademic_publi_contact';
	var $spicademic_file_type = 'spicademic_ref_file';
	var $spicademic_file_status = 'spicademic_ref_file_status';

	var $so_ref_publi_status;
	var $so_ref_publi_status_transition;
	var $so_ref_publi_type;
	var $so_ref_projects;
	var $so_ref_subjects;
	var $so_ref_roles;
	var $so_publi_subject;
	var $so_extension;
	var $so_file;
	var $so_comment;
	var $so_type_field;
	var $so_field;
	var $so_extra;
	var $so_contact;
	var $so_file_type;
	var $so_file_status;

	var $so_sqlfs;
	
	var $account_id;
	var $obj_accounts;
	
	/**
	 * Constructeur
	 *
	 */
	function spicademic_so(){
		$GLOBALS['egw_info']['user']['account_id']=$GLOBALS['egw_info']['user']['account_id'];
		$this->obj_accounts = CreateObject('phpgwapi.accounts',$GLOBALS['egw_info']['user']['account_id'],'u');

		parent::so_sql('spicademic',$this->spicademic_publi);
		$this->so_ref_publi_status = new so_sql('spicademic',$this->spicademic_ref_pub_status);
		$this->so_ref_publi_status_transition = new so_sql('spicademic',$this->spicademic_ref_pub_status_transition);
		$this->so_ref_publi_type = new so_sql('spicademic',$this->spicademic_ref_pub_type);
		$this->so_ref_project = new so_sql('spicademic',$this->spicademic_ref_project);
		$this->so_ref_subject = new so_sql('spicademic',$this->spicademic_ref_subject);
		$this->so_ref_roles = new so_sql('spicademic',$this->spicademic_ref_role);
		$this->so_publi_subject = new so_sql('spicademic',$this->spicademic_publi_subject);
		$this->so_extension = new so_sql('spicademic',$this->spicademic_ref_extension);
		$this->so_file = new so_sql('spicademic',$this->spicademic_publi_file);
		$this->so_comment = new so_sql('spicademic',$this->spicademic_publi_comment);
		$this->so_type_field = new so_sql('spicademic',$this->spicademic_ref_type_field);
		$this->so_field = new so_sql('spicademic',$this->spicademic_ref_field);
		$this->so_extra = new so_sql('spicademic',$this->spicademic_publi_extra);
		$this->so_contact = new so_sql('spicademic',$this->spicademic_publi_contact);
		$this->so_file_type = new so_sql('spicademic',$this->spicademic_file_type);
		$this->so_file_status = new so_sql('spicademic',$this->spicademic_file_status);

		$this->so_sqlfs = new so_sql('phpgwapi', 'egw_sqlfs');
	}
	
	function is_manager(){
	/**
	 * Vérifie si l'utilisateur est manager ou non
	 *
	 * @return boolean
	 */
		$groupeUser = array_keys($GLOBALS['egw']->accounts->memberships($GLOBALS['egw_info']['user']['account_id']));
		
		$config = CreateObject('phpgwapi.config');
		$obj_config = $config->read('spicademic');
		
		if($GLOBALS['egw_info']['user']['apps']['admin'] || in_array($obj_config['ManagementGroup'],$groupeUser)){
			return true;
		}else{
			return false;
		}
	}
	
	function construct_search($search){
	/**
	 * Crée une recherche. Le tableau de retour contiendra toutes les colonnes de la table en cours, en leur faisant correspondre la valeur $search 
	 *
	 * La requête ainsi crée est prète à être utilisée comme filtre
	 *
	 * @param int $search tableau des critères de recherche
	 * @return array
	 */
		$tab_search=array();
		 foreach((array)$this->db_data_cols as $id=>$value){
			 $tab_search[$id]=$search;
		 }

		return $tab_search;
	}

	function set_readonlys(){
	/**
	 * Genere la liste des informations a mettre en readonly
	 */
		foreach((array)$this->db_data_cols as $key => $value){
			$retour[$key] = true;
		}
		return $retour;
	}
	
	
	function add_update_publi($info){
	/**
	 * Fonction permettant la mise à jour ou la creation d'une reference
	 *
	 * @param $info tableau contenant les valeurs
	 * @return string
	 */
		$msg='';
		if(is_array($info)){
			if(isset($info['publi_id'])){
				// Existant
				$this->history($info);

				$this->add_update_subject($info['publi_id'],$info['publi_subject']);
				// $this->add_update_comment($info['publi_id'],$info['comment']);
				$this->add_update_extra($info['publi_id'], $info['publi_type'],$_POST['exec']['details']);

				$this->data = $info;
				$this->data['publi_modified'] = time();
				$this->data['publi_modifier'] = $GLOBALS['egw_info']['user']['account_id'];
				$this->update($this->data,true);
				
				$msg .= ' '.lang('Publication updated');
			}else{
				// Nouveau
				$this->data = $info;
				$this->data['publi_id'] = '';
				$this->data['publi_created'] = time();
				$this->data['publi_creator'] = $GLOBALS['egw_info']['user']['account_id'];
				$this->save();

				$this->add_update_subject($this->data['publi_id'],$info['publi_subject']);
				
				$msg .= ' '.lang('Publication created');
			}
		}
		return $msg;
	}

	function add_update_comment($publi_id, $comment){
	/**
	 * Fonction de creation des commentaires pour la publication $publi_id
	 *
	 * @param $publi_id : identifiant de la publication
	 * @param $comment : informations concernant les commentaires
	 */
		if(!empty($comment['comment_text']) && $comment['comment_text'] != '<br />'){
			$this->so_comment->data = array(
				'comment_id' => '',
				'comment_publi' => $publi_id,
				'comment_text' => $comment['comment_text'],
				'comment_visa' => $comment['comment_visa'],
				'comment_status' => '',
				'comment_creator' => $GLOBALS['egw_info']['user']['account_id'],
				'comment_created' => time(),
			);
			$this->so_comment->save();

			return lang('Comment added');
		}else{
			return lang('Comment text must not be empty');
		}
	}

	function add_update_subject($publi_id, $subjects){
	/**
	 * Fonction de mise a jour / creation des sujets pour la publication $publi_id
	 *
	 * @param $publi_id : identifiant de la publication
	 * @param $subjects : sujets à ajouter pour cette publication
	 */
		$this->so_publi_subject->delete(array('publi_id' => $publi_id));

		if(!empty($subjects)){
			foreach((array)explode(',',$subjects) as $key => $value){
				$this->so_publi_subject->data['publi_id'] = $publi_id;
				$this->so_publi_subject->data['subject_id'] = $value;
				$this->so_publi_subject->save();
			}
		}
	}

	function add_update_extra($publi_id, $type_id, $extras){
	/**
	 * Fonction de mise a jour / creation des extra pour la publication $publi_id
	 *
	 * @param $publi_id : identifiant de la publication
	 * @param $extras : extra pour cette publication
	 */
		$this->so_extra->delete(array('publi_id' => $publi_id));	

		foreach((array)$extras as $extra){
			foreach((array)$extra as $field_id => $value){
				if(is_array($value)) $value = implode(',',$value);
				
				$type_field = $this->so_type_field->read(array('type_id' =>$type_id, 'field_id' => $field_id));
				if(is_array($type_field)){
					$this->so_extra->data = array(
						'publi_id' => $publi_id,
						'field_id' => $field_id,
						'extra_value' => $value,
						'extra_creator' => $GLOBALS['egw_info']['user']['account_id'],
						'extra_created' => time()
					);
				}
				$this->so_extra->save();
			}
		}
	}

	function add_update_file($list){
	/**
	 * Fonction d'ajout de fichier (par l'onglet docs uniquement)
	 *
	 * @param $list : liste des documents a ajouter/modifier
	 */
		
		foreach((array)$list as $file){
			$exist = $this->so_file->read($file['file_id']);
			$this->so_file->data = $file;
			if(!is_array($exist)){
				$this->so_file->data['file_created'] = time();
				$this->so_file->data['file_creator'] = $GLOBALS['egw_info']['user']['account_id'];
			}else{
				$this->so_file->data['file_modified'] = time();
				$this->so_file->data['file_modifier'] = $GLOBALS['egw_info']['user']['account_id'];
			}
			$this->so_file->save();
		}
		
		return implode("\n",$msg);
	}

	function truncate($string, $limit=30, $break="-", $pad=" ...") { 
		// Tronque une chaine de caractere et ajout $ad a la fin de celle-ci
		if(strlen($string) <= $limit) return $string; 
		
		
		$string = substr($string, 0, $limit) . $pad; 

		return $string; 
	}

	function history($content){
	/**
	 * Fonction permettant l'historisation des valeurs (lors de la mise a jour d'une reference)
	 *
	 * @param $content : info concernant la référence (contient les infos avec les nouvelles valeurs)
	 */
		// Valeur actuel du contrat
		$id = $content['publi_id'];
		$old = $this->read($id);

		// Nouvelles valeurs
		$history = array_diff_assoc($content,$old);
		$infoHistory = $history['history'];

		$FieldIgnore = array('msg','general|publication|contact|subject|files|comment|link|history','comment','details','files','contact','history','link_to','publi_modified','publi_modifier','publi_creator','publi_created','button','file_status','file_type','upload_file','upload_name','publi_subject','search','status_only','no_links','hideupload','general|publication|contact|subject|files|comment|link|history','help_type','publi_type_html','tab_details');
		$FieldDate = array('');
		$FieldExternal = array(
			'publi_status' => array('table' => $this->so_ref_publi_status,'field' => 'status_label'),
			'publi_type' => array('table' => $this->so_ref_publi_type,'field' => 'type_label'),
			'publi_project' => array('table' => $this->so_ref_project,'field' => 'project_title'),
		);
		$FieldUser = array('publi_responsible');
		$FieldText = array('publi_abstract','publi_desc');
		
		$historylog = CreateObject('phpgwapi.historylog','spicademic');


		// Historisation des field
		foreach((array)$history as $field => $value){
			if(!in_array($field,$FieldIgnore)){				
				// test afin de savoir si on est sur une valeur qui etait null (mais qui apparait avec la valeur 0) cas des listes
				if(!($value == null && $old[$field] == '0')){
					if(in_array($field, $FieldDate)){
						$historylog->add(lang($field),$id,empty($value) ? '' : date('d/m/Y H:i',$value),empty($old[$field]) ? '' : date('d/m/Y H:i',$old[$field]));
					}else{
						if(array_key_exists($field,$FieldExternal)){
							$new_value = $FieldExternal[$field]['table']->read($value);
							$old_value = $FieldExternal[$field]['table']->read($old[$field]);
							$historylog->add(lang($field),$id,$new_value[$FieldExternal[$field]['field']],$old_value[$FieldExternal[$field]['field']]);
						}else{
							if(in_array($field,$FieldUser)){
								$new_contact = $GLOBALS['egw']->accounts->read($value);
								$old_contact = $GLOBALS['egw']->accounts->read($old[$field]);
								
								$new_name = $new_contact['account_firstname'].' '.$new_contact['account_lastname'];
								$old_name = $old_contact['account_firstname'].' '.$old_contact['account_lastname'];
								$historylog->add(lang($field),$id,$new_name,$old_name);
							}else{
								if(in_array($field,$FieldText)){
									$value = $this->truncate($value);
									$old[$field] = $this->truncate($old[$field]);
								}
								$historylog->add(lang($field),$id,$value,$old[$field]);
							}
						}
					}
				}
			}
		}
	}

	function send_mail($content){
	/**
	 * Fonction d'envoi de mail
	 *
	 * @param $content array : information sur le mail a envoyer (message / sendto / sendby / sendcc)
	 * @return string
	 */
		$content['message'] = htmlentities($content['message'], ENT_NOQUOTES, "UTF-8");
		$content['message'] = htmlspecialchars_decode($content['message']);

		$to = $content['sendto'];
		$subject = 	$content['subject'];

		$bound_text = 	"spirea";
		 
		$bound = 	"--".$bound_text."\n";
		 
		$bound_last = 	"--".$bound_text."--\n";
		 
		$headers = 	"From: ".$content['sendby']."\n";
		
		if(!empty($content['sendcc'])){
			$headers .= "Cc: ".$content['sendcc']."\n";
		}

		if($content['notification']){
			$headers .='Disposition-Notification-To: '.$content['sendby']."\n";
			$headers .='Return-Receipt-To: '.$content['sendby']."\n";
		}

		$headers .= "MIME-Version: 1.0\n"
			."Content-Type: multipart/mixed; boundary=\"$bound_text\"\n";
		 
		$message .= 	"If you can see this MIME than your client doesn't accept MIME types!\n"
			.$bound;
		
		$message .= 	"Content-Type: text/html; charset=\"ISO-8859-1\"\n"
			."Content-Transfer-Encoding: 8bit\n\n"
			.$content['message']."\n"
			.$value
			.$bound;
		
		if(mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers)){
			$msg = lang('Notification sent successfull');
		}else{
			$msg = lang('Notification failed');
		}
		
		return $msg;
	}

	function add_update_config($info){
	/**
	 * Routine permettant de créer/modifier la config
	 *
	 * @param array $content=null
	 * @return string
	 */
		$obj = CreateObject('phpgwapi.config');
		foreach((array)$info as $id => $value){
			$obj->save_value($id,$value,'spicademic');
		}
		$this->obj_config = $obj->read('spicademic');
		return lang('Configuration updated');
	}
	
}
?>
