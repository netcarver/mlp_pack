<?php

/*	To do...
Article filtering by language
	add cookie tracking?
	User permissions to set/filter by language
	ID	Group				Clone4xl18n		List restrictions		Article creation lang
	0	Others				No access		No access				No access
	1	Publishers			Yes				None					Any
	2	Managing editor		Yes				None					Any
	3	Copy editor			No				None					Known site langs
	4	Staff writer		No				Known site langs		Known site langs
	5	Freelancer			No				Known site langs		No access
	6	Designer			No				No access				No access

	404 error handling

*/

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
	register_callback( 'l10n_add_article_to_group_cb' 			, 'article' );
	//register_callback( 'l10n_generate_lang_tables'				, 'article' );

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

	echo br , "_l10n_get_user_languages() ... user: " . var_dump( $user_id );

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
	//$langs = array( 'fr-fr' , 'de-de' );
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
			#
			#	Create a temporary 'textpattern' table, filtered by this txp_user's
			# language permissions AND their view choices ...
			#
			$langs = LanguageHandler::get_site_langs();
			$selected = array();
			foreach( $langs as $lang )
				{
				if( ps( $lang ) )
					$selected[] = "'$lang'";
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
	$id = $matches[1];
	$rs = safe_row(	'*', 'textpattern', "ID=$id" );
	$code	= $rs['Lang'];
	$group	= $rs['Group'];
	$lang = LanguageHandler::get_native_name_of_lang( $code );
	return $matches[0] . br . $lang . ' [' . gTxt('group'). ' :' .$group . ']';
	}
function _l10n_chooser( $permitted_langs )
	{
	$count = 0;
	$langs = LanguageHandler::get_site_langs();
	$o[] = '<div style="text-align: center;" ><fieldset style="margin: 0px auto;"><legend>' . 'Show languages&#8230;' . '</legend>' . n;
	foreach( $langs as $lang )
		{
		$rw = '';
		$checked = ps( $lang ) ? 'checked' : '' ;
		$lang_name = LanguageHandler::get_native_name_of_lang( $lang );

		if( !in_array( $lang , $permitted_langs ) )
			{
			$rw = 'disabled="disabled"';
			$checked = '';
			}

		if( strlen( $checked ) > 0 )
			$count++;

		$o[] = t . '<input type="checkbox" class="checkbox" '.$rw.' '.$checked.' value="'.$lang.'" name="'.$lang.'" id="'.$lang.'"/>' . n;
		$o[] = t . '<label for="'.$lang.'">'.$lang_name.'</label>' . n;
		}
	$o[] = eInput( 'list' );
	$o[] = t.'<input type="submit" value="'.gTxt('go').'" class="smallerbox" />' . n;
	$o[] = '</fieldset></div>' . n;

	//if( 0 === $count )
		#
		#	No checked boxes => nothing to show here!
		#
		//$o = '';

	$o = form( join( '' , $o ) );

	return $o;
	}
function l10n_list_buffer_processor( $buffer )
	{
	$count = 0;
	$pattern = '/"><a href="\?event=article&#38;step=edit&#38;ID=(\d+)">.*<\/a>/';

	#	Inject the language chooser...
	$chooser = _l10n_chooser( LanguageHandler::get_site_langs() );
	$f = '<form action="index.php" method="post" style="margin: auto; text-align: center;"><p><label for="list-search">';
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
	global $txp_user;

	#
	#	The buffer processing routine injects page elements when editing an article.
	#
	$remaining	= GroupManager::get_remaining_langs( $l10n_vars['article_group'] );
	$can_clone	= (count($remaining) > 0);
	$author 	= (@$l10n_vars['article_author_id']) ? $l10n_vars['article_author_id'] : $txp_user;
	$cloning_permitted	= has_privs( 'l10n.clone' );

	$lang 		= $l10n_vars['article_lang'];
	$user_langs = LanguageHandler::do_fleshout_names( _l10n_get_user_languages() , true );

	$reassigning_permitted = has_privs( 'l10n.reassign' );

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
	global $l10n_article_message;
	if( isset($l10n_article_message) )
		{
		$r = strong( htmlspecialchars($l10n_article_message) ) . n . br;
		unset( $l10n_article_message );
		}
	$r.= 'ID: ' . strong( $id_no ) . ' / ';

	if( $group_id == '-' )	#	New article , don't setup a 'Group' element in the page!...
		{
		$r .=	gTxt('language') . ': ' . selectInput( 'Lang' , $user_langs , $lang ) . ' / ';
		$r .= 	gTxt('group')    . ': ' . strong( $group_id );
		}
	else	# Existing article, either being cloned/edited with re-assignment language rights or not...
		{
		if( $reassigning_permitted )
			{
			$r .=	gTxt('language') . ': ' . selectInput( 'Lang' , $user_langs , $lang ) . ' / ';
			$r .=	gTxt('group')    . ': ' . fInput('edit','Group',$group_id , '', '', '', '4');
			}
		else
			{
			$r .= 	hInput( 'Lang' , $lang )      . gTxt('language') . ': ' . strong( LanguageHandler::get_native_name_of_lang($lang) ) . ' / ';
			$r .= 	hInput( 'Group' , $group_id ) . gTxt('group')    . ': ' . strong( $group_id );
			}
		}
	$r = graf( $r );
	$buffer = str_replace( $f , $r.n.$f , $buffer );

	return $buffer;
	}

function l10n_add_article_to_group_cb( $event , $step )
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

	//echo br , "l10n_add_article_to_group_cb( $event , $step ) ... " ;
	switch(strtolower($step))
		{
		case 'publish':
			#
			#	Create a group for this article
			#
			GroupManager::create_group_and_add( $incoming );

			#
			#	Update the language table for the target language...
			#
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
			$article_id	= $incoming['ID'];

			$info = safe_row( '*' , 'textpattern' , "`ID`='$article_id'" );
			if( $info !== false )
				{
				$current_lang	= $info['Lang'];
				}

			#
			#	Check for changes to the article language and groups ...
			#
			GroupManager::move_to_group( $incoming );

			#
			#	Now we can setup the tables again...
			#
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
	#	Permissions for email...
	$notify_new_authors = true;
	$notify_self = true;

	if( !$notify_new_authors )
		return false;

	global $statuses, $sitename, $siteurl, $txp_user;
	$new_user = ps('AuthorID');
	$selected = ps('selected');
	$links    = array();
	$same	  = ($new_user == $txp_user);

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
				$msg = "\"$Title\"\r\nStatus: $status , Language: $lang [$Lang] , Group: $Group.\r\n";
				$msg.= "http://$siteurl/textpattern/index.php?event=article&step=edit&ID=$id\r\n";
				$links[] = $msg;
				}
			}

		extract(safe_row('RealName AS txp_username,email AS replyto','txp_users',"name='$txp_user'"));
		extract(safe_row('RealName AS new_user,email','txp_users',"name='$new_user'"));

		$count = count( $links );
		$s = (($count===1) ? '' : 's');
		if( $same )
			$body = "To\t: $txp_username,\r\nFrom\t: Self\r\nMemo\t: you transferred the following article$s to yourself...\r\n\r\n";
		else
			$body = "To\t: $new_user,\r\nFrom\t: $txp_username\r\nMemo\t: I have transferred the following article$s to you...\r\n\r\n";
		$body.= join( "\r\n" , $links ) . "\r\n\r\n";

		$subject = "[$sitename] Notice ... $count article$s transferred to you.";

		echo br , "Building email to send to new author: $new_user &lt;$email&gt;, from $txp_user &lt;$replyto&gt;..." , br;
		echo "$subject" , n , n , br , $body ;

		$ok = txpMail($email, $subject, $body, $replyto);
		/*$ok = mail	(
					$email, $subject, $message,
					"From: $replyto <$replyto>\r\n"
					."Reply-To: $replyto <$replyto>\r\n"
					."X-Mailer: Textpattern\r\n"
					."Content-Transfer-Encoding: 8bit\r\n"
					."Content-Type: text/plain; charset=\"UTF-8\"\r\n"
					);*/
		if( $ok )
			echo br , "Mail sent ok.";
		else
			echo br , "Mail send failed.";
		}
	}
function l10n_post_multi_edit_cb( $event , $step )
	{
	global $l10n_vars;
	$method   = ps('edit_method');
	$redirect = false;
	$update   = true;

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

		global $l10n_view;
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
				$group = $info['Group'];
				$lang  = $info['Lang'];
				$languages[$lang] = $lang;
				if( 'delete' === $method )
					GroupManager::remove_article( $group , $id , $lang );
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
	$table_name = 'textpattern_' . $code['short'];

	echo br , "Updating the {$code['short']} table...";

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
	echo ($ok) ? ' done.' : ' failed.';
	}
function l10n_generate_lang_tables( $event , $step )
	{
	//echo br , "l10n_generate_lang_tables( $event , $step )";
	global $l10n_vars;

	$save = gps('save');
	if ($save) $step = 'save';

	$publish = gps('publish');
	if ($publish) $step = 'publish';

	//echo " ... new step: $step";

	switch( $step )
		{
		case 'publish' :
		case 'save' :
			$lang = $l10n_vars['article_lang'];
			$langs = LanguageHandler::get_site_langs();
			//echo " ... Lang=$lang, Langs=" , var_dump( $langs ) , br;
			if( in_array( $lang, $langs ) )
				{
				//_l10n_generate_lang_table( $lang );
				}
		break;
		}
	}
function l10n_pre_discuss_multi_edit( $event , $step )
	{
	global $l10n_vars;
	$languages = array();

	$things = ps('selected');
	$method = ps('edit_method');

	if( $things )
		{
		//echo br , "pre-discuss-multi-edit($event, $step)... method is '$method'";
		foreach( $things as $id )
			{
			$id = intval($id);
			$comment = safe_row( 'parentid as id,visible as current_visibility' , 'txp_discuss' , "`discussid`='$id'" );
			if( $comment !== false )
				{
				//echo br , "read discussion $id as " , var_dump( $comment ) , "VISIBLE=", VISIBLE;

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
						//echo br , "Marking language $lang for update.";
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

