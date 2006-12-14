<?php

// require_plugin() will reset the $txp_current_plugin global
global $txp_current_plugin;
$l10n_current_plugin = $txp_current_plugin;
require_plugin('gbp_admin_library');
$txp_current_plugin = $l10n_current_plugin;

// Constants
if( !defined( 'L10N_PLUGIN_CONST' ))
	define('L10N_PLUGIN_CONST', 'plugin');
if( !defined( 'L10N_SEP' ))
	define( 'L10N_SEP' , '-' );
if( !defined( 'L10N_NAME' ))
	define( 'L10N_NAME' , 'l10n' );
if( !defined( 'L10N_PREFS_LANGUAGES' ))
	define( 'L10N_PREFS_LANGUAGES', $l10n_current_plugin.'_l10n-languages' );
if( !defined( 'L10N_ARTICLES_TABLE' ) )
	define( 'L10N_ARTICLES_TABLE' , 'l10n_articles' );
if( !defined( 'L10N_RENDITION_TABLE_PREFIX' ) )
	define( 'L10N_RENDITION_TABLE_PREFIX' , 'l10n_textpattern_' );
if( !defined( 'L10N_SNIPPET_IO_HEADER' ) )
	define( 'L10N_SNIPPET_IO_HEADER' , 'MDoibDEwbi1jbG9uZSI7czoxMjoi' );

global $event;

if( @txpinterface==='admin' && gps( 'l10nfile' ) === 'mlp.js' )
	{
	ob_start();
	print _l10n_inject_js();
	exit;
	}

function _l10n_inject_js()
	{
	function _l10n_php2js_array($name, $array)
		{
		// From PHP.net
		if (is_array($array))
			{
			$result = $name.' = new Array();'.n;
			foreach ($array as $key => $value)
				$result .= _l10n_php2js_array($name.'[\''.$key.'\']',$value,'').n;
			}
		else
			{
			$result = $name.' = \''.$array.'\';';
			}
		return $result;
		}

	$ltr = doSlash( gTxt( 'l10n-ltr' ) );
	$rtl = doSlash( gTxt( 'l10n-rtl' ) );
	$toggle_title = doSlash( gTxt('l10n-toggle') );

	$langs = LanguageHandler::get_installation_langs();
	$langs = LanguageHandler::do_fleshout_dirs( $langs );
	$langs = _l10n_php2js_array( 'langs' , $langs );

	$fn = <<<end_js
var {$langs}
	var search_box   = null;
	var search_term  = null;
	var result_div   = null;
	var result_list  = null;
	var cresult_div  = null;
	var result_num   = null;
	var csearch_box  = null;
	var csearch_lang = null;
	var str_edit_div = null;
	var sbn_lang_sel = null;
	var last_req     = "";

	var	xml_manager = false;
	if( window.XMLHttpRequest )
		{
		xml_manager = new XMLHttpRequest();
		}

function addLoadEvent(func)
	{
	var oldonload = window.onload;
	if (typeof window.onload != 'function')
		{
		window.onload = func;
		}
	else
		{
		window.onload = function()
			{
			oldonload();
			func();
			}
		}
	}

addLoadEvent( function(){l10n_js_init();} );
function l10n_js_init()
	{
	if (!document.getElementById)
		{
		return false;
		}

	search_box   = document.getElementById('l10n_search_by_name');
	result_div   = document.getElementById('l10n_div_sbn_result_list');
	result_num   = document.getElementById('l10n_result_count');
	csearch_box  = document.getElementById('l10n_search_by_content');
	cresult_div  = document.getElementById('l10n_sbc_result_list');
	csearch_lang = document.getElementById('sbc_lang_selection');
	str_edit_div = document.getElementById('l10n_div_string_edit');
	sbn_lang_sel = document.getElementById('sbn_lang_selection');

	if( search_box == null )
		return;

	addEvent( search_box , 'keyup' , l10nRefineResultsEventHandler , false );

	var search_type = getCookie( 'l10n_string_search_by' );
	if( search_type == '' || search_type == 'name' )
		{
		var selection = getCookie( 'l10n_string_search_by_subtype' );
		if( selection == null || selection == 'all' )
			{
			sbn_lang_sel.disabled = true;
			selection = '';
			}
		else
			{
			sbn_lang_sel.disabled = false;
			selection = sbn_lang_sel.value;
			}
		do_name_search( selection );
		}
	else
		{
		result_div.className="l10n_hidden";
		cresult_div.className="l10n_visible";
		do_content_search();
		}
	}

function l10nRefineResultsEventHandler(event)
	{
	l10nRefineResults();
	}

function l10nRefineResults()
	{
	var result_list  = document.getElementById('l10n_sbn_result_list');
	var target = trim( search_box.value );
	target = target.toLowerCase()
	var t_len = target.length;

	//
	// Iterate over all strings showing those that match, hiding those that don't...
	//
	var item = result_list.firstChild;
	var visible = 0;
	while( item != null )
		{
		var match_text = item.id;
		var tmp = match_text.substring(0,t_len)
		var match = (tmp == target);

		if( match )
			{
			item.className = 'l10n_visible';
			++visible;
			}
		else
			{
			item.className = 'l10n_hidden';
			}

		item = item.nextSibling;
		}
	var result_num   = document.getElementById('l10n_result_count');
	result_num.innerHTML = visible;
	setCookie( 'search_string_name_live' , target , 365 );
	}

function trim(term)
	{
	var len = term.length;
	var lenm1 = len - 1;

	while (term.substring(0,1) == ' ')
		{
		term = term.substring(1, term.length);
		}
	while (term.substring(term.length-1, term.length) == ' ')
		{
		term = term.substring(0,term.length-1);
		}
	return term;
	}

function make_xml_req(req,req_receiver)
	{
	if( !xml_manager || (req_receiver == null) )
		return false;

	if( (last_req != req) && (req != '') )
		{
		if( xml_manager && xml_manager.readyState < 4 )
			{
			xml_manager.abort();
			}
		if( window.ActiveXObject )
			{
			xml_manager = new ActiveXObject("Microsoft.XMLHTTP");
			}

		xml_manager.onreadystatechange = req_receiver;
		xml_manager.open("GET", req);
		xml_manager.send(null);
		last_req = req;
		}
	}

function do_name_search( lang )
	{
	var req = "?event=l10n&tab=snippets&step=l10n_search_for_names&l10n-sfn=" + lang;
	make_xml_req( req , ns_result_handler );
	}
function ns_result_handler()
	{
	if (xml_manager.readyState == 4)
		{
		var results = xml_manager.responseText;
		result_div.innerHTML = results;
		l10nRefineResults();
		}
	}

function do_content_search()
	{
	var search_term = encodeURI(csearch_box.value);
	var search_lang = csearch_lang.value;
	var query       = search_term + search_lang;

	if( search_term != '' )
		{
		var req = "?event=l10n&tab=snippets&step=l10_search_for_content&l10n-sfc=" + search_term + "&l10n-lang=" + search_lang;
		make_xml_req( req , cs_result_handler );
		setCookie( 'search_string_content' , search_term , 365 );
		setCookie( 'search_string_lang' , search_lang , 365 );
		}
	}

function cs_result_handler()
	{
	if (xml_manager.readyState == 4)
		{
		var results = xml_manager.responseText;
		cresult_div.innerHTML = results;
		}
	}

function do_string_edit(id)
	{
	var req = "?event=l10n&tab=snippets&XMLHTTP=true&id=" + id;
	make_xml_req( req , string_edit_handler );
	}


function string_edit_handler()
	{
	if (xml_manager.readyState == 4)
		{
		var results = xml_manager.responseText;
		str_edit_div.innerHTML = results;
        window.scrollTo(0,128);
		}
	}

function update_search( id )
	{
	var by_name = document.getElementById('l10n_div_s_by_n');
	var by_cont = document.getElementById('l10n_div_s_by_c');
	if( id == 'sbn_radio_button' )
		{
		by_name.className="l10n_visible";
		by_cont.className="l10n_hidden";
		result_div.className="l10n_visible";
		cresult_div.className="l10n_hidden";
		setCookie( 'l10n_string_search_by' , 'name' , 365 );
		var selection = getCookie( 'l10n_string_search_by_subtype' );
		if( selection == null || selection == 'all' )
			selection = '';
		else
			selection = sbn_lang_sel.value;
		do_name_search( selection );
		}
	else if ( id == 'sbc_radio_button' )
		{
		by_name.className="l10n_hidden";
		by_cont.className="l10n_visible";
		result_div.className="l10n_hidden";
		cresult_div.className="l10n_visible";
		setCookie( 'l10n_string_search_by' , 'cont' , 365 );
		do_content_search();
		}
	else if( id == 'sbn_missing_radio_button' )
		{
		sbn_lang_sel.disabled = false;
		var selection = sbn_lang_sel.value;
		setCookie( 'l10n_string_search_by_subtype' , 'missing' , 365 );
		do_name_search( selection );
		}
	else if( id == 'sbn_all_radio_button' )
		{
		sbn_lang_sel.disabled = true;
		setCookie( 'l10n_string_search_by_subtype' , 'all' , 365 );
		do_name_search( '' );
		}
	}
function on_sbn_lang_change()
	{
	var selection = sbn_lang_sel.value;
	setCookie( 'search_string_name_lang' , selection , 365 );
	do_name_search( selection );
	}

function toggleTextElements()
	{
	toggleDirection('title');
	toggleDirection('body');
	toggleDirection('excerpt');
	}

function togglePreview()
	{
	toggleDirection('article-main');
	}

function toggleDirection(id)
	{
	if (!document.getElementById)
		{
		return false;
		}

	var textarea = document.getElementById(id + '-data');
	if( textarea == null )
		textarea = document.getElementById(id);
	var toggler  = document.getElementById(id + '-toggle');

	if (textarea.style.direction == 'ltr')
		{
		textarea.style.direction = 'rtl';
		if( toggler != null )
			toggler.innerHTML = '$rtl';
		}
	else
		{
		textarea.style.direction = 'ltr';
		if( toggler != null )
			toggler.innerHTML = '$ltr';
		}
	}
function resetToggleDir( id , dir )
	{
	var e = document.getElementById(id);
	if( e == null )
		return;

	e.style.direction = dir;
	}
function on_lang_selection_change()
	{
	var selection = document.getElementById('l10n_lang_selector').value;
	var dir = langs[selection];

	resetToggleDir( 'title', dir );
	resetToggleDir( 'body', dir );
	resetToggleDir( 'excerpt', dir );
	resetToggleDir( 'article-main', dir );

	var toggler = document.getElementById('title-toggle');
	if( toggler != null )
		toggler.innerHTML = '$toggle_title';

	var toggler  = document.getElementById('article-main-toggle');
	if( toggler != null )
		toggler.innerHTML = '$toggle_title';
	}

end_js;
	return $fn;
	}


class ArticleManager
	{
	function create_table()
		{
		$sql = array();
		$sql[] = 'CREATE TABLE IF NOT EXISTS `'.PFX.L10N_ARTICLES_TABLE.'` (';
		$sql[] = '`ID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , ';
		$sql[] = '`names` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , ';
		$sql[] = '`members` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
		$sql[] = ') TYPE=MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci';
		return @safe_query( join('', $sql) );
		}
	function destroy_table()
		{
		$sql = 'drop table `'.PFX.L10N_ARTICLES_TABLE.'`';
		return @safe_query( $sql );
		}
	function clean_table_name( $name )
		{
		if( !is_string( $name ) )
			{
			$error = "clean_table_name() given a non string input.";
			trigger_error( $error , E_USER_ERROR );
			}

		#Make sure the table name has no sql opeartors...
		$result = strtr( $name , array( '-' => '_' ) );
		return $result;
		}
	function make_textpattern_name( $full_code )
		{
		if( is_string( $full_code ) )
			{
			$code = $full_code;
			}
		else
			{
			if( isset( $full_code['long'] ) )
				$code = $full_code['long'];
			elseif( isset( $full_code['short'] ) )
				$code = $full_code['short'];
			else
				{
				$error = "make_textpattern_name() given an invalid input value $full_code";
				trigger_error( $error , E_USER_ERROR );
				}
			}

		if( strlen( $code ) < 2 )
			{
			trigger_error( "$code is too short!" , E_USER_ERROR );
			}

		$result = ArticleManager::clean_table_name( L10N_RENDITION_TABLE_PREFIX . $code );

		return $result;
		}
	function _get_article_info( $id )
		{
		$info = safe_row( '*' , L10N_ARTICLES_TABLE , "`ID`='$id'" );
		if( !empty($info) )
			$info['members'] = unserialize( $info['members'] );
		return $info;
		}
	function create_article( $title , $members , $article_id=0 )
		{
		$members = serialize( $members );
		if( 0 === $article_id )
			$article = safe_insert( L10N_ARTICLES_TABLE , "`names`='$title', `members`='$members'" );
		else
			$article = safe_insert( L10N_ARTICLES_TABLE , "`names`='$title', `members`='$members', `ID`='$article_id'" );
		return $article;
		}
	function destroy_article( $article_id )
		{
		return safe_delete( L10N_ARTICLES_TABLE , "`ID`='$article_id'" );
		}
	function _update_article( $article_id , $title , $members )
		{
		$members = serialize( $members );
		$title = doSlash( $title );
		$article = safe_update( L10N_ARTICLES_TABLE , "`names`='$title', `members`='$members'" , "`ID`='$article_id'" );
		return $article;
		}
	function change_rendition_language( $article_id , $rendition_id , $rendition_lang , $target_lang )
		{
		extract( ArticleManager::_get_article_info( $article_id ) );

		if( array_key_exists( $target_lang , $members ) )
			return "Article $article_id already has a rendition for $target_lang.";

		if( !array_key_exists( $rendition_lang , $members ) )
			return "Rendition $rendition_id in $rendition_lang does not belong to article $article_id.";
		unset( $members[$rendition_lang] );

		$members[$target_lang] = $rendition_id;

		$ok = ArticleManager::_update_article( $article_id , $names , $members );
		return $ok;
		}
	function add_rendition( $article_id , $rendition_id , $rendition_lang , $check_membership = true , $insert_group = false , $name = '' )
		{
		$info = ArticleManager::_get_article_info( $article_id );
		if( empty( $info ) )
			{
			if( $insert_group )
				{
				$title = '';
				$article_id = ArticleManager::create_article( $title , array() , $article_id );
				$info = ArticleManager::_get_article_info( $article_id );
				if( empty( $info ) )
					return "Article $article_id does not exist and could not be added";
				}
			else
				return "Article $article_id does not exist";
			}

		extract( $info );

		if( array_key_exists( $rendition_lang , $members ) )
			return "A rendition in $rendition_lang is already present in article $article_id.";
		if( $check_membership and in_array( $rendition_id , $members ) )
			return "Rendition $rendition_id is already a member of article $article_id.";

		$members[$rendition_lang] = $rendition_id;
		$lang_match = ($rendition_lang === LanguageHandler::get_site_default_lang());

		if( !empty( $name ) and $lang_match and $insert_group )
			$names = $name;
		$ok = ArticleManager::_update_article( $ID , $names , $members );
		if( !$ok )
			$ok = "Could not update article $article_id.";
		return $ok;
		}
	function remove_rendition( $article_id , $rendition_id , $rendition_lang )
		{
		$g_info = ArticleManager::_get_article_info( $article_id );
		if( empty($g_info) )
			return "Article $article_id does not exist";

		extract( $g_info );

		if( $members[$rendition_lang] != $rendition_id )	# Rendition is not in this article under this language!
			{
			return "No $rendition_lang rendition in article $article_id.";
			}

		unset( $members[$rendition_lang] );

		if( !empty( $members ) )
			{
			$result = ArticleManager::_update_article( $ID , $names , $members );
			if(!$result)
				$result = "Could not update article $article_id.";
			}
		else
			{
			$result = safe_delete( L10N_ARTICLES_TABLE , "`ID`='$ID'" );
			if(!$result)
				$result = "Could not delete article $article_id.";
			}

		return $result;
		}
	function _add_mapping( $article_id , $mapping )
		{
		$info = ArticleManager::_get_article_info( $article_id );
		if( empty( $info ) or (count($mapping)!==1) )
			return false;

		$mappings = $info['members'];

		foreach( $mapping as $lang=>$id )
			{
			if( in_array( $id , $mappings ) or array_key_exists( $lang, $mappings ) )
				return false;
			}

		$mappings[$lang] = $id;

		ArticleManager::_update_article( $article_id , $info['names'] , $mappings );
		return true;
		}
	function create_article_and_add( $rendition )
		{
		$result = false;
		$name = doSlash($rendition['Title']);
		$lang = (@$rendition['Lang'] !== '-') ? $rendition['Lang'] : LanguageHandler::get_site_default_lang();
		$id = @$GLOBALS['ID'];
		if( !isset( $id ) or empty( $id ) )
			$id = $rendition['ID'];
		$mapping =  array( $lang=>strval($id) );

		if( isset( $rendition['Group'] ) and !empty($rendition['Group']) )
			{
			$article_id = $rendition['Group'];
			ArticleManager::_add_mapping( $article_id , $mapping );
			}
		else
			{
			$article_id = ArticleManager::create_article( $name , $mapping );
			}

		if( $article_id !== false and $article_id !== true )
			{
			//	echo br, "Added article '$name'[$article_id], updating rendition $id ... `Lang` = '$lang',`Group` = '$article_id'";
			#	Update the rendition to point to its article and have a translation accounted to it...
			$result = @safe_update( 'textpattern', "`Lang` = '$lang',`Group` = '$article_id'" , "ID='$id'" );
			}
		return $result;
		}
	function get_article_members( $article_id , $exclude_lang , $status='4' )
		{
		#
		#	Returns an array of the lang->rendition mappings for all members of the
		# given article...
		#
		$result = array();
		$where = "`Group`='$article_id' and `Status` >= '$status' and `Lang`<>'$exclude_lang'";
		$rows = safe_rows_start( 'ID,Title,Lang' , 'l10n_master_textpattern' , $where );
		if( count( $rows ) )
			{
			while( $row = nextRow($rows) )
				{
				$lang = $row['Lang'];
				$result[$lang] = array( 'id' => $row['ID'] , 'title' => escape_title($row['Title']) );
				}
			}
		return $result;
		}
	function get_alternate_mappings( $rendition_id , $exclude_lang , $use_master=false )
		{
		if( $use_master )
			$info = safe_row( '`Group`' , 'l10n_master_textpattern' , "`ID`='$rendition_id'" );
		else
			$info = safe_row( '`Group`' , 'textpattern' , "`ID`='$rendition_id'" );
		if( empty($info) )
			{
			return $info;
			}

		$article_id = $info['Group'];
		$alternatives = ArticleManager::get_article_members( $article_id , $exclude_lang );
		return $alternatives;
		}
	function get_remaining_langs( $article_id )
		{
		#
		#	Returns an array of the site languages that do not have existing renditions in this article...
		#
		$langs 	= LanguageHandler::get_site_langs();
		$info 	= ArticleManager::_get_article_info( $article_id );
		$to_do	= array();

		if( !empty( $info ) and !empty($langs) )
			{
			$mapped_langs = $info['members'];
			foreach( $langs as $lang )
				{
				if( !array_key_exists($lang , $mapped_langs) )
					$to_do[$lang] = LanguageHandler::get_native_name_of_lang($lang);
				}
			}

		return $to_do;
		}
	function move_to_article( $rendition )
		{
		global $l10n_article_message;

		#	Get the new entries...
		$new_article	= $rendition['Group'];
		$new_lang		= (@$rendition['Lang']) ? $rendition['Lang'] : LanguageHandler::get_site_default_lang();
		$rendition_id	= $rendition['ID'];

		#	Read the existing rendition entries...
		$info = safe_row( '*' , 'textpattern' , "`ID`='$rendition_id'" );
		if( $info === false )
			{
			$l10n_article_message = "Error: failed to read rendition $rendition_id data.";
			return false;
			}

		$current_article	= $info['Group'];
		$current_lang		= $info['Lang'];

		if( ($new_article == $current_article) and ($new_lang == $current_lang) )
			{
			return true;
			}

		#	Add rendition to new article...
		$result = ArticleManager::add_rendition( $new_article , $rendition_id , $new_lang , false );
		if( $result !== true )
			{
			$l10n_article_message = 'Error: ' . $result;
			return false;
			}

		#	Remove article from existing group...
		$result = ArticleManager::remove_rendition( $current_article , $rendition_id , $current_lang );
		if( $result !== true )
			{
			#	Attempt to remove from the article we just added to...
			ArticleManager::remove_rendition( $new_article , $rendition_id , $new_lang );
			$l10n_article_message = 'Error: ' . $result;
			return false;
			}

		# 	Update the entries in the article...
		$ok = safe_update( 'textpattern', "`Group`='$new_article' , `Lang`='$new_lang'" , "`ID`='$rendition_id'" );
		if( $ok )
			$l10n_article_message = "Language: {$current_lang}->{$new_lang}, article:{$current_article}->{$new_article}";
		else
			$l10n_article_message = 'Warning: Failed to record changes to renditions table';

		return true;
		}
	function get_articles( $criteria , $sort_sql='ID' , $offset='0' , $limit='' )
		{
		if( $offset == '0' and $limit == '' )
			$rs = safe_rows_start('*', L10N_ARTICLES_TABLE, "$criteria order by $sort_sql" );
		else
			$rs = safe_rows_start('*', L10N_ARTICLES_TABLE, "$criteria order by $sort_sql limit $offset, $limit" );
		return $rs;
		}
	function check_groups()
		{
		#
		#	index => array( add|delete|skip , rendition-id , article-id , description );
		#
		$result = array();

		$members_count = 0;
		$langs = LanguageHandler::get_site_langs();

		#
		#	Examing the groups table...
		#
		$articles = ArticleManager::get_articles( '1=1' );
		if( count( $articles ) )
			{
			while( $article = nextRow($articles) )
				{
				#
				#	Get the article's members...
				#
				extract( $article );
				$members = unserialize( $members );
				$m_count = count( $members );
				$members_count += $m_count;

				#
				#	Find the members from the textpattern table too...
				#
				$renditions = safe_column( 'ID', 'textpattern' , "`Group`='$ID'" );
				$t_count = count( $renditions );

				#
				#	Take the diffs...
				#
				$diff_members_renditions = array_diff( $members , $renditions );
				$diff_renditions_members = array_diff( $renditions , $members );
				$count_m_r = count($diff_members_renditions);
				$count_r_m = count($diff_renditions_members);

				if( $count_m_r > 0 )
					{
					#
					#	Need to delete extra renditions from the articles table...
					#
					foreach( $diff_members_renditions as $lang=>$rendition )
						{
						unset( $members[$lang] );
						$result[] = array( 'delete' , $rendition , $ID , gTxt('l10n-del_phantom', array( '$rendition'=>$rendition, '$ID'=>$ID) ) );
						}
					ArticleManager::_update_article( $ID , $names , $members );
					}
				if( $count_r_m > 0 )
					{
					#
					#	Need to add missing renditions to the articles table...
					#
					foreach( $diff_renditions_members as $rendition )
						{
						$details = safe_row( '*' , 'textpattern' , "`ID`='$rendition'" );
						if( !empty( $details ) )
							$lang = $details['Lang'];
						else
							continue;

						#
						#	Check it's a valid site language...
						#
						if( !in_array( $lang , $langs ) )
							{
							$result[] = array( 'skip' , $rendition , $ID , gTxt('l10n-skip_rendition' , array('$rendition'=>$rendition,'$ID'=>$ID,'$lang'=>$lang)) );
							continue;
							}
						$members[$lang] = $rendition;
						ArticleManager::_update_article( $ID , $names , $members );
						$result[] = array( 'add' , $rendition , $ID , gTxt('l10n-add_missing_rend',array('$rendition'=>$rendition, '$ID'=>$ID)) );
						}
					}
				}
			}
		return $result;
		}
	function get_total()
		{
		return safe_count(L10N_ARTICLES_TABLE, "1" );
		}

	}

class LocalisationView extends GBPPlugin
	{
	var $gp = array();
	var $preferences = array(
		'l10n-languages' => array('value' => array(), 'type' => 'gbp_array_text'),
		'l10n-show_legends' => array( 'value' => 1, 'type' => 'yesnoradio' ),
		'l10n-send_notifications'	=>	array( 'value' => 1, 'type' => 'yesnoradio' ),
		'l10n-send_notice_to_self'	=>	array( 'value' => 0, 'type' => 'yesnoradio' ),
		'l10n-send_notice_on_changeauthor' => array( 'value' => 0, 'type' => 'yesnoradio' ),
		'l10n-allow_writetab_changes' => array( 'value' => 0, 'type' => 'yesnoradio' ),
		'l10n-inline_editing' => array('value' => 1, 'type' => 'yesnoradio'),
		);
	var $strings_lang = 'en-gb';
	var $strings_prefix = L10N_NAME;
	var $insert_in_debug_mode = false;
	var $perm_strings = array( # These strings are always needed.
		'l10n-localisation'			=> 'MLP',
		'l10n-toggle'				=> 'Toggle',
		'l10n-done'					=> 'Done',
		'l10n-failed'				=> 'Failed',
		'l10n-wizard'				=> 'Wizards',
		'l10n-snippets_tab'			=> 'Snippets',
		);
	var $strings = array(
		'l10n-add_tags'				=> 'Add localisation tags to this window?' ,
		'l10n-add_missing_rend'		=> 'Added missing rendition($rendition) to article $ID',
		'l10n-all_languages'		=> 'Any language',
		'l10n-allow_writetab_changes' => "Power users can change a rendition's language or article?",
		'l10n-article_table_ok'		=> 'Article table ok.',
		'l10n-by'					=> 'by',
		'l10n-by_content'			=> 'by content',
		'l10n-by_name'				=> 'by name',
		'l10n-clone'				=> 'Clone',
		'l10n-clone_and_translate'	=> 'Clone "{article}" for translation',
		'l10n-cannot_delete_all'	=> 'Must have 1+ rendition(s).',
		'l10n-del_phantom' 			=> 'Deleted phantom rendition($rendition) from article $ID',
		'l10n-delete_plugin'		=> 'This will remove ALL strings for this plugin.',
		'l10n-delete_whole_lang'	=> 'Delete all ($var2) strings in $var1?',
		'l10n-edit_resource'		=> 'Edit $type: $owner ',
		'l10n-email_xfer_subject'	=> '[{sitename}] Notice: {count} rendition{s} transferred to you.',
		'l10n-email_body_other'		=> "{txp_username} has transferred the following rendition{s} to you...\r\n\r\n",
		'l10n-email_body_self'		=> "You transferred the following rendition{s} to yourself...\r\n\r\n",
		'l10n-email_end'			=> "Please don't forget to clear the url-only-title when you translate the rendition{s}!\r\n\r\nThank you,\r\n--\r\n{txp_username}.",
		'l10n-empty'				=> 'empty',
		'l10n-explain_extra_lang'	=> '<p>* These languages are not specified in the public site preferences but may be in use for the admin interface.</p><p>If they are not needed for your site you can delete them.</p>',
		'l10n-explain_no_tags'		=> '<p>* = These forms/pages have snippets but do not have the <em>localise tags</em> needed to display the snippets.</p><p>You can fix this by inserting the needed tags into these pages/forms.</p>',
		'l10n-explain_specials'		=> 'A list of snippets that appear in the TxP system but not on any page or form.',
		'l10n-export'				=> 'Export',
		'l10n-export_title'			=> '<h2>Export {type} Strings</h2><br/><p>Select languages you wish to include and then click the button.</p>',
		'l10n-import'				=> 'Import',
		'l10n-import_count'			=> 'Imported {count} {type} strings.',
		'l10n-import_title'			=> '<h2>Import {type} Strings</h2><br/><p>Paste exported file into the box below and click the button.</p>',
		'l10n-import_warning'		=> 'This will insert or OVERWRITE all of the displayed strings.',
		'l10n-inline_editing'		=> 'Inline editing of pages and forms ',
		'l10n-into'					=> 'into',
		'l10n-inout'				=> 'Export/Import',
		'l10n-invalid_import_file'	=> '<p><strong>This is not a valid string file.</strong></p>',
		'l10n-lang_remove_warning'	=> 'This will remove ALL plugin strings in $var1. ',
		'l10n-lang_remove_warning2'=> 'Delete ALL strings in $var1. You will need to re-install all strings for $var1 if you want to use it again, even in the admin interface. ',
		'l10n-language_not_supported' => 'Skipping: Language not supported.',
		'l10n-languages' 			=> 'Languages ',
		'l10n-legend_warning'		=> 'Warning/Error',
		'l10n-legend_fully_visible'	=> 'Visible in all languages',
		'l10n-localised'			=> 'Localised',
		'l10n-ltr'					=> 'LTR&nbsp;>',
		'l10n-matches'				=> ' strings match.',
		'l10n-missing'				=> ' missing.',
		'l10n-missing_rendition'	=> 'Article: {id} missing a rendition.',
		'l10n-no_langs_selected' 	=> 'No languages selected for clone.',
		'l10n-no_plugin_heading'	=> 'Notice&#8230;',
		//'l10n-only'					=> 'only',
		'l10n-pageform-markup'		=> '<p><strong>Bold</strong> = localised.<br/>(Not all items will need localising.)<br/>[#] = snippet count.</p>',
		'l10n-plugin'				=> 'Plugin',
		'l10n-registered_plugins'	=> 'Registered Plugins.' ,
		'l10n-remove_plugin'		=> "This plugin is no longer installed or may be running from the cache directory.<br/><br/>If this plugin's strings are no longer needed you can remove them.",
		'l10n-renditions'			=> 'Renditions',
		'l10n-rendition_delete_ok'	=> 'Rendition {rendition} deleted.',
		'l10n-renditions_for'		=> 'Renditions for ',
		'l10n-rtl'					=> '<&nbsp;RTL',
		'l10n-sbn_rubrik'			=> 'Type your search phrase above.',
		'l10n-sbn_title'			=> 'Type your search term in here.',
		'l10n-search_for_strings'	=> 'Search For Strings',
		'l10n-send_notifications'	=> 'Email user when you assign them a rendition?',
		'l10n-send_notice_to_self'	=> '&#8230; even when assigning to yourself?',
		'l10n-send_notice_on_changeauthor' => '&#8230; even when author changed in content > renditions list?',
		'l10n-show_langs'			=> 'Show languages&#8230;',
		'l10n-show_legends' 		=> 'Show article table legend?',
		'l10n-skip_rendition'		=> 'Skipped rendition($rendition) while processing article($ID) as it uses unsupported language $lang',
		'l10n-snippet'				=> 'Snippet',
		'l10n-snippets'				=> ' snippets.',
		'l10n-special'				=> 'Special',
		'l10n-specials'				=> 'Specials',
		'l10n-statistics'			=> 'Show Statistics ',
		'l10n-strings'				=> ' strings',
		'l10n-strings_match'		=> ' strings match&#8230;',
		'l10n-summary'				=> 'Statistics.',
		'l10n-table_rebuilt'		=> 'Article table corrected, try again.',
		'l10n-textbox_title'		=> 'Type in the text here.',
		'l10n-total'				=> 'Total',
		'l10n-unlocalised'			=> 'Unlocalised',
		'l10n-view_site'			=> 'View localised site',
		'l10n-warn_section_mismatch' => 'Section mismatch',
		'l10n-warn_lang_mismatch'	=> 'Language mismatch',
		'l10n-xlate_to'				=> 'Translating into: ',
		);
	var $permissions = '1,2,3,6';

	// Constructor
	function LocalisationView( $title_alias , $event , $parent_tab = 'extensions' )
		{
		global $textarray , $production_status , $prefs;

		if( @txpinterface === 'admin' )
			{
			#	Register callbacks to get admin-side plugins' strings registered.
			register_callback(array(&$this, '_initiate_callbacks'), 'l10n' , '' , 1 );

			# First run, setup the languages array to the currently installed admin side languages...
			$langs = LanguageHandler::get_site_langs( false );
			if( NULL === $langs )
				{
				# Make sure the currently selected admin-side language is the site default...
				$languages = array(LANG);

				# Get the remaining admin-side langs...
				$installed_langs = safe_column('lang','txp_lang',"lang != '".LANG."' GROUP BY 'lang'");
				$languages = array_merge( $languages, array_values($installed_langs) );

				# Finally set the preference
				$this->set_preference('l10n-languages', $languages);
				}

			$installed = $this->installed();
			$installed = !empty( $installed );

			# Merge the default language strings into the textarray so that non-English
			# users at least see an English message in the plugin.
			if( $prefs['language'] !== $this->strings_lang )
				{
				$textarray = array_merge( $this->strings , $textarray );
				}

			#	These strings are always needed (for example, by setup/cleanup wizards)...
			$textarray = array_merge( $this->perm_strings , $textarray );

			#	To ease development, allow new strings to be inserted...
			if( $installed and $this->insert_in_debug_mode and ('debug' === @$production_status) )
				{
				$this->strings = array_merge( $this->strings , $this->perm_strings );
				$ok = StringHandler::remove_strings_by_name( $this->strings , 'admin' , 'l10n' , $this->strings_lang );
				$ok = StringHandler::insert_strings( $this->strings_prefix , $this->strings , $this->strings_lang , 'admin' , 'l10n' , true );
				StringHandler::load_strings_into_textarray( LANG );
				}
			}

		# Be sure to call the parent constructor *after* the strings it needs are added and loaded!
		GBPPlugin::GBPPlugin( gTxt($title_alias) , $event , $parent_tab );
		}

	function _insert_css()
		{
		return n . '<link href="lib/mlp.css" rel="Stylesheet" type="text/css" />' . n;
		}
	function preload()
		{
		if( has_privs('plugin') )
			new LocalisationStringView( gTxt('plugins'), 'plugin', $this );
		if( has_privs('page') or has_privs('form') )
			{
			$snippet_tab = new SnippetTabView( gTxt('l10n-snippets_tab') , 'snippets' , $this );
			new LocalisationStringView( gTxt('search') , 'search' , $snippet_tab );
			new LocalisationStringView( gTxt('l10n-specials') , 'special' , $snippet_tab );
			if (has_privs('page'))
				new LocalisationStringView( gTxt('pages') , 'page' , $snippet_tab , true );
			if (has_privs('form'))
				new LocalisationStringView( gTxt('forms') , 'form' , $snippet_tab );
			new SnippetInOutView( gTxt( 'l10n-inout' ) , 'inout' , $snippet_tab );
			}
		if( has_privs('article.edit') )
			new LocalisationArticleTabView( gTxt('articles'), 'article', $this, true );

		new GBPPreferenceTabView($this);
		new LocalisationWizardView($this, NULL , gTxt('l10n-wizard') );
		}

	function prefs_save_cb( $event='' , $step='' )
		{
		#
		#	Update the set of translation tables based on any changes made to the site
		# languages...
		#
		$langs = LanguageHandler::get_site_langs();
		$tables = getThings( 'show tables like \''.PFX.L10N_RENDITION_TABLE_PREFIX.'%\'' );

		#
		#	Expand language names to match translation table name format...
		#
		$names = array();
		if( count( $langs ) )
			foreach( $langs as $name )
				{
				$name = PFX.L10N_RENDITION_TABLE_PREFIX.$name;
				$names[] = ArticleManager::clean_table_name($name);
				}

		#
		#	Perform the diffs and detect additions/deletions needed...
		#
		$diff_names_tables = array_diff( $names  , $tables );
		$diff_tables_names = array_diff( $tables , $names );
		$add_count = count($diff_names_tables);
		$del_count = count($diff_tables_names);

		if( $add_count )
			{
			foreach( $diff_names_tables as $full_name )
				{
				#
				#	Get the language code...
				#
				$lang = str_replace( PFX.L10N_RENDITION_TABLE_PREFIX , '' , $full_name );
				$lang = strtr( $lang , array( '_'=>'-' ) );
				if( !LanguageHandler::is_valid_code( $lang ) )
					continue;

				#
				#	Add language tables as needed and populate them as far as possible...
				#
				$indexes = "(PRIMARY KEY  (`ID`), KEY `categories_idx` (`Category1`(10),`Category2`(10)), KEY `Posted` (`Posted`), FULLTEXT KEY `searching` (`Title`,`Body`))";
				$sql = "create table `$full_name` $indexes select * from `".PFX."textpattern` where Lang='$lang'";
				$ok = @safe_query( $sql );

				#
				#	Add fields for this language...
				#
				l10n_mappings_walker( array( &$this , 'add_field' ) , $lang );
				}
			}

		if( $del_count )
			{
			foreach( $diff_tables_names as $full_name )
				{
				#
				#	Drop language tables that are no longer needed...
				#
				$sql = 'drop table `'.$full_name.'`';
				$ok = @safe_query( $sql );

				#
				#	Get the language code...
				#
				$lang = str_replace( PFX.L10N_RENDITION_TABLE_PREFIX , '' , $full_name );
				$lang = strtr( $lang , array( '_'=>'-' ) );
				if( !LanguageHandler::is_valid_code( $lang ) )
					continue;

				#
				#	Remove fields for this language...
				#
				l10n_mappings_walker( array( &$this , 'drop_field' ) , $lang );
				}
			}

		#
		#	Process the new default language ... copy fields as needed...
		#
		l10n_mappings_walker( array( &$this , 'copy_defaults' ) , $langs[0] );
		}
	function add_field( $table , $field , $attributes , $language )
		{
		$f = "$language-$field";
		$exists = getThing( "SHOW COLUMNS FROM $table LIKE '$f'" );
		if( !$exists )
			{
			$sql = "ADD `$f` ".$attributes['sql'];
			$ok = @safe_alter( $table , $sql );
			}
		}
	function drop_field( $table , $field , $attributes , $language )
		{
		$f = "$language-$field";
		$sql = "DROP `$f`";
		$ok = @safe_alter( $table , $sql );
		}
	function copy_defaults( $table , $field , $attributes , $language )
		{
		$f = "$language-$field";

		#
		#	If we make an existing lang the default, overwrite the master field
		# with any data from the (now default) field...
		#
		$sql = "UPDATE $table SET `$field`=`$f` WHERE `$f` <> ''";
		$ok = @safe_query( $sql );

		#
		#	Copy the master fields over to the new defualt field
		#
		$sql = "UPDATE $table SET `$f`=`$field` WHERE `$field` <> ''";
		$ok = @safe_query( $sql );
		}

	function installed( $recheck=false )
		{
		static $result;
		if (!isset($result) || $recheck)
			$result = LocalisationWizardView::installed();
		return $result;
		}

	function _process_string_callbacks( $event , $step , $pre , $func )
		{
		$key = '';
		if( !is_callable($func , false , $key) )
			return "Cannot call function '$key'.";

		$r = call_user_func($func, $event, $step);
		if( !is_array( $r ) )
			return "Call of '$key' returned a non-array value.";

		extract( $r );

		$result = "Skipped insertion of strings for '$key'.";
		if( $owner and $prefix and $strings and $lang and $event and (count($strings)) )
			{
			//echo br , "In _process_string_callbacks( $event , $step , $pre , $func ), inserting strings...";
			if( StringHandler::insert_strings( $prefix , $strings , $lang , $event , $owner ) )
				$result = true;
			}

		return $result;
		}

	function _initiate_callbacks( $event , $step='' , $pre=0 )
		{
		$results = array();

		$tab = gps( 'tab' );
		$plugin = gps( 'plugin' );
		if( $tab === 'plugin' and empty($plugin) )
			{
			#	Force the loading of public side plugins on a visit to the MLP>Plugins, in case they do register strings...
			//echo "Initiating callbacks and loading active public plugins ... ";
			load_plugins( 0 );

			#	Initiates our string enumeration event...
			$results = $this->_do_callback( "l10n.enumerate_strings", '', 0, array(&$this , '_process_string_callbacks') );
			}

		return $results;
		}

	function _do_callback( $event, $step='', $pre=0, $func=NULL )
		{
		#	Graeme, move this to base class??
		global $plugin_callback;

		#	Make sure we use a copy of the array to avoid messing with it's internal pointer.
		if( !is_array($plugin_callback) )
			return;

		$results = array();

		$cb_copies = $plugin_callback;
		reset( $cb_copies );
		foreach ($cb_copies as $c)
			{
			if( $c['event'] == $event and (empty($c['step']) or $c['step'] == $step) and $c['pre'] == $pre)
				{
				$key = '';
				if( !is_callable($c['function'] , false , $key ) )
					continue;
				# If a processing routinue has been specified then use it otherwise use the callback directly.
				if( $func and is_callable($func) )
					$results[ $key ] = call_user_func( $func , $event , $step , $pre , $c['function'] );
				else
					$results[ $key ] = call_user_func($c['function'], $event, $step);
				}
			}
		return $results;
		}

	function serve_file( $data , $title )
		{
		ob_clean();
		$size = strlen( $data );
		header('Content-Description: File Download');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . $size);
		header('Content-Disposition: attachment; filename="' . $title . '"');
		@ini_set("zlib.output_compression", "Off");
		@set_time_limit(0);
		@ignore_user_abort(true);
		$d = chunk_split( $data , 8*1024 , n );
		$data = explode( n , $d );
		foreach( $data as $chunk )
			{
			echo $chunk;
			ob_flush();
			flush();
			}
		exit(0);
		}

	function main()
		{
		require_privs($this->event);

		$out[] = '<div class="l10n_main_tab">';
		$out[] = $this->_insert_css();
		if( $this->installed(1) )
			{
			# Only render the common area at the head of the tabs if the table is installed ok.
			foreach( $this->pref('l10n-languages') as $key )
				{
				$safe_key = trim( $key );	# make sure we trim any spaces out -- they mess up the gTxt call.
				$languages['value'][$safe_key] = gTxt($safe_key);
				}
			}
		$out[] = '</div>';

		echo join('', $out);
		}

	function end()
		{
		$step = gps('step');
		if( $step )
			{
			switch( $step )
				{
				case 'prefs_save':
					$this->prefs_save_cb();
					#	Force a redirect to ourself to refresh the view with any tab changes as needed.
					$this->redirect( array() );
				break;
				}
			}
		}

	}

function l10n_redirect_textpattern($table)
	{
	if( @txpinterface !== 'public' )
		return $table;

	$installed = l10n_installed();
	if( 'textpattern' === $table && $installed )
		{
		//echo "_redirect_textpattern($table)";
		global $l10n_language;

		$language_set 	= isset( $l10n_language );
		$language_ok	= true;
		if( $language_set and $language_ok )
			{
			$table = ArticleManager::make_textpattern_name( $l10n_language );
			}
		//echo " ... returning($table).",br;
		}
	elseif ( 'l10n_master_textpattern' === $table && $installed )
		{
		$table = 'textpattern';
		//echo " ... returning($table).",br;
		}
	return $table;
	}
function l10n_remap_fields( $thing , $table , $get_mappings=false )
	{
	static $interfaces = array( 'public' , 'admin' );
	static $mappings = array	(
		'txp_category'	=> array( 'title' 		=> array(
														'sql' 			=> "varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''" ,
														'e' 			=> 'category',
														'paint_steps'	=> array( 'cat_article_edit', 'cat_link_edit', 'cat_image_edit', 'cat_file_edit' ),
														'paint' 		=> 'l10n_category_paint',
														'save_steps'	=> array( 'cat_article_create', 'cat_article_save', 'cat_link_create', 'cat_link_save', 'cat_image_create', 'cat_image_save', 'cat_file_create', 'cat_file_save', ),
														'save'			=> 'l10n_category_save',
														),
								),
		'txp_file' 		=> array( 'description'	=> array(
														'sql'			=> "text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL" ,
														'e' 			=> 'file',
														'paint_steps'	=> array( 'file_edit' ),
														'paint' 		=> 'l10n_file_paint',
														'save_steps'	=> array( 'file_save' ),
														'save'			=> 'l10n_file_save',
														),
								),
		'txp_image'		=> array( 'alt'			=> array(
														'sql'			=> "varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",
														'e' 			=> 'image',
														'paint_steps'	=> array( 'image_edit' ),
														'paint' 		=> 'l10n_image_paint',
														'save_steps'	=> array( 'image_save' ),
														'save'			=> 'l10n_image_save',
														),
								  'caption' 	=> array(
								  						'sql'			=> "text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
														'e' 			=> '',
														'paint_steps'	=> '',
														'paint' 		=> '',
														'save_steps'	=> '',
														'save'			=> '',
														),
								),
		'txp_link' 		=> array( 'description'	=> array(
														'sql'			=> "text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
														'e' 			=> 'link',
														'save_steps'	=> array( 'link_post', 'link_save' ),
														'save'			=> 'l10n_link_save',
														'paint_steps'	=> '',
														'paint' 		=> 'l10n_link_paint',
														),
								),
		'txp_section'	=> array( 'title' 		=> array(
														'sql'			=> "varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",
														'e' 			=> 'section',
														'paint_steps'	=> array( '' ),
														'paint' 		=> 'l10n_section_paint',
														'save_steps'	=> array( 'section_save', 'section_create' ),
														'save'			=> 'l10n_section_save',
														),
								),
		);

	if( $get_mappings )
		return $mappings;

	if( !in_array( @txpinterface , $interfaces ) )
		return $thing;

	if( !isset( $mappings[$table] ) )
		return $thing;

	if( @txpinterface === 'admin' )
		$lang = LanguageHandler::get_site_default_lang();
	else
		{
		global $l10n_language;
		if( isset( $l10n_language['long'] ) )
			$lang = $l10n_language['long'];
		else
			$lang = LANG;
		}

	foreach( $mappings[$table] as $field => $sql )
		{
		$r = '`'.$lang."-$field` as `$field`";

		#
		#	Replace specific matches...
		#
		$thing = str_replace( $field , $r , $thing );

		#
		#	Don't forget to override any wildcard search with specific mappings,
		# but not in count ops...
		#
		if( false === stripos( $thing, '(*)' ) )
			$thing = str_replace( '*' , '*,'.$r , $thing );
		}


	return $thing;
	}
function l10n_mappings_walker( $fn , $atts='' )
	{
	if( !is_callable( $fn ) )
		return false;

	global $l10n_mappings;
	if( !is_array( $l10n_mappings ) )
		$l10n_mappings = l10n_remap_fields( '' , '' , true );

	foreach( $l10n_mappings as $table=>$fields )
		{
		$table = safe_pfx( $table );
		foreach( $fields as $field=>$attributes )
			{
			call_user_func( $fn , $table , $field , $attributes , $atts );
			}
		}

	return true;
	}

class SnippetTabView extends GBPAdminTabView
	{
	var $tabs = array();
	var $active_tab = 0;
	var $use_tabs = false;

	function get_canvas_style()
		{
		if( $this->is_active )
			{
			return ' style="padding: 0;"';
			}
		return false;
		}
	function &add_tab($tab, $is_default = NULL)
		{
		// Check to see if the tab is active
		$gps_tab = gps(gbp_tab);
		$sub_tab = gps('subtab');

		if (($is_default && !$gps_tab) || ($gps_tab == $tab->event && $sub_tab == $tab->sub_tab) )
			$this->active_tab = count($this->tabs);

		// Store the tab
		$this->tabs[] = $tab;

		// We've got a tab, lets assume we want to use it
		$this->use_tabs = true;

		return $this;
		}
	function preload()
		{
		// Let the active_tab know it's active and call it's preload()
		$tab = &$this->tabs[$this->active_tab];
		$tab->is_active = 1;
		$tab->preload();
		}

	function main()
		{
		$this->render_tabs();
		$this->render_tab_main();
		}
	function render_tab_main()
		{
		// Call main() for the active_tab
		$tab = &$this->tabs[$this->active_tab];
		$tab->main();
		}
	function render_tabs()
		{
		// This table, which contains the tags, will have to be changed if any improvements
		// happen to the admin interface
		$out[] = '<table cellpadding="0" cellspacing="0" width="100%" style="margin-top:-2em;margin-bottom:2em;">';
		$out[] = '<tr><td align="center" class="tabs">';
		$out[] = '<table cellpadding="0" cellspacing="0" align="center"><tr>';

		// Force the wizard to be the only tab if the plugin isn't installed
		foreach (array_keys($this->tabs) as $key)
			{
			$tab = &$this->tabs[$key];
			$out[] = $tab->render_tab();
			}

		$out[] = '</tr></table>';
		$out[] = '</td></tr>';
		$out[] = '</table><div class="l10n_subtab">';

		echo join('', $out);
		}
	}
class GBPAdminSubTabView extends GBPAdminTabView
	{
	var $sub_tab = '';
	function GBPAdminSubTabView( $title, $event, &$parent, $is_default = NULL , $subtab = '' )
		{
		if( !empty($subtab) )
			$this->sub_tab = $subtab;
		GBPAdminTabView::GBPAdminTabView( $title , $event , $parent , $is_default );
		}

	function render_tab()
		{
		// Grab the url to this tab
		$url = $this->url(array(gbp_tab => $this->event), true);

		// Will need updating if any improvements happen to the admin interface
		$out[] = '<td class="' . ($this->is_active ? 'tabup' : 'tabdown2');
		$out[] = '" onclick="window.location.href=\'' .$url. '\'">';
		$out[] = '<a href="' .$url. '" class="plain">' .$this->title. '</a></td>';

		return join('', $out);
		}
	function url( $vars, $gp=false )
		{
		$vars = array_merge( $vars , array('subtab'=>$this->sub_tab) );
		return $this->parent->url( $vars , $gp );
		}
	}
class LocalisationStringView extends GBPAdminTabView
	{
	/*
	Implements a three-pane view for the categorisation, selection and editing of string based
	data from the txp_lang table.
	*/

	var $sub_tab = '';
	function render_tab()
		{
		// Grab the url to this tab
		$url = $this->url(array(gbp_tab => $this->event), true);

		// Will need updating if any improvements happen to the admin interface
		$out[] = '<td class="' . ($this->is_active ? 'tabup' : 'tabdown2');
		$out[] = '" onclick="window.location.href=\'' .$url. '\'">';
		$out[] = '<a href="' .$url. '" class="plain">' .$this->title. '</a></td>';

		return join('', $out);
		}
	function url( $vars, $gp=false )
		{
		$vars = array_merge( $vars , array('subtab'=>$this->sub_tab) );
		return $this->parent->url( $vars , $gp );
		}

	function LocalisationStringView($title, $event, &$parent, $is_default = NULL)
		{
		if( $event !== 'plugin' )
			{
			$this->sub_tab = $event;
			GBPAdminTabView::GBPAdminTabView( $title, 'snippets', $parent, $is_default );
			}
		else
			GBPAdminTabView::GBPAdminTabView( $title, $event, $parent, $is_default );
		}


	function xml()
		{
		$xml	= gps('XMLHTTP');
		$xml	= !empty($xml);
		return $xml;
		}

	function preload()
		{
		$step 	= gps('step');

		if( $step )
			{
			switch( $step )
				{
				# Called to save the stringset the user has been editing.
				case 'l10n_save_strings' :
					$this->save_strings();
					break;

				# Called if the user chooses to delete the string set for a removed plugin.
				case 'l10n_remove_stringset' :
					$this->remove_strings();
					break;

				# Called if the user chooses to remove a specific languages' strings.
				# eg if they entered some french translations but later drop french from the site.
				case 'l10n_remove_languageset' :
					$this->remove_strings();
					break;

				case 'l10n_save_pageform':
					$this->save_pageform();
					break;

				case 'l10n_localise_pageform':
					$this->localise_pageform();
					break;

				case 'l10n_export_languageset':
					$this->export_languageset();
					break;

				case 'l10n_import_languageset':
					$this->import_languageset();
					break;

				case 'l10n_remove_language':
					$this->remove_language();
					break;

				case 'l10_search_for_content':
					$this->search_for_content();
					break;
				case 'l10n_search_for_names':
					$this->search_for_names();
					break;
				}
			}
		}

	function main()
		{
		$id = gps(gbp_id);
		$step = gps('step');
		$pf_steps = array('l10n_save_pageform', 'l10n_edit_pageform', 'l10n_localise_pageform');
		$pl_steps = array('l10n_import_languageset');
		$can_edit = $this->pref('l10n-inline_editing');

		if( !empty($this->sub_tab) )
			$this->event = $this->sub_tab;

		switch ($this->event)
			{
			case 'search':
				if( !$this->xml() )
					$this->render_search_pane( $id );
				elseif( $id )
					$this->render_string_edit( 'search' , 'search' , $id );
				break;

			case 'special':
				$this->render_owner_list('special');
				$this->render_specials_list( $id );
				if( $container = gps('container') and $id )
					$this->render_string_edit( 'special', 'special' , $id );
				break;

			case 'page':
				$this->render_owner_list('page');
				if ($container = gps('container'))
					{
					$this->render_string_list( 'txp_page' , 'user_html' , $container , $id );
					if( $id )
						$this->render_string_edit( 'page', $container , $id );
					elseif( $can_edit and in_array($step , $pf_steps) )
						$this->render_pageform_edit( 'txp_page' , 'name' , 'user_html' , $container );
					}
				break;

			case 'form':
				$this->render_owner_list('form');
				if ($container = gps('container'))
					{
					$this->render_string_list( 'txp_form' , 'Form' , $container , $id );
					if( $id )
						$this->render_string_edit( 'form' , $container , $id );
					elseif( $can_edit and in_array($step , $pf_steps) )
						$this->render_pageform_edit( 'txp_form' , 'name' , 'Form' , $container );
					}
				break;

			case 'plugin':
				$this->render_owner_list('plugin');
				if( $step and in_array( $step , $pl_steps ) )
					{
					$this->render_import_list();
					}
				elseif( $container = gps(L10N_PLUGIN_CONST) and $prefix = gps('prefix') )
					{
					$this->render_plugin_string_list( $container , $id , $prefix );
					if( $id and $event=gps('string_event') )
						{
						$owner = $container;
						$this->render_string_edit( 'plugin', $container , $id, $owner , $event );
						}
					}
				break;
			}
		}


	function search_for_names()
		{
		#
		#	Start our XML output...
		#
		ob_start();
		header( "Content-Type: text/xml" );
		print '<?xml version=\'1.0\' encoding=\'utf-8\'?>'.n;

		#
		#	Grab the names of every string in the system...
		#
		$admin_langs = LanguageHandler::get_installation_langs();

		$stats = array();
		$full_names = safe_rows_start( 'name,lang', 'txp_lang', '1=1 ORDER BY name ASC' );
		$names = StringHandler::get_strings( $full_names , $stats );
		$num_names = count( $names );

		if( !$names || $num_names == 0 )
			exit;



		#
		#	Grab the search term...
		#
		$search_term = gps( 'l10n-sfn' );
		$out = array();
		switch( $search_term )
			{
			case '':
			case 'undefined':
				#
				#	send a full list of strings...
				#
				foreach( $names as $string => $value )
					{
					$out[] = '<li id="' . $string . '" class="l10n_hidden"><a href="'.hu.'" onClick="do_string_edit(\''.$string.'\'); return false;">' . $string . '</a></li>';
					}
				break;

			case '-':
				#
				#	send those missing a rendition in any language...
				#
				foreach( $names as $string => $value )
					{
					$lang_classes = '';
					$vals = explode( ',', $value );
					$vals = doArray( $vals , 'trim' );
					$missing = array_diff( $admin_langs , $vals );
					if( !empty( $missing ) )
						{
						$out[] = '<li id="' . $string . '" class="l10n_hidden"><a href="'.hu.'" onClick="do_string_edit(\''.$string.'\'); return false;">' . $string . ' [' . join( ', ' , $missing ). ']</a></li>';
						}
					}
				break;

			default:
				#
				#	send those missing a rendition in the specified language...
				#
				foreach( $names as $string => $value )
					{
					$lang_classes = '';
					$vals = explode( ',', $value );
					$vals = doArray( $vals , 'trim' );
					$missing = array_diff( $admin_langs , $vals );
					if( !empty( $missing ) )
						{
						foreach( $missing as $l )
							if( $l === $search_term )
								$out[] = '<li id="' . $string . '" class="l10n_hidden"><a href="'.hu.'" onClick="do_string_edit(\''.$string.'\'); return false;">' . $string . '</a></li>';
						}
					}
				break;
			}
		print graf( '<span id="l10n_result_count">'.$search_term.'</span>/' . count( $out ) . ' ' . gTxt('l10n-strings_match') ).n;
		print '<ul id="l10n_sbn_result_list" class="l10n_visible" >';
		print join( '' , $out );
		print '</ul>'.n;

		#
		#	Done; send it out...
		#
		exit;
		}
	function search_for_content()
		{
		#
		#	Start our XML output...
		#
		ob_start();
		header( "Content-Type: text/xml" );
		print '<?xml version=\'1.0\' encoding=\'utf-8\'?>'.n;

		#
		#	Grab the search term...
		#
		$search_term = gps( 'l10n-sfc' );

		if( empty( $search_term ) )
			{
			$rs = array();
			$count = 0;
			}
		else
			{
			#
			#	If it's not empty, build and execute SQL query...
			#
			$search_term = doSlash( $search_term );
			$where = "`data` LIKE '%$search_term%'";

			$lang = gps( 'l10n-lang' );
			$lang = doSlash( $lang );
			if( $lang and $lang!=='-' )
				$where .= " AND `lang`='$lang'";

			$rs = @safe_rows_start( 'DISTINCT name,data,lang', 'txp_lang', $where . " ORDER BY name ASC LIMIT 200" );
			$count = @mysql_num_rows($rs);
			}

		if( $rs and $count > 0 )
			{
			$o[] = '<div>'.n.'<h3>' . $count.' '.gTxt('l10n-matches').'</h3>'.n.'<ul>'.n;
			while ( $a = nextRow($rs) )
				{
				$name = $a['name'];
				$name = '<a href="'.hu.'" onClick="do_string_edit(\''.$name.'\'); return false;">' . $name . '</a>';

				#
				#	Now encode the data and highlight the search term...
				#
				$data = $a['data'];
				$data = htmlspecialchars($data);
				$data = str_ireplace( $search_term, '<span class="l10n_highlite">'.$search_term.'</span>', $data );

				if( empty($lang) or $lang==='-' )
					$language = ' ['.LanguageHandler::get_native_name_of_lang( $a['lang'] ).']';
				else
					$language = '';

				$o[] = "<li>$name$language<br/>$data</li>".n;
				}
			$o[] = '</ul>'.n.'</div>'.n;
			$o = join( '' , $o );

			print $o;
			}
		else
			{
			print '<div>'.n.'<h3>' . gTxt('none') . '</h3>'.n.'</div>';
			}

		#
		#	Done; send it out...
		#
		exit;
		}

	function render_search_pane( $id )
		{
		global $l10n_language;

		$site_langs  = LanguageHandler::get_site_langs();
		$admin_langs = LanguageHandler::get_installation_langs();

		#
		#	Grab the names of every string in the system...
		#
		$stats = array();
		$full_names = safe_rows_start( 'name,lang', 'txp_lang', '1=1 ORDER BY name ASC' );
		$names = StringHandler::get_strings( $full_names , $stats );
		$num_names = count( $names );

		#
		#	Render the search column...
		#
		$out[] = 	'<div class="l10n_owner_list">' . n;
		$out[] = 	'<h3>' . gTxt('l10n-search_for_strings') . '</h3>' . n;

		#
		#	Render the search type picker form...
		#
		$method = cs( 'l10n_string_search_by' );
		if( empty( $method ))
			$method = 'name';
		$ch1 = ($method == 'name') ? ' checked="checked"' : '';
		$ch2 = ($method == 'cont') ? ' checked="checked"' : '';
		$picker[] = t.'<input type="radio" name="search_by" value="name" id="sbn_radio_button"'.$ch1.' tabindex="0" class="radio" onClick="update_search(\'sbn_radio_button\')" />'.n;
		$picker[] = t.'<label for="sbn_radio_button">'.gTxt('l10n-by_name').'</label><br/>' . n;
		$picker[] = t.'<input type="radio" name="search_by" value="cont" id="sbc_radio_button"'.$ch2.' tabindex="1" class="radio" onClick="update_search(\'sbc_radio_button\')" />'.n;
		$picker[] = t.'<label for="sbc_radio_button">'.gTxt('l10n-by_content').'</label><br/>' . n;
		$out[] = form( join( '', $picker ) ) . br . n;


		$langs = LanguageHandler::get_installation_langs();
		$langs = LanguageHandler::do_fleshout_names( $langs , '' , false );
		$sel   = gTxt('l10n-all_languages');
		$langs = array_merge( array( '-' => $sel ) , $langs );

		#
		#	Render the search-by-name box...
		#
		$value = cs( 'search_string_name_live' );
		$f[] = fInput( 	'edit',
						'l10n_search_by_name',
						$value,
						'',							 			/*class*/
						gTxt('l10n-sbn_title'),					/*title*/
						'',										/*onClick*/
						'20', 									/*size*/
						'1', 									/*tab*/
						'l10n_search_by_name' 					/*id*/
						);

		$f[] = graf( gTxt('l10n-sbn_rubrik') ) . n;
		$subtype = cs( 'l10n_string_search_by_subtype' );
		if( empty( $subtype ))
			$subtype = 'all';
		$ch1 = ($subtype == 'all') ? ' checked="checked"' : '';
		$ch2 = ($subtype == 'missing') ? ' checked="checked"' : '';
		$f[] = t.'<input type="radio" name="search_by" value="all" id="sbn_all_radio_button"'.$ch1.' tabindex="0" class="radio" onClick="update_search(\'sbn_all_radio_button\')" />'.n;
		$f[] = t.'<label for="sbn_all_radio_button">'.gTxt('all strings').'</label><br/>' . n;
		$f[] = t.'<input type="radio" name="search_by" value="missing" id="sbn_missing_radio_button"'.$ch2.' tabindex="1" class="radio" onClick="update_search(\'sbn_missing_radio_button\')" />'.n;
		$f[] = t.'<label for="sbn_missing_radio_button">'.gTxt('missing renditions in&#8230;').'</label><br/>' . n;
		$language = cs( 'search_string_name_lang' );
		if( empty($language) )
			$language = $l10n_language['long'];
		$f[] = graf( selectInput( 'l10n-lang' , $langs , $language , 0 , ' onchange="on_sbn_lang_change()"' , 'sbn_lang_selection' ) ) . n;
		$out[] = '<div id="l10n_div_s_by_n" class="'.(($method=='name')?'l10n_visible':'l10n_hidden').'">' . n;
		$out[] = form( join( '', $f) ) . n;
		$out[] = '</div>' . n;
		#
		#	===============================================================
		#
		#	Render the search-by-content form...
		#
		$out[] = 	'<div id="l10n_div_s_by_c" class="'.(($method=='cont')?'l10n_visible':'l10n_hidden').'">' . n;


		$value = cs( 'search_string_content' );
		$language = cs( 'search_string_lang' );
		if( empty($language) )
			$language = $l10n_language['long'];
		$f = array();
		$f[] = graf( selectInput( 'l10n-lang' , $langs , $language , 0 , '' , 'sbc_lang_selection' ) ) . n;
		$f[] = fInput( 	'edit',
						'l10n-sfc',
						$value,
						'',
						gTxt('l10n-sbn_title'),
						'',
						'20',
						'1',
						'l10n_search_by_content'
						) . n;
		$f[] = fInput( 	'submit',
						'',
						gTxt('go'),
						'',
						'',
						"do_content_search(); return false;"
						) . n;

		$out[] = form( join('', $f) , '' , 'false' ) . n;
		$out[] = '</div>' . n;


		#
		#	Render the stats...
		#
		$out[] = '<br /><h3>'.gTxt('l10n-summary').'</h3>'.n;
		$out[] = '<table>'.n.'<thead>'.n.tr( '<td align="right">'.gTxt('language').'</td>'.n.'<td align="right">&nbsp;&nbsp;&#035;&nbsp;</td>' . td('') . td('') ).n.'</thead><tbody>';
		$extras_found = false;
		$plugin = gps( 'plugin' );
		foreach( $stats as $iso_code=>$count )
			{
			$lang_extras_found = false;
			$name = LanguageHandler::get_native_name_of_lang( $iso_code );
			$out[]= tr( td( $name ).td( '&nbsp;'.$count ) , ' style="text-align:right;" ' );
			}
		$out[] = tr( tdcs( '<hr/>' , 2 ) );
		$out[] = tr( td( gTxt('l10n-total').' '.gTxt('l10n-renditions') ).td('&nbsp;'.array_sum($stats)) , ' style="text-align:right;" ' );
		$out[] = tr( td( gTxt('l10n-total').' '.gTxt('l10n-strings') ).td('&nbsp;'.$num_names) , ' style="text-align:right;" ' );
		$out[] = tr( tdcs( '<hr/>' , 2 ) );
		$out[] = '</tbody></table>';


		$out[] = '</div>' . n;
 		#
		#	===============================================================
		#
		#	Render the results column.
		#
		$out[] = '<div class="l10n_string_list" id="l10n_sbn_result_div">';
		$out[] = '<h3>'.gTxt('search_results').'</h3>'.n;
		$out[] = '<div id="l10n_div_sbn_result_list">'.n;
		$out[] = '</div>' . n;

		#
		#	DIV for the search-by-content result list...
		#
		$out[] = '<div class="l10n_hidden" id="l10n_sbc_result_list">'.'</div>'.n;

		#
		#	Closing DIV
		#
		$out[] = '</div>' . n;

		#
		#	DIV for string edit pane.
		#
		$out[] = '<div class="l10n_values_list" id="l10n_div_string_edit">';
		if( $id )
			$this->render_string_edit( 'search' , 'search' , $id );
		$out[] = '</div>'.n;

		echo join('', $out);
		}

	function _generate_list( $table , $fname , $fdata )						# left pane subroutine
		{
		$rs = safe_rows_start( "$fname as name, $fdata as data", $table, '1=1' ) ;
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			$explain = false;
			while ( $a = nextRow($rs) )
				{
				$snippets 	= array();
				$raw_count = 0;
				$snippets = SnippetHandler::find_snippets_in_block( $a['data'] , $raw_count );
				$localised = SnippetHandler::do_localise( $a['data'] );
				$count = count( $snippets );
				$marker = ($count) ? '['.$count.']' : '';
				$guts = $a['name'].' '.$marker;
				if( !$localised and ($raw_count) )
					{
					$guts .= ' *';
					$explain = true;
					}
				if( $localised or ($count) )
					$guts = '<strong>'.$guts.'</strong>';
				$out[] = '<li><a href="'.$this->url( array('container'=>$a['name']) , true).'">'.$guts.'</a></li>' . n;
				}
			$out[] = br . gTxt('l10n-pageform-markup') . n;
			if( $explain )
				$out[] = gTxt('l10n-explain_no_tags') . n;
			}
		else
			$out[] = '<li>'.gTxt('none').'</li>'.n;
		return join('', $out);
		}

	function _extend_plugin_list()
		{
		global $plugins_ver;
		static $full_plugin_list;

		if( !isset( $full_plugin_list ) )
			{
			#
			#	Note the plugins_ver list only contains admin and library plugins at the moment.
			# So expand the plugin list to include public plugins from the txp_plugin table.
			# Cache directory is ignored to stop the eval() of the plugin.
			#
			$temps = @safe_column( 'name' , 'txp_plugin' , "`status`='1' and `type`='0'" );
			$full_plugin_list = array_merge( $temps , $plugins_ver );
			}

		return $full_plugin_list;
		}

	function _generate_plugin_list()											# left pane subroutine
		{
		$rps = StringHandler::discover_registered_plugins();
		if( count( $rps ) )
			{
			$plugins = $this->_extend_plugin_list();

			foreach( $rps as $plugin=>$vals )
				{
				if( !is_array( $vals ) )
					continue;

				extract( $vals );
				$plugin_found = array_key_exists( $plugin, $plugins );

				$marker = ( !$plugin_found )
					? ' <strong>*</strong>' : '';
				$out[] = '<li><a href="' . $this->parent->url( array(L10N_PLUGIN_CONST=>$plugin,'prefix'=>$pfx) , true ) . '">' .
						//$plugin . br . ' [~' .$num . sp . LanguageHandler::get_native_name_of_lang($lang) . '] ' . $marker.
						$plugin . ' [' .$event . '] ' . $marker.
						'</a></li>';
				}
			}
		else
			$out[] = '<li>'.gTxt('none').'</li>'.n;

		return join('', $out);
		}

	function render_owner_list( $type )										#	Render the left pane
		{
		/*
		Renders a list of resource owners for the left-hand pane.
		*/
		$out[] = '<div class="l10n_owner_list">';

		switch( $type )
			{
			case 'special':
				$out[] = 	'<h3>' . gTxt('l10n-specials') . '</h3>' . n .
							'<div id="l10n_specials">' . n .
							graf( gTxt( 'l10n-explain_specials' ) );
				break;

			case 'plugin':
				$out[] = 	'<h3>' . gTxt('l10n-registered_plugins') . '</h3>' . n .
							'<div id="l10n_plugins">' . n .
							'<ol>' . n;
				$out[] = $this->_generate_plugin_list();
				$out[] = n . '</ol>';
				break;

			case 'page':
				$out[] = 	'<h3>' . gTxt('pages') . '</h3>' . n .
							'<div id="l10n_pages"' . n .
							'<ol>' . n;
				$out[] = $this->_generate_list( 'txp_page' , 'name' , 'user_html' );
				$out[] = n . '</ol>';
				break;

			default:
				case 'form':
				$out[] = 	'<h3>' . gTxt('forms') . '</h3>' . n .
							'<div id="l10n_forms">' . n .
							'<ol>' . n;
				$out[] = $this->_generate_list( 'txp_form' , 'name' , 'Form' );
				$out[] = n . '</ol>';
				break;
			}

		$out[] = n . '</div>';
		$out[] = n . '</div>';
		echo join('', $out);
		}

	function _render_string_list( $strings , $owner_label , $owner_name , $prefix , $event )	# Center pane string render subroutine
		{
		$strings_exist 	= ( count( $strings ) > 0 );
		if( !$strings_exist )
			return '';

		$site_langs = LanguageHandler::get_site_langs();

		$out[] = '<ol>';
		if( $strings_exist )
			{
			$complete_langs = StringHandler::get_full_langs_string();
			foreach( $strings as $string=>$langs )
				{
				$complete = ($complete_langs === $langs);
				$guts = $string . ' ['.( ($langs) ? $langs : gTxt('none') ).']';
				if( !$complete )
					$guts = '<strong>'. $guts . '</strong>';
				$out[]= '<li><a href="' .
					$this->url( array($owner_label=>$owner_name, gbp_id=>$string, 'prefix'=>$prefix, 'string_event'=>$event) , true ) .
					'">' . $guts . '</a></li>';
				}
			}
		else
			$out[] = '<li>'.gTxt('none').'</li>'.n;

		$out[] = '</ol>';
		return join('', $out);
		}

	function _render_string_stats( $string_name , &$stats )					# Right pane stats render subroutine
		{
		$site_langs  = LanguageHandler::get_site_langs();
		$admin_langs = LanguageHandler::get_installation_langs();

		$out[] = '<h3>'.gTxt('l10n-summary').'</h3>'.n;
		$out[] = '<table>'.n.'<thead>'.n.tr( '<td align="right">'.gTxt('language').'</td>'.n.'<td align="right">&nbsp;&nbsp;&#035;&nbsp;</td>' . td('') . td('') ).n.'</thead><tbody>';
		$extras_found = false;
		$plugin = gps( 'plugin' );
		foreach( $stats as $iso_code=>$count )
			{
			$lang_extras_found = false;
			$name = LanguageHandler::get_native_name_of_lang( $iso_code );
			$remove = '';
			$export = '';
			$supported = in_array( $iso_code , $site_langs ) || in_array( $iso_code , $admin_langs );
			if( !$supported )
				{
				$extras_found = true;
				$lang_extras_found = true;
				if( !empty($plugin) )
					{
					$remove[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</span>';
					$remove[] = sInput( 'l10n_remove_languageset');
					$remove[] = $this->parent->form_inputs();
					$remove[] = hInput( 'plugin' , $plugin );
					$remove[] = hInput( 'prefix' , gps( 'prefix' ) );
					$remove[] = hInput( 'lang_code' , $iso_code );
					$remove[] = hInput( 'subtab' , $this->sub_tab );
					$remove = form( join( '' , $remove ) ,
									'' ,
									"verify('" . doSlash(gTxt('l10n-lang_remove_warning' , array('$var1'=>$name)) ) .
									 doSlash(gTxt('are_you_sure')) . "')");
					}
				}

			$details =  StringHandler::if_plugin_registered( $string_name , $iso_code );
			if( false !== $details )
				{
				$export[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('l10n-export'), '').'</span>';
				$export[] = sInput( 'l10n_export_languageset');
				$export[] = $this->parent->form_inputs();
				$export[] = hInput( 'language' , $iso_code );
				$export[] = hInput( 'prefix' , $details['pfx'] );
				$export[] = hInput( 'plugin' , $string_name );
				$export = form( join( '' , $export ) );
				}

			$out[]= tr( td( ($lang_extras_found ? ' * ' : '').$name ).td( '&nbsp;'.$count.'&nbsp;' ).td($export).td($remove) , ' style="text-align:right;" ' );
			}
		$out[] = tr( tdcs( '<hr/>' , 4 ) );
		$out[] = tr( td( gTxt('l10n-total') ).td('&nbsp;'.array_sum($stats).'&nbsp;').td('').td('') , ' style="text-align:right;" ' );
		$out[] = tr( tdcs( '<hr/>' , 4 ) );
		$out[] = '</tbody></table>';

		if( $extras_found )
			{
			$out[] = gTxt('l10n-explain_extra_lang');

			if( empty( $string_name ) )
				{
				foreach( $stats as $iso_code=>$count )
					{
					$supported = in_array( $iso_code , $site_langs ) || in_array( $iso_code , $admin_langs );
					if( !$supported )
						{
						$remove = array();
						$name = LanguageHandler::get_native_name_of_lang( $iso_code );
						$count = safe_count( 'txp_lang' , "`lang`='$iso_code'" );

						$remove[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</span>';
						$remove[] = sInput( 'l10n_remove_language');
						$remove[] = $this->parent->form_inputs();
						$remove[] = hInput( 'container' , gps('container') );
						$remove[] = hInput( 'lang_code' , $iso_code );
						$remove[] = hInput( 'subtab' , $this->sub_tab );
						$out[]    = form( gTxt('l10n-delete_whole_lang' , array('$var1'=>$name,'$var2'=>$count) ) . sp . join( '' , $remove ) ,
										'' ,
										"verify('" . doSlash(gTxt('l10n-lang_remove_warning2' , array('$var1'=>$name)) ) .
										 doSlash(gTxt('are_you_sure')) . "')") . br . n;
						}
					}
				}
			}

		if( !empty( $string_name ) )
			{
			$import[] = gTxt('l10n-import_title' , array( '{type}'=>gTxt('l10n-plugin') )) . br;
			$import[] = '<textarea name="data" cols="60" rows="2" id="l10n_string_import">';
			$import[] = '</textarea>' .br . br;
			$import[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('l10n-import'), '').'</span>';
			$import[] = sInput( 'l10n_import_languageset');
			$import[] = $this->parent->form_inputs();
			$import[] = hInput( 'plugin' , gps('plugin') );
			$import[] = hInput( 'prefix' , gps('prefix') );
			$import[] = hInput( 'language' , gps('language') );
			$import[] = hInput( 'subtab' , $this->sub_tab );
			$out[] = form( join( '' , $import ) , 'border: 1px solid #ccc; padding:1em; margin:1em;' );
			}

		return join( '' , $out );
		}

	function render_plugin_string_list( $plugin , $string_name , $prefix )	# Center pane plugin wrapper
		{
		/*
		Show all the strings and localisations for the given plugin.
		*/
		$stats 			= array();
		$strings 		= StringHandler::get_plugin_strings( $plugin , $stats , $prefix );
		$strings_exist 	= ( count( $strings ) > 0 );
		$details		= StringHandler::if_plugin_registered( $plugin , '' );
		$event			= $details['event'];

		$out[] = '<div class="l10n_plugin_list">';
		$out[] = '<h3>'.$plugin.' '.gTxt('l10n-strings').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' .
				 $this->url( array( L10N_PLUGIN_CONST => $plugin, 'prefix'=>$prefix ) , true ) . '">' .
				 gTxt('l10n-statistics') . '&#187;</a></span>' . br . n;

		$out[] = br . n . $this->_render_string_list( $strings , L10N_PLUGIN_CONST , $plugin , $prefix, $event );
		$out[] = '</div>';

		# Render default view details in right hand pane...
 		if( empty( $string_name ) )
			{
			$out[] = '<div class="l10n_values_list">';
			$out[] = $this->_render_string_stats( $plugin , $stats );

			# If the plugin is not present offer to delete the lot
			$plugins = $this->_extend_plugin_list();
			if( !array_key_exists( $plugin, $plugins ) )
				{
				$out[] = '<h3>'.gTxt('l10n-no_plugin_heading').'</h3>'.n;
				$del[] = graf( gTxt('l10n-remove_plugin') );
				$del[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</div>';
				$del[] = sInput('l10n_remove_stringset');
				$del[] = $this->parent->form_inputs();
				$del[] = hInput(L10N_PLUGIN_CONST, $plugin);

				$out[] = form(	join('', $del) ,
								'border: 1px solid grey; padding: 0.5em; margin: 1em;' ,
								"verify('".doSlash(gTxt('l10n-delete_plugin')).' '.doSlash(gTxt('are_you_sure'))."')");
				}

			$out[] = '</div>';
			}

		echo join('', $out);
		}

	function render_string_list( $table , $fdata , $owner , $id='' )			# Center pane snippet wrapper
		{
		/*
		Renders a list of strings belonging to the chosen owner in the center pane.
		*/
		$stats 	= array();
		$data 	= safe_field( $fdata , $table , " `name`='$owner'" );
		$raw_count = 0;
		$snippets = SnippetHandler::find_snippets_in_block( $data , $raw_count );
		$strings  = SnippetHandler::get_snippet_strings( $snippets , $stats );
		$can_edit = $this->pref('l10n-inline_editing');

		$out[] = '<div class="l10n_string_list">';
		$out[] = '<h3>'.$owner.' '.gTxt('l10n-snippets').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' .
				 $this->url( array( 'container' => $owner ) , true ) . '">' .
				 gTxt('l10n-statistics') . '&#187;</a></span>' . br . n;
		if( $can_edit )
			 $out[] = '<span style="float:right;"><a href="' .
					 $this->parent->url( array( 'container'=>$owner , 'step'=>'l10n_edit_pageform' , 'subtab'=>$this->sub_tab ) , true ) . '">' .
					 gTxt('l10n-edit_resource' , array('$type'=>gTxt($this->event),'$owner'=>$owner) ) .
					 '&#187;</a></span>' . br . n;

		#	Render the list...
		$out[] = br . n . $this->_render_string_list( $strings , 'container', $owner , '' , 'public' ) . n;
		$out[] = '</div>';

		#	Render default view details in right hand pane...
		$step = gps('step');
 		if( empty( $id ) and empty( $step ) )
			{
			$out[] = '<div class="l10n_values_list">';
			$out[] = $this->_render_string_stats( '' , $stats );
			$out[] = '</div>';
			}

		echo join('', $out);
		}

	function render_specials_list( $id='')
		{
		/*
		Renders a list of special strings...
		*/
		$stats 	= array();
		$owner = 'special';
		$raw_count = 1;
		$snippets = SnippetHandler::get_special_snippets();
		$strings  = SnippetHandler::get_snippet_strings( $snippets , $stats );

		$out[] = '<div class="l10n_string_list">';
		$out[] = '<h3>'.gTxt('l10n-special').' '.gTxt('l10n-snippets').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' .
				 $this->url( array( 'owner' => $owner ) , true ) . '">' .
				 gTxt('l10n-statistics') . '&#187;</a></span>' . br . n;

		#	Render the list...
		$out[] = br . n . $this->_render_string_list( $strings , 'container', $owner , '' , 'public' ) . n;
		$out[] = '</div>';

		#	Render default view details in right hand pane...
		$step = gps('step');
 		if( empty( $id ) and empty( $step ) )
			{
			$out[] = '<div class="l10n_values_list">';
			$out[] = $this->_render_string_stats( '' , $stats );
			$out[] = '</div>';
			}

		echo join('', $out);
		}
	function render_pageform_edit( $table , $fname, $fdata, $owner )			# Right pane page/form edit textarea.
		{
		$out[] = '<div class="l10n_values_list">';
		$out[] = '<h3>'.gTxt('l10n-edit_resource' , array('$type'=>$this->event,'$owner'=>$owner) ).'</h3>' . n;

		$data = safe_field( $fdata , $table , '`'.$fname.'`=\''.doSlash($owner).'\'' );
		$localised = SnippetHandler::do_localise( $data );

		if( !$localised )
			{
			$l[] = '<p>'.gTxt('l10n-add_tags').n;
			$l[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('add'), '').'</div></p>';
			$l[] = sInput('l10n_localise_pageform').n;
			$l[] = $this->parent->form_inputs();
			$l[] = hInput('container', $owner);
			$l[] = hInput('data', $data);
			$l[] = hInput('subtab' , $this->sub_tab );
			$out[] = form( join('', $l) , 'border: 1px solid grey; padding: 0.5em; margin: 1em;' );
			}

		$f[] = '<p><textarea name="data" cols="70" rows="20" title="'.gTxt('l10n-textbox_title').'">' .
			 $data .
			 '</textarea></p>'.br.n;
		$f[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$f[] = sInput('l10n_save_pageform');
		$f[] = $this->parent->form_inputs();
		$f[] = hInput('container', $owner);
		$f[] = hInput('subtab' , $this->sub_tab );
		$out[] = form( join('', $f) , 'padding: 0.5em; margin: 1em;' );

		$out[] = '</div>';
		echo join('', $out);
		}

	function render_string_edit( $type , $container , $id, $owner = '' , $event='public' )	# Right pane string edit routine
		{
		/*
		Render the edit controls for all localisations of the chosen string.
		This can either be called in the flow of normal HTTP request processing or
		as an XMLHttp request from the l10n JavaScript.
		*/
		$xml = $this->xml();
		if( $xml )
			{
			while (@ob_end_clean());
			ob_start();
			header( "Content-Type: text/xml" );
			echo '<?xml version=\'1.0\' encoding=\'utf-8\'?>'.n.'<div>';
			}
		else
			$out[] = '<div class="l10n_values_list" id="l10n_div_string_edit">';

		$out[] = '<h3>'.gTxt('l10n-renditions_for').$id.'</h3>'.n.'<form action="index.php" method="post"><dl>';

		$x = StringHandler::get_string_set( $id );
		$final_codes = array();

		#	Complete the set with any missing language codes and empty data...
		$lang_codes = LanguageHandler::get_site_langs();
		if( $type === 'plugin' )
			{
			$admin_plugin = safe_field( 'type' , 'txp_plugin' , "`name`='$owner'" );

			#
			#	if this string is in an admin or library plugin then use the
			# installation (admin) languages...
			#
			if( $admin_plugin > 0 )
				$lang_codes = LanguageHandler::get_installation_langs();
			}
		elseif( $type === 'search' )
			{
			$lang_codes = LanguageHandler::get_installation_langs();
			}

		foreach($lang_codes as $code)
			{
			if( array_key_exists( $code , $x ) )
				continue;
			$x[ $code ] = array( 'id'=>'', 'event'=>'', 'data'=>'' );
			}
		ksort( $x );
		foreach( $x as $code => $data )
			{
			$final_codes[] = $code;
			$e = $data['event'];
			if( empty( $e ) )
				$e = $event;
			$lang = LanguageHandler::get_native_name_of_lang($code);
			$dir  = LanguageHandler::get_lang_direction_markup( $code );

			$warning = '';
			if( empty( $data['id'] ) )
				$warning .= ' * '.gTxt('l10n-missing').sp;
			elseif( empty( $data['data'] ))
				$warning .= ' * '.gTxt('l10n-empty').sp;

			$out[] = '<dt>'.$lang.' ['.$code.']. '.$warning.sp.'<a href="#" onClick="toggleDirection(\''.$code.'\')"><span id="'.$code.'-toggle">'.gTxt('l10n-toggle').'</span></a>' .'</dt>';
			$out[] = '<dd><p>'.
						'<textarea class="l10n_string_edit" id="' . $code . '-data" name="' . $code . '-data" cols="60" rows="2" title="' .
						gTxt('l10n-textbox_title') . '"'. $dir .'>' . $data['data'] . '</textarea>' .
						hInput( $code.'-id' , $data['id'] ) .
						hInput( $code.'-event' , $e ) .
						'</p></dd>';
			}

		$out[] = '</dl>';
		$out[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$out[] = sInput('l10n_save_strings');
		$out[] = $this->parent->form_inputs();
		$out[] = hInput('codes', trim( join( ',' , $final_codes ) , ', ' ) );
		//$out[] = hInput(L10N_LANGUAGE_CONST, gps(L10N_LANGUAGE_CONST));
		$out[] = hInput('prefix', gps('prefix'));
		if( $type === 'plugin' )
			$out[] = hInput(L10N_PLUGIN_CONST, $container);
		else
			{
			$out[] = hInput('container', $container);
			$out[] = hInput('subtab' , $this->sub_tab );
			}
		$out[] = hInput('l10n_type', $type );
		$out[] = hInput('owner', $owner);
		$out[] = hInput('string_event', $event);
		$out[] = hInput(gbp_id, $id);
		$out[] = '</form>'.n;
		$out[] = '</div>';
		echo join('', $out);
		if( $xml )
			{
			exit;
			}
		}

	function render_import_list()
		{
		$d = gps( 'data' );
		$d = @unserialize( @base64_decode( @str_replace( "\r\n", '', $d ) ) );

		$o[] = '<div style="float:left;">';
		$o[] = '<h2>'.gTxt('preview').' '.gTxt('file').'</h2>';
		if( !isset($d['owner']) or !isset($d['prefix']) or !isset($d['event']) or !isset($d['strings']) )
			$o[] = gTxt('l10n-invalid_import_file');
		else
			{
			$f1[] = gTxt('plugin') . ': <strong>'.$d['owner'].'</strong>'.br.n;
			$f1[] = gTxt('language') . ': <strong>'.LanguageHandler::get_native_name_of_lang($d['lang']).' ['.$d['lang'].']</strong>'.br.br.n;
			$f1[] = hInput( 'data' , gps('data') );
			$f1[] = hInput( 'plugin' , $d['owner'] );
			$f1[] = hInput( 'prefix' , $d['prefix'] );
			$f1[] = hInput( 'language' , gps('language') );
			$f1[] = sInput( 'l10n_import_languageset');
			$fl[] = hInput( 'subtab' , $this->sub_tab );
			$f1[] = hInput( 'commit', 'true' );
			$f1[] = $this->parent->form_inputs();

			$direction_markup = LanguageHandler::get_lang_direction_markup( $d['lang'] );

			foreach( $d['strings'] as $k=>$v )
				{
				$v = htmlspecialchars( $v );
				$l[] = tr( '<td style="text-align: right;">'.$k.' : </td>' . n . td("<input type=\"text\" readonly size=\"100\" value=\"$v\" $direction_markup/>") ) .n ;
				}

			$f2[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</span>';
			$content = join( '' , $f1 ) . tag( join( '' , $l ) , 'table' ) . join( '' , $f2 );
			$o[] = form( $content , '' ,
						"verify('" . doSlash( gTxt('l10n-import_warning') ) . ' ' . doSlash(gTxt('are_you_sure')) . "')");
			}
		$o[] = '</div>';
		echo join( '' , $o );
		}

	function remove_strings()
		{
		$remove_langs 	= gps('lang_code');
		$plugin 		= gps( L10N_PLUGIN_CONST );
		StringHandler::remove_strings( $plugin , $remove_langs );
		unset( $_POST['step'] );
		}
	function remove_language()
		{
		$remove_lang 	= gps('lang_code');
		if( !empty($remove_lang) )
			StringHandler::remove_lang( $remove_lang );
		unset( $_POST['step'] );
		}

	function save_strings()
		{
		$string_name 	= gps( gbp_id );
		$owner       	= gps( 'owner' );
		$codes			= gps( 'codes' );
		$lang_codes		= explode( ',' , $codes );
		$i				= 0;

		#	Check that we are not deleting every string!
		foreach($lang_codes as $code)
			{
			$t = gps( $code.'-data' );
			if( !empty( $t ) )
				$i += 1;
			}
		if( 0 === $i )
			{
			$this->parent->message = gTxt('l10n-cannot_delete_all');
			return;
			}

		foreach($lang_codes as $code)
			{
			$translation 	= gps( $code.'-data' );
			$id 			= gps( $code.'-id' );
			if( $owner == 'snippet' )
				$event = 'public';
			else
				$event			= gps( $code.'-event' );
			$exists			= !empty( $id );
			if( !$exists and empty( $translation ) )
				continue;

			StringHandler::store_translation_of_string( $string_name , $event , $code , $translation , $id , $owner );
			}
		}

	function save_pageform()
		{
		$data = doSlash( gps('data') );
		$owner = doSlash( gps('container') );

		if( !empty( $this->sub_tab) )
			$tab = doSlash( gps( 'subtab' ) );
		else
			$tab = doSlash( gps( gbp_tab ) );

		if( $tab === 'form' )
			@safe_update( 'txp_form' , "`Form`='$data'" , "`name`='$owner'" );
		elseif( $tab === 'page' )
			@safe_update( 'txp_page' , "`user_html`='$data'" , "`name`='$owner'" );
		}

	function localise_pageform()
		{
		$data = gps('data');
		$data = SnippetHandler::do_localise( $data , 'insert' );
		$_POST['data'] = $data;
		LocalisationStringView::save_pageform();
		}

	function export_languageset()
		{
		$plugin = gps('plugin');
		$lang   = gps('language');
		$prefix = gps('prefix');

		$details =  StringHandler::if_plugin_registered( $plugin , $lang );
		if( false !== $details )
			{
			//$details = unserialize( $details );
			$data = StringHandler::serialize_strings( $lang , $plugin , $prefix , $details['event'] );
			$this->parent->serve_file( $data , $plugin . '.' . $lang . '.inc' );
			}
		}

	function import_languageset()
		{
		$commit = gps( 'commit' );
		if( !empty($commit) and ('true' === $commit) )
			{
			$d 	= gps( 'data' );
			$d = unserialize( base64_decode( str_replace( "\r\n", '', $d ) ) );
			if( is_array( $d ) )
				{
				if( array_key_exists( 'strings' , $d ) )
					StringHandler::insert_strings( $d['prefix'] , $d['strings'] , $d['lang'] , $d['event'] , $d['owner'] , true );
				}
			unset( $_POST['step'] );
			}
		}

	}

class SnippetInOutView extends GBPAdminSubTabView
	{
	function SnippetInOutView($title, $event, &$parent, $is_default = NULL)
		{
		GBPAdminSubTabView::GBPAdminSubTabView( $title , 'snippets' , $parent , $is_default , $event );
		}

	function preload()
		{
		$step = gps('step');
		if( $step )
			{
			switch( $step )
				{
				case 'l10n_export_languageset':
					$this->export_languageset();
					break;

				case 'l10n_import_languageset':
					$this->import_languageset();
					break;
				}
			}
		}

	function main()
		{
		$step = gps('step');
		switch( $step )
			{
			case 'l10n_import_languageset':
				$this->render_import_list();
				break;

			default:
				$this->render_main();
				break;
			}
		}

	function render_main()
		{
		$site_langs 	= LanguageHandler::get_site_langs();

		$snip_string = gTxt('l10n-snippet');
		$out[] = '<div class="l10n_bordered l10n_export_list">';
		$out[] = gTxt('l10n-export_title' , array( '{type}'=>$snip_string )). br;
		$out[] = '<table>'.n.'<thead>'.n.tr( '<td align="right">'.gTxt('language').'</td>'.n.'<td align="right">'.sp.sp.gTxt('select').sp.'</td>' ).n.'</thead><tbody>';


		foreach( $site_langs as $lang )
			{
			$name = t . '<label for="'.$lang.'">' . LanguageHandler::get_native_name_of_lang( $lang ) . '</label>';
			$choice = t . '<input type="checkbox" class="checkbox" value="'.$lang.'" name="'.$lang.'" id="'.$lang.'"/>' . n;
			$export[]= tr( td( $name ).td( $choice ) , ' style="text-align:right;" ' );
			}
		$export[] = sInput( 'l10n_export_languageset');
		$export[] = $this->parent->form_inputs();
		$export[] = hInput( 'subtab' , $this->sub_tab );
		$export[] = tr( td(sp) . td(sp.sp.'<span class="l10n_form_submit">'.fInput('submit', '', gTxt('l10n-export'), '').'</span>') );

		$out[] = form( join( '' , $export) );
		$out[] = '</tbody></table>'.n.'</div>'.n.n;

		$import[] = '<div class="l10n_bordered l10n_import_list">';
		$import[] = gTxt('l10n-import_title' , array( '{type}'=>$snip_string) ) . br;
		$import[] = '<textarea name="data" cols="40" rows="2" id="l10n_string_import">';
		$import[] = '</textarea>' .br . br;
		$import[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('l10n-import'), '').'</span>';
		$import[] = sInput( 'l10n_import_languageset');
		$import[] = $this->parent->form_inputs();
		$import[] = hInput( 'language' , gps('language') );
		$import[] = hInput( 'subtab' , $this->sub_tab );
		$out[] = form( join( '' , $import ) ).n.'</div>'.n.n;

		echo join( '' , $out );
		}

	function _get_snippet_names( $table , $fname , $fdata )
		{
		$snippets 	= array();
		$rs = safe_rows_start( "$fname as name, $fdata as data", $table, '1=1' ) ;
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			$explain = false;
			while ( $a = nextRow($rs) )
				{
				$raw_count = 0;
				$snips = SnippetHandler::find_snippets_in_block( $a['data'] , $raw_count );
				foreach( $snips as $k=>$v )
					$snippets[$v] = $v;
				}
			}
		return $snippets;
		}

	function get_special_snippets()
		{
		$snippets = array();
		$snips = SnippetHandler::get_special_snippets();
		foreach( $snips as $k=>$v )
			$snippets[$v] = $v;
		return $snippets;
		}

	function export_languageset()
		{
		$plugin = gps('owner');
		$lang   = gps('language');

		$sources = array	(
							array	(
									'table'	=>	'txp_page',
									'name'	=>	'name',
									'data'	=>	'user_html',
									'fn'	=>	'',
									),
							array	(
									'table'	=>	'txp_form',
									'name'	=>	'name',
									'data'	=>	'Form',
									'fn'	=>	'',
									),
							array	(
									'table'	=>	'',
									'name'	=>	'',
									'data'	=>	'',
									'fn'	=>	'get_special_snippets',
									),
							);
		$snippet_names = array();

		#
		#	Scan sources for the name of snippets...
		#
		foreach( $sources as $source )
			{
			$snips = array();
			extract( $source );
			if( !empty( $fn ) )
				{
				$key = '';
				if( is_callable( array($this,$fn) , false , $key ) )
					$snips = call_user_func( array($this,$fn) );
				}
			else
				{
				$snips = $this->_get_snippet_names( $table , $name , $data );
				}
			if( is_array( $snips ) )
				$snippet_names = array_merge( $snippet_names , $snips );
			}

		sort( $snippet_names );

		$snippet_nameset = StringHandler::make_nameset($snippet_names);

		#
		#	For each selected language, grab the snippet strings from the txp_lang table and add it to the
		# export structure...
		#
		$site_langs 	= LanguageHandler::get_site_langs();

		$export_data = array();
		foreach( $site_langs as $lang )
			{
			$lang_set = array( 'lang'=>$lang );
			$present = ($lang === gps( $lang ) );
			if( !$present )
				continue;

			$lang_set = StringHandler::get_set_by_lang( $snippet_nameset , $lang );

			$export_data[$lang] = $lang_set;
			}

		#
		#	Serve the export data as a file...
		#
		$export_data = array( 'header'=>L10N_SNIPPET_IO_HEADER , 'data'=>$export_data );
		$export_data = chunk_split( base64_encode( serialize($export_data) ) , 64 );
		$this->parent->parent->serve_file( $export_data , 'snippets.inc' );
		}

	function render_import_list()
		{
		$count = 0;
		$site_langs = LanguageHandler::get_site_langs();
		$d = gps( 'data' );
		$d = @unserialize( @base64_decode( @str_replace( "\r\n", '', $d ) ) );
		$subtab = gps('subtab');

		$o[] = '<div style="float:left;">';
		$o[] = '<h2>'.gTxt('preview').' '.gTxt('file').'</h2>';

		if( is_array( $d ) and !empty( $d ) )
			{
			if( !isset( $d['header'] ) or $d['header'] !== L10N_SNIPPET_IO_HEADER )
				{
				$o[] = gTxt('l10n-invalid_import_file');
				}
			else
				{
				$f[] = hInput( 'data' , gps('data') ).n;
				$f[] = hInput( 'language' , gps('language') ).n;
				$f[] = sInput( 'l10n_import_languageset').n;
				$f[] = hInput( 'subtab' , $this->sub_tab );
				$f[] = hInput( 'commit', 'true' ).n;
				$f[] = $this->parent->form_inputs().n;
				foreach( $d['data'] as $lang=>$set )
					{
					$dir_markup	= LanguageHandler::get_lang_direction_markup( $lang );

					if( empty( $lang ) or !in_array( $lang, $site_langs ) or empty( $set ) )
						{
						$l[] = tr( n.td( sp ).n.td( gTxt('l10n-language_not_supported') ) ).n;
						}
					else
						{
						$l[] = tr( n.tdcs( gTxt('language') . ': <strong>'.LanguageHandler::get_native_name_of_lang($lang).' ['.$lang.']&#8230;</strong>'.br.br.n , 2 ) ).n;
						foreach( $set as $name=>$couplet )
							{
							$data	= htmlspecialchars($couplet[0]);
							$event	= htmlspecialchars($couplet[1]);

							if( empty( $name ) or empty($event) or empty($data) )
								continue;

							$l[] = tr( n.t.'<td style="text-align: right;">'.$name.' <em>('.$event.')</em> : </td>' . n . td("<input type=\"text\" readonly size=\"100\" value=\"$data\" $dir_markup/>") ) .n;
							$count++;
							}
						}
					$l[] = tr( n.t.tdcs( sp , 2 ) ).n;
					}
				$l[] = tr( n.tdcs( sp , 2 ) ).n;
				$l[] = tr( n.tdcs( gTxt( 'l10n-total' ) . sp . ': ' . $count . sp . gTxt('strings') , 2 ) ).n;
				$l[] = tr( n.tdcs( sp , 2 ) ).n;

				$f2[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</span>';
				$content = join( '' , $f ) . tag( join( '' , $l ) , 'table' ) . join( '' , $f2 );
				$o[] = form( $content , '' ,
							"verify('" . doSlash( gTxt('l10n-import_warning') ) . ' ' . doSlash(gTxt('are_you_sure')) . "')");
				}
			}

		$o[] = '</div>';
		echo join( '' , $o );
		}

	function import_languageset()
		{
		$commit = gps( 'commit' );
		if( empty($commit) or ('true' !== $commit) )
			{
			return;
			}

		$site_langs 	= LanguageHandler::get_site_langs();

		$d 	= gps( 'data' );
		$d = unserialize( base64_decode( str_replace( "\r\n", '', $d ) ) );
		$count = 0;

		if( is_array( $d ) and !empty( $d ) )
			{
			if( !isset( $d['header'] ) or $d['header'] !== L10N_SNIPPET_IO_HEADER )
				{
				return;
				}

			foreach( $d['data'] as $lang=>$set )
				{
				if( empty( $lang ) or !in_array( $lang, $site_langs ) or empty( $set ) )
					continue;

				foreach( $set as $name=>$couplet )
					{
					$data = $couplet[0];
					$event = $couplet[1];

					if( empty( $name ) or empty($event) or empty($data) )
						continue;

					$name = doSlash( $name );
					$event = doSlash( $event );
					$data = doSlash( $data );

					$set = "`lang`='$lang', `event`='$event', `data`='$data', `owner`='', `name`='$name'";

					$id = safe_field( 'id' , 'txp_lang' , "`name`='$name' AND `lang`='$lang'" );
					if( false === $id )
						{
						$res = @safe_insert( 'txp_lang', $set);
						if( $res !== false and $res !== 0 )
							$count++;
						}
					else
						{
						$res = @safe_update( 'txp_lang', $set, "`id`='$id'");
						if( true === $res )
							$count++;
						}
					}
				}
			}

			$this->parent->parent->message = gTxt('l10n-import_count',array('{count}'=>$count,'{type}'=>gTxt('l10n-snippet')));
			unset( $_POST['step'] );
		}

	}

class LocalisationArticleTabView extends GBPAdminTabView
	{
	var	$statuses = array();
	function LocalisationArticleTabView( $title, $event, &$parent, $is_default = NULL )
		{
		$this->statuses = array(
			1 => gTxt('draft'),
			2 => gTxt('hidden'),
			3 => gTxt('pending'),
			4 => gTxt('live'),
			5 => gTxt('sticky'),
			);
		GBPAdminTabView::GBPAdminTabView( $title , $event , $parent , $is_default );
		}

	function preload()
		{
		$rebuild = false;
		$step = gps('step');
		if( $step )
			{
			switch( $step )
				{
				case 'clone':
					$this->clone_for_translation();
				break;

				case 'l10n_change_pageby':
					event_change_pageby('article');
				break;

				case 'delete_article':
					$this->delete_article();
				break;

				case 'delete_rendition':
					$rebuild = $this->delete_rendition();
				break;
				}
			}

		if( $rebuild )
			{
			$results = ArticleManager::check_groups();
			if( !empty( $results ) )
				{
				$desc = '';
				foreach( $results as $record )
					{
					$desc .= $record[3] . br . n;
					}
				$desc .= gTxt('l10n-table_rebuilt') . n;
				$this->parent->message = $desc;
				}
			else
				$this->parent->message = gTxt('l10n-article_table_ok');
			}
		}

	function clone_for_translation()
		{
		$has_privs = has_privs( 'l10n.clone' );
		if( !$has_privs )
			{
			//$this->parent->message( 'You cannot clone articles.' );
			return;
			}

		$vars = array( 'rendition' );
		extract( gpsa( $vars ) );

		$langs = LanguageHandler::get_site_langs();

		$clone_to = array();
		foreach( $langs as $lang )
			{
			$clone = ( $lang === gps( $lang ));
			if( $clone )
				{
				$new_author = gps( $lang.'-AuthorID' );
				$clone_to[$lang] = $new_author;
				}
			}

		if( count( $clone_to ) < 1 )
			{
			$this->parent->message = gTxt('l10n-no_langs_selected');
			$_POST['step'] = 'start_clone';
			return;
			}

		#
		#	Prepare the source rendition data...
		#
		$source = safe_row( '*' , 'textpattern' , "`ID`='$rendition'" );
		$article_id = $source['Group'];

		#
		#	Create the articles, substituting new authors and status as needed...
		#
		$notify   = array();		#	For email notices.
		foreach( $clone_to as $lang=>$new_author )
			{
			unset( $source['ID' ] );
			$source['AuthorID'] = $new_author;
			$source['Lang'] = $lang;
			$source['Status'] = 1;
			//$source['Posted'] = 'now()';	# Don't reset the time to now() as we want the articles to appear in the same order in both the cloned and original language sites.
			$source['LastMod'] = 'now()';
			$source['feed_time'] = 'now()';
			$source['uid'] = md5(uniqid(rand(),true));
			$source['comments_count'] = 0;	//	Don't clone the comment count!

			$insert = array();
			foreach( $source as $k => $v )
				{
				$v = doSlash( $v );
				if( $v === 'now()' )
					$insert[] = "`$k`= $v";
				else
					$insert[] = "`$k`='$v'";
				}
			$insert_sql = join( ', ' , $insert );

			#
			#	Insert into the master textpattern table...
			#
			safe_insert( 'textpattern' , $insert_sql );
			$rendition_id = mysql_insert_id();

			#
			#	Add this to the group (article) table...
			#
			ArticleManager::add_rendition( $article_id , $rendition_id , $lang );

			#
			#	Add into the rendition table for this lang ensuring this has the ID of the
			# just added master entry!
			#
			$insert[] = '`ID`=\''.doSlash( $rendition_id ).'\'';
			$insert_sql = join( ', ' , $insert );
			$table_name = ArticleManager::make_textpattern_name( array( 'long'=>$lang ) );
			safe_insert( $table_name , $insert_sql );

			#
			#	Now we know the article ID, store this against the author for email notification...
			#
			$language = LanguageHandler::get_native_name_of_lang( $lang );
			$notify[$new_author][$lang] = array( 'id' => $rendition_id , 'title'=>$source['Title'] , 'language'=>$language );
			}

		#
		#	Send the notifications?
		#
		$send_notifications = ( '1' == $this->pref('l10n-send_notifications') ) ? true : false;
		$notify_self = ( '1' == $this->pref('l10n-send_notice_to_self') ) ? true : false;
		if( $send_notifications )
			{
			global $sitename, $siteurl, $txp_user;

			extract(safe_row('RealName AS txp_username,email AS replyto','txp_users',"name='$txp_user'"));

			foreach( $notify as $new_user => $list )
				{
				#
				#	Skip if no articles...
				#
				$count = count( $list );
				if( $count < 1 )
					continue;

				#
				#	Skip if users are the same and no notifications are to be sent in that case...
				#
				$same = ($new_user == $txp_user);
				if( $same and !$notify_self )
					continue;

				#
				#	Construct a list of links to the renditions assigned to this user...
				#
				$links = array();
				foreach( $list as $lang => $record )
					{
					extract( $record );
					$msg = gTxt('title')  . ": \"$title\"\r\n";
					$msg.= gTxt( 'l10n-xlate_to' ) . "$language [$lang].\r\n";
					$msg.= "http://$siteurl/textpattern/index.php?event=article&step=edit&ID=$id\r\n";
					$links[] = $msg;
					}

				extract(safe_row('RealName AS new_user,email','txp_users',"name='$new_user'"));

				$s = (($count===1) ? '' : 's');

				$subs = array(	'{sitename}' => $sitename ,
								'{count}' => $count ,
								'{s}' => $s ,
								'{txp_username}' => $txp_username,
								);

				if( $same )
					$body = gTxt( 'l10n-email_body_self' , $subs );
				else
					$body = gTxt( 'l10n-email_body_other' , $subs );

				$body.= join( "\r\n" , $links ) . "\r\n" . gTxt( 'l10n-email_end' , $subs );
				$subject = gTxt( 'l10n-email_xfer_subject' , $subs );

				@txpMail($email, $subject, $body, $replyto);
				}
			}
		}
	function delete_article()
		{
		$has_privs = has_privs( 'article.delete' );
		if( !$has_privs )
			{
			//$this->parent->message( 'You cannot delete articles.' );
			return;
			}

		#
		#	Deletes an article (multiple renditions) from the DB.
		#
		$vars = array( 'article' );
		extract( gpsa( $vars ) );

		#
		#	Read the translation from the master table, extracting Group and Lang...
		#
		$renditions = safe_rows( '*' , 'textpattern' , "`Group`='$article'" );

		#
		#	Delete from the master table...
		#
		$master_deleted = safe_delete( 'textpattern' , "`Group`='$article'" );

		#
		#	Delete from the rendition tables...
		#
		foreach( $renditions as $rendition )
			{
			$lang = $rendition['Lang'];
			$rendition_table = ArticleManager::make_textpattern_name( array( 'long'=>$lang ) );
			safe_delete( $rendition_table , "`Group`='$article'" );
			}

		#
		#	Delete from the articles table...
		#
		ArticleManager::destroy_article( $article );
		}

	function delete_rendition()
		{
		$has_privs = has_privs( 'article.delete' );
		if( !$has_privs )
			{
			return false;
			}

		$vars = array( 'rendition' );
		extract( gpsa( $vars ) );

		#
		#	Read the translation from the master table, extracting Group and Lang...
		#
		$details = safe_row( '*' , 'textpattern' , "`ID`='$rendition'" );
		if( empty( $details ) )
			{
			return true;
			}

		$lang = $details['Lang'];
		$article = $details['Group'];

		#
		#	Delete from the master table...
		#
		$master_deleted = @safe_delete( 'textpattern' , "`ID`='$rendition'" );

		#
		#	Delete from the correct language rendition table...
		#
		$rendition_table = ArticleManager::make_textpattern_name( array( 'long'=>$lang ) );
		$rendition_deleted = @safe_delete( $rendition_table , "`ID`='$rendition'" );

		#
		#	Delete from the article table...
		#
		$article_updated = ArticleManager::remove_rendition( $article , $rendition , $lang );

		$result = false;
		if( $master_deleted and $rendition_deleted and $article_updated )
			$this->parent->message = gTxt( 'l10n-rendition_delete_ok' , array('{rendition}' => $rendition) );
		else
			{
			$results = ArticleManager::check_groups();
			if( !empty( $results ) )
				{
				$this->parent->message = $results[0][3];
				}
			else
				{
				$this->parent->message = 'Groups ok.';
				}
			}
		return $result;
		}

	function main()
		{
		switch ($this->event)
			{
			case 'article':
				{
				$step = gps('step');
				if( $step )
					{
					switch( $step )
						{
						case 'start_clone':
							$this->render_start_clone();
							break;

						default:
							$this->render_article_table();
							break;
						}
					}
				else
					$this->render_article_table();
				}
				break;
			}
		}


	function _apply_filter( $name , $set , $langs )
		{
		#
		#	This function works by reducing a working set, eliminating translations that don't match the criteria...
		#
		$string = gps($name);
		if( empty($string) or empty($langs) )
			return $langs;


		if( 'match_status' === $name )
			{
			#
			#	Convert to title case for the comparison...
			#
			$matches = do_list( StringHandler::convert_case( $string, MB_CASE_TITLE ) );

			#
			#	Status strings to status codes...
			#
			$sesutats = array_flip( $this->statuses );
			foreach( $matches as $key=>$status )
				{
				if( is_numeric( $status ) )
					continue;

				if( array_key_exists( $status , $sesutats ) )
					$matches[$key] = $sesutats[$status];
				}
			}
		else
			{
			#
			#	Convert names or sections to lower case for the comparison...
			#
			$matches = do_list( StringHandler::convert_case( $string, MB_CASE_LOWER ) );
			}

		#
		#	Do the comparison here...
		#
		foreach( $set as $lang=>$item )
			{
			$item = StringHandler::convert_case( $item, MB_CASE_LOWER );
			if( !in_array($item , $matches) )
				unset($langs[$lang]);
			}

		return $langs;
		}
	function _render_filter_form()
		{
		$f[] = '<label for="match_section">'.gTxt('Section').'</label>'.sp.
				fInput( /*type*/ 	'input',
						/*name*/	'match_section',
						/*value*/	gps('match_section'),
						/*class*/	'',
						/*title*/	'',
						/*onClick*/	'',
						/*size*/	'',
						/*tab*/		'1',
						/*id*/		'match_section' ).sp.n;
		$f[] = '<label for="match_author">'.gTxt('Author').'</label>'.sp.
				fInput( /*type*/ 	'input',
						/*name*/	'match_author',
						/*value*/	gps('match_author'),
						/*class*/	'',
						/*title*/	'',
						/*onClick*/	'',
						/*size*/	'',
						/*tab*/		'2',
						/*id*/		'match_author' ).sp.n;
		$f[] = '<label for="match_status">'.gTxt('Status').'</label>'.sp.
				fInput( /*type*/ 	'input',
						/*name*/	'match_status',
						/*value*/	gps('match_status'),
						/*class*/	'',
						/*title*/	'',
						/*onClick*/	'',
						/*size*/	'',
						/*tab*/		'3',
						/*id*/		'match_status' ).n;
		$f[] = $this->form_inputs().n;
		$f[] = fInput( 'submit', 'search', gTxt('go'), 'smallerbox' , '', '', '', '4' );

		return n.n.form( graf( n.join('', $f).n ).br.n , 'margin: auto; text-align: center;' );
		}
	function render_article_table()
		{
		$event = $this->parent->event;

		#
		#	Pager calculations...
		#
		extract( get_prefs() );				#	Need to do this to keep the articles/page count in sync.
		extract( gpsa(array('page')) );
		$total = ArticleManager::get_total();
		$limit = max(@$article_list_pageby, 15);
		list($page, $offset, $numPages) = pager($total, $limit, $page);

		#
		#	User permissions...
		#
		$can_delete = has_privs( 'article.delete' );
		$can_clone  = has_privs( 'l10n.clone' );

		#
		#	Init language related vars...
		#
		$langs = LanguageHandler::get_site_langs();
		$full_lang_count = count( $langs );
		$default_lang = LanguageHandler::get_site_default_lang();

		#
		#	Render the filter/search form...
		#
		$o[] = $this->_render_filter_form();

		#
		#	Start the table...
		#
		$o[] = startTable( /*id*/ 'l10n_articles_table' , /*align*/ '' , /*class*/ '' , /*padding*/ '5px' );
		$o[] = '<caption><strong>'.gTxt('l10n-renditions').'</strong></caption>';

		#
		#	Setup the colgroup/thead...
		#
		$colgroup[] = n.t.'<col id="id" />';
		$thead[] = tag( gTxt('articles') , 'th' , ' class="id"' );
		foreach( $langs as $lang )
			{
			$colgroup[] = n.t.'<col id="'.$lang.'" />';
			$name = LanguageHandler::get_native_name_of_lang($lang);

			#
			#	Default language markup -- if needed.
			#
			if( $lang === $default_lang )
				$name .= br . gTxt('default');

			$thead[] = hCell( $name );
			$counts[$lang] = 0;
			}
		$o[] = n . tag( join( '' , $colgroup ) , 'colgroup' );
		$o[] = n .  tr( join( '' , $thead ) );

		$counts['article'] = 0;		#	Initialise the article count.
		$w = '';					#	Var for td width -- set empty to skip its inclusion / other val overrides css.
		$body = array();

		#
		#	Process the articles table...
		#
		#	Use values from the pager to grab the right sections of the table.
		#
		$articles = ArticleManager::get_articles( '1=1' , 'ID DESC' , $offset , $limit );
		if( count( $articles ) )
			{
			while( $article = nextRow($articles) )
				{
				$num_visible = 0;		# 	Holds a count of visible (=Sticky/Live) translations of this article.
				$trclass = '';			#	Class for the row (=article)
				$cells = array();		#	List of table cells (=translations) in this row
				$sections = array();	#	Holds a list of the unique sections used by translations in this article.

				#
				#	Pull out the article (NB: Not translation!)...
				#
				extract( $article );
				$members = unserialize( $members );
				$n_translations_expected = count( $members );

				#
				#	Pull the translations for this article from the master translations table
				# (that is, from the textpattern table)...
				#
				$translations = safe_rows( '*' , 'textpattern' , "`Group`='$ID'" );
				$n_translations = count( $translations );
				$n_valid_translations = 0;

				#
				#	Index the translations for later use...
				#
				$index = array();
				$tr_statuses = array();
				$tr_sections = array();
				$tr_authors  = array();
				for( $i=0 ; $i < $n_translations ; $i++ )
					{
					$lang = $translations[$i]['Lang'];
					if( in_array( $lang , $langs ) )
						{
						$n_valid_translations++;
						$index[$lang] = $i;

						#
						#	Record the sections/status/authors for possible filtering...
						#
						$tr_sections[$lang] = $translations[$i]['Section'];
						$tr_statuses[$lang] = $translations[$i]['Status'];
						$tr_authors[$lang]  = $translations[$i]['AuthorID'];
						}
					else
						continue;

					#
					#	Check that the translation is recorded in the article members!
					#
					if( !array_key_exists( $lang , $members ) )
						{
						$this->parent->message = gTxt( 'l10n-missing_rendition' , array( '{id}'=>$ID ) );
						$members[$lang] = $translations[$i]['ID'];
						ArticleManager::_update_article( $ID , $names , $members );
						$n_valid_translations++;
						}
					else
						{
						$master_id = $translations[$i]['ID'];
						$rend_id   = $members[$lang];
						if( $master_id !== $members[$lang] )
							{
							//echo br , "Found incorrect rendition ID $rend_id in article table. Replacing with ID $master_id.";
							$members[$lang] = $master_id;
							ArticleManager::_update_article( $ID , $names , $members );
							$n_valid_translations++;
							}
						}
					}

				#
				#	Are all expected translations present?
				#
				$all_translations_present = ($n_valid_translations === $full_lang_count);

				#
				#	Apply filters...
				#
 				$res = $this->_apply_filter( 'match_section' , $tr_sections , $tr_sections );
				$res = $this->_apply_filter( 'match_author'  , $tr_authors  , $res );
				$res = $this->_apply_filter( 'match_status'  , $tr_statuses , $res );
				if( empty($res) )
					continue;

				#
				#	This article has at least one translation that passes the filter so increment the article count...
				#
				$counts['article']+= 1;

				#
				#	Compose the leading (article) cell...
				#
				if( $can_delete )
					$delete_art_link = '<a href="'. $this->parent->url( array('page'=>$page,'step'=>'delete_article', 'article'=>$ID), true ) .
										'" title="' . gTxt('delete') . ' ' . gTxt('article') .
										'" class="clone-link" onclick="return verify(\'' .
										doSlash(gTxt('confirm_delete_popup')) .
										'\')"><img src="txp_img/l10n_delete.png" /></a>';
				else
					$delete_art_link = '';
				$cells[] = td( $delete_art_link . $ID . br . htmlspecialchars($names) , '' , 'id' );

				#
				#	Compose the rest of the row - one cell per translation...
				#
				foreach( $langs as $lang )
					{
					if( !array_key_exists( $lang , $members ) )
						{
						if( $lang === $default_lang )
							$cells[] = td( gTxt('default') . gTxt('l10n-missing') , $w , 'warning' );
						else
							$cells[] = td( '' , $w , 'empty' );
						}
					else
						{
						#
						#	Ok, there is a translation for this language so...
						#
						$tdclass = '';
						$msg = '';
						$id = $members[$lang];

						#
						#	Get the details for this translation
						#
						$i = $index[$lang];
 						$details = $translations[$i];
						$author  = $details['AuthorID'];
						$status_no = $details['Status'];
						if( $status_no >= 4 )
							$num_visible++;

						$tdclass .= 'status_'.$status_no;
						$status = !empty($status_no) ? $this->statuses[$status_no] : '';
						if( empty($details['Title']) )
							$title = '<em>'.eLink('article', 'edit', 'ID', $id, gTxt('untitled')).'</em>';
						else
							$title = eLink('article', 'edit', 'ID', $id, $details['Title'] );

						#
						#	Check for consistency with the group data...
						#	Deprecated?
						if( $details['Lang'] != $lang )
							{
							$tdclass .= 'warning';
							$msg = br . strong( gTxt('l10n-warn_lang_mismatch') ) . br . "Art[$lang] : tsl[{$details['Lang']}]";
							}

						#
						#	Grab the section and check for consistency across the row...
						#
						$section = $details['Section'];
						$sections[$section] = $ID;

						#
						#	Make a clone link if possible...
						#
						$status_ok = ( $status_no > 2 );
						if( !$can_clone or !$status_ok or $all_translations_present )
							$clone_link = '';
						else
							$clone_link = 	'<a href="' . $this->parent->url( array('page'=>$page,'step'=>'start_clone','rendition'=>$id,'article'=>$ID), true ) .
											'" class="clone-link" title="' . gTxt('l10n-clone') . '"><img src="txp_img/l10n_clone.png" /></a>';

						#
						#	Make the delete link...
						#
						if( $can_delete )
							$delete_trans_link = 	'<a href="' . $this->parent->url( array('page'=>$page,'step'=>'delete_rendition', 'rendition'=>$id), true ) .
													'" title="' . gTxt('delete') .
													'" class="delete-link" onclick="return verify(\'' .
													doSlash(gTxt('confirm_delete_popup')) .
													'\')"><img src="txp_img/l10n_delete.png" /></a>';
						else
							$delete_trans_link = '';

						$content = 	$delete_trans_link . strong( $title ) . br . small($section . ' &#8212; ' . $author) .
									$msg . $clone_link;
						$cells[] = td( $content , $w , trim($tdclass) );
						$counts[$lang] += 1;
						}
					}


				#
				#	Tag articles which are fully visible or have warnings...
				#
				if( count( $sections ) != 1 )
					{
					$trclass .= ' warning';
					$cells[0] = td( $ID . br . gTxt('l10n-warn_section_mismatch') , $w , 'id' );
					}
				else if( $num_visible == $full_lang_count )
					{
					$trclass .= ' fully_visible';
					}
				$trclass .= (0 == ($counts['article'] & 0x01)) ? '' : ' odd';
				$trclass = trim( $trclass );
				if( !empty( $trclass ) )
					$trclass = ' class="' . $trclass . '"';
				$css_id = ' id="article_' . $ID . '"';
				$body[] = n.tr( n.join('' , $cells) , $css_id . $trclass );
				}
			}

		#
		#	Show the counts for the page...
		#
		$show_legend = ( '1' == $this->pref('l10n-show_legends') ) ? true : false;

		if( $show_legend )
			{
			$cells = array();
			$cells[] = td( $counts['article'] , '' , 'id count' );
			foreach( $langs as $lang )
				{
				$cells[] = td( $counts[$lang] , '' , 'count' );
				}
			$body[] = n.tr( n.join('' , $cells) );

			#
			#	Show the table legend...
			#
			$cells = array();
			$l[] = $this->add_legend_item( 'status_1' , $this->statuses[1] );
			$l[] = $this->add_legend_item( 'status_2' , $this->statuses[2] . '/'. gTxt('none') );
			$l[] = $this->add_legend_item( 'status_3' , $this->statuses[3] );
			$l[] = $this->add_legend_item( 'status_4' , $this->statuses[4] );
			$l[] = $this->add_legend_item( 'status_5' , $this->statuses[5] );
			$l[] = br.br;
			$l[] = $this->add_legend_item( 'fully_visible' , gTxt('l10n-legend_fully_visible') );
			$l[] = $this->add_legend_item( 'warning' , gTxt('l10n-legend_warning') );
			if( $can_delete or $can_clone )
				$l[] = br.br;
			if( $can_delete )
				{
				$l[] = t.tag( '<img src="txp_img/l10n_delete.png" />' , 'dt' ).n;
				$l[] = t.tag( gTxt('delete') , 'dd' ).n;
				}
			if( $can_clone )
				{
				$l[] = t.tag( '<img src="txp_img/l10n_clone.png" />' , 'dt' ).n;
				$l[] = t.tag( gTxt('l10n-clone') , 'dd' ).n;
				}
			$l = tag( n.join('',$l) , 'dl' );
			$cells[] = tdcs( $l , $full_lang_count+1, '' , 'legend' );
			$body[] = n.tr( n.join('' , $cells) );
			}

		$o[] = tag( join( '' , $body) , 'tbody' );
		$o[] = endTable();
		$o[] = n.nav_form( $event, $page, $numPages, '', '', '', '');
		$o[] = n.pageby_form( $event, $article_list_pageby );

		echo join( '' , $o );
		}

	function render_start_clone()
		{
		$vars = array( 'rendition' , 'page' );
		extract( gpsa( $vars ) );

		#
		#	Get the un-translated languages for the article that owns this rendition...
		#
		$details = safe_row( '*' , 'textpattern' , "`ID`='$rendition'" );
		$title   = $details['Title'];
		$article = $details['Group'];
		$author  = $details['AuthorID'];
		$to_do = ArticleManager::get_remaining_langs( $article );
		$count = count( $to_do );

		#
		#	Get the list of possible authors...
		#
		$assign_authors = false;
		$authors = safe_column('name', 'txp_users', "privs not in(0,6)");
		if( $authors )
			{
			$assign_authors = true;
			}

		$o[] = startTable( /*id*/ 'l10n_clone_table' , /*align*/ '' , /*class*/ '' , /*padding*/ '5px' );
		$o[] = '<caption><strong>'.gTxt('l10n-clone_and_translate' , array( '{article}'=>$title ) ).'</strong></caption>';

		#
		#	If there is only one available unused language, check it by default.
		#
		$checked = '';
		if( $count === 1 )
			{
			$checked = 'checked';
			}

		#
		#	Build the thead...
		#
		$thead[] = hCell( gTxt('l10n-into').'&#8230;' );
		$thead[] = hCell( gTxt('l10n-by').'&#8230;' );
		$o[] = n .  tr( join( '' , $thead ) );

		#
		#	Build the clone selection form...
		#
		foreach( $to_do as $lang=>$name )
			{
			$r = td(	'<input type="checkbox" class="checkbox" '.$checked.' value="'.$lang.'" name="'.$lang.'" id="'.$lang.'"/>' .
						'<label for="'.$lang.'">'.$name.'</label>' );
			$r .= td( stripslashes(selectInput($lang.'-AuthorID' , $authors , $author , false )) );
			$f[] =	tr( $r );
			}

		#
		#	Submit and hidden entries...
		#
		$s = '<input type="submit" value="'.gTxt('l10n-clone').'" class="publish" />' . n;
		$s .= eInput( $this->parent->event );
		$s .= sInput( 'clone' );
		$s .= hInput( 'rendition' , $rendition );
		$s .= hInput( 'page' , $page );

		$f[] = tr( tdrs( $s , 2 ) );

		$o[] = tag( form( join( '' , $f )) , 'tbody' );
		$o[] = endTable();

		echo join( '' , $o );
		}

	function add_legend_item( $id , $text )
		{
		$r[] = t.tag( '&#160;' , 'dt' , " class=\"$id\"" ).n;
		$r[] = t.tag( $text , 'dd' ).n;
		return join( '' , $r );
		}

	}


class LocalisationWizardView extends GBPWizardTabView
	{
 	var $installation_steps = array(
		'1' => array(
			'setup' => 'Extend the txp_lang.data field from TINYTEXT to TEXT and add the \'owner\' field.',
			'cleanup' => 'Drop the \'owner\' field from the txp_lang table.'	),
		'2' => array(
			'setup' => 'Insert the strings for this plugin',
			'cleanup' => 'Remove all MLP strings and unregister plugins'),
		'3' => array(
			'setup' => 'Add `Lang` and `Group` fields to the textpattern table'),
		'3a'=> array(
			'cleanup' => 'Drop the `Lang` and `Group` fields from the textpattern table.<br/>Check this if you do not want to re-install the MLP Pack.', 'optional' => true , 'checked'=>0 ),
		'4' => array(
			'setup' => 'Localise fields in content tables',
			),
		'4a' => array(
			'cleanup' => 'Drop localised content fields.<br/>Remove localised titles/descriptions etc&#8230;', 'optional'=>true, 'checked'=>0
			),
		'5' => array(
			'setup' => 'Add the l10n_articles table',
			'cleanup' => 'Drop the l10n_articles table'),
		'6' => array(
			'setup' => 'Process existing articles' ),
		'7' => array(
			'setup' => 'Add new l10n_textpattern tables for each site language',
			'cleanup' => 'Drop the extra l10n_textpattern tables'),
		'8' => array (
			'cleanup' => 'Delete cookies' ),
		'10' =>array (
			'setup' => 'Clear the default comment invitation.',
			'cleanup' => 'Restore the default comment invitation.'
			),
		);

	function installed()
		{
		$result = getThing( 'show tables like \''.PFX.L10N_ARTICLES_TABLE.'\'' );
		return ($result);
		}

	function get_required_versions()
		{
		global $prefs;

		$tests = array(
					'TxP' => array(
						'current'	=> $prefs['version'] ,
						'min'		=> '4.0.4' ,
						),
					'PHP' => array(
						'current'	=> PHP_VERSION ,
						'min'		=> '4.1.0' ,
						),
					'MySQL'  => array(
						'current'	=> mysql_get_server_info() ,
						'min'		=> '4.0' ,
						),
					);
		return $tests;
		}

	function cleanup_4a()
		{
		$this->add_report_item( 'Remove Localised content from tables...' );
		l10n_mappings_walker( array( &$this , 'cleanup_4a_cb' ) );
		}
	function cleanup_4a_cb( $table , $field , $attributes )
		{
		$langs = LanguageHandler::get_site_langs();
		foreach( $langs as $lang )
			{
			$f = "$lang-$field";
			$sql = "DROP `$f`";
			$ok = @safe_alter( $table , $sql );
			$this->add_report_item( "Drop the $table.$lang-$field field" , $ok , true );
			}
		}
	function setup_4_cb( $table , $field , $attributes )
		{
		$langs = LanguageHandler::get_site_langs();
		$default = LanguageHandler::get_site_default_lang();
		foreach( $langs as $lang )
			{
			$f = "$lang-$field";
			$exists = getThing( "SHOW COLUMNS FROM $table LIKE '$f'" );
			if( $exists )
				{
				$this->add_report_item( "Skipped the $table.$lang-$field field, it already exists" , true , true );
				continue;
				}

			$sql = "ADD `$f` ".$attributes['sql'];
			$ok = @safe_alter( $table , $sql );
			$this->add_report_item( "Added the $table.$f field" , $ok , true );

			if( $ok && $lang===$default )
				{
				$sql = "UPDATE $table SET `$f`=`$field`";
				$ok = @safe_query( $sql );
				$this->add_report_item( "Copy defaults to $f field" , $ok , true );
				}
			}
		}
	function setup_4()
		{
		$this->add_report_item( 'Localise the content tables...' );
		l10n_mappings_walker( array( &$this , 'setup_4_cb' ) );
		}
	function setup_1()
		{
		$this->add_report_item( 'Change the txp_lang table...' );

		# Extend the txp_lang table to allow text instead of tinytext in the data field.
		$sql = ' CHANGE `data` `data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
		$ok = @safe_alter( 'txp_lang' , $sql );
		$this->add_report_item( 'Extend the txp_lang.data field from TINYTEXT to TEXT' , $ok , true );

		$sql = "ADD `owner` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `data`";
		$ok = @safe_alter( 'txp_lang' , $sql );
		$this->add_report_item( 'Add the \'owner\' field.' , $ok , true );
		}

	function cleanup_1()
		{
		$sql = "DROP `owner`";
		$ok = @safe_alter( 'txp_lang' , $sql );
		$this->add_report_item( 'Delete the \'owner\' field.' , $ok );
		}

	function setup_2()
		{
		# Adds the strings this class needs. These lines makes them editable via the "plugins" string tab.
		# Make sure we only call insert_strings() once!
		$this->parent->strings = array_merge( $this->parent->strings , $this->parent->perm_strings );
		$ok = StringHandler::insert_strings( $this->parent->strings_prefix , $this->parent->strings , $this->parent->strings_lang , 'admin' , 'l10n' );
		$this->add_report_item( 'Insert the strings for this plugin' , $ok );
		}

	function setup_3()
		{
		# Extend the textpattern table...
		$sql = array();

		$desc = 'COLUMNS';
		$result = @safe_show( $desc , 'textpattern' );
		$lang_found  = false;
		$article_id_found = false;

		if( count( $result ) )
			{
			foreach( $result as $r )
				{
				if( !$lang_found and $r['Field'] === 'Lang' )
					$lang_found = true;
				if( !$article_id_found and $r['Field'] === 'Group' )
					$article_id_found = true;

				if( $article_id_found and $lang_found )
					break;
				}
			}

		if( !$lang_found )
			{
			$sql[] = " ADD `Lang` VARCHAR( 8 ) CHARACTER SET utf8 COLLATE utf8_general_ci ";
			$sql[] = " NOT NULL DEFAULT '-' AFTER `LastModID` , ";
			}

		if( !$article_id_found )
			$sql[] = " ADD `Group` INT( 11 ) NOT NULL DEFAULT '0' AFTER `Lang`";

		$this->add_report_item( 'Add fields to the "textpattern" table' );
		if( !empty( $sql ) )
			{
			$ok = @safe_alter( 'textpattern' , join('', $sql) );

			if( $lang_found )
				$this->add_report_item( 'Skip adding the `Lang` field -- it already exists' , $ok , true );
			else
				$this->add_report_item( 'Add the `Lang` field' , $ok , true );
			if( $article_id_found )
				$this->add_report_item( 'Skip adding the `Group` field -- it already exists' , $ok , true );
			else
				$this->add_report_item( 'Add the `Group` field' , $ok , true );
			}
		else
			$this->add_report_item( 'Skip adding `Lang` and `Group` fields, they already exist.' , true , true );
		}

	function setup_5()
		{
		$ok = ArticleManager::create_table();
		$this->add_report_item( 'Add the "'.L10N_ARTICLES_TABLE.'" table' , $ok );
		}

	function setup_6()
		{
		# Run the import routine selected by the user from the install wizard tab...
		$ok = $this->_import_fixed_lang();
		$this->add_report_item( ($ok===true)?'Process all articles':"Process $ok articles "  , true );
		}

	function setup_7()
		{
		# Create the first instances of the language tables as straight copies of the existing
		# textpattern table so users on the public side still see everything until we start editing
		# articles.
		$langs = $this->pref('l10n-languages');
		$this->add_report_item( 'Add the language native textpattern tables' );
		foreach( $langs as $lang )
			{
			$code  = LanguageHandler::compact_code( $lang );
			$table_name = ArticleManager::make_textpattern_name( $code );
			$indexes = "(PRIMARY KEY  (`ID`), KEY `categories_idx` (`Category1`(10),`Category2`(10)), KEY `Posted` (`Posted`), FULLTEXT KEY `searching` (`Title`,`Body`))";
			$sql = "create table `".PFX."$table_name` $indexes select * from `".PFX."textpattern` where `Lang`='$lang'";
			$ok = @safe_query( $sql );
			$this->add_report_item( 'Add the '. LanguageHandler::get_native_name_of_lang( $lang ) .' ['.$table_name.'] table' , $ok , true );
			}
		}

	function setup_10()
		{
		$default = @$GLOBALS['prefs']['comments_default_invite'];
		$ok = set_pref( 'comments_default_invite', '', 'comments', 0 );
		$this->add_report_item( 'Clear the default comment invitation.' , $ok );
		}

	function cleanup_10()
		{
		$default = gTxt('comment');
		$ok = set_pref( 'comments_default_invite', $default, 'comments', 0 );
		$this->add_report_item( 'Restore the default comment invitation.' , $ok );
		}

	function cleanup_2()
		{
		# Remove the l10n strings...
		$this->add_report_item( 'Cleaning up all l10n strings and plugin registrations' );
		$this->parent->strings = array_merge( $this->parent->strings , $this->parent->perm_strings );
		$ok = StringHandler::remove_strings_by_name( $this->parent->strings , 'admin' , 'l10n' );
		$this->add_report_item( ($ok===true)?'Remove plugin strings':"Removed $ok strings" , true , true );

		$rps = StringHandler::discover_registered_plugins();
		if( count($rps) )
			{
			foreach($rps as $name=>$vals)
				{
				if( !is_array( $vals ) )
					continue;

				$ok = StringHandler::unregister_plugin( $name );
				$this->add_report_item( "Unregistered plugin '$name'." , $ok , true );
				}
			}
		}

	function cleanup_3a()
		{
		$sql = "drop `Lang`, drop `Group`";
		$ok = @safe_alter( 'textpattern' , $sql );
		$this->add_report_item( 'Drop the `Lang` and `Group` fields from the textpattern table' , $ok );
		}

	function cleanup_5()
		{
		$ok = ArticleManager::destroy_table();
		$this->add_report_item( 'Delete the "'.L10N_ARTICLES_TABLE.'" table' , $ok );
		}

	function cleanup_7()
		{
		# Drop the per-language textpattern_XX tables...
		global $prefs;
		$langs = $this->pref('l10n-languages');
		$this->add_report_item( 'Drop the language native textpattern tables&#8230;' );
		foreach( $langs as $lang )
			{
			$code  = LanguageHandler::compact_code( $lang );
			$table_name = ArticleManager::make_textpattern_name( $code );
			$sql = 'drop table `'.PFX.$table_name.'`';
			$ok = @safe_query( $sql );
			$this->add_report_item( 'Drop the '. LanguageHandler::get_native_name_of_lang( $lang ) .' ['.$table_name.'] table' , $ok , true );
			}
		}

	function cleanup_8()
		{
		$langs = $this->pref('l10n-languages');
		$this->add_report_item( 'Delete the cookies&#8230;' );
		foreach( $langs as $lang )
			{
			$lang = trim( $lang );
			$time = time() - 3600;
			$ok = setcookie( $lang , $lang , $time );
			$this->add_report_item( 'Delete the '. LanguageHandler::get_native_name_of_lang( $lang ) . ' cookie' , $ok , true );
			}
		}

	function _import_fixed_lang()
		{
		# 	Scans the articles, creating a group for each and adding it and setting the
		# language to the site default...
		$where = "1";
		$rs = @safe_rows_start( 'ID , Title , Lang , `Group`' , 'textpattern' , $where );
		$count = @mysql_num_rows($rs);

		$i = 0;
		if( $rs && $count > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				$title = $a['Title'];
				$lang  = $a['Lang'];
				$article_id = $a['Group'];
				$id    = $a['ID'];

				if( $lang !== '-' and $article_id !== 0 )
					{
					#
					#	Use any existing Lang/Group data there might be...
					#
					if( true === ArticleManager::add_rendition( $article_id , $id , $lang , true , true , $title ) )
						$i++;
					}
				else
					{
					#
					#	Create a fresh group and add the info...
					#
					if( ArticleManager::create_article_and_add( $a ) )
						$i++;
					}
				}
			}
		if( $i === $count )
			return true;

		return "$i of $count";
		}

	}

class LanguageHandler
	{
	/*
	class LanguageHandler implements ISO-693-1 language support.
	*/
	function do_fleshout_names( &$langs , $suffix='' , $append_code = true , $append_default=false , $use_long=true )
		{
		$result = array();
		if( is_array($langs) and !empty($langs) )
			{
			foreach( $langs as $code )
				{
				$code = trim( $code );
				$tmp = LanguageHandler::get_native_name_of_lang( $code );
				if( $append_code )
					$tmp .= ' [' . $code . ']';
				if( !$use_long )
					$code = substr( $code , 0 , 2 );
				if( !empty( $suffix ) )
					$tmp .= ' ' . $suffix;
				if( $append_default and ($code === LanguageHandler::get_site_default_lang() ) )
					$tmp .= ' - ' . gTxt('default');
				$result[$code] = $tmp;
				}
			}
		return $result;
		}
	function do_fleshout_dirs( &$langs )
		{
		$result = array();
		if( is_array($langs) and !empty($langs) )
			{
			foreach( $langs as $code )
				{
				$code = trim( $code );
				$tmp = LanguageHandler::get_lang_direction( $code );
				$result[$code] = $tmp;
				}
			}
		return $result;
		}

	function compact_code( $long_code )
		{
		/*
		Pull apart a long form language code into components.
		Output = {short , COUNTRY , [long]}	So, en-gb=> {en , GB , en-gb}
		*/

		# Cache the results as they are probably going to get used many times per tab...
		static $code_mappings;
		if( !is_string( $long_code ) )
			{
			echo br , 'compact_code( ' , var_dump( $long_code ) , ').';
			trigger_error( 'Invalid type passed to LanguageHandler::compact_code()', E_USER_ERROR);
			}

		$long_code = trim( $long_code );
		if( isset( $code_mappings[$long_code] ) )
			return $code_mappings[$long_code];

		$result = array();
		$result['short'] 	= @substr( $long_code , 0 , 2 );
		$result['country']  = @substr( $long_code , 3 , 2 );
		$result['long'] = '';

		if( isset( $result['country'] ) and (2 == strlen($result['country'])) )
			$result['long'] = $long_code;

		if( isset( $result['country'] ) )
			$result['country'] = strtoupper( $result['country'] );

		$code_mappings[$long_code] = $result;
		return $result;
		}

	function expand_code( $short_code )
		{
		$result = array();
		$short_code = trim( $short_code );
		$langs = LanguageHandler::get_site_langs();
		foreach( $langs as $code )
			{
			$code = trim( $code );
			$r = LanguageHandler::compact_code( $code );
			if( $short_code === $r['short'] )
				$result[] = $code;
			}
		if( count( $result ) )
			return $result[0];
		return NULL;
		}

	function iso_693_1_langs ( $input, $to_return='lang' )
		{
		static $iso_693_1_langs = array(
			'aa'=>array( 'aa'=>'Afaraf' ),
			'ab'=>array( 'ab'=>'аҧсуа бызшәа' ),
			'af'=>array( 'af'=>'Afrikaans' ),
			'am'=>array( 'am'=>'አማርኛ' ),
			'ar'=>array( 'ar'=>'العربية' , 'ar-dz'=>'جزائري عربي' , 'dir'=>'rtl' ),
			'as'=>array( 'as'=>'অসমীয়া' ),
			'ay'=>array( 'ay'=>'Aymar aru' ),
			'az'=>array( 'az'=>'Azərbaycan dili' ),
			'ba'=>array( 'ba'=>'башҡорт теле' ),
			'be'=>array( 'be'=>'Беларуская мова' ),
			'bg'=>array( 'bg'=>'Български' ),
			'bh'=>array( 'bh'=>'भोजपुरी' ),
			'bi'=>array( 'bi'=>'Bislama' ),
			'bn'=>array( 'bn'=>'বাংলা' ),
			'bo'=>array( 'bo'=>'Bod Skad' ) ,
			'br'=>array( 'br'=>'ar Brezhoneg' ) ,
			'ca'=>array( 'ca'=>'Català' ) ,
			'co'=>array( 'co'=>'Corsu' ) ,
			'cs'=>array( 'cs'=>'Čeština' ) ,
			'cy'=>array( 'cy'=>'Cymraeg' ) ,
			'da'=>array( 'da'=>'Dansk' ) ,
			'de'=>array( 'de'=>'Deutsch' ) ,
			'dz'=>array( 'dz'=>'Dzongkha' ) ,
			'el'=>array( 'el'=>'Ελληνικά' ) ,
			'en'=>array( 'en'=>'English' , 'en-gb'=>'English (GB)' , 'en-us'=>'English (US)' ),
			'eo'=>array( 'eo'=>'Esperanto' ),
			'es'=>array( 'es'=>'Español' ),
			'et'=>array( 'et'=>'Eesti Keel' ),
			'eu'=>array( 'eu'=>'Euskera' ),
			'fa'=>array( 'fa'=>'Fārsī' ),
			'fi'=>array( 'fi'=>'Suomi' ),
			'fj'=>array( 'fj'=>'vaka-Viti' ),
			'fo'=>array( 'fo'=>'Føroyska' ),
			'fr'=>array( 'fr'=>'Français' ),
			'fy'=>array( 'fy'=>'Frysk' ),
			'ga'=>array( 'ga'=>'Gaeilge' ),
			'gd'=>array( 'gd'=>'Gàidhlig' ),
			'gl'=>array( 'gl'=>'Galego' ),
			'gn'=>array( 'gn'=>"Avañe'ẽ" ),
			'gu'=>array( 'gu'=>'ગુજરાતી' ),
			'ha'=>array( 'ha'=>'حَوْسَ حَرْش۪' , 'dir'=>'rtl' ),
			'he'=>array( 'he'=>'עִבְרִית' ,'dir'=>'rtl' ),
			'hi'=>array( 'hi'=>'हिन्दी' ),
			'hr'=>array( 'hr'=>'Hrvatski' ),
			'hu'=>array( 'hu'=>'Magyar' ),
			'hy'=>array( 'hy'=>'Հայերէն' ),
			'ia'=>array( 'ia'=>'Interlingua' ),
			'id'=>array( 'id'=>'Bahasa Indonesia' ),
			'ie'=>array( 'ie'=>'Interlingue' ),
			'ik'=>array( 'ik'=>'Iñupiak' ),
			'is'=>array( 'is'=>'Íslenska' ),
			'it'=>array( 'it'=>'Italiano' ),
			'iu'=>array( 'iu'=>'ᐃᓄᒃᑎᑐᑦ' ),
			'ja'=>array( 'ja'=>'日本語' ),
			'jw'=>array( 'jw'=>'basa Jawa' ),
			'ka'=>array( 'ka'=>'ქართული' ),
			'kk'=>array( 'kk'=>'Қазақ' ),
			'kl'=>array( 'kl'=>'Kalaallisut' ),
			'km'=>array( 'km'=>'ភាសាខ្មែរ' ),
			'kn'=>array( 'kn'=>'ಕನ್ನಡ' ),
			'ko'=>array( 'ko'=>'한국어' ),
			'ks'=>array( 'ks'=>'काऽशुर' ),
			'ku'=>array( 'ku'=>'Kurdí' ),
			'ky'=>array( 'ky'=>'Кыргызча' ),
			'la'=>array( 'la'=>'Latine' ),
			'ln'=>array( 'ln'=>'lokótá ya lingála' ),
			'lo'=>array( 'lo'=>'ລາວ' ),
			'lt'=>array( 'lt'=>'Lietuvių Kalba' ),
			'lv'=>array( 'lv'=>'Latviešu' ),
			'mg'=>array( 'mg'=>'Malagasy fiteny' ),
			'mi'=>array( 'mi'=>'te Reo Māori' ),
			'mk'=>array( 'mk'=>'Македонски' ),
			'ml'=>array( 'ml'=>'മലയാളം' ),
			'mn'=>array( 'mn'=>'Монгол' ),
			'mo'=>array( 'mo'=>'лимба молдовеняскэ' ),
			'mr'=>array( 'mr'=>'मराठी' ),
			'ms'=>array( 'ms'=>'Bahasa Melayu' ),
			'mt'=>array( 'mt'=>'Malti' ),
			'my'=>array( 'my'=>'ဗမာစကား' ),
			'na'=>array( 'na'=>'Ekakairũ Naoero' ),
			'ne'=>array( 'ne'=>'नेपाली' ),
			'nl'=>array( 'nl'=>'Nederlands' ),
			'no'=>array( 'no'=>'Norsk' ),
			'oc'=>array( 'oc'=>'lenga occitana' ),
			'om'=>array( 'om'=>'Afaan Oromo' ),
			'or'=>array( 'or'=>'ଓଡ଼ିଆ' ),
			'pa'=>array( 'pa'=>'ਪੰਜਾਬੀ' ),
			'pl'=>array( 'pl'=>'Polski' ),
			'ps'=>array( 'ps'=>'پښتو' , 'dir'=>'rtl' ),
			'pt'=>array( 'pt'=>'Português' ),
			'qu'=>array( 'qu'=>'Runa Simi/Kichwa' ),
			'rm'=>array( 'en'=>'Rhaeto-Romance' ),
			'rn'=>array( 'rn'=>'Kirundi' ),
			'ro'=>array( 'ro'=>'Română' ),
			'ru'=>array( 'ru'=>'Русский' ),
			'rw'=>array( 'rw'=>'Kinyarwandi' ),
			'sa'=>array( 'sa'=>'संस्कृतम्' ),
			'sd'=>array( 'sd'=>'سنڌي' , 'dir'=>'rtl' ),
			'sg'=>array( 'sg'=>'yângâ tî sängö' ),
			'sh'=>array( 'sh'=>'Српскохрватски' ),
			'si'=>array( 'si'=>'(siṁhala bʰāṣāva)' ),
			'sk'=>array( 'sk'=>'Slovenčina' ),
			'sl'=>array( 'sl'=>'Slovenščina' ),
			'sm'=>array( 'sm'=>"gagana fa'a Samoa" ),
			'sn'=>array( 'sn'=>'chiShona' ),
			'so'=>array( 'so'=>'af Soomaali' ),
			'sq'=>array( 'sq'=>'Shqip' ),
			'sr'=>array( 'sr'=>'Srpski' ),
			'ss'=>array( 'ss'=>'siSwati' ),
			'st'=>array( 'st'=>'seSotho' ),
			'su'=>array( 'su'=>'basa Sunda' ),
			'sv'=>array( 'sv'=>'Svenska' ),
			'sw'=>array( 'sw'=>'Kiswahili' ),
			'ta'=>array( 'ta'=>'தமிழ்' ),
			'te'=>array( 'te'=>'తెలుగు' ),
			'tg'=>array( 'tg'=>'زبان تاجکی' , 'dir'=>'rtl' ),
			'th'=>array( 'th'=>'ภาษาไทย' ),
			'ti'=>array( 'ti'=>'ትግርኛ' ),
			'tk'=>array( 'tk'=>'Türkmençe' ),
			'tl'=>array( 'tl'=>'Tagalog' ),
			'tn'=>array( 'tn'=>'Setswana' ),
			'to'=>array( 'to'=>'Faka-Tonga' ),
			'tr'=>array( 'tr'=>'Türkçe' ),
			'ts'=>array( 'ts'=>'xiTsonga' ),
			'tt'=>array( 'tt'=>'تاتارچا' , 'dir'=>'rtl' ),
			'tw'=>array( 'tw'=>'Twi' ),
			'ug'=>array( 'ug'=>'uyghur tili' ),
			'uk'=>array( 'uk'=>"Українська" ),
			'ur'=>array( 'ur'=>'اردو', 'dir'=>'rtl' ),
			'uz'=>array( 'uz'=>"Ўзбек (o'zbek)" ),
			'vi'=>array( 'vi'=>'Tiếng Việt' ),
			'vo'=>array( 'vo'=>"vad'd'a tšeel" ),
			'wo'=>array( 'wo'=>'Wollof' ),
			'xh'=>array( 'xh'=>'isiXhosa' ),
			'yi'=>array( 'yi'=>'ײִדיש' , 'dir'=>'rtl' ),
			'yo'=>array( 'yo'=>'Yorùbá' ),
			'za'=>array( 'za'=>'Sawcuengh' ),
			'zh'=>array( 'zh'=>'中文(简体)' , 'zh-cn'=>'中文(简体)' , 'zh-tw'=>'中文(國語)'  ),
			'zu'=>array( 'zu'=>'isiZulu' ),
			);

		switch ( $to_return )
			{
			default:
			case 'lang':
				$r = LanguageHandler::compact_code( $input );
				$short = $r['short'];
				if( isset($r['long']) ) $long = $r['long'];

				if( !array_key_exists( $short , $iso_693_1_langs ))
					return NULL;

				$row = $iso_693_1_langs[$short];

				if( isset( $long ) )
					{
					#	Try getting the language name for the long code...
					if( array_key_exists( $long , $row ) )
						return $row[$long];
					}

				# Fall back to the default entry for the short code...
				return $row[$short];
			break;

			case 'long2short':
				$r = LanguageHandler::compact_code( $input );
				return $r['short'];
			break;

			case 'short2long':
				return LanguageHandler::expand_code( $input );
			break;

			case 'dir':
				extract( LanguageHandler::compact_code( $input ) );
				return (array_key_exists( $short, $iso_693_1_langs ) and array_key_exists('dir', $iso_693_1_langs[$short]))
					?	$iso_693_1_langs[$short]['dir']
					:	NULL;
			break;

			case 'code':
				foreach( $iso_693_1_langs as $code => $data )
					{
					if( in_array( $input , $data ) )
						{
						return $code;
						}
					}
				return NULL;
			break;
			}
		}

	function is_valid_code($code)
		{
		/*
		Check the given string is a valid language code.
		*/
		$lang = LanguageHandler::compact_code( $code );
		$short = $lang['short'];
		if( isset( $short ) )
			return LanguageHandler::is_valid_short_code($short);

		return false;
		}

	function is_valid_short_code($code)
		{
		/*
		Check the given string is a valid 2-digit language code from the ISO-693-1 table.
		*/
		$result = false;
		$code = trim( $code );
		if( 2 == strlen( $code ) )
			{
			$result = ( LanguageHandler::iso_693_1_langs( $code ) );
			}
		return $result;
		}

	function find_code_for_lang( $name )
		{
		/*
		Returns the ISO-693-1 code for the given native language.
		*/
		$out = '';

		if( $name and !empty( $name ) )
			{
			$out = LanguageHandler::iso_693_1_langs( $name, 'code' );
			}

		if (empty($out))
			$out = gTxt( 'none' );

		return $out;
		}

	function get_lang_direction_markup( $lang )
		{
		/*
		Builds the xhtml direction markup needed based upon the directionality of the language requested.
		*/
		$dir = ' dir="ltr"';
		if( !empty($lang) and ('rtl' == LanguageHandler::iso_693_1_langs( $lang, 'dir' ) ) )
			$dir = ' dir="rtl"';
		return $dir;
		}

	function get_lang_direction( $lang )
		{
		/*
		Builds the xhtml direction markup needed based upon the directionality of the language requested.
		*/
		$dir = 'ltr';
		if( !empty($lang) and ('rtl' == LanguageHandler::iso_693_1_langs( $lang, 'dir' ) ) )
			$dir = 'rtl';
		return $dir;
		}

	function get_native_name_of_lang( $code )
		{
		/*
		Returns the native name of the given language code.
		*/
		return (LanguageHandler::iso_693_1_langs( $code )) ? LanguageHandler::iso_693_1_langs( $code ) : LanguageHandler::iso_693_1_langs( 'en' ) ;
		}

	function get_site_langs( $set_if_empty = true )
		{
		/*
		Returns an array of the languages the public site supports.
		*/
		global $prefs;

		$exists = array_key_exists(L10N_PREFS_LANGUAGES, $prefs);
		if( $set_if_empty and !$exists )
			{
			$prefs[L10N_PREFS_LANGUAGES] = array( LANG );
			$exists = true;
			}

		if( $exists )
			{
			$lang_codes = $prefs[L10N_PREFS_LANGUAGES];
			if( !is_array( $lang_codes ) )
				{
				$lang_codes = explode( ',' , $lang_codes );
				}
			$lang_codes = doArray( $lang_codes , 'trim' );
			}
		else
			$lang_codes = NULL;

		return $lang_codes;
		}

	function get_site_default_lang()
		{
		/*
		Returns a string containing the ISO-693-1 language to be used as the site's default.
		*/
		$lang_codes = LanguageHandler::get_site_langs();
		return $lang_codes[0];
		}
	function get_installation_langs( $limit = 400 )
		{
		/*
		Returns an array of all the languages in this TXP installation with more
		than the limit number of strings in that lang...
		*/
		$installation_langs = array();
		$langs = safe_column('lang','txp_lang',"1=1 GROUP BY 'lang'");
		foreach( $langs as $lang )
			{
			$count = safe_count( 'txp_lang' , "`lang`='$lang'" );
			if( $count >= $limit )
				$installation_langs[] = $lang;
			}
		unset( $langs );

		return $installation_langs;
		}

	}

class SnippetHandler
	{
	/*
	class SnippetHandler implements localised "snippets" within page and
	form templates. Uses the services of the string_handler to localise the
	strings therein.
	*/

	function get_special_snippets()
		{
		return array('snip-site_slogan');
		}
	function get_pattern( $name )
		{
		# Use the first snippet detection pattern for a simple snippet format that is visible when the substitution fails.
		# Use the second snippet detection pattern if you want unmatched snippets as xhtml comments.
		static $snippet_pattern = "/##([\w|\.|\-]+)##/";

		# The following pattern is used to match any l10n_snippet tags in pages and forms.
		static $snippet_tag_pattern = "/\<txp:text item=\"([\w|\.|\-]+)\"\s*\/\>/";

		# The following are the localise tag pattern(s)...
		static $tag_pattern = '/\<\/*txp:l10n_localise(\w|\=|\"|\-|\_|\'|\s)*\>/';

		switch( $name )
			{
			case 'snippet' :
				return $snippet_pattern;
			break;
			case 'tag_localise':
				return $tag_pattern;
			break;
			default :
			case 'snippet_tag' :
				return $snippet_tag_pattern;
			break;
			}
		}

	function substitute_snippets( &$thing )
		{
		/*
		Replaces all snippets within the contained block with their text from the global textarray.
		Allows TxP devs to include snippets* in their forms and page templates.
		*/
		$out = preg_replace_callback( 	SnippetHandler::get_pattern('snippet') ,
										create_function(
							           '$match',
								       'global $l10n_language;
										global $textarray;
										if( $l10n_language )
											$lang = $l10n_language[\'long\'];
										else
											$lang = "??";
										$snippet = strtolower($match[1]);
										if( array_key_exists( $snippet , $textarray ) )
											$out = $textarray[$snippet];
										else
											$out = "($lang)$snippet";
										return $out;'
									), $thing );
		return $out;
		}

	function has_localisation_tags( &$thing )
		{
		$p = SnippetHandler::get_pattern( 'tag_localise' );
		$i = 0;
		$matches = array();
		$r = preg_match_all( $p , $thing , $matches );
		if( $r !== false )
			$i += $r;
		return ($i > 1);
		}

	function do_localise( &$thing , $action = 'check' )
		{
		if( !$thing or empty( $thing ) )
			return NULL;
		switch( $action )
			{
			case 'remove' :
			$count = 0;
			$p = SnippetHandler::get_pattern( 'tag_localise' );
			$thing = trim( preg_replace( $p , '' , $thing , -1 , $count ) );
			return $count;
			break;

			case 'insert' :
				$subtab = gps('subtab');
				if( $subtab === 'page' )
					return '<txp:l10n_localise page="true">'.$thing.'</txp:l10n_localise>';
				else
					return '<txp:l10n_localise>'.$thing.'</txp:l10n_localise>';
				break;

			default:
			case 'check':
			return SnippetHandler::has_localisation_tags( $thing );
			break;
			}
		}

	function find_snippets_in_block( &$thing , &$raw_snippet_count , $merge = false , $get_data = false )
		{
		/*
		Scans the given block ($thing) for snippets and returns their names as the values of an array.
		If merge is true then these values are expanded with txp_lang data
		*/
		$out = array();
		$tags = array();

		# Match all directly included snippets...
		preg_match_all( SnippetHandler::get_pattern('snippet') , $thing , $out );
		# Match all snippets included as txp tags...
		preg_match_all( SnippetHandler::get_pattern('snippet_tag') , $thing , $tags );

		#	cleanup and trim the output arrays a little...
		array_shift( $out );
		$out = $out[0];
		$out = doArray( $out , 'strtolower' );
		$raw_snippet_count = count( $out );
		array_shift( $tags );
		$tags = $tags[0];
		$tags = doArray( $tags , 'strtolower' );
		$out = array_merge( $out , $tags );
		unset($tags);

		if( $merge and count($out) )
			{
			#	Enlarge the array with details of any txp_lang entries that match that snippet name.
			$temp = array();
			foreach( $out as $name )
				{
				#	Load details of named entries...
				$rs = safe_rows_start(
					'id , lang , data , lastmod',
					'txp_lang',
					"`name`='" . doSlash($name) . "' AND `event`='snippet'" );

				if( $rs and mysql_num_rows($rs) > 0 )
					{
					while( $a = nextRow($rs) )
						{
						$lng = $a['lang'];
						$temp[$name][$lng]['id'] 		= $a['id'];
						$temp[$name][$lng]['lastmod'] 	= $a['lastmod'];
						if( $get_data )
							$temp[$name][$lng]['data'] 		= $a['data'];
						}
					}
				else
					{
					$temp[$name] = NULL;
					}
				}
			$out = &$temp;
			}

		return $out;
		}

	function get_snippet_strings( $names , &$stats )
		{
		$result = array();

		if( !is_array( $names ) )
			$names = array( $names );

		$name_set = '';
		foreach( $names as $name )
			{
			$name_set .= "'$name', ";
			$result[$name] = '';
			}
		$name_set = rtrim( $name_set , ', ' );
		if ( empty( $name_set ) )
			$name_set = "''";

		$where = " `name` IN ($name_set)";
		$rs = safe_rows_start( 'lang, name, owner', 'txp_lang', $where );

		return array_merge( $result , StringHandler::get_strings( $rs , $stats ) );
		}
	}

class StringHandler
	{
	function convert_case( $string , $convert = MB_CASE_TITLE )
		{
		static $exists;

		$exists = function_exists('mb_convert_case');

		$result = $string;

		if( $exists )
			$result = mb_convert_case( $result, $convert, "UTF-8" );
		else
			{
			switch( $convert )
				{
				case MB_CASE_TITLE:
					$result = ucwords( $result );
					break;
				case MB_CASE_UPPER:
					$result = strtoupper( $result );
					break;
				case MB_CASE_LOWER:
					$result = strtolower( $result );
					break;
				}
			}

		return $result;
		}
	function make_legend( $title , $args = null )
		{
		$title = gTxt( $title , $args );
		$title = StringHandler::convert_case( $title , MB_CASE_TITLE );
		$title = tag( $title.'&#8230;', 'legend' );
		return $title;
		}
	function strip_leading_section( $string , $delim='.' )
		{
		/*
		Simply removes anything that prefixes a string up to the delimiting character.
		So 'hello.world' -> 'world'
		*/
		if( empty( $string ))
			return '';

		$i = strstr( $string , $delim );
		if( false === $i )
			return $string;
		$i = ltrim( $i , $delim );
		return $i;
		}

	function do_prefs_name( $plugin , $add = true )
		{
		static $pfx;
		static $pfx_len;

		if( !isset( $pfx ) )
			{
			$pfx = 'l10n_registered_plugin'.L10N_SEP;
			$pfx_len = strlen( $pfx );
			}

		if( $add )
			return  $pfx.$plugin;
		else
			return substr( $plugin , $pfx_len );
		}

	function if_plugin_registered( $plugin , $lang , $count = 0 , $strings = null )
		{
		//echo br , "Checking [$plugin] ";
		global $prefs;
		static $cache = array();

		if( empty($plugin) )
			{
			//echo " ... not a plugin name, returning.";
			return false;
			}

		$name = StringHandler::do_prefs_name( $plugin );
		if( empty($name) )
			{
			//echo " ... Invalid name, returning.";
			return false;
			}

		if( !isset( $cache[$name] ) )
			{
			$details = @$prefs[$name];
			if( !isset( $details ) )
				{
				//echo " ... not registered.";
				return false;	#	Not registered.
				}

			//echo " ... caching details";
			$cache[$name] = unserialize( $details );	#	Cache registered values.
			}

		//echo " ... reading cache";
		$details = $cache[$name];

		if( empty( $details ) )
			{
			//echo " ... not registered.";
			return false;
			}

		if( null === $strings )
			{
			//echo " ... registered!";
			return $details;
			}

		#
		#	Registered, but changed?
		#
		$md5 = @$details['md5'];

		#
		#	Do an md5 check vs the strings keys...
		#
		$keys = serialize( array_keys( $strings ) );
		//echo br , var_dump( $keys );
		$keys = md5(  $keys );
		//echo " ... comparing hashes original($md5), new($keys)";
		if( $keys !== $md5 )
			{
			//echo " ... !!!CHANGED!!!";
			return false;
			}
		//echo " ... not changed ... registered.";
		return $details;
		}

	function register_plugin( $plugin , $pfx , $string_count , $lang , $event , &$strings )
		{
		//echo br , "register_plugin( $plugin , $pfx , $string_count , $lang , $event , $strings )";
		$keys = serialize( array_keys($strings) );
		//echo br , "\$keys = $keys";
		$keys = md5( $keys );

		$name = StringHandler::do_prefs_name( $plugin );
		$vals = serialize( array( 'pfx'=>doSlash($pfx) , 'num'=>$string_count , 'lang'=>$lang , 'event'=>doSlash($event) , 'md5'=>$keys ) );
		$result = set_pref( doSlash($name) , $vals , L10N_NAME , 2 );
		if( $result !== false )
			{
			global $prefs;
			@$prefs[$name] = $vals;
			}

		#
		#	Perform a one-shot update of the owner field of any existing string that
		# has a matching prefix. This catches and re-enables residual strings on a re-install.
		#
		$where = ' `name` LIKE "'.$pfx.L10N_SEP.'%"';
		@safe_update( 'txp_lang' , "`owner`='$plugin'" , $where );

		return $result;
		}

	function unregister_plugin( $plugin )
		{
		global $prefs;
		$name = doSlash( StringHandler::do_prefs_name( $plugin ) );
		$ok = @safe_delete( 'txp_prefs' , "`name`='$name' AND `event`='".L10N_NAME.'\'' );
		unset( $prefs[$name] );
		return $ok;
		}

	function insert_strings( $pfx , &$strings , $lang , $event='' , $owner='' , $override = false )
		{
		//echo br , "insert_strings( $pfx , $strings , $lang , $event , $owner ," , var_dump($override), " )";
		//echo br , "where keys = " , var_dump( serialize( array_keys( $strings ) ) );
		global	$txp_current_plugin;
		if( empty($strings) or !is_array($strings) or empty($lang) )
			return null;

		$owner_is_plugin = ( $owner !== '' and $owner !== 'snippets' );
		if( $owner_is_plugin )
			{
			if( empty($owner) )
				$owner = $txp_current_plugin;
			if( empty( $pfx) )
				$pfx = $owner;

			# If needed, register the plugin...
			$num = count($strings);
			if( false === StringHandler::if_plugin_registered( $owner , $lang , $num , $strings ) )
				StringHandler::register_plugin( $owner , $pfx , $num , $lang , $event , $strings );
			elseif( !$override )
				return false;

			# If the prefix doesn't end with the required sep character, add it...
			$pfx_len = strlen( $pfx );
			if( $pfx[$pfx_len-1] != L10N_SEP )
				{
				$pfx .= L10N_SEP;
				$pfx_len += 1;
				}
			}

		#	Iterate over the $strings and, for each that is not present, enter them into the sql table...
		$lastmod 	= date('YmdHis');
		$lang 		= doSlash( $lang );
		$event 		= doSlash( $event );
		foreach( $strings as $name=>$data )
			{
			$data = doSlash($data);

			# If the name isn't prefixed yet, add the prefix...
			if( $owner_is_plugin and (substr( $name , 0 , $pfx_len ) !== $pfx) )
				$name = doSlash($pfx . $name);
			else
				$name = doSlash( $name );

			//echo br , "Processing string [$name]";

			$set 	= "`lang`='$lang', `lastmod`='$lastmod', `event`='$event', `data`='$data', `owner`='$owner'";
			$where 	= ", `name`='$name'";
			$added = @safe_insert( 'txp_lang' , $set . $where );
			if( $override )
				@safe_update( 'txp_lang' , $set , $where );
			}

		# Cleanup empty strings.
		@safe_delete( 'txp_lang', "`data`=''");
		return true;
		}

	function store_translation_of_string( $name , $event , $new_lang , $translation , $id='' , $owner = '' )
		{
		/*
		Can create, delete or update a row in the DB depending upon the calling arguments.
		*/
		global	$txp_current_plugin;

		if( empty($name) or empty($event) or empty($new_lang) )
			{
			return null;
			}

		if( $owner === 'snippet' )
			{
			$event = 'public';
			}

		$owner = doSlash( $owner );
		$name = doSlash( $name );
		$event = doSlash( $event );
		$new_lang = doSlash( $new_lang );
		$translation = doSlash( $translation );
		$id = doSlash( $id );

		$lastmod 		= date('YmdHis');
		$set 	= " `lang`='$new_lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$translation', `owner`='$owner'" ;

		if( !empty( $id ) )
			{
			$where	= " `id`='$id'";
			if( empty( $translation ) )
				$result = @safe_delete( 'txp_lang', $where );
			else
				$result = @safe_update( 'txp_lang' , $set , $where );
			}
		else
			$result = @safe_insert( 'txp_lang' , $set );

		return $result;
		}

	function remove_strings( $plugin , $remove_lang , $debug = '' )
		{
		/*
		PLUGIN SUPPORT ROUTINE
		Either: Removes all the occurances of all plugins' strings in the given langs...
		OR:		Removes all of the named plugin's strings.
		*/
		if( $remove_lang and !empty( $remove_lang ) )
			{
			$where = "(`lang` IN ('$remove_lang')) AND (`owner` <> '')";
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			}
		elseif( $plugin and !empty( $plugin ) )
			{
			$where = "`owner`=\'$plugin\'";
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			StringHandler::unregister_plugin( $plugin );
			}
		}

	function remove_strings_by_name( &$strings , $event = '' , $plugin='' , $lang='' )
		{
		/*
		Uses the keys of the strings array to remove all of the named strings in EITHER ...
			1) ALL languages.
						OR
			2) Just the supplied language.
		*/
		global	$txp_current_plugin , $prefs;
		if( !$strings or !is_array( $strings ) or empty( $strings ) )
			return null;

		if( empty( $plugin ) )
			$plugin = $txp_current_plugin;

		$event 	= doSlash( $event );

		$result = false;
		$n_strings = count( $strings );
		if( $n_strings > 0 )
			{
			$deletes = 0;
			foreach( $strings as $name=>$data )
				{
				$name 	= doSlash($name);
				$where 	= " `name`='$name'";

				if( !empty($lang) )
					$where .= " AND `lang`='".doSlash($lang)."'";

				if( !empty($event) )
					$where .= " AND `event`='".doSlash($event)."'";

				$ok = @safe_delete( 'txp_lang' , $where );
				if( $ok === true )
					$deletes++;
				}

			if($deletes === $n_strings)
				$result = true;
			else
				$result = "$deletes of $n_strings";

			@safe_optimize( 'txp_lang' );
			}

		if( !empty($plugin) )
			StringHandler::unregister_plugin( $plugin );

		return $result;
		}

	function load_strings_into_textarray( $lang )
		{
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Loads all strings of the given language into the global $textarray so that any plugin can call
		gTxt on it's own strings. Can be used for admin and public work.
		*/
		global $textarray;

		$extras = StringHandler::load_strings($lang);
		$textarray = array_merge( $textarray , $extras );
		return count( $extras );
		}
	function remove_lang( $lang , $debug = '' )
		{
		$lang = doSlash( $lang );
		$where = " `lang`='$lang'";
		@safe_delete( 'txp_lang' , $where , $debug );
		@safe_optimize( 'txp_lang' , $debug );
		}

	function load_strings( $lang , $filter='' )
		{
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Loads all strings of the given language into an array and returns them.
		*/
		$extras = array();
		$where  = ' AND ( event=\'public\' OR event=\'common\' ';
		$close = ')';
		if( @txpinterface == 'admin' )
			$close = 'OR event=\'admin\' )';

		$rs = safe_rows_start('name, data, owner','txp_lang','lang=\''.doSlash($lang).'\'' . $where . $close . $filter );
		$count = @mysql_num_rows($rs);
		if( $rs && $count > 0 )
			{
			while ( $a = nextRow($rs) )
				$extras[$a['name']] = $a['data'];
			}
		return $extras;
		}

	function serialize_strings( $lang , $owner , $prefix , $event )
		{
		$r = array	(
					'owner'		=> $owner,		#	Name the plugin these strings are for.
					'prefix'	=> $prefix,		#	Its unique string prefix
					'lang'		=> $lang,		#	The language of the initial strings.
					'event'		=> $event,		#	public/admin/common = which interface the strings will be loaded into
					);

		$filter = " AND `owner`='$owner'";
		$r['strings'] = StringHandler::load_strings( $lang, $filter );
		$result = chunk_split( base64_encode( serialize($r) ) , 64 );
		return $result;
		}

	function discover_registered_plugins()
		{
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Gets an array of the names of plugins that have registered strings in the correct format.
		*/
		global $prefs;

		$result = array();
		$p = StringHandler::do_prefs_name( '' );

		foreach( $prefs as $k=>$v )
			if( false !== strpos($k , $p) )
				$result[StringHandler::do_prefs_name( $k , false )] = unserialize($v);

		if( count( $result ) > 1 )
			ksort( $result );

		return $result;
		}

	function get_strings( &$rs , &$stats )
		{
		$result = array();
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				$name = $a['name'];
				$lang = $a['lang'];

				if( !array_key_exists( $name , $result ) )
					$result[$name] = array();

				if( array_key_exists( $lang , $result[$name] ) )
					$result[$name][$lang] += 1;
				else
					$result[$name][$lang] = 1;
				}
			ksort( $result );
			foreach( $result as $name => $langs )
				{
				ksort( $langs );

				//
				//	Build the language stats for the strings...
				//
				foreach( $langs as $lang=>$count )
					{
					if( array_key_exists( $lang, $stats ) )
						$stats[$lang] += $count;
					else
						$stats[$lang] = $count;
					}

				$string_of_langs = rtrim( join( ', ' , array_keys($langs) ) , ' ,' );
				$result[$name] = $string_of_langs;
				}
			ksort( $stats );
			}
		return $result;
		}

	function get_plugin_strings( $plugin , &$stats , $prefix )
		{
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Given a plugin name, will extract a list of strings the plugin has registered, collapsing all
		the translations into one entry. Thus...
		name	lang	data
		alpha	en		Alpha
		alpha	fr		Alpha
		alpha	el		Alpha
		beta	en		Beta
		Gives...
		alpha => 'fr, el, en'  (Sorted order)
		beta  => 'en'
		*/
		$plugin = doSlash( $plugin );
		$prefix = doSlash( $prefix );
		$where = " `owner`='$plugin'";
		$rs = safe_rows_start( 'lang, name, owner', 'txp_lang', $where );
		return StringHandler::get_strings( $rs , $stats );
		}

	function get_full_langs_string( )
		{
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Returns a string of the site languages. Used to work out if a given string has a complete
		set of translations.
		*/
		$langs = LanguageHandler::get_site_langs();
		sort( $langs );
		$langs = rtrim( join( ', ' , $langs ) , ' ,' );
		return $langs;
		}

	function get_string_set( $string_name , $string_event='' )
		{
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Given a string name, will extract an array of the matching translations.
		translation_lang => string_id , event , data
		*/
		$result = array();

		$where = ' `name` = "'.doSlash($string_name).'"';
		if( $string_event )
			$where .= ' AND `event`="' . doSlash($string_event) . '"';
		$rs = safe_rows_start( 'lang, id, event, data, owner', 'txp_lang', $where );
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				$lang = $a['lang'];
				if( LanguageHandler::is_valid_code( $lang ) )
					{
					unset( $a['lang'] );	# will be used as key, no need to store it twice.
					$result[ $lang ] = $a;
					}
				}
			ksort( $result );
			}
		return $result;
		}

	function make_nameset( $names )
		{
		if( !is_array( $names ) )
			$names = array( $names );

		$name_set = '';
		foreach( $names as $name )
			{
			$name = doSlash( $name );
			$name_set .= "'$name', ";
			}

		$name_set = rtrim( $name_set , ', ' );
		if ( empty( $name_set ) )
			$name_set = "''";

		return $name_set;
		}
	function get_set_by_lang( $nameset , $lang )
		{
		$result = array();

		$where = ' `name` IN ('.$nameset.') AND `lang` = "'.doSlash($lang).'"';
		$rs = safe_rows_start( 'name,data,event', 'txp_lang', $where );
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				extract( $a );
				$result[$name] = array($data,$event);
				}
			ksort( $result );
			}
		return $result;
		}
	function get_set_by_name( $langset , $name )
		{
		$result = array();

		$where = ' `lang` IN ('.$langset.') AND `name` = "'.doSlash($name).'"';
		$rs = safe_rows_start( 'lang,data,event', 'txp_lang', $where );
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				extract( $a );
				$result[$lang] = array($data,$event);
				}
			ksort( $result );
			}
		return $result;
		}

	} // End class StringHandler

?>
