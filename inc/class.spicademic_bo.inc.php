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

require_once(EGW_INCLUDE_ROOT. '/spicademic/inc/class.spicademic_so.inc.php');	

class spicademic_bo extends spicademic_so{
	
	var $obj_config;

	function spicademic_bo()
	{
		$config = CreateObject('phpgwapi.config');
		$this->obj_config = $config->read('spicademic');
		parent::spicademic_so();
	}
	
	function get_info($id){
	/**
	 * Retourne les informations pour la note de frais ayant l'identifiant $id
	 *
	 * @param int $id : identifiant de la note de frais
	 * @return array
	 */
		$info = $this->read($id);

		// Récupération des sujets
		$publi_subjects = $this->so_publi_subject->search(array('publi_id' => $id),false);
		foreach ((array)$publi_subjects as $publi_subject) {
			$info['publi_subject'][] = $publi_subject['subject_id'];
		}

		// Champs utilisé pour google scholar
		$info['search'] = urlencode('"'.$info['publi_title'].'"');
			
		// Auteurs
		$temp_author = array();
		$authors = $this->so_contact->search(array('contact_publi' => $info['publi_id'],'contact_role' => $this->obj_config['author_role']),false);
		foreach((array)$authors as $author){
			$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
			$temp_author[] = urlencode($contact['n_family']);
		}

		if(!empty($temp_author))
			$info['search'] .= '+'. urlencode('authornbsp:').implode(',',$temp_author);
		
		return $info;
	}

	function get_rows($query,&$rows,&$readonlys){
	/**
	 * Récupére et filtre les références
	 *
	 * @param array $query avec des clefs comme 'start', 'search', 'order', 'sort', 'col_filter'. Pour définir d'autres clefs comme 'filter', 'cat_id', vous devez créer une classe fille
	 * @param array &$rows lignes complétes
	 * @param array &$readonlys pour mettre les lignes en read only en fonction des ACL, non utilisé ici (à utiliser dans une classe fille)
	 * @return int
	 */
		$GLOBALS['egw']->session->appsession('index','spicademic',$query);

		$order = $query['order'].' '.$query['sort'];
		$id_only = false;
		$start=array(
			(int)$query['start'],
			(int) $query['num_rows']
		);
		$wildcard = '%';
		$op = 'OR';
		$fields = '*';

		// $search = $query['search'];

		// Filter2 - Session type filter
		switch ($query['filter2']) {
			case 'archived':
				$query['col_filter']['publi_status'] = explode(',',$this->obj_config['archived_pub_status']);
				break;
			case 'validated':
				$query['col_filter']['publi_status'] = $this->obj_config['validated_pub_status'];
				break;
			case 'pending':
				$query['col_filter']['publi_status'] = explode(',',$this->obj_config['pending_pub_status']);
				break;
			case 'import':
				if(!empty($this->obj_config['imported_pub_status']))
					$query['col_filter']['publi_status'] = $this->obj_config['imported_pub_status'];

				// Non admin = Uniquement ces propres imports
				if($GLOBALS['egw_info']['user']['SpicademicLevel'] < 59){
					$query['col_filter']['publi_responsible'] = $GLOBALS['egw_info']['user']['account_id'];
				}
				break;
			case 'all':
				if(!empty($this->obj_config['imported_pub_status']))
					$where[] = 'publi_status <> '.$this->obj_config['imported_pub_status'];
				
				break;
		}

		// Filter - Type de publication
		if(!empty($query['filter']))
			$query['col_filter']['publi_type'] = $query['filter'];

		// Recherche avancée - Projet
		if(!empty($query['nm']['publi_project']))
			$query['col_filter']['publi_project'] = $query['nm']['publi_project'];

		// Recherche avancée - Année
		if(!empty($query['publi_year']))
			$query['col_filter']['publi_year'] = $query['publi_year'];

		// Recherche avancée - Langue
		if(!empty($query['publi_lang']))
			$query['col_filter']['publi_lang'] = $query['publi_lang'];

		// Recherche avancée - Responsable
		if(!empty($query['publi_responsible']))
			$query['col_filter']['publi_responsible'] = $query['publi_responsible'];

		// Recherche avancée - Themes
		$temp_order = $order;
		if(!empty($query['publi_subject'])){
			$where[] = 'subject_id IN ('.$query['publi_subject'].')';
			$inner[] = 'INNER JOIN spicademic_publi_subject ON spicademic_publi.publi_id = spicademic_publi_subject.publi_id';

			$order = ' GROUP BY spicademic_publi.publi_id ORDER BY spicademic_publi.'.$temp_order;
		}

		// Rechercher avancée - Auteurs
		if(!empty($query['authors'])){
			$where[] = 'contact_add_id IN ('.$query['authors'].')';
			$inner[] = 'LEFT JOIN spicademic_publi_contact ON spicademic_publi_contact.contact_publi = spicademic_publi.publi_id';

			$order = ' GROUP BY spicademic_publi.publi_id ORDER BY spicademic_publi.'.$temp_order;
		}

		// Recherche doit prendre en compte les auteurs
		if(!empty($query['search'])){
			if(!in_array('LEFT JOIN spicademic_publi_contact ON spicademic_publi_contact.contact_publi = spicademic_publi.publi_id', $inner))
				$inner[] = 'LEFT JOIN spicademic_publi_contact ON spicademic_publi_contact.contact_publi = spicademic_publi.publi_id';

			$inner[] = 'LEFT JOIN egw_addressbook ON spicademic_publi_contact.contact_add_id = egw_addressbook.contact_id';

			$search = $this->construct_search($query['search']);
			$search['n_family'] = $query['search'];
			$search['n_given'] = $query['search'];

			$order = ' GROUP BY spicademic_publi.publi_id ORDER BY spicademic_publi.'.$temp_order;
		}

		// Certains tri requiert des joins et des conditions spécifique
		switch($query['order']){
			case 'publi_type':
				$inner[] = 'INNER JOIN spicademic_ref_pub_type ON spicademic_ref_pub_type.type_id = spicademic_publi.publi_type';
				$order = ' GROUP BY spicademic_publi.publi_id ORDER BY type_title '.$query['sort'];
				break;
			case 'publi_responsible':
				break;
			case 'authors':
				if(!in_array('LEFT JOIN spicademic_publi_contact ON spicademic_publi_contact.contact_publi = spicademic_publi.publi_id', $inner))
					$inner[] = 'LEFT JOIN spicademic_publi_contact ON spicademic_publi_contact.contact_publi = spicademic_publi.publi_id';
				
				if(!in_array('LEFT JOIN egw_addressbook ON spicademic_publi_contact.contact_add_id = egw_addressbook.contact_id', $inner))
					$inner[] = 'LEFT JOIN egw_addressbook ON spicademic_publi_contact.contact_add_id = egw_addressbook.contact_id';
				
				$where[] = '(contact_order IN (SELECT MIN(contact_order) FROM spicademic_publi_contact WHERE contact_publi = publi_id) OR contact_order IS NULL)';

				$order = ' GROUP BY spicademic_publi.publi_id ORDER BY CONCAT(n_family,n_given) '.$query['sort'];
				break;
		}

		// Contruction du join
		if(is_array($inner)){
			$join = implode(' ',$inner);
		}

		if(is_array($where)){
			$join .= ' WHERE '.implode(' AND ',$where);
		}

// $this->debug = 5;
		$rows = $this->search($search,$fields,$order,'',$wildcard,false,$op,$start,$query['col_filter'],$join);
		
		foreach((array)$rows as $id => $row){
			// Simple utilisateur
			if($GLOBALS['egw_info']['user']['SpicademicLevel'] < 20){
				$readonlys['edit['.$row['publi_id'].']'] = true;
			}else{
				$readonlys['view['.$row['publi_id'].']'] = true;
			}

			// Responsable
			if($row['publi_responsible'] == $GLOBALS['egw_info']['user']['account_id']){
				$readonlys['edit['.$row['publi_id'].']'] = false;
				$readonlys['view['.$row['publi_id'].']'] = true;
			}

			// Statut archivé
			if(in_array($row['publi_status'],explode(',',$this->obj_config['archived_pub_status']))){
				$readonlys['edit['.$row['publi_id'].']'] = true;
				$readonlys['view['.$row['publi_id'].']'] = false;
			}

			// Recherche google scholar
			$rows[$id]['search'] = urlencode('"'.$row['publi_title'].'"');
			
			$temp_author = array();
			$authors = $this->so_contact->search(array('contact_publi' => $row['publi_id'],'contact_role' => $this->obj_config['author_role']),false);
			if(is_array($authors)){
				foreach((array)$authors as $author){
					$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
					$temp_author[] = urlencode($contact['n_family']);
				}
			}
			
			if(!empty($temp_author))
				$rows[$id]['search'] .= '+'. urlencode('authornbsp:').implode(',',$temp_author);

			// Auteurs
			$temp = array();
			$current_role = '';

			// Nb auteurs
			$rows[$id]['nb_authors'] = count($temp_author);
			
			// Récupération de tout les contacts de la publication
			
			if($this->obj_config['author_only']){
				$authors = $this->so_contact->search(array('contact_publi' => $row['publi_id'],'contact_role' => $this->obj_config['author_role']),false,'contact_order');
			}else{
				$authors = $this->so_contact->search(array('contact_publi' => $row['publi_id']),false,'contact_order');
			}
			foreach((array)$authors as $author){
				// On récupère le nom de chacun des contacts
				$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
				$rows[$id]['authors'] .= $contact['n_family'].' '.$contact['n_given'].'<br />';
			}
		
			/* champs pour l'export csv */
			if($query['csv_export']==true)
			{
				// Champs issus du referentiel...
				$publi_type_temp = $this->so_ref_publi_type->read($rows[$id]['publi_type']);
				$rows[$id]['publi_type_export'] = $publi_type_temp['type_code'];
				unset($publi_type_temp);
				
				$publi_status_temp = $this->so_ref_publi_status->read($rows[$id]['publi_status']);
				$rows[$id]['publi_status_export'] = lang($publi_status_temp['status_label']);
				unset($publi_status_temp);
				
				$publi_project_temp = $this->so_ref_project->read($rows[$id]['publi_project']);
				$rows[$id]['publi_project_export'] = $publi_project_temp['proj_code'];
				unset($publi_project_temp);
			}

			// Suppression pour admin / gestionnaire et responsable
			$readonlys['delete['.$row['publi_id'].']'] = true;
			if($GLOBALS['egw_info']['user']['SpicademicLevel'] > 19 || $row['publi_responsible'] == $GLOBALS['egw_info']['user']['account_id']){
				$readonlys['delete['.$row['publi_id'].']'] = false;
			}
			
		}
		
		return $this->total;	
    }

	/**
	 * get title for an tracker item identified by $entry
	 *
	 * Is called as hook to participate in the linking
	 *
	 * @param int/array $entry int ts_id or array with tracker item
	 * @return string/boolean string with title, null if tracker item not found, false if no perms to view it
	 */
	function link_title($entry)
	{
		if (!is_array($entry))
		{
			$entry = $this->read($entry);
		}
		if (!$entry)
		{
			return $entry;
		}
		return '#'.$entry['publi_id'].': '.$entry['publi_title'];
	}

	/**
	 * get titles for multiple tracker items
	 *
	 * Is called as hook to participate in the linking
	 *
	 * @param array $ids array with tracker id's
	 * @return array with titles, see link_title
	 */
	function link_titles($ids)
	{
		$titles = array();
		if (($references = $this->search(array('publi_id' => $ids),'publi_id,publi_title')))
		{
			foreach((array)$references as $reference)
			{
				$titles[$reference['publi_id']] = $this->link_title($reference);
			}
		}
		// we assume all not returned tickets are not readable by the user, as we notify egw_link about each deleted ticket
		foreach((array)$ids as $id)
		{
			if (!isset($titles[$id])) $titles[$id] = false;
		}
		return $titles;
	}

	/**
	 * query clients for entries matching $pattern
	 *
	 * Is called as hook to participate in the linking
	 *
	 * @param string $pattern pattern to search
	 * @return array with client_id - client_company pairs of the matching entries
	 */
	function link_query($pattern)
	{
		$result = array();
		foreach((array) $this->search(array('publi_title' => $pattern),false,'publi_id ASC','','%',false,'OR',false,'') as $item )
		{
			if ($item) $result[$item['publi_id']] = $this->link_title($item);
		}
		return $result;
	}


	function get_publi_files($publi_id, &$readonlys){
	/**
	 * Fonction permettant la récupération des fichiers d'une publication
	 *
	 * @param $publi_id int : identifiant de la publication
	 * @return array
	 */
		$GLOBALS['egw_info']['user']['spicademic_add'] = true;
		$so_sqlfs = new so_sql('phpgwapi', 'egw_sqlfs');
		
		$return = array();
		$info = $this->so_file->search(array('file_publi' => $publi_id),false);

		$i = 1;
		foreach((array)$info as $file){
			$return[$i] = $file;

			$fs_file = $so_sqlfs->read($file['file_fs_id']);
			$return[$i]['filename'] = $fs_file['fs_name'];

			if($GLOBALS['egw_info']['user']['SpicademicLevel'] == 1)
				$readonlys['edit['.$return[$i]['file_id'].']'] = true;
			
			++$i;
		}

		unset($so_sqlfs);
		unset($GLOBALS['egw_info']['user']['spicademic_add']);
		return $return;
	}

	function get_file_info($file_id){
	/**
	 * Retourne les informations d'un fichier
	 *
	 * @param $file_id : identifiant du fichier
	 * @return array
	 */
		$so_sqlfs = new so_sql('phpgwapi', 'egw_sqlfs');

		$info = $this->so_file->read($file_id);

		$fs_file = $so_sqlfs->read($info['file_fs_id']);
		$info['filename'] = $fs_file['fs_name'];


		unset($so_sqlfs);
		return $info;
	}

	function get_comments($publi_id){
	/**
	 * Récupération des commentaires pour la publication $publi_id
	 *
	 * @param $publi_id : identifiant de la publication
	 * @return array
	 */
		$return = array();

		$info = $this->so_comment->search(array('comment_publi' => $publi_id),false,'comment_created DESC');

		$i = 1;
		foreach((array)$info as $data){
			$return[$i] = $data;
			++$i;
		}

		return $return;
	}

	function get_visa(){
	/**
	 * Retourne la liste des valeurs possible pour le champs comment_visa
	 *
	 * @return array
	 */
		return array(
			0 => lang('Empty'),
			1 => lang('Positive'),
			2 => lang('Negative'),
			3 => lang('Average'),
		);
	}
	
	function get_publi_status($status_id=''){
    /**
     * Listes de statut de publication
     *
     * @return array
     */
    	$groups = $GLOBALS['egw']->accounts->memberships($GLOBALS['egw_info']['user']['account_id']);

    	$return = array();
		$info = $this->so_ref_publi_status->read($status_id);

		if(!empty($status_id)){
			$childs[$status_id] = $status_id;

			$transition = $this->so_ref_publi_status_transition->search(array('status_source' => $status_id),false);
			foreach((array)$transition as $key => $data){
				$childs[] = $data['status_target'];
			}
			
			// foreach($childs as $status_id){
			$info = $this->so_ref_publi_status->search(array('status_id' => $childs), false, 'status_order');
			foreach((array)$info as $data){
				// On ajoute le groupe dans la liste seulement si l'utilisateur a les droits sur le statut
				if(array_key_exists($data['status_group'], $groups) || empty($data['status_group']) || $GLOBALS['egw_info']['user']['SpiqualLevel'] >= 99){
	    			$return[$data['status_id']] = $data['status_label'];
	    		}
	    	}
			// }
		}else{
			$return[$status_id] = $info['status_label'];

			$info = $this->so_ref_publi_status->search(array('status_active'=>'1'),false,'status_order');
	    	foreach((array)$info as $data){
	    		$return[$data['status_id']] = $data['status_label'];
	    	}
		}
		
		return $return;
    }
	
	function get_publi_type($type='', $selected_id){
    /**
     * Retourne la liste des types de publication
     *
     * @return array
     */
    	$root_types = $this->get_root_types();

    	$info = $this->so_ref_publi_type->search(array('type_active'=>'1'),false,'type_order');
    	switch($type){
    		case 'html':
    			$return .= '<select name="exec[publi_type]" id="exec[publi_type]" >';
    			$return .= '<option value="" >'.lang('Select one').'</option>';
    			break;
    		case 'help':
    		case 'rows':
    			$return = '';
    			break;
    		case 'level':
    		default:
    			$return = array();
    			break;
    	}
    	
    	// $i = 1;
    	// foreach((array)$info as $data){
    	// 	switch($type){
	    // 		case 'html':
	    // 			if(in_array($data['type_id'],$root_types)){
	    // 				$title = $data['type_title'];
	    // 			}else{
	    // 				$title = '-- '.$data['type_title'];
	    // 			}
	    // 			$selected = '';
		   //  		if($data['type_id'] == $selected_id){
		   //  			$selected = 'selected=selected';
		   //  		}
		   //  		$return .= '<option value='.$data['type_id'].' title="'.$data['type_description'].'" '.$selected.' >'.$title.'</option>';
	    // 			break;
	    // 		case 'help':
	    // 			$return .= '&bull; '.$data['type_title'].' : '.$data['type_description']."\n";
	    // 			break;
	    // 		case 'rows':
	    // 			$return[$i]['type_id'] = $data['type_id'];
    	// 			$return[$i]['type_description'] = $data['type_description'];
    	// 			++$i;
	    // 			break;
	    // 		case 'level':
	    // 			if(in_array($data['type_id'],$root_types)){
	    // 				$return[$data['type_id']] = $data['type_title'];
	    // 			}else{
	    // 				$return[$data['type_id']] = '-- '.$data['type_title'];
	    // 			}
	    // 		default:
    	// 			$return[$data['type_id']] = $data['type_title'];    			
	    // 			break;
	    // 	}
    	// }

    	foreach((array)$root_types as $type_id){
    		$type_data = $this->so_ref_publi_type->read($type_id);
    		switch($type){
	    		case 'html':
	    			$selected = '';
		    		if($type_data['type_id'] == $selected_id){
		    			$selected = 'selected=selected';
		    		}
		    		$return .= '<option value='.$type_data['type_id'].' title="'.$type_data['type_description'].'" '.$selected.' >'.$type_data['type_title'].'</option>';
	    			break;
	    		case 'help':
	    			$return .= '&bull; '.$type_data['type_title'].' : '.$type_data['type_description']."\n";
	    			break;
	    		case 'rows':
	    			$return[$i]['type_id'] = $type_data['type_id'];
    				$return[$i]['type_description'] = $type_data['type_description'];
    				++$i;
	    			break;
	    		default:
    				$return[$type_data['type_id']] = $type_data['type_title'];    			
	    			break;
	    	}

	    	$this->get_childs_list($type_id, $type, $return, $selected_id);
    	}

    	if($type == 'html') $return .= '</select>';
    	return $return;
    }

    function get_root_types(){
    /** 
	 * Retourne la liste des types qui sont parent
     *
     * @return array
     */
    	$return = array();
    	$info = $this->so_ref_publi_type->search(array('type_active'=>'1'),false,'type_order');
    	foreach((array)$info as $data){
    		if(empty($data['type_parent'])){ 
    			$return[$data['type_id']] = $data['type_id'];
    		}
    	}

    	return $return;
    }

    function get_childs_list($type_id, $type, &$return, $selected_id){
	/**
	 * Recupere recursivement les enfants de chaque site
	 *
	 * @return array
	 */
		$childs = $this->so_ref_publi_type->search(array('type_parent' => $type_id),false,'type_order');

		foreach((array)$childs as $child){
			$label = '';
			$level = $this->get_level($child['type_id']);
			for($i=0;$i < $level;$i++){
				$label .= '-';
			}
			
			switch($type){
	    		case 'html':
	    			$selected = '';
		    		if($child['type_id'] == $selected_id){
		    			$selected = 'selected=selected';
		    		}
		    		$return .= '<option value='.$child['type_id'].' title="'.$child['type_description'].'" '.$selected.' >'.$label.' '.$child['type_title'].'</option>';
	    			break;
	    		case 'help':
	    			$return .= '&bull; '.$child['type_title'].' : '.$child['type_description']."\n";
	    			break;
	    		case 'rows':
	    			$return[$i]['type_id'] = $child['type_id'];
    				$return[$i]['type_description'] = $child['type_description'];
    				++$i;
	    			break;
	    		default:
    				$return[$child['type_id']] = $label.' '.$child['type_title'];    			
	    			break;
	    	}

			$this->get_childs_list($child['type_id'], $type, $return, $selected_id);
		}

		return $return;
	}

	function get_level($type_id){
	/**
	 * Retourne le niveau d'un site
	 *
	 * @return int
	 */
		$type = $this->so_ref_publi_type->read($type_id);
		$parent = $type['type_parent'];

		$level = 0;
		while ($parent) {
			$level++;
			$type_parent = $this->so_ref_publi_type->read($parent);
			$parent = $type_parent['type_parent'];
		}

		return $level;
	}
		
	function get_projects(){
    /**
     * Liste des projets
     *
     * @return array
     */
    	$return = array();
    	$info = $this->so_ref_project->search(array('proj_active'=>'1'),false,'proj_order');
    	foreach((array)$info as $data){
    		$return[$data['proj_id']] = $data['proj_title'];
    	}

    	return $return;
    }
	
	function get_subjects(){
    /**
     * liste des sujets
     *
     * @return array
     */
    	$return = array();
    	$infos = $this->so_ref_subject->search(array('subject_active'=>'1'),false,'subject_order');
    	
    	// Filtre les parents et enfants dans des tableaux différents.
		foreach((array)$infos as $info){
			if($info['subject_parent'] > 0){
				$childs[$info['subject_parent']][] = $info;
			}else{
				$parents[] = $info;
			}
		}
		
		// On récupère les parents puis les enfants pour ces parents
		foreach((array)$parents as $parent){
			$temp_rows[$parent['subject_id']] = $parent['subject_title'];

			foreach((array)$childs[$parent['subject_id']] as $child){
				$temp_rows[$child['subject_id']] = '-- '.$child['subject_title'];
			}
		}
		$return = $temp_rows;

    	return $return;
    }
	
		
	function get_roles($id=''){
    /**
     * Liste des roles
     *
     * @return array
     */
    	$return = array();
    	$info = $this->so_ref_roles->search(array('role_id' => $id, 'role_active'=>'1'),false,'role_order');
    	foreach((array)$info as $data){
    		$return[$data['role_id']] = $data['role_label'];
    	}

    	return $return;
    }

    function get_auth_extension(){
    /**
     * Retourne la liste des extensions autorisées
     *
     * @return array
     */
    	$return = array();
		$extensions = $this->so_extension->search(array('extension_active' => true),false);
		foreach((array)$extensions as $extension){
			$return[$extension['extension_id']] = $extension['extension_label'];
		}
		
		return $return;
    }

    function get_files($publi_id = ''){
	/**
	 * Liste des fichiers
	 *
	 * @return array
	 */
		$return = array();
		$info = $this->so_file->search(array('file_publi'=>$publi_id),false);
		foreach((array)$info as $file){
			$return[$file['file_id']] = '#'.$file['file_id'].' - '.$file['file_name'];
		}
		
		return $return;
	}

	function get_fields_values($type_id, $publi_id){
	/**
	 * Liste des champs
	 *
	 * @return array
	 */
		$publi = $this->read($publi_id);

		// Simple utilisateur OU Statut archivé => lecture seule
		$no_write = false;
		if($GLOBALS['egw_info']['user']['account_id'] != $publi['publi_responsible'] && ($GLOBALS['egw_info']['user']['SpicademicLevel'] < 20 || in_array($publi['publi_status'],explode(',',$this->obj_config['archived_pub_status'])))){
			$no_write = true;
		}

		$return = array();
		$i = 0;

		$join = 'INNER JOIN spicademic_ref_field ON spicademic_ref_field.field_id = spicademic_ref_type_field.field_id';
		$order = 'field_order';
		$type_fields = $this->so_type_field->search(array('type_id' => $type_id),false,$order,'',$wildcard,false,$op,$start,$query['col_filter'],$join);
		foreach((array)$type_fields as $type_field){
			// Info champ
			$field = $this->so_field->read($type_field['field_id']);
			$readonly = '';
			
			$return[$i]['class'] = '';
			// Suivant l'acces de l'association champ/type on assigne des valeurs
			switch ($type_field['type_field_access']) {
				case 0:
					// Obligatoire
					$return[$i]['class'] = 'inputRequired';
					break;
				case 2:
					// Lecture seule
					$readonly = 'disabled="disabled"';
					break;
				case 3:
					// Masqué
					$readonly = 'hidden="hidden"';
					break;
			}

			$return[$i]['field_label'] = $field['field_label'];
			$return[$i]['field_id'] = $field['field_id'];
			$return[$i]['field_help'] = $field['field_desc'];

			$value = $this->get_extra($publi_id,$field['field_id']);
			if(!$no_write){
				switch ($field['field_type']) {
					case 'txt':
					// Champs texte
						if($field['field_rows'] == 1){
							$return[$i]['field_value'] = '<input name="exec[details]['.$i.']['.$field['field_id'].']" value="'.$value.'" id="exec[details]['.$i.']['.$field['field_id'].']" size="'.$field['field_lenght'].'" maxlength="'.$field['field_lenght_max'].'" '.$readonly.'>';
						}else{
							$return[$i]['field_value'] = '<textarea name="exec[details]['.$i.']['.$field['field_id'].']" id="exec[details]['.$i.']['.$field['field_id'].']" rows="'.$field['field_rows'].'" '.$readonly.' cols="30">'.$value.'</textarea>';
						}
						break;
					case 'nbr':
					// champs nombre
						$return[$i]['field_value'] = '<input name="exec[details]['.$i.']['.$field['field_id'].']" value="'.$value.'" id="exec[details]['.$i.']['.$field['field_id'].']" size="5" '.$readonly.'>';
						break;
					case 'box':
					// champs boite de selection
						$options = array();
						$value = explode(',',$value);
						if($field['field_rows'] == 1){
						// Une seule option selectionnable
							$selected = '';
							// Ajout d'un "Selectionnez un(e)" si la liste est optionnelle
							if($type_field['type_field_access'] != 0)
								$options[] = '<option value="" selected >'.lang('Select one').'</option>';

							foreach((array)explode("\n",$field['field_options']) as $data){
								$temp_value = explode('=',$data);
								if(in_array($temp_value[0],$value)) $selected = 'selected';
								$options[] = '<option value="'.$temp_value[0].'" '.$selected.' >'.$temp_value[1].'</option>';
								$selected = '';
							}

							$return[$i]['field_value'] = '<select name="exec[details]['.$i.']['.$field['field_id'].']" id="exec[details]['.$i.']['.$field['field_id'].']" '.$readonly.'>
								'.implode("\n",$options).'
								</select>';
						}else{
						// plusieurs options selectionnable
							foreach((array)explode("\n",$field['field_options']) as $data){
								$temp_value = explode('=',$data);

								$checked = '';
								if(in_array($temp_value[0],$value)) $checked = 'checked="1"';

								$options[] = '<label for="exec[details]['.$i.']['.$field['field_id'].']"><input name="exec[details]['.$i.']['.$field['field_id'].'][]" value="'.$temp_value[0].'" '.$checked.' id="exec[details]['.$i.']['.$field['field_id'].']['.$temp_value[0].']" type="checkbox" '.$readonly.'>'.$temp_value[1].'</label><br>';
							}
							$height = 1.7 * $field['field_rows'];
							$return[$i]['field_value'] = '<div id="exec[details]['.$i.']['.$field['field_id'].']" style="height: '.$height.'em; width: 100%; background-color: white; overflow: auto; border: lightgray 2px inset; text-align: left;">
								'.implode("\n",$options).'
								</div>';
						}
						break;
				}
			}else{
			// Lecture seule
				switch ($field['field_type']) {
					case 'txt':
					// champs texte
						$return[$i]['field_value'] = $value;
						break;
					case 'nbr':
					// champs nombre
						$return[$i]['field_value'] = $value;
						break;
					case 'box':
					// champs boite de selection
						$value = explode(',',$value);
						foreach((array)explode("\n",$field['field_options']) as $data){
							$temp_value = explode('=',$data);
							if(in_array($temp_value[0],$value)) $options[] = $temp_value[1];
						}
						foreach($options as $option){
							$return[$i]['field_value'] .= $option.'<br />';
						}
						break;
				}
			}
			++$i;
		}

		return $return;
	}

	function get_extra($publi_id,$field_id){
	/**
	 * Recupere les valeurs des champs extra
	 *
	 * @return array
	 */
		$extra = $this->so_extra->read(array('publi_id' => $publi_id,'field_id' => $field_id));
		return $extra['extra_value'];
	}

	function get_fields(){
	/**
	 * Retourne la liste des champs
	 *
	 * @return array
	 */
		$return = array();
   		$info = $this->so_field->search(array('field_active'=>'1'),false,'field_order');
   		foreach((array)$info as $id => $data){
   			$return[$data['field_id']] = $data['field_label'];
   		}
   		return $return;
	}

	function get_contact($publi_id,$contact_role=''){
	/**
	 * Retourne la liste des contact pour la publication $publi_id
	 *
	 * @param $publi_id : Id de la publication
	 * @return array
	 */
		$return = $contacts = array();
		$i = 4;
		
		$contacts = $this->so_contact->search(array('contact_publi' => $publi_id,'contact_role' => $contact_role),false,'contact_order','',$wildcard,false,'AND',$start,$query['col_filter']);

		foreach((array)$contacts as $contact){
			$addressbook_contact = $GLOBALS['egw']->contacts->read($contact['contact_add_id']);

			$return[$i] = $contact + array(
				'link' => '<a href=\'\' onclick="window.open(\''.$GLOBALS['egw_info']['server']['webserver_url'].'/index.php?menuaction=addressbook.addressbook_ui.edit&contact_id='.$contact['contact_add_id'].'\',\'\',\'width=600,height=600,scrollbars=1\')">'.$this->get_contact_fn($contact['contact_add_id']).'</a>',
				'tel_work' => $addressbook_contact['tel_work'],
				'email' => $addressbook_contact['email'],
			);
			++$i;
		}

		return $return;
	}

	function get_max_contact_order($publi_id){
	/**
	 * Retourne l'ordre le plus grand pour les contact de la publication
	 *
	 * @param $publi_id int : id de la publication
	 * @return int
	 */
		$max = $this->so_contact->search(array('contact_publi' => $publi_id),'MAX(contact_order)','contact_order','',$wildcard,false,'AND',$start,$query['col_filter']);
		return $max[0]['MAX(contact_order)']+1;
	}

	function get_contact_fn($id){
	/**
	 * Retourne le fullname (n_fn) du contact ayant l'id $id
	 *
	 * @param $id : identifiant du contact
	 * @return string : n_fn du contact
	 */
		$contact = $GLOBALS['egw']->contacts->read($id);
		return $this->truncate_word($contact['n_family'].' '.$contact['n_given']);
	}
	
	function truncate_word($string, $limit=30, $break="-", $pad="...") { 
		// return with no change if string is shorter than $limit 
		if(strlen($string) <= $limit) return $string; 
		
		
		$string = substr($string, 0, $limit) . $pad; 

		return $string; 
	}

	function get_file_type(){
	/**
	 * Retourne la liste des types de fichier
	 *
	 * @return array
	 */
		$return = array();
   		$info = $this->so_file_type->search(array('file_type_active'=>'1'),false,'file_type_order');
   		foreach((array)$info as $id => $data){
   			$return[$data['file_type_id']] = $data['file_type_label'];
   		}
   		return $return;
	}

	function get_file_status(){
	/**
	 * Retourne la liste des statuts de fichier
	 *
	 * @return array
	 */
		$return = array();
   		$info = $this->so_file_status->search(array('status_active'=>'1'),false,'status_order');
   		foreach((array)$info as $id => $data){
   			$return[$data['status_id']] = $data['status_label'];
   		}
   		return $return;
	}

	function get_peer_review(){
	/**
	 * Retourne la liste des valeurs possible pour le champs publi_peer_review
	 *
	 * @return array
	 */
		return array(
			'0' => lang('Yes'),
			'1' => lang('No'),
			'2' => lang('Not transmitted'),
		);
	}

	function get_scope(){
	/**
	 * Retourne la liste des valeurs possible pour le champs publi_scope
	 *
	 * @return array
	 */
		return array(
			'0' => lang('Public'),
			'1' => lang('Private'),
		);
	}

	function get_authors(){
	/**
	 * Retourne une liste contenant les auteurs de toutes les publications
	 *
	 * @return array
	 */
		$return = array();
		// Récupération des contacts de toutes les publications
		$authors = $this->so_contact->search(array(),false,'contact_order');

		foreach((array)$authors as $author){
			// On récupère le nom de chacun des contacts
			$contact = $GLOBALS['egw']->contacts->read($author['contact_add_id']);
			$return[$author['contact_add_id']] = $contact['n_family'].' '.$contact['n_given'];
		}

		asort($return);

		return $return;
	}
	
	function export(){
	/**
	 * Retourne la liste des champs a exporter
	 *
	 * Voir la fonction get_rows pour le traitement special si le flag d'export est defini
	 *
	 * @return array
	 */
		$retour = array(
			'publi_id' => 'publi_id',
			'publi_type_export' => 'publi_type',
			'publi_status_export' => 'publi_status',
			'publi_project_export' => 'publi_project',
			'publi_other' => 'publi_other',
			'publi_title' => 'publi_title',
			'publi_year' => 'publi_year',
			'publi_responsible' => 'publi_responsible',
			'publi_scope' => 'publi_scope',
			'publi_peer_review' => 'publi_peer_review',
			'publi_internal_url' => 'publi_internal_url',
			'publi_external_url' => 'publi_external_url',
			'publi_doi' => 'publi_doi',
			'publi_doi_url' => 'publi_doi_url',
			'publi_pdf' => 'publi_pdf',
			'publi_desc' => 'publi_desc',
			'publi_keywords' => 'publi_keywords',
			'publi_lang' => 'publi_lang',

		);
		/* Champs non retournés
			'publi_creator' => 'publi_creator',
			'publi_created' => 'publi_created',
			'publi_modifier' => 'publi_modifier',
			'publi_modified' => 'publi_modified',
			'search' => 'search',
			'publi_abstract' => 'publi_abstract',
			'nb_authors' => 'nb_authors',
			'publi_address' => 'publi_address',
		*/
		
		return $retour;
	}

	
	function get_all_publi_status(){
    /**
     * Retourne la liste des statuts de publication
     *
     * @return array
     */
    	$return = array();
		$info = $this->so_ref_publi_status->search(array('status_active'=>'1'),false);
    	foreach((array)$info as $data){
    		$return[$data['status_id']] = $data['status_label'];
    	}
		
		return $return;
    }
}
?>