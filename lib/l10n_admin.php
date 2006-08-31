<?php

/*	To do...

Article filtering by language
	add cookie tracking
	User permissions to set/filter by language

						Clone4xl18n		List restrictions		Article creation lang
0	Others				No access		No access				No access
1	Publishers			Yes				None					Any
2	Managing editor		Yes				None					Any
3	Copy editor			No				None					Known site langs
4	Staff writer		No				Known site langs		Known site langs
5	Freelancer			No				Known site langs		No access
6	Designer			No				No access				No access


ajw_admin_workflow like...
	article tranfser capabilities
		REQUIRES
			user language permissions.
	emailing of transfer notices

*/

$l10n_vars = array();

add_privs( 'l10n_clone_js_link'		, '1,2,3,4,5,6' );
add_privs( 'l10n_serve_clone_js'	, '1,2,3,4,5,6' );

//register_callback( 'l10n_setup_buffer_processor'			, 'article' , 'edit' , 1 );
register_callback( 'l10n_setup_article_buffer_processor'	, 'article' , '' , 1 );
register_callback( 'l10n_add_article_to_group_cb' 			, 'article' );
register_callback( 'l10n_generate_lang_tables'				, 'article' );
register_callback( 'l10n_delete_articles_from_group_cb'	, 'list' , 'list_multi_edit' , 1 );
register_callback( 'l10n_delete_articles_and_redirect'		, 'list' , 'list_multi_edit' );
register_callback( 'l10n_clone_js_link'         			, 'article' );
register_callback( 'l10n_serve_clone_js'        			, 'l10n_clone_js', '', 1 );
register_callback( 'l10n_list_filter'						, 'list' , '' , 1 );

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
//			$languages = "'fr-fr','de-de','en-gb','el-gr'";
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
	$code = $rs['Lang'];
	$lang = LanguageHandler::get_native_name_of_lang( $code );
	return $matches[0] . br . $lang . ' [' . $code . ']';
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

//	if( 0 === $count )
		#
		#	No checked boxes => nothing to show here!
		#
//		$o = '';

	$o = form( join( '' , $o ) );

	return $o;
	}
function l10n_list_buffer_processor( $buffer )
	{
	$count = 0;
	$pattern = '/"><a href="\?event=article&#38;step=edit&#38;ID=(\d+)">.*<\/a>/';

	#	Inject the language chooser...
	$chooser = _l10n_chooser( LanguageHandler::get_site_langs() );
	$f = '<form action="index.php" method="post"><p style="text-align: center;"><label for="list-search">';
	$buffer = str_replace( $f , $chooser.br.n.$f , $buffer );

	#	Inject the language markers...
	$result = preg_replace_callback( $pattern , '_l10n_match_cb' , $buffer , -1 , $count );
	if( !empty( $result ) )
		return $result;

	return $buffer;
	}
function l10n_clone_js_link()
	{
	echo n.n.'<script type="text/javascript" src="index.php?event=l10n_clone_js"></script>'.n.n;
	}

function l10n_serve_clone_js()
	{
	while (@ob_end_clean());
	header("Content-type: text/javascript");
	$text = doSlash( gTxt('are_you_sure') );
	echo <<<js
	function l10n_clone()
		{
		var check = confirm('$text');
		return check;
		}
js;
	exit;
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
		}
	}
function l10n_article_buffer_processor( $buffer )
	{
	global $l10n_vars;

	#
	#	The buffer processing routine injects page elements when editing an article.
	#
	$remaining	= GroupManager::get_remaining_langs( $l10n_vars['article_group'] );
	$can_clone	= (count($remaining) > 0);
	$lang 		= $l10n_vars['article_lang'];
	$id_no		= '-';
	if( $l10n_vars['article_id'] )
		$id_no = $l10n_vars['article_id'];
	$group_id 	= '-';
	if( $l10n_vars['article_group'] )
		$group_id = $l10n_vars['article_group'];

	if( $can_clone and $id_no !== '-' )
		{
		#	Insert the clone panel...
		$f = '<input type="submit" name="save" value="'.gTxt('save').'" class="publish" tabindex="4" />';
		$r = '<fieldset><legend>'.gTxt('l10n-clone_and_translate').'</legend>'.
				hInput('original_ID' , $id_no) .
				hInput('Group' , $group_id) .
				'<p>Translating into: ' . selectInput( 'Lang', $remaining ) . '</p>' .
				'<input type="submit" name="publish" value="'.gTxt('l10n-clone').'" class="publish" onclick="return l10n_clone();" />' .
				'</fieldset>';
		$buffer = str_replace( $f , $f.n.$r , $buffer );
		}

	#	Insert the Language/Group markers...
	$f = '<p><input type="text" id="title"';
	$r = '<p>ID: <strong>' . $id_no . '</strong>, ' . gTxt('language') . ': <strong>'.LanguageHandler::get_native_name_of_lang($lang) . ' ['.$lang.']</strong>, ' . gTxt('group') . ': <strong>' . $group_id . '</strong></p>';
	$buffer = str_replace( $f , $r.n.$f , $buffer );

	return $buffer;
	}

function l10n_add_article_to_group_cb( $event , $step )
	{
	require_privs('article');

	global $vars;
	$new_vars = array_merge( $vars , array( 'Lang' , 'Group' ,  'original_ID' ) );

	//echo br , "l10n_add_article_to_group_cb( $event , $step ) ... " ;

	$save = gps('save');
	if ($save) $step = 'save';

	$publish = gps('publish');
	if ($publish) $step = 'publish';

	//	echo br , "l10n_add_article_to_group_cb( $event , $step ) ... " ;
	switch(strtolower($step))
		{
		case 'publish':
			#	Create a group for this article
			//echo br , "Publishing new article...";
			$incoming = psa($new_vars);
			GroupManager::create_group_and_add( $incoming );
			l10n_setup_vars( $event , $step );
			break;
		}
	}
function l10n_delete_articles_and_redirect( $event , $step )
	{
	global $l10n_vars;
	$method = ps('edit_method');

	if( isset( $l10n_vars['update_tables'] ) AND 'delete' == $method )
		{
		$tables = $l10n_vars['update_tables'];

		if( $tables AND !empty( $tables ) )
			{
			unset( $l10n_vars['update_tables'] );

			#
			#	Re-generate each language table touched by the delete...
			#
			foreach( $tables as $k=>$lang )
				{
				_l10n_generate_lang_table( $lang );
				}
			}
		}

	#
	#	Force a redirect to the 'event=list' page, because the delete
	# multi-edit event will call list_list directly, without us getting a chance
	# to generate the temporary textpattern table to limit the results.
	#
	while (@ob_end_clean());

	$search = gpsa( array( 'search_method' , 'crit' , 'event' , 'step' ) );
	$search['event'] = 'list';
	$search['step'] = '';

	global $l10n_view;
	$l10n_view->redirect( $search );
	}
function l10n_delete_articles_from_group_cb( $event , $step )
	{
	#
	#	This routine handles the delete of articles from containing translation groups
	# It is called when the multi_list of articles is used to delete articles from the
	# DB.
	#
	$method = ps('edit_method');
	$things = ps('selected');

	if( !$things )
		return;

	if( 'delete' == $method )
		{
		global $l10n_vars;
		$languages = array();
		foreach( $things as $id )
			{
			$id = intval($id);
			$info = safe_row( '*' , 'textpattern' , "`ID`='$id'" );
			if( $info !== false )
				{
				$group = $info['Group'];
				$lang = $info['Lang'];
				$languages[$lang] = $lang;
				GroupManager::remove_article( $info['Group'] , $id , $info['Lang'] );
				}
			}
		#
		#	Pass the languages array to the post-process routine to reconstruct the
		# per-language tables that were changed by the edit...
		#
		if( !empty( $languages ) )
			$l10n_vars['update_tables'] = $languages;
		}
	}


function _l10n_generate_lang_table( $lang )
	{
	$code  = LanguageHandler::compact_code( $lang );
	$table_name = 'textpattern_' . $code['short'];

	//echo br , "_l10n_generate_lang_table( $lang ) ... \$table_name=[$table_name] ... ";

	$sql = 'drop table `'.PFX."$table_name`";
	@safe_query( $sql );
	$indexes = "(PRIMARY KEY  (`ID`), KEY `categories_idx` (`Category1`(10),`Category2`(10)), KEY `Posted` (`Posted`), FULLTEXT KEY `searching` (`Title`,`Body`))";
	$sql = "create table `".PFX."$table_name` $indexes select * from `".PFX."textpattern` where `Lang`='$lang'";
	//echo br , "sql: $sql";
	@safe_query( $sql );
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
				_l10n_generate_lang_table( $lang );
				}
		break;
		}
	}
?>

