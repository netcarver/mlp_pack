<?php


global $l10n_vars;
$l10n_vars = array();

if( $l10n_view->installed() )
	{
	add_privs( 'l10n.clone' 	, '1,2' );
	add_privs( 'l10n.reassign'	, '1,2' );

	#
	#	Article handlers...
	#
	register_callback( 'l10n_setup_article_buffer_processor'	, 'article' , '' , 1 );
	register_callback( 'l10n_add_rendition_to_article_cb' 		, 'article' );

	#
	#	Article list handlers...
	#
	register_callback( 'l10n_pre_multi_edit_cb'				, 'list' , 'list_multi_edit' , 1 );
	register_callback( 'l10n_post_multi_edit_cb'				, 'list' , 'list_multi_edit' );
	register_callback( 'l10n_list_filter'						, 'list' , '' , 1 );

	#
	#	Comment handlers...
	#
	register_callback( 'l10n_pre_discuss_multi_edit' 			, 'discuss' , 'discuss_multi_edit' , 1 );
	register_callback( 'l10n_post_discuss_multi_edit' 			, 'discuss' , 'discuss_multi_edit' );

	#
	#	Language management handlers (to stop language strings from being deleted) ...
	#
	register_callback( 'l10_language_handler_callback_pre'  , 'prefs' , 'get_language' , 1 );
	register_callback( 'l10_language_handler_callback_post' , 'prefs' , 'get_language' );
	}

#
#	The following two routines were added to stop the TxP language update/intsall
# from a file from trampling all over any other strings in that language.
#
function l10_language_handler_callback_pre( $event , $step )
	{
	global $l10n_file_import_details;
	//echo br , "l10_language_handler_callback_pre( $event , $step )";

	$force = gps( 'force' );
	if( 'file' !== $force )
		return;

	$lang = gps('lang_code');
	$lang_file = txpath.'/lang/'.$lang.'.txt';
	if (is_file($lang_file) && is_readable($lang_file))
		{
		$lang_file = txpath.'/lang/'.$lang.'.txt';
		if (!is_file($lang_file) || !is_readable($lang_file))
			return;

		$lastmod = filemtime($lang_file);
		$lastmod = date('YmdHis',$lastmod);

		#
		#	Set the timestamp of all lines that will be deleted by the file import to a safe value.
		# The 'post' routine will restore the timestamp.
		#
		$new_time = '19990101000000';
		$ok = safe_update( 'txp_lang' , "`lastmod`='$new_time'" , "`lang`='$lang' and `lastmod` > $lastmod" );
		}
	}
function l10_language_handler_callback_post( $event , $step )
	{
	$force = gps( 'force' );
	if( 'file' !== $force )
		return;

	#
	#	Restore the timestamp of all the lins that would have been deleted...
	#
	$lang = gps('lang_code');
	$new_time = date('YmdHis');
	$old_time = '19990101000000';
	$ok = safe_update( 'txp_lang' , "`lastmod`='$new_time'" , "`lang`='$lang' and `lastmod` = '$old_time'" );
	}

function _l10n_get_user_languages( $user_id = null )
	{
	#
	#	Returns an array of the languages that the given TxP user can create/edit
	# If the input user id is null (default) then the current txp_user is used...
	#
	if( null === $user_id )
		{
		global $txp_user;
		$user_id = $txp_user;
		}

	$langs = array();

	#
	#	Certain user groups get full rights...
	#
	$power_users = array( '1', '2' );
	$privs = safe_field('privs', 'txp_users', "user_id='$user_id'");
	if( in_array( $privs , $power_users ) )
		$langs = LanguageHandler::get_site_langs();

	#
	#	Stub... replace with lookup of the user's languages....
	#
	$langs = LanguageHandler::get_site_langs();

	return $langs;
	}

function _l10n_create_temp_textpattern( $languages )
	{
	$indexes = "(PRIMARY KEY  (`ID`), KEY `categories_idx` (`Category1`(10),`Category2`(10)), KEY `Posted` (`Posted`), FULLTEXT KEY `searching` (`Title`,`Body`))";
	$sql = "create TEMPORARY table `".PFX."textpattern` $indexes select * from `".PFX."textpattern` where `Lang` IN ($languages)";
	@safe_query( $sql );
	}
function l10n_list_filter( $event, $step )
	{
	if( $event !== 'list' )
		return;

	switch( $step )
		{
		case '':
		case 'list':
			$langs = LanguageHandler::get_site_langs();
			$selected = array();
			$use_cookies = (ps( 'l10n_filter_method' ) !== 'post');
			foreach( $langs as $lang )
				{
				if( $use_cookies )
					{
					if( cs($lang) )
						$selected[] = "'$lang'";
					}
				else
					{
					if( ps($lang) )
						{
						$selected[] = "'$lang'";
						$time = time() + (3600 * 24 * 365);
						}
					else
						$time = time() - 3600;

					$ok = setcookie( $lang , $lang , $time );
					}
				}
			$languages = join( ',' , $selected );
			_l10n_create_temp_textpattern( $languages );
			break;
		default:
			break;
		}
	ob_start( 'l10n_list_buffer_processor' );
	}
function _l10n_match_cb( $matches )
	{
	#
	#	$matches[0] is the entire pattern...
	#	$matches[1] is the article ID...
	#
	$id 		= $matches[1];
	$rs 		= safe_row(	'*', 'textpattern', "ID=$id" );
	$code		= $rs['Lang'];
	$article	= $rs['Group'];
	$lang 		= LanguageHandler::get_native_name_of_lang( $code );
	return $matches[0] . br . '<span class="articles_detail">' . $lang . ' [' . gTxt('article'). ' :' .$article . ']</span>';
	}
function _l10n_chooser( $permitted_langs )
	{
	$count = 0;
	$langs = LanguageHandler::get_site_langs();
	$o[] = '<div style="text-align: center;" ><fieldset style="margin: 0px auto;"><legend>' . 'Show languages&#8230;' . '</legend>' . n;
	$use_cookies = (ps( 'l10n_filter_method' ) !== 'post');

	#
	#	See if there are any languages selected. If not, select them all -- to give the user something to look at!
	#
	$showlangs = array();
	$rendition_count = 0;
	$count = 0;
	foreach( $langs as $lang )
		{
		$table = ArticleManager::make_textpattern_name(array('long'=>$lang));
		$lang_rendition_count = safe_count( $table , "`Lang`='$lang'" );
		$lang_has_renditions = ($lang_rendition_count > 0);

		$rw = '';
		if( $use_cookies )
			$checked = cs( $lang ) ? 'checked' : '' ;
		else
			$checked = ps( $lang ) ? 'checked' : '' ;

		$lang_name = LanguageHandler::get_native_name_of_lang( $lang );

		if( !in_array( $lang , $permitted_langs ) )
			{
			$rw = 'disabled="disabled"';
			$checked = '';
			}
		elseif( !$lang_has_renditions )
			{
			$rw = 'disabled="disabled"';
			$checked = 'checked';
			}

		$showlangs[$lang]['lang_name']	= $lang_name;
		$showlangs[$lang]['rw'] 	= $rw;
		$showlangs[$lang]['checked']	= $checked;
		if( !empty($checked) )
			$rendition_count += $lang_rendition_count;
		}

	$override_check = false;
	if( $rendition_count === 0 )
		{
		$override_check = true;
		}

	foreach( $showlangs as $lang=>$record )
		{
		extract( $record );
		$checked = ($override_check) ? 'checked' : $checked;
		$o[] = t . '<input type="checkbox" class="checkbox" '.$rw.' '.$checked.' value="'.$lang.'" name="'.$lang.'" id="'.$lang.'"/>' . n;
		$o[] = t . '<label for="'.$lang.'">'.$lang_name.'</label>' . n;
		}
	$o[] = hInput( 'l10n_filter_method' , 'post' );
	$o[] = t.'<input type="submit" value="'.gTxt('go').'" class="smallerbox" />' . n;
	$o[] = '</fieldset></div>' . n;

	$o = join( '' , $o );
	return $o;
	}
function l10n_list_buffer_processor( $buffer )
	{
	$count = 0;
    $pattern = '/<\/td>'.n.t.'<td><a href="\?event=article&#38;step=edit&#38;ID=(\d+)">.*<\/a>/';

	#	Inject the language chooser...
	$chooser = _l10n_chooser( LanguageHandler::get_site_langs() );
	$f = '<p><label for="list-search">';
	$buffer = str_replace( $f , $chooser.br.n.$f , $buffer );

	#	Inject the language markers...
	$result = preg_replace_callback( $pattern , '_l10n_match_cb' , $buffer , -1 , $count );
	if( !empty( $result ) )
		return $result;

	return $buffer;
	}
function l10n_setup_vars( $event , $step )
	{
	#
	#	Read the variables we need and stash them away for use in the buffer
	# processor...
	#
	global $l10n_vars;

	if(!empty($GLOBALS['ID']))
		{
		// newly-saved article
		$ID = intval($GLOBALS['ID']);
		}
	else
		{
		$ID = gps('ID');
		}

	if( $ID )
		{
		$rs = safe_row(	'*, unix_timestamp(Posted) as sPosted, unix_timestamp(LastMod) as sLastMod', 'textpattern', "ID=$ID" );
		$l10n_vars['article_id'] 	= $ID;
		$l10n_vars['article_lang']	= $rs['Lang'];
		$l10n_vars['article_group']	= $rs['Group'];
		$l10n_vars['article_author_id'] = $rs['AuthorID'];
		}
	else
		{
		$l10n_vars['article_lang']	= LanguageHandler::get_site_default_lang();
		}

	$l10n_vars['step']			= $step;
	}
function l10n_setup_article_buffer_processor( $event , $step )
	{
	#	Setup the buffer process routine. It will inject new page elements
	# into the article edit page...
	#
	ob_start( 'l10n_article_buffer_processor' );
	l10n_setup_vars( $event , $step );

	#
	#	If we are posting a new article from an existing one, force some simple
	# values into the article...
	#
	global $l10n_vars;
	$publish = gps('publish');
	if( $publish and @$l10n_vars['article_id'] )
		{
		$_POST['Status'] = '1';			#	All cloned articles are DRAFTS, pending translation.
		$_POST['publish_now'] = '1';	#	Force update of publish time to NOW.
		unset($_POST['reset_time']);
		$_POST['url_title'] = '';		#	Force the url_title to be rebuilt.
		$_POST['Lang'] = $_POST['CloneLang'];		#	The article language and group comes
		$_POST['Group'] = $_POST['CloneGroup'];	# from the clone selector elements.
		}
	}
function l10n_article_buffer_processor( $buffer )
	{
	global $l10n_vars;
	global $l10n_view;
	global $l10n_article_message;
	global $txp_user;

	#
	#	The buffer processing routine injects page elements when editing an article.
	#
	$remaining	= ArticleManager::get_remaining_langs( $l10n_vars['article_group'] );
	$can_clone	= (count($remaining) > 0);
	$author 	= (@$l10n_vars['article_author_id']) ? $l10n_vars['article_author_id'] : $txp_user;

	#
	#	Disallow cloning in the write tab now...
	#
	//$cloning_permitted	= has_privs( 'l10n.clone' );
	$cloning_permitted	= false;

	$lang 		= $l10n_vars['article_lang'];
	$user_langs = LanguageHandler::do_fleshout_names( _l10n_get_user_languages() , true );

	$reassigning_permitted = ( '1' == $l10n_view->pref('l10n-allow_writetab_changes') ) ? true : false;
	$has_reassign_privs = has_privs( 'l10n.reassign' );

	$id_no		= '-';
	if( $l10n_vars['article_id'] )
		$id_no = $l10n_vars['article_id'];

	$group_id 	= '-';
	if( $l10n_vars['article_group'] )
		$group_id = $l10n_vars['article_group'];

	if( $cloning_permitted and $can_clone and $id_no !== '-' )
		{
		#	Insert the clone panel...
		$checkit = "'".doSlash(gTxt('are_you_sure'))."'";
		$f = '<input type="submit" name="save" value="'.gTxt('save').'" class="publish" tabindex="4" />';
		$r = '<fieldset><legend>'.gTxt('l10n-clone_and_translate').'</legend>'.
				hInput('original_ID' , $id_no) .
				hInput('CloneGroup' , $group_id) .
				'<p>'. gTxt('l10n-xlate_to') . selectInput( 'CloneLang', $remaining ) . '</p>' .
				'<input type="submit" name="publish" value="'.gTxt('l10n-clone').'" class="publish" onclick="return confirm('.$checkit.');" />' .
				'</fieldset>';
		$buffer = str_replace( $f , $f.n.$r , $buffer );
		}

	#
	#	Insert the ID/Language/Group display elements...
	#
	$f = '<p><input type="text" id="title"';

	$r = '';
	if( isset($l10n_article_message) )
		{
		$r = strong( htmlspecialchars($l10n_article_message) ) . n . br;
		unset( $l10n_article_message );
		}
	$r.= 'ID: ' . strong( $id_no ) . ' / ';

	if( $group_id == '-' )	#	New article , don't setup a 'Group' element in the page!...
		{
		$r .=	gTxt('language') . ': ' . selectInput( 'Lang' , $user_langs , $lang ) . ' / ';
		$r .= 	gTxt('article')    . ': ' . strong( $group_id );
		}
	else	# Existing article, either being cloned/edited with re-assignment language rights or not...
		{
		if( $reassigning_permitted and $has_reassign_privs )
			{
			$r .=	gTxt('language') . ': ' . selectInput( 'Lang' , $user_langs , $lang ) . ' / ';
			$r .=	gTxt('article')    . ': ' . fInput('edit','Group',$group_id , '', '', '', '4');
			}
		else
			{
			$r .= 	hInput( 'Lang' , $lang )      . gTxt('language') . ': ' . strong( LanguageHandler::get_native_name_of_lang($lang) ) . ' / ';
			$r .= 	hInput( 'Group' , $group_id ) . gTxt('article')    . ': ' . strong( $group_id );
			}
		}
	$r = graf( $r );
	$buffer = str_replace( $f , $r.n.$f , $buffer );

	return $buffer;
	}

function l10n_add_rendition_to_article_cb( $event , $step )
	{
	require_privs('article');

	global $vars;
	$new_vars = array_merge( $vars , array( 'Lang' , 'Group' , 'original_ID' ) );

	$save = gps('save');
	if ($save) $step = 'save';

	$publish = gps('publish');
	if ($publish) $step = 'publish';

	$incoming = psa($new_vars);
	$new_lang	= (@$incoming['Lang']) ? $incoming['Lang'] : LanguageHandler::get_site_default_lang();

	switch(strtolower($step))
		{
		case 'publish':
			#
			#	Create a group for this article
			#
			ArticleManager::create_article_and_add( $incoming );

			#
			#	Update the language table for the target language...
			#
			/* WIP OPTIMISE THIS */
			_l10n_generate_lang_table( $new_lang );

			#
			#	Read the variables to continue the edit...
			#
			l10n_setup_vars( $event , $step );
			break;
		case 'save':
			#
			#	Record the old and new languages, if there are any changes we need to update
			# both the old and new tables after moving the group/lang over...
			#
			$rendition_id	= $incoming['ID'];

			$info = safe_row( '*' , 'textpattern' , "`ID`='$rendition_id'" );
			if( $info !== false )
				{
				$current_lang	= $info['Lang'];
				}

			#
			#	Check for changes to the article language and groups ...
			#
			ArticleManager::move_to_article( $incoming );

			#
			#	Now we can setup the tables again...
			#
			/* OPTIMISE THIS */
			_l10n_generate_lang_table( $new_lang );
			if( $new_lang != $current_lang )
				_l10n_generate_lang_table( $current_lang );

			#
			#	Read the variables to continue the edit...
			#
			l10n_setup_vars( $event , $step );
			break;
		}
	}

function l10n_changeauthor_notify_routine()
	{
	global $l10n_view;

	#	Permissions for email...
	$send_notifications	= ( '1' == $l10n_view->pref('l10n-send_notifications') ) ? true : false;
	$on_changeauthor	= ( '1' == $l10n_view->pref('l10n-send_notice_on_changeauthor') ) ? true : false;
	$notify_self 		= ( '1' == $l10n_view->pref('l10n-send_notice_to_self') ) ? true : false;

	if( !$send_notifications or !$on_changeauthor )
		return false;

	global $statuses, $sitename, $siteurl, $txp_user;
	$new_user = ps('AuthorID');
	$selected = ps('selected');
	$links    = array();
	$same	  = ($new_user == $txp_user);

	if( empty( $new_user ) )
		return;

	if( !$same or $notify_self )
		{
		if( $selected and !empty($selected) )
			{
			foreach( $selected as $id )
				{
				#
				#	Make a link to the article...
				#
				extract( safe_row('Title,Lang,`Group`,Status' , 'textpattern' , "`ID`='$id'") );
				$lang   = LanguageHandler::get_native_name_of_lang( $Lang );
				$status = $statuses[$Status];
				$msg = 	gTxt('title')  . ": \"$Title\"\r\n" .
						gTxt('status') . ": $status , " . gTxt('language') . ": $lang [$Lang] , " . gTxt('group' ) . ": $Group.\r\n";
				$msg.= "http://$siteurl/textpattern/index.php?event=article&step=edit&ID=$id\r\n";
				$links[] = $msg;
				}
			}

		extract(safe_row('RealName AS txp_username,email AS replyto','txp_users',"name='$txp_user'"));
		extract(safe_row('RealName AS new_user,email','txp_users',"name='$new_user'"));

		$count = count( $links );
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
		$body.= join( "\r\n" , $links ) . "\r\n\r\n" . gTxt( 'thanks' ) . "\r\n--\r\n$txp_username.";
		$subject = gTxt( 'l10n-email_xfer_subject' , $subs );

		$ok = @txpMail($email, $subject, $body, $replyto);
		}
	}
function l10n_post_multi_edit_cb( $event , $step )
	{
	global $l10n_vars;
	global $l10n_view;

	$method   		= ps('edit_method');
	$redirect 		= true;	#	Always redirect to the 'list' event. This forces a re-draw of the screen
							#	with the correct language filters applied.
	$update   		= true;

	#
	#	Special cases...
	#
	switch( $method )
		{
		case 'changeauthor':
			l10n_changeauthor_notify_routine();
		break;
		}

	if( $update and isset( $l10n_vars['update_tables'] ) )
		{
		$tables = $l10n_vars['update_tables'];

		if( $tables AND !empty( $tables ) )
			{
			unset( $l10n_vars['update_tables'] );

			#
			#	Re-generate each language table touched by the edit...
			#
			foreach( $tables as $k=>$lang )
				{
				_l10n_generate_lang_table( $lang );
				}
			}
		}

	if( $redirect )
		{
		while (@ob_end_clean());

		$search = gpsa( array( 'search_method' , 'crit' , 'event' , 'step' ) );
		$search['event'] = 'list';
		$search['step'] = '';

		$l10n_view->redirect( $search );
		}
	}
function l10n_pre_multi_edit_cb( $event , $step )
	{
	global $l10n_vars;
	$method = ps('edit_method');
	$things = ps('selected');

	$languages = array();

	#
	#	Scan the selected items, building a table of languages touched by the edit.
	# Also delete any group info on the delete method calls.
	#
	if( $things )
		{
		foreach( $things as $id )
			{
			$id = intval($id);
			$info = safe_row( '*' , 'textpattern' , "`ID`='$id'" );
			if( $info !== false )
				{
				$article	= $info['Group'];
				$lang  		= $info['Lang'];
				$languages[$lang] = $lang;
				if( 'delete' === $method )
					ArticleManager::remove_rendition( $article , $id , $lang );
				}
			}
		}

	#
	#	Pass the languages array to the post-process routine to reconstruct the
	# per-language tables that were changed by the edit...
	#
	if( !empty( $languages ) )
		$l10n_vars['update_tables'] = $languages;
	}
function _l10n_generate_lang_table( $lang , $filter = true )
	{
	$code  = LanguageHandler::compact_code( $lang );
	$table_name = ArticleManager::make_textpattern_name( $code );

	$where = '';
	if( $filter )
		$where = " where `Lang`='$lang'";
	$indexes = "(PRIMARY KEY  (`ID`), KEY `categories_idx` (`Category1`(10),`Category2`(10)), KEY `Posted` (`Posted`), FULLTEXT KEY `searching` (`Title`,`Body`))";
	$sql = "create table `".PFX."$table_name` $indexes select * from `".PFX."textpattern`$where";
	$drop_sql = 'drop table `'.PFX."$table_name`";
	@safe_query( 'lock tables `'.PFX."$table_name` WRITE" ) ;
	@safe_query( $drop_sql );
	$ok = @safe_query( $sql );
	@safe_query( 'unlock tables' ) ;
	}
function l10n_pre_discuss_multi_edit( $event , $step )
	{
	global $l10n_vars;
	$languages = array();

	$things = ps('selected');
	$method = ps('edit_method');

	if( $things )
		{
		foreach( $things as $id )
			{
			$id = intval($id);
			$comment = safe_row( 'parentid as id,visible as current_visibility' , 'txp_discuss' , "`discussid`='$id'" );
			if( $comment !== false )
				{
				$mark_lang = false;
				extract( $comment );

				#
				# It's only going from non_visible->visible or visible->non_visible that
				# needs an update.
				#
				if( 'visible' == $method )
					$mark_lang = (VISIBLE != $current_visibility);
				else
					$mark_lang = (VISIBLE == $current_visibility);

				if( $mark_lang )
					{
					$info = safe_row( 'Lang' , 'textpattern' , "`ID`='$id'" );
					if( $info !== false )
						{
						$lang = $info['Lang'];
						$languages[$lang] = $lang;
						}
					}
				}
			}
		}

	#
	#	Pass the languages array to the post-process routine to reconstruct the
	# per-language tables that were changed by the edit...
	#
	if( !empty( $languages ) )
		$l10n_vars['update_tables'] = $languages;
	}
function l10n_post_discuss_multi_edit( $event , $step )
	{
	global $l10n_vars;
	$method   = ps('edit_method');

	if( isset( $l10n_vars['update_tables'] ) )
		{
		$tables = $l10n_vars['update_tables'];

		if( $tables AND !empty( $tables ) )
			{
			unset( $l10n_vars['update_tables'] );

			#
			#	Re-generate each language table touched by the edit...
			#
			foreach( $tables as $k=>$lang )
				{
				_l10n_generate_lang_table( $lang );
				}
			}
		}
	}

?>

