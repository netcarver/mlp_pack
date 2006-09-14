<?php

/*	TO DO...
	Add new categorisasation feature to the setup wizard -- allow language spec on section prefix/cat/custom field
	Convert the render_lang_list tag handler to use the new group model.

	Add new plugin to handle section/user templates
	//$prefs['custom_3_set'] = 'Injected Name!';
	//$_POST['Category1'] = 'lingua';
	//$_POST['override_form'] = 'lofi';
	//$_POST['Section'] = 'test';
	//$_POST['custom_3'] = 'Value!';
	//$_POST['from_view'] = 'text';	# Needed to force the section picklist from reseting to 'article'.
	//$_POST['Keywords'] = 'Hello World';
	//$_POST['Body'] = "<txp:l10n_if_lang lang=\"en\">\n\t##snip_no_translation##\n</txp:l10n_if_lang>";
	//$_POST['Excerpt'] = "<txp:l10n_if_lang lang=\"en\">\n\t##snip_no_translation##\n</txp:l10n_if_lang>";
*/

// require_plugin() will reset the $txp_current_plugin global
global $txp_current_plugin;
$l10n_current_plugin = $txp_current_plugin;
require_plugin('gbp_admin_library');
$txp_current_plugin = $l10n_current_plugin;

// Constants
if( !defined( 'L10N_LANGUAGE_CONST' ))
	define('L10N_LANGUAGE_CONST', 'language');
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
if( !defined( 'L10N_TRANSLATION_TABLE_PREFIX' ) )
	define( 'L10N_TRANSLATION_TABLE_PREFIX' , 'l10n_textpattern_' );

class GroupManager
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
	function make_textpattern_name( $full_code )
		{
		return L10N_TRANSLATION_TABLE_PREFIX . $full_code['long'];
		}
	function _get_group_info( $id )
		{
		$info = safe_row( '*' , L10N_ARTICLES_TABLE , "`ID`='$id'" );
		if( !empty($info) )
			$info['members'] = unserialize( $info['members'] );
		return $info;
		}
	function create_group( $title , $members , $group_id=0 )
		{
		$members = serialize( $members );
		if( 0 === $group_id )
			$group = safe_insert( L10N_ARTICLES_TABLE , "`names`='$title', `members`='$members'" );
		else
			$group = safe_insert( L10N_ARTICLES_TABLE , "`names`='$title', `members`='$members', `ID`='$group_id'" );
		return $group;
		}
	function destroy_group( $group )
		{
		return safe_delete( L10N_ARTICLES_TABLE , "`ID`='$group'" );
		}
	function _update_group( $group , $title , $members )
		{
		//echo br , "_update_group( $group , $title ," , var_dump( $members ), " )";
		$members = serialize( $members );
		$title = doSlash( $title );
		$group = safe_update( L10N_ARTICLES_TABLE , "`names`='$title', `members`='$members'" , "`ID`='$group'" );
		return $group;
		}
	function change_article_language( $group , $article_id , $article_lang , $target_lang )
		{
		//echo br , "change_article_language( $group , $article_id , $article_lang -> $target_lang ) ... ";
		# get the group info...
		extract( GroupManager::_get_group_info( $group ) );

		if( array_key_exists( $target_lang , $members ) )
			return "Group $group already has a translation for $target_lang.";

		if( !array_key_exists( $article_lang , $members ) )
			return "Article $article_id in $article_lang does not belong to group $group.";
		unset( $members[$article_lang] );

		$members[$target_lang] = $article_id;

		$ok = GroupManager::_update_group( $group , $names , $members );
		return $ok;
		}
	function add_article( $group , $article_id , $article_lang , $check_membership = true , $insert_group = false )
		{
		# get the group info...
		$info = GroupManager::_get_group_info( $group );
		if( empty( $info ) )
			{
			if( $insert_group )
				{
				$group = GroupManager::create_group( '' , array() , $group );
				$info = GroupManager::_get_group_info( $group );
				if( empty( $info ) )
					return "Group $group does not exist and could not be added";
				}
			else
				return "Group $group does not exist";
			}

		extract( $info );

		if( array_key_exists( $article_lang , $members ) )
			return "A translation in $article_lang is already present in group $group.";
		if( $check_membership and in_array( $article_id , $members ) )
			return "Article $article_id is already a member of group $group.";

		$members[$article_lang] = $article_id;
		$ok = GroupManager::_update_group( $ID , $names , $members );
		if( !$ok )
			$ok = "Could not update group $group.";
		return $ok;
		}
	function remove_article( $group , $article_id , $article_lang )
		{
		$g_info = GroupManager::_get_group_info( $group );
		if( empty($g_info) )
			return "Group $group does not exist";

		extract( $g_info );

		if( $members[$article_lang] != $article_id )	# Article is not in this group under this language!
			{
			return "Article $article_lang, not $article_lang translation in group $group.";
			}

		unset( $members[$article_lang] );

		if( !empty( $members ) )
			{
			$result = GroupManager::_update_group( $ID , $names , $members );
			if(!$result)
				$result = "Could not update group $group.";
			}
		else
			{
			$result = safe_delete( L10N_ARTICLES_TABLE , "`ID`='$ID'" );
			if(!$result)
				$result = "Could not delete group $group.";
			}

		return $result;
		}
	function _add_mapping( $group , $mapping )
		{
		$info = GroupManager::_get_group_info( $group );
		if( empty( $info ) or (count($mapping)!==1) )
			return false;

		$mappings = $info['members'];

		foreach( $mapping as $lang=>$id )
			{
			if( in_array( $id , $mappings ) or array_key_exists( $lang, $mappings ) )
				return false;
			}

		$mappings[$lang] = $id;

		GroupManager::_update_group( $group , $info['names'] , $mappings );
		return true;
		}
	function create_group_and_add( $article )
		{
		//echo br , "create_group_and_add(\$article) ... ", var_dump ($article) ,br,br;
		$result = false;
		$name = doSlash($article['Title']);
		$lang = (@$article['Lang']) ? $article['Lang'] : LanguageHandler::get_site_default_lang();
		$id = @$GLOBALS['ID'];
		if( !isset( $id ) or empty( $id ) )
			$id = $article['ID'];
		$mapping =  array( $lang=>strval($id) );

		if( isset( $article['Group'] ) and !empty($article['Group']) )
			{
			$group = $article['Group'];
			GroupManager::_add_mapping( $group , $mapping );
			}
		else
			{
			$group = GroupManager::create_group( $name , $mapping );
			}

		if( $group !== false and $group !== true )
			{
			//	echo br, "Added group '$name'[$group], updating article $id ... `Lang` = '$lang',`Group` = '$group'";
			#	Update the article to point to its group and have a translation accounted to it...
			$result = @safe_update( 'textpattern', "`Lang` = '$lang',`Group` = '$group'" , "ID='$id'" );
			}
		return $result;
		}
	function get_group_members( $group , $exclude_lang )
		{
		#
		#	Returns an array of the lang->article mappings for all members of the
		# given group...
		#
		$info		= GroupManager::_get_group_info( $group );
		$members 	= array();

		if( !empty( $info ) )
			{
			$members = $info['members'];
			if( $exclude_lang and !empty($exclude_lang) and array_key_exists( $exclude_lang , $members ) )
				unset( $members[$exclude_lang] );
			}
		return $members;
		}
	function get_alternate_mappings( $article_id , $exclude_lang , $use_master=false )
		{
		if( $use_master )
			$info = safe_row( '`Group`' , 'l10n_master_textpattern' , "`ID`='$article_id'" );
		else
			$info = safe_row( '`Group`' , 'textpattern' , "`ID`='$article_id'" );
		if( $info === false )
			{
			//echo " ... returning: failed to read article data.";
			return false;
			}

		$group = $info['Group'];
		$alternatives = GroupManager::get_group_members( $group , $exclude_lang );
		return $alternatives;
		}
	function get_remaining_langs( $group )
		{
		#
		#	Returns an array of the site languages that do not have existing translations in this group...
		#
		$langs 	= LanguageHandler::get_site_langs();
		$info 	= GroupManager::_get_group_info( $group );
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
	function move_to_group( $article )
		{
		global $l10n_article_message;
		//echo br , "move_to_group( $article ) ... ";

		#	Get the new entries...
		$new_group	= $article['Group'];
		$new_lang	= (@$article['Lang']) ? $article['Lang'] : LanguageHandler::get_site_default_lang();
		$article_id	= $article['ID'];

		#	Read the existing article entries...
		$info = safe_row( '*' , 'textpattern' , "`ID`='$article_id'" );
		if( $info === false )
			{
			$l10n_article_message = "Error: failed to read article $article_id data.";
			return false;
			}

		$current_group	= $info['Group'];
		$current_lang	= $info['Lang'];

		if( ($new_group == $current_group) and ($new_lang == $current_lang) )
			{
			return true;
			}

		#	Add article to new group...
		$result = GroupManager::add_article( $new_group , $article_id , $new_lang , false );
		if( $result !== true )
			{
			$l10n_article_message = 'Error: ' . $result;
			return false;
			}

		#	Remove article from existing group...
		$result = GroupManager::remove_article( $current_group , $article_id , $current_lang );
		if( $result !== true )
			{
			#	Attempt to remove from the group we just added to...
			remove_article( $new_group , $article_id , $new_lang );
			$l10n_article_message = 'Error: ' . $result;
			return false;
			}

		# 	Update the entries in the article...
		$ok = safe_update( 'textpattern', "`Group`='$new_group' , `Lang`='$new_lang'" , "`ID`='$article_id'" );
		if( $ok )
			$l10n_article_message = "Language: {$current_lang}->{$new_lang}, group:{$current_group}->{$new_group}";
		else
			$l10n_article_message = 'Warning: Failed to record changes to article table';

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
		#	index => array( add|delete|skip , trans-id , group-id , description );
		#
		$result = array();

		$members_count = 0;
		$langs = LanguageHandler::get_site_langs();

		#
		#	Examing the groups table...
		#
		$articles = GroupManager::get_articles( '1=1' );
		if( count( $articles ) )
			{
			while( $article = nextRow($articles) )
				{
				#
				#	Get the group members...
				#
				extract( $article );
				$members = unserialize( $members );
				$m_count = count( $members );
				$members_count += $m_count;

				#
				#	Find the members from the textpattern table too...
				#
				$translations = safe_column( 'ID', 'textpattern' , "`Group`='$ID'" );
				$t_count = count( $translations );

				#
				#	Check the counts are the same...
				#
				if( $t_count !== $m_count )
					{
					#
					#	Take the diffs...
					#
					$diff_members_translations = array_diff( $members , $translations );
					$diff_translations_members = array_diff( $translations , $members );
					$count_m_t = count($diff_members_translations);
					$count_t_m = count($diff_translations_members);

					if( $count_m_t > 0 )
						{
						#
						#	Need to delete translations from the groups table...
						#
						foreach( $diff_members_translations as $lang=>$translation )
							{
							unset( $members[$lang] );
							$result[] = array( 'delete' , $translation , $ID , "Deleted translation $translation from group $ID" );
							}
						GroupManager::_update_group( $ID , $names , $members );
						}
					if( $count_t_m > 0 )
						{
						#
						#	Need to add missing translations to the group table...
						#
						foreach( $diff_translations_members as $translation )
							{
							$details = safe_row( '*' , 'textpattern' , "`ID`='$translation'" );
							$lang = $details['Lang'];

							#
							#	Check it's a valid site language...
							#
							if( !in_array( $lang , $langs ) )
								{
								$result[] = array( 'skip' , $translation , $ID , "Skipped translation $translation while processing group $ID as it uses unsupported language $lang" );
								continue;
								}
							$members[$lang] = $translation;
							GroupManager::_update_group( $ID , $names , $members );
							$result[] = array( 'add' , $translation , $ID , "Added translation $translation to group $ID" );
							}
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
	var $gp = array(L10N_LANGUAGE_CONST);
	var $preferences = array(
		'l10n-languages' => array('value' => array(), 'type' => 'gbp_array_text'),

		'articles' => array('value' => 1, 'type' => 'yesnoradio'),
		'l10n-send_notifications'	=>	array( 'value' => 1, 'type' => 'yesnoradio' ),
		'l10n-send_notice_to_self'	=>	array( 'value' => 0, 'type' => 'yesnoradio' ),
		'l10n-send_notice_on_changeauthor' => array( 'value' => 0, 'type' => 'yesnoradio' ),
		'l10n-allow_writetab_changes' => array( 'value' => 0, 'type' => 'yesnoradio' ),

		'categories' => array('value' => 1, 'type' => 'yesnoradio'),
		'l10n-category_vars' => array('value' => array('title'), 'type' => 'gbp_array_text'),
		'l10n-category_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		// 'links' => array('value' => 0, 'type' => 'yesnoradio'),
		// 'link_vars' => array('value' => array('linkname', 'description'), 'type' => 'gbp_array_text'),
		// 'link_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		'sections' => array('value' => 1, 'type' => 'yesnoradio'),
		'l10n-section_vars' => array('value' => array('title'), 'type' => 'gbp_array_text'),
		'l10n-section_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		'forms'	=> array('value' => 1, 'type' => 'yesnoradio'),

		'pages'	=> array('value' => 1, 'type' => 'yesnoradio'),

		'plugins'	=> array('value' => 1, 'type' => 'yesnoradio'),

		'l10n-inline_editing' => array('value' => 1, 'type' => 'yesnoradio'),
		);
	var $strings_lang = 'en-gb';
	var $strings_prefix = L10N_NAME;
	var $insert_in_debug_mode = false;
	var $perm_strings = array( # These strings are always needed.
		'l10n-localisation'			=> 'Localisation',
		);
	var $strings = array(
		'l10n-allow_writetab_changes' => "Power users can change a translation's language or group",
		'l10n-send_notifications'	=> 'Email user when you assign them a translation?',
		'l10n-send_notice_to_self'	=> '&#8230; even when assigning to yourself?',
		'l10n-send_notice_on_changeauthor' => '&#8230; even when author changed in content > articles list?',
		'l10n-add_tags'				=> 'Add localisation tags to this window?' ,
		'l10n-article_vars'			=> 'Article variables ',
		'l10n-article_hidden_vars'	=> 'Hidden article variables ',
		'l10n-category_vars'		=> 'Category variables ',
		'l10n-category_hidden_vars'	=> 'Hidden category variables ',
		'l10n-clone'				=> 'Clone',
		'l10n-clone_and_translate'	=> 'Clone "{article}" for translation',
		'l10n-section_vars'			=> 'Section variables ',
		'l10n-section_hidden_vars'	=> 'Hidden section variables ',
		'l10n-cleanup_verify'		=> "This will totally remove all l10n tables, strings and translations and the operation cannot be undone. Plugins that require or load l10n will stop working.",
		'l10n-cleanup_wiz_text'		=> 'This allows you to remove the custom tables and almost all of the strings that were inserted.',
		'l10n-cleanup_wiz_title'	=> 'Cleanup Wizard',
		'l10n-cannot_delete_all'	=> 'Must have 1+ translations.',
		'l10n-delete_plugin'		=> 'This will remove ALL strings for this plugin.',
		'l10n-edit_resource'		=> 'Edit $type: $owner ',
		'l10n-empty'				=> 'empty',
		'l10n-explain_no_tags'		=> '<p>* = These forms/pages have snippets but do not have the <em>localise tags</em> needed to display the snippets.</p><p>You can fix this by inserting the needed tags into these pages/forms.</p>',
		'l10n-explain_extra_lang'	=> '<p>* These languages are not specified in the site preferences.</p><p>If they are not needed for your site you can delete them.</p>',
		'l10n-export'				=> 'Export',
		'l10n-import'				=> 'Import',
		'l10n-import_title'			=> '<h2>Import Strings</h2><br/><p>Paste exported files into the box below and click the button.</p>',
		'l10n-inline_editing'		=> 'Inline editing of pages and forms ',
		'l10n-invalid_import_file'	=> '<p><strong>This is not a valid string file.</strong></p>',
		'l10n-import_warning'		=> 'This will insert or OVERWRITE all of the displayed strings.',
		'l10n-lang_remove_warning'	=> 'This will remove ALL plugin strings/snippets in $var1. ',
		'l10n-languages' 			=> 'Languages ',
		'l10n-localised'			=> 'Localised',
		'l10n-pageform-markup'		=> '<p><strong>Bold</strong> = localised.<br/>(Not all items will need localising.)<br/>[#] = snippet count.</p>',
		'l10n-missing'				=> ' missing.',
		'l10n-no_plugin_heading'	=> 'Notice&#8230;',
		'l10n-plugin_not_installed'	=> '<strong>*</strong> These plugins have registered strings but are not installed.<br/><br/>If you have removed the plugin and will not be using it again, you can strip the strings out.',
		'l10n-registered_plugins'	=> 'Registered Plugins.' ,
		'l10n-remove_plugin'		=> "This plugin is not installed.<br/><br/>If this plugin's strings are no longer needed you can remove them.",
		'l10n-setup_verify'			=> 'This will add some tables to your Database. It will also insert a lot of new strings into your txp_lang table and change the `data` field of that table from type TINYTEXT to type TEXT. It will then insert some new fields into the textpattern table.',
		'l10n-setup_wiz_text'		=> 'This allows you to install the custom tables and all of the strings needed (in British English). You will be able to edit and translate the strings once this plugin is setup.',
		'l10n-setup_wiz_title'		=> 'Setup Wizard',
		'l10n-snippets'				=> ' snippets.',
		'l10n-statistics'			=> 'Show Statistics ',
		'l10n-strings'				=> ' strings.',
		'l10n-summary'				=> 'Statistics.',
		'l10n-textbox_title'		=> 'Type in the text here.',
		'l10n-translations_for'		=> 'Translations for ',
		'l10n-total'				=> 'Total',
		'l10n-unlocalised'			=> 'Unlocalised',
		'l10n-view_site'			=> 'View localised site',
		'l10n-wizard'				=> 'Wizards',
		'l10n-site_default_lang'	=> 'Detected $lang as the default language for this site.',
		'l10n-import_fixed_lang' 	=> 'use the default language',
		'l10n-import_cat1_lang'		=> 'use category1 for language',
		'l10n-import_cat2_lang'		=> 'use category2 for language',
		'l10n-import_section_lang'	=> 'use section names for language',
		'l10n-xlate_to'				=> 'Translating into: ',
		'l10n-done'					=> 'Done',
		'l10n-failed'				=> 'Failed',
		'l10n-email_xfer_subject'	=> '[{sitename}] Notice: {count} article{s} transferred to you.',
		'l10n-email_body_other'		=> "{txp_username} has transferred the following article{s} to you...\r\n\r\n",
		'l10n-email_body_self'		=> "You transferred the following article{s} to yourself...\r\n\r\n",
		'l10n-email_end'			=> "Please don't forget to clear the url-only-title when you translate the article{s}!\r\n\r\nThank you,\r\n--\r\n{txp_username}.",
		'l10n-legend_warning'		=> 'Warning/Error',
		'l10n-legend_fully_visible'	=> 'Visible in all languages',
		'l10n-translations'			=> 'Translations',
		'l10n-warn_section_mismatch' => 'Section mismatch',
		'l10n-warn_lang_mismatch'	=> 'Language mismatch',
		'l10n-missing' 				=> ' missing!',
		'l10n-missing_translation'	=> 'Article: {id} missing a translation.',
		'l10n-by'					=> 'by',
		'l10n-into'					=> 'into',
		);
	var $permissions = '1,2,3,6';

	// Constructor
	function LocalisationView( $title_alias , $event , $parent_tab = 'extensions' )
		{
		global $textarray , $production_status;

		if( @txpinterface == 'admin' )
			{
			#	Register callbacks to get admin-side plugins' strings registered.
			register_callback(array(&$this, '_initiate_callbacks'), 'l10n' , '' , 0 );

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

			#	Merge the string that is always needed for the localisation tab title...
			$textarray = array_merge( $textarray , $this->perm_strings );

			#	Only merge and load the rest of the strings if this view's event is active.
			$txp_event = gps('event');
			if( $txp_event === $event )
				{
				if( !$this->installed() or ($this->strings_lang != LANG) )
					{
					# Merge the default language strings into the textarray so that non-English
					# users at least see an English message in the plugin.
					$textarray = array_merge( $textarray , $this->strings );
					}

				#	To ease development, allow new strings to be inserted...
				if( $this->installed() and $this->insert_in_debug_mode and ('debug' === @$production_status) )
					{
					$this->strings = array_merge( $this->strings , $this->perm_strings );
					$ok = StringHandler::remove_strings_by_name( $this->strings , 'admin.l10n' );
					$ok = StringHandler::insert_strings( $this->strings_prefix , $this->strings , $this->strings_lang , 'admin' , 'l10n' , true );
					}

				# Load the strings from the store to the $textarray. This will override the
				# strings inserted above, if they have been translated or edited.
				StringHandler::load_strings_into_textarray( LANG );
				}
			}
		else
			#	Register callbacks to get public-side plugins' strings registered.
			register_callback(array(&$this, '_initiate_callbacks'), 'pretext' , '' , 0 );

		# Be sure to call the parent constructor *after* the strings it needs are added and loaded!
		GBPPlugin::GBPPlugin( gTxt($title_alias) , $event , $parent_tab );
		}

	function preload()
		{
		if ($this->pref('plugins') and has_privs('plugin') )
			new LocalisationStringView( gTxt('plugins'), 'plugin', $this );
		if ($this->pref('pages') and has_privs('page') )
			new LocalisationStringView( gTxt('pages') , 'page' , $this );
		if ($this->pref('forms') and has_privs('form') )
			new LocalisationStringView( gTxt('forms') , 'form' , $this );
		if ($this->pref('articles') and has_privs('article.edit') )
			new LocalisationArticleTabView( gTxt('articles'), 'article', $this, true );
		if ($this->pref('categories') and has_privs('category') )
			new LocalisationTabView( gTxt('categories'), 'category', $this );
		// if ($this->pref('links') and has_privs('link') )
		// 	new LocalisationTabView('links', 'link', $this );
		if ($this->pref('sections') and has_privs('section') )
			new LocalisationTabView( gTxt('sections'), 'section', $this );

		new GBPPreferenceTabView($this);
		new LocalisationWizardView($this);
		}

	function prefs_save_cb( $event='' , $step='' )
		{
		#
		#	Update the set of translation tables based on any changes made to the site
		# languages...
		#
		$langs = LanguageHandler::get_site_langs();
		$tables = getThings( 'show tables like \''.PFX.L10N_TRANSLATION_TABLE_PREFIX.'%\'' );

		#
		#	Expand language names to match translation table name format...
		#
		$names = array();
		if( count( $langs ) )
			foreach( $langs as $name )
				{
				$names[] = PFX.L10N_TRANSLATION_TABLE_PREFIX.$name;
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
			#
			#	Add language tables as needed and populate them as far as possible...
			#
			foreach( $diff_names_tables as $full_name )
				{
				$lang = str_replace( PFX.L10N_TRANSLATION_TABLE_PREFIX , '' , $full_name );
				if( !LanguageHandler::is_valid_code( $lang ) )
					continue;

				//echo br , "Adding $full_name -> $lang [" , LanguageHandler::get_native_name_of_lang( $lang ) , '].';
				$indexes = "(PRIMARY KEY  (`ID`), KEY `categories_idx` (`Category1`(10),`Category2`(10)), KEY `Posted` (`Posted`), FULLTEXT KEY `searching` (`Title`,`Body`))";
				$sql = "create table `$full_name` $indexes select * from `".PFX."textpattern` where Lang='$lang'";
				//echo br , "Using $sql";
				$ok = @safe_query( $sql );
				}
			}
		if( $del_count )
			{
			#
			#	Drop language tables that are no longer needed...
			#
			foreach( $diff_tables_names as $full_name )
				{
				$sql = 'drop table `'.$full_name.'`';
				//echo br , "Using $sql";
				$ok = @safe_query( $sql );
				}
			}
		}

	function _redirect_textpattern($table)
		{
		# Only redirect calls to the textpattern table...
		if( 'textpattern' === $table )
			{
			global $l10n_language;

			$language_set 	= isset( $l10n_language );
			$language_ok	= true;
			if( $language_set and $language_ok )
				{
				$table = GroupManager::make_textpattern_name( $l10n_language );
				//$table = $table.'_'.$l10n_language['long'];
				}
			}
		elseif ( 'l10n_master_textpattern' === $table )
			{
			$table = 'textpattern';
			}
		return $table;
		}

	function installed()
		{
		return LocalisationWizardView::installed();
		}

	function _process_string_callbacks( $event , $step , $pre , $func )
		{
		#	May need to move this to base class when the string handler moves to Admin Lib.
		$key = '';
		if( !is_callable($func , false , $key) )
			return "Cannot call function '$key'.";

		$r = call_user_func($func, $event, $step);
		if( !is_array( $r ) )
			return "Call of '$key' returned a non-array value.";

		extract( $r );

		$result = "Skipped insertion of strings for '$key'.";
		if( $plugin and $prefix and $strings and $lang and $event and (count($strings)) )
			{
			//echo " ... attempting insertions ... ";
			if( StringHandler::insert_strings( $prefix , $strings , $lang , $event , $plugin ) )
				$result = true;
			}

		//echo " ... returning " , (($result === true) ? 'added ok.' : $result) ;
		return $result;
		}

	function _initiate_callbacks( $event , $step='' , $pre=0 )
		{
		#	May need to move this to base class when the string handler moves to Admin Lib.
		#	Our callback routine, in turn, initiates our string enumeration event...
		return $this->_do_callback( "l10n.enumerate_strings", '', 0, array(&$this , '_process_string_callbacks') );
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
		/*
		Graeme - possible routine for the admin library, serves a data block as a file download.
		*/
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

		$out[] = '<div style="padding-bottom: 3em; text-align: center;">';
		if( $this->installed() )
			{
			# Only render the common area at the head of the tabs if the table is installed ok.
			foreach( $this->pref('l10n-languages') as $key )
				{
				$safe_key = trim( $key );	# make sure we trim any spaces out -- they mess up the gTxt call.
				$languages['value'][$safe_key] = gTxt($safe_key);
				}

			if (!gps(L10N_LANGUAGE_CONST))
				{
				$langs = $this->pref('l10n-languages');
				$_GET[L10N_LANGUAGE_CONST] = $langs[0];
				}

			setcookie(L10N_LANGUAGE_CONST, gps(L10N_LANGUAGE_CONST), time() + 3600 * 24 * 365);

			#	Render the top of page div.
			$out[] = form(
				fLabelCell( gTxt('language').': ' ).
				selectInput(L10N_LANGUAGE_CONST, $languages['value'], gps(L10N_LANGUAGE_CONST), 0, 1).
				'<br /><a href="'.hu.gps(L10N_LANGUAGE_CONST).'/">'.gTxt('l10n-view_site').'</a>'.
				$this->form_inputs()
				);
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
					return $this->redirect( '' );
				break;
				}
			}
		}

	}

class LocalisationStringView extends GBPAdminTabView
	{
	/*
	Implements a three-pane view for the categorisation, selection and editing of string based
	data from the txp_lang table.
	*/

	function preload()
		{
		$step = gps('step');
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

		switch ($this->event)
			{
			case 'page':
			$this->render_owner_list('page');
			if ($owner = gps('owner'))
				{
				$this->render_string_list( 'txp_page' , 'user_html' , $owner , $id );
				if( $id )
					$this->render_string_edit( 'page', $owner , $id );
				elseif( $can_edit and in_array($step , $pf_steps) )
					$this->render_pageform_edit( 'txp_page' , 'name' , 'user_html' , $owner );
				}
			break;

			case 'form':
			$this->render_owner_list('form');
			if ($owner = gps('owner'))
				{
				$this->render_string_list( 'txp_form' , 'Form' , $owner , $id );
				if( $id )
					$this->render_string_edit( 'form' , $owner , $id );
				elseif( $can_edit and in_array($step , $pf_steps) )
					$this->render_pageform_edit( 'txp_form' , 'name' , 'Form' , $owner );
				}
			break;

			case 'plugin':
			$this->render_owner_list('plugin');
			if( $step and in_array( $step , $pl_steps ) )
				{
				$this->render_import_list();
				}
			elseif( $owner = gps(L10N_PLUGIN_CONST) and $prefix = gps('prefix') )
				{
				$this->render_plugin_string_list( $owner , $id , $prefix );
				if( $id )
					$this->render_string_edit( 'plugin', $owner , $id );
				}
			break;
			}
		}

	function _generate_list( $table , $fname , $fdata )	# left pane subroutine
		{
		$rs = safe_rows_start( "$fname as name, $fdata as data", $table, '1=1' ) ;
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			$explain = false;
			while ( $a = nextRow($rs) )
				{
				$snippets 	= array();
				$snippets = SnippetHandler::find_snippets_in_block( $a['data'] );
				$localised = SnippetHandler::do_localise( $a['data'] );
				$count = count( $snippets );
				$marker = ($count) ? '['.$count.']' : '';
				$guts = $a['name'].' '.$marker;
				if( !$localised and ($count) )
					{
					$guts .= ' *';
					$explain = true;
					}
				if( $localised or ($count) )
					$guts = '<strong>'.$guts.'</strong>';
				$out[] = '<li><a href="'.$this->parent->url( array('owner'=>$a['name']) , true).'">'.$guts.'</a></li>' . n;
				}
			$out[] = br . gTxt('l10n-pageform-markup') . n;
			if( $explain )
				$out[] = gTxt('l10n-explain_no_tags') . n;
			}
		else
			$out[] = '<li>'.gTxt('none').'</li>'.n;
		return join('', $out);
		}

	function _generate_plugin_list()	# left pane subroutine
		{
		$rps = StringHandler::discover_registered_plugins();
		if( count( $rps ) )
			{
			global $plugins;
			foreach( $rps as $plugin=>$vals )
				{
				extract( $vals );
				$marker = ( !array_search( $plugin, $plugins ) )
					? ' <strong>*</strong>' : '';
				$out[] = '<li><a href="' . $this->parent->url( array(L10N_PLUGIN_CONST=>$plugin,'prefix'=>$pfx) , true ) . '">' .
						$plugin . br . ' [~' .$num . sp . LanguageHandler::get_native_name_of_lang($lang) . '] ' . $marker.
						'</a></li>';
				}
			}
		else
			$out[] = '<li>'.gTxt('none').'</li>'.n;

		return join('', $out);
		}

	function render_owner_list( $type )	#	Render the left pane
		{
		/*
		Renders a list of resource owners for the left-hand pane.
		*/
		$out[] = '<div style="float: left; width: 20%;" class="l10n_owner_list">';

		switch( $type )
			{
			case 'plugin':
			$out[] = '<h3>'.gTxt('l10n-registered_plugins').'</h3>'.n.'<ol>'.n;
			$out[] = $this->_generate_plugin_list();
			break;

			case 'page':
			$out[] = '<h3>'.gTxt('pages').'</h3>'.n.'<ol>'.n;
			$out[] = $this->_generate_list( 'txp_page' , 'name' , 'user_html' );
			break;

			default:
			case 'form':
			$out[] = '<h3>'.gTxt('forms').'</h3>'.n.'<ol>'.n;
			$out[] = $this->_generate_list( 'txp_form' , 'name' , 'Form' );
			break;
			}

		$out[] = '</ol>';
		$out[] = '</div>';
		echo join('', $out);
		}

	function _render_string_list( $strings , $owner_label , $owner_name , $prefix )	# Center pane string render subroutine
		{
		//echo br, "_render_string_list( $strings , $owner_label , $owner_name , $prefix )";

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
					$this->parent->url( array($owner_label=>$owner_name, gbp_id=>$string, 'prefix'=>$prefix) , true ) .
					'">' . $guts . '</a></li>';
				}
			}
		else
			$out[] = '<li>'.gTxt('none').'</li>'.n;

		$out[] = '</ol>';
		return join('', $out);
		}

	function _render_string_stats( $string_name , &$stats )	# Right pane stats render subroutine
		{
		$site_langs 	= LanguageHandler::get_site_langs();

		$out[] = '<h3>'.gTxt('l10n-summary').'</h3>'.n;
		$out[] = '<table>'.n.'<thead>'.n.tr( '<td align="right">'.gTxt('language').'</td>'.n.'<td align="right">&nbsp;&nbsp;&#035;&nbsp;</td>' . td('') . td('') ).n.'</thead><tbody>';
		$extras_found = false;
		foreach( $stats as $iso_code=>$count )
			{
			$name = LanguageHandler::get_native_name_of_lang( $iso_code );
			$remove = '';
			$export = '';
			if( !in_array( $iso_code , $site_langs ) )
				{
				$extras_found = true;
				$remove[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</span>';
				$remove[] = sInput( 'l10n_remove_languageset');
				$remove[] = $this->parent->form_inputs();
				$remove[] = hInput( 'lang_code' , $iso_code );
				$remove = form( join( '' , $remove ) ,
								'' ,
								"verify('" . doSlash(l10n_gTxt('l10n-lang_remove_warning' , array('$var1'=>$name)) ) .
								 doSlash(gTxt('are_you_sure')) . "')");
				}

			$details =  StringHandler::if_plugin_registered( $string_name , $iso_code );
			if( false !== $details )
				{
				$details = unserialize( $details );
				$export[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('l10n-export'), '').'</span>';
				$export[] = sInput( 'l10n_export_languageset');
				$export[] = $this->parent->form_inputs();
				$export[] = hInput( 'language' , $iso_code );
				$export[] = hInput( 'prefix' , $details['pfx'] );
				$export[] = hInput( 'plugin' , $string_name );
				$export = form( join( '' , $export ) );
				}

			$out[]= tr( td( ($extras_found ? ' * ' : '').$name ).td( $count.'&nbsp' ).td($export).td($remove) , ' style="text-align:right;" ' );
			}
		$out[] = tr( td( gTxt('l10n-total') ).td(array_sum($stats).'&nbsp;').td('').td('') , ' style="text-align:right;" ' );
		$out[] = '</tbody></table>';

		if( $extras_found )
			$out[] = gTxt('l10n-explain_extra_lang');

		if( !empty( $string_name ) )
			{
			$import[] = gTxt('l10n-import_title') . br;
			$import[] = '<textarea name="data" cols="60" rows="2" id="l10n_string_import">';
			$import[] = '</textarea>' .br . br;
			$import[] = '<span class="l10n_form_submit">'.fInput('submit', '', gTxt('l10n-import'), '').'</span>';
			$import[] = sInput( 'l10n_import_languageset');
			$import[] = $this->parent->form_inputs();
			$import[] = hInput( 'plugin' , gps('plugin') );
			$import[] = hInput( 'prefix' , gps('prefix') );
			$import[] = hInput( 'language' , gps('language') );
			$out[] = form( join( '' , $import ) , 'border: 1px solid #ccc; padding:1em; margin:1em;' );
			}

		return join( '' , $out );
		}

	function render_plugin_string_list( $plugin , $string_name , $prefix )	# Center pane plugin wrapper
		{
		/*
		Show all the strings and localisations for the given plugin.
		*/
		//echo br, "render_plugin_string_list( $plugin , $string_name , $prefix )";
		$stats 			= array();
		$strings 		= StringHandler::get_plugin_strings( $plugin , $stats , $prefix );
		$strings_exist 	= ( count( $strings ) > 0 );

		$out[] = '<div style="float: left; width: 25%;" class="l10n_plugin_list">';
		$out[] = '<h3>'.$plugin.' '.gTxt('l10n-strings').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' .
				 $this->parent->url( array( L10N_PLUGIN_CONST => $plugin, 'prefix'=>$prefix ) , true ) . '">' .
				 gTxt('l10n-statistics') . '&#187;</a></span>' . br . n;

		$out[] = br . n . $this->_render_string_list( $strings , L10N_PLUGIN_CONST , $plugin , $prefix );
		$out[] = '</div>';

		# Render default view details in right hand pane...
 		if( empty( $string_name ) )
			{
			$out[] = '<div style="float: right; width: 50%;" class="l10n_values_list">';
			$out[] = $this->_render_string_stats( $plugin , $stats );

			# If the plugin is not present offer to delete the lot
			global $plugins;
			if( !array_search( $plugin, $plugins ) )
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

	function render_string_list( $table , $fdata , $owner , $id='' )	# Center pane snippet wrapper
		{
		/*
		Renders a list of strings belonging to the chosen owner in the center pane.
		*/
		$stats 	= array();
		$data 	= safe_field( $fdata , $table , " `name`='$owner'" );
		$snippets = SnippetHandler::find_snippets_in_block( $data );
		$strings  = SnippetHandler::get_snippet_strings( $snippets , $stats );
		$can_edit = $this->pref('l10n-inline_editing');

		$out[] = '<div style="float: left; width: 25%;" class="l10n_string_list">';
		$out[] = '<h3>'.$owner.' '.gTxt('l10n-snippets').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' .
				 $this->parent->url( array( 'owner' => $owner ) , true ) . '">' .
				 gTxt('l10n-statistics') . '&#187;</a></span>' . br . n;
		if( $can_edit )
			 $out[] = '<span style="float:right;"><a href="' .
					 $this->parent->url( array( 'owner'=>$owner , 'step'=>'l10n_edit_pageform' ) , true ) . '">' .
					 l10n_gTxt('l10n-edit_resource' , array('$type'=>$this->event,'$owner'=>$owner) ) .
					 '&#187;</a></span>' . br . n;

		#	Render the list...
		$out[] = br . n . $this->_render_string_list( $strings , 'owner', $owner , '' ) . n;
		$out[] = '</div>';

		#	Render default view details in right hand pane...
		$step = gps('step');
 		if( empty( $id ) and empty( $step ) )
			{
			$out[] = '<div style="float: right; width: 50%;" class="l10n_values_list">';
			$out[] = $this->_render_string_stats( '' , $stats );
			$out[] = '</div>';
			}

		echo join('', $out);
		}

	function render_pageform_edit( $table , $fname, $fdata, $owner )	# Right pane page/form edit textarea.
		{
		$out[] = '<div style="float: right; width: 50%;" class="l10n_values_list">';
		$out[] = '<h3>'.l10n_gTxt('l10n-edit_resource' , array('$type'=>$this->event,'$owner'=>$owner) ).'</h3>' . n;

		$data = safe_field( $fdata , $table , '`'.$fname.'`=\''.doSlash($owner).'\'' );
		$localised = SnippetHandler::do_localise( $data );

		if( !$localised )
			{
			$l[] = '<p>'.gTxt('l10n-add_tags').n;
			$l[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('add'), '').'</div></p>';
			$l[] = sInput('l10n_localise_pageform').n;
			$l[] = $this->parent->form_inputs();
			$l[] = hInput('owner', $owner);
			$l[] = hInput('data', $data);
			$out[] = form( join('', $l) , 'border: 1px solid grey; padding: 0.5em; margin: 1em;' );
			}

		$f[] = '<p><textarea name="data" cols="70" rows="20" title="'.gTxt('l10n-textbox_title').'">' .
			 $data .
			 '</textarea></p>'.br.n;
		$f[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$f[] = sInput('l10n_save_pageform');
		$f[] = $this->parent->form_inputs();
		$f[] = hInput('owner', $owner);
		$out[] = form( join('', $f) , 'padding: 0.5em; margin: 1em;' );

		$out[] = '</div>';
		echo join('', $out);
		}

	function render_string_edit( $type , $owner , $id ) # Right pane string edit routine
		{
		/*
		Render the edit controls for all localisations of the chosen string.
		*/
		$out[] = '<div style="float: right; width: 50%;" class="l10n_values_list">';
		$out[] = '<h3>'.gTxt('l10n-translations_for').$id.'</h3>'.n.'<form action="index.php" method="post"><dl>';

		$string_event = 'snippet';
		if( $type == L10N_PLUGIN_CONST )
			$string_event = $owner;
		$x = StringHandler::get_string_set( $id );
		$final_codes = array();

		#	Complete the set with any missing language codes and empty data...
		$lang_codes = LanguageHandler::get_site_langs();
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
			if( !empty($e) and ($e != $string_event) )
				$string_event = $e;
			$lang = LanguageHandler::get_native_name_of_lang($code);

			$warning = '';
			if( empty( $data['id'] ) )
				$warning .= ' * '.gTxt('l10n-missing').sp;
			elseif( empty( $data['data'] ))
				$warning .= ' * '.gTxt('l10n-empty').sp;

			$out[] = '<dt>'.$lang.' ['.$code.']. '.$warning.'</dt>';
			$out[] = '<dd><p>'.
						'<textarea name="' . $code . '-data" cols="60" rows="2" title="' .
						gTxt('l10n-textbox_title') . '">' . $data['data'] . '</textarea>' .
						hInput( $code.'-id' , $data['id'] ) .
						hInput( $code.'-event' , $data['event'] ) .
						'</p></dd>';
			}

		$out[] = '</dl>';
		$out[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$out[] = sInput('l10n_save_strings');
		$out[] = $this->parent->form_inputs();
		$out[] = hInput('codes', trim( join( ',' , $final_codes ) , ', ' ) );
		$out[] = hInput(L10N_LANGUAGE_CONST, gps(L10N_LANGUAGE_CONST));
		$out[] = hInput('prefix', gps('prefix'));
		if( $type === 'plugin' )
			$out[] = hInput(L10N_PLUGIN_CONST, $owner);
		else
			$out[] = hInput('owner', $owner);
		$out[] = hInput('l10n_type', $type );
		$out[] = hInput('string_event', $string_event);
		$out[] = hInput(gbp_id, $id);
		$out[] = '</form></div>';
		echo join('', $out);
		}

	function render_import_list()
		{
		$d 	= gps( 'data' );
		$d = @unserialize( @base64_decode( @str_replace( "\r\n", '', $d ) ) );

		$o[] = '<div style="float:left;">';
		$o[] = '<h2>'.gTxt('preview').' '.gTxt('file').'</h2>';
		if( !isset($d['plugin']) or !isset($d['prefix']) or !isset($d['event']) or !isset($d['strings']) )
			$o[] = gTxt('l10n-invalid_import_file');
		else
			{
			$f1[] = gTxt('plugin') . ': <strong>'.$d['plugin'].'</strong>'.br.n;
			$f1[] = gTxt('language') . ': <strong>'.LanguageHandler::get_native_name_of_lang($d['lang']).' ['.$d['lang'].']</strong>'.br.br.n;
			$f1[] = hInput( 'data' , gps('data') );
			$f1[] = hInput( 'plugin' , $d['plugin'] );
			$f1[] = hInput( 'prefix' , $d['prefix'] );
			$f1[] = hInput( 'language' , gps('language') );
			$f1[] = sInput( 'l10n_import_languageset');
			$f1[] = hInput( 'commit', 'true' );
			$f1[] = $this->parent->form_inputs();

			foreach( $d['strings'] as $k=>$v )
				{
				$v = htmlspecialchars( $v );
				$l[] = tr( '<td style="text-align: right;">'.$k.' : </td>' . n . td("<input type=\"text\" readonly size=\"100\" value=\"$v\"/>") ) .n ;
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
		}

	function save_strings()
		{
		$string_name 	= gps( gbp_id );
		$event       	= gps( 'string_event' );
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
			$exists			= !empty( $id );
			if( !$exists and empty( $translation ) )
				continue;

			StringHandler::store_translation_of_string( $string_name , $event , $code , $translation , $id );
			}
		}

	function save_pageform()
		{
		$data = doSlash( gps('data') );
		$owner = doSlash( gps('owner') );
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
			$details = unserialize( $details );
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
			StringHandler::insert_strings( $d['prefix'] , $d['strings'] , $d['lang'] , $d['event'] , $d['plugin'] , true );
			unset( $_POST['step'] );
			}
		}

	}

class LocalisationTabView extends GBPAdminTabView
	{

	function preload()
		{
		$step = gps('step');
		if( $step )
			{
			switch( $step )
				{
				case 'l10n_subs_save':
				case 'l10n_subs_post':
					$this->save_post();
				break;
				}
			}
		}

	function main()
		{
		switch ($this->event)
			{
			case 'article':
				if ($id = gps(gbp_id))
					$this->render_edit($this->pref('l10n-article_vars'), $this->pref('l10n-article_hidden_vars'), 'textpattern', "id = '$id'", $id);
				$this->render_list('ID', 'Title', 'textpattern', '1 order by Title asc');
			break;
			case 'category':
				if ($id = gps(gbp_id))
					$this->render_edit($this->pref('l10n-category_vars'), $this->pref('l10n-category_hidden_vars'), 'txp_category', "id = '$id'", $id);
				$this->render_list('id', 'title', 'txp_category', "name != 'root' order by title asc");
			break;
			// case 'link':
			// 	if ($id = gps(gbp_id))
			// 		$this->render_edit($this->pref('link_vars'), $this->pref('link_hidden_vars'), 'txp_link', "id = '$id'", $id);
			// 	$this->render_list('id', 'linkname', 'txp_link', '1 order by linkname asc');
			// break;
			case 'section':
				if ($id = gps(gbp_id))
					$this->render_edit($this->pref('l10n-section_vars'), $this->pref('l10n-section_hidden_vars'), 'txp_section', "name = '$id'", $id);
				$this->render_list('name', 'title', 'txp_section', "name != 'default' order by name asc");
			break;
			}
		}

	function render_list($key, $value, $table, $where)
		{
		$out[] = '<div style="float: left; width: 50%;" class="l10n_list">';

		// SQL used in both queries
		$sql = "FROM ".PFX."$table AS source, ".PFX.L10N_SUBS_TABLE." AS l10n WHERE source.$key = l10n.entry_id AND l10n.entry_value != '' AND l10n.table = '".PFX."$table' AND l10n.language = '".gps(L10N_LANGUAGE_CONST)."' AND $where";

		// Localised
		$rows = startRows("SELECT DISTINCT source.$key as k, source.$value as v ".$sql);
		if ($rows)
			{
			$out[] = '<ul><h3>'.gTxt('l10n-localised').'</h3>';
			while ($row = nextRow($rows))
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_id.'='.$row['k'].'">'.$row['v'].'</a></li>';

			$out[] = '</ul>';
			}

		// Unlocalised
		$rows = startRows("SELECT DISTINCT $key as k, $value as v FROM ".PFX."$table WHERE $key NOT IN (SELECT DISTINCT source.$key $sql) AND $where");
		if ($rows)
			{
			$out[] = '<ul><h3>'.gTxt('l10n-unlocalised').'</h3>';
			while ($row = nextRow($rows))
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_id.'='.$row['k'].'">'.$row['v'].'</a></li>'.n;

			$out[] = '</ul>';
			}

		$out[] = '</div>';
		echo join('', $out);
		}

	function render_edit($vars, $hidden_vars, $table, $where, $entry_id)
		{
		global $_GBP;

		$fields = trim(join(',', array_merge($vars, $hidden_vars)), ' ,');

		if ($rs1 = safe_row($fields, $table, $where))
			{
			$out[] = '<div style="float: right; width: 50%;" class="l10n_edit">';

			foreach($rs1 as $field => $value)
				{
				$entry_value = '';
				$rs2 = safe_row(
					'id, entry_value',
					L10N_SUBS_TABLE,
					"`language` = '".gps(L10N_LANGUAGE_CONST)."' AND `entry_id` = '$entry_id' AND `entry_column` = '$field' AND `table` = '".PFX."$table'"
					);

				$field_type = mysql_field_type(mysql_query("SELECT $field FROM ".PFX.$table), 0);

				if ($rs2)
					extract($rs2);

				if (!isset($entry_value))
					$entry_value = '';

				if (in_array($field_type, array('blob')))
					{
					$out[] = '<p class="l10n_field">'.gTxt($field).'</p>';
					$out[] = '<div class="l10n_value_disable">'.text_area('" readonly class="', 200, 420, $value).'</div>';
					$out[] = '<div class="l10n_value">'.text_area($field, 200, 420, $entry_value).'</div><br/>';
					}
				else if (in_array($field_type, array('string')))
					{
					$out[] = '<p class="l10n_field">'.gTxt($field).'</p>';
					$out[] = '<div class="l10n_value_disable">'.fInput('text', '', $value, 'edit" readonly title="', '', '', 60).'</div>';
					$out[] = '<div class="l10n_value">'.fInput('text', $field, $entry_value, 'edit', '', '', 60).'</div><br/>';
					}
				else
					$out[] = hInput($field, $value);
				}

			$out[] = '<div class="l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
			$out[] = '</div>';

			$out[] = $this->parent->form_inputs();
			$out[] = sInput(((isset($id)) || (gps('step') == 'l10n_subs_save')) ? 'l10n_subs_save' : 'l10n_subs_post');

			$out[] = hInput('l10n_table', $table);
			$out[] = hInput(L10N_LANGUAGE_CONST, gps(L10N_LANGUAGE_CONST));
			$out[] = hInput(gbp_id, $entry_id);

			echo form(join('', $out));
			}
		}

	function save_post()
		{
		global $txpcfg;
		extract(get_prefs());

		$hidden_vars = @gpsa($this->parent->preferences['l10n-'.$this->event.'_hidden_vars']['value']);
		$vars = @gpsa($this->parent->preferences['l10n-'.$this->event.'_vars']['value']);
		if( !empty( $hidden_vars ) )
			extract( $hidden_vars );

		$table = PFX.$_POST['l10n_table'];
		$language = $_POST[L10N_LANGUAGE_CONST];
		$entry_id = $_POST[gbp_id];

		include_once $txpcfg['txpath'].'/lib/classTextile.php';
		$textile = new Textile();

		foreach($vars as $field => $value)
			{

			if ($field == 'Body')
				{

				if (!isset($textile_body))
				$textile_body = $use_textile;

				if ($use_textile == LEAVE_TEXT_UNTOUCHED or !$textile_body)
					$value_html = trim($value);

				else if ($use_textile == CONVERT_LINEBREAKS)
					$value_html = nl2br(trim($value));

				else if ($use_textile == USE_TEXTILE && $textile_body)
					$value_html = $textile -> TextileThis($value);

				}

			if ($field == 'Title')
				$value = $textile->TextileThis($value, '', 1);

			if ($field == 'Excerpt')
				{
				if (!isset($textile_excerpt))
					$textile_excerpt = 1;

				if ($textile_excerpt)
					{
					$value_html = $textile -> TextileThis($value);
					}
				else
					{
					$value_html = $textile -> TextileThis($value, 1);
					}
				}

			if (!isset($id))
				$id = '';

			if (!isset($value_html))
				$value_html = '';

			$value = doSlash( $value );
			$value_html = doSlash( $value_html );

			switch(gps('step'))
				{
				case 'l10n_subs_post':
					$rs = safe_insert(L10N_SUBS_TABLE, "`id` = '$id', `table` = '$table', `language` = '$language', `entry_id` = '$entry_id', `entry_column` = '$field', `entry_value` = '$value', `entry_value_html` = '$value_html'");
				break;
				case 'l10n_subs_save':
					$rs = safe_update(L10N_SUBS_TABLE, "`entry_value` = '$value', `entry_value_html` = '$value_html'",
						"`table` = '$table' AND `language` = '$language' AND `entry_id` = '$entry_id' AND `entry_column` = '$field'"
					);
				break;
				}
			}
		}

	}

class LocalisationArticleTabView extends GBPAdminTabView
	{
	function preload()
		{
		$step = gps('step');
		if( $step )
			{
			switch( $step )
				{
				case 'clone':
					$this->clone_translation();
				break;

				case 'l10n_change_pageby':
					event_change_pageby('article');
				break;

				case 'delete_article':
					$this->delete_article();
				break;

				case 'delete_translation':
					$this->delete_translation();
				break;
				}
			}

		//$results = GroupManager::check_groups();
		//if( !empty( $results ) )
		//	$this->parent->message = 'Groups rebuilt.';
		//else
		//	$this->parent->message = 'Groups ok.';
		}

	function clone_translation()
		{
		$vars = array( 'translation' );
		extract( gpsa( $vars ) );
		$this->parent->message = 'cloning translation:' . $translation;

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
			$this->parent->message = 'No languages selected for clone.';
			return;
			}

		#
		#	Prepare the source translation data...
		#
		$source = safe_row( '*' , 'textpattern' , "`ID`='$translation'" );
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
			$source['Posted'] = 'now()';
			$source['LastMod'] = 'now()';
			$source['feed_time'] = 'now()';
			$source['uid'] = md5(uniqid(rand(),true));

			$insert = array();
			foreach( $source as $k => $v )
				{
				$v = doSlash( $v );
				if( $v === 'now()' )
					$insert[] = "`$k`= $v";
				else
					$insert[] = "`$k`='$v'";
				}
			$insert = join( ', ' , $insert );

			#
			#	Insert into the master textpattern table...
			#
			safe_insert( 'textpattern' , $insert );
			$translation_id = mysql_insert_id();

			#
			#	Add this to the group (article) table...
			#
			GroupManager::add_article( $article_id , $translation_id , $lang );

			#
			#	Add into the translation table for this lang ensure this has the ID of the
			# just added master entry!
			#
			$source['ID'] = $translation_id;
			$insert = array();
			foreach( $source as $k => $v )
				{
				$v = doSlash( $v );
				if( $v === 'now()' )
					$insert[] = "`$k`= $v";
				else
					$insert[] = "`$k`='$v'";
				}
			$insert = join( ', ' , $insert );
			$table_name = GroupManager::make_textpattern_name( array( 'long'=>$lang ) );
			safe_insert( $table_name , $insert );

			#
			#	Now we know the article ID, store this against the author for email notification...
			#
			$language = LanguageHandler::get_native_name_of_lang( $lang );
			$notify[$new_author][$lang] = array( 'id' => $translation_id , 'title'=>$source['Title'] , 'language'=>$language );
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
				#	Construct a list of links to the translations...
				#
				$links = array();
				foreach( $list as $lang => $record )
					{
					extract( $record );
					$msg = 	gTxt('title')  . ": \"$title\"\r\n";
					$msg.= "For translation into $language [$lang].\r\n";
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
					$body = l10n_gTxt( 'l10n-email_body_self' , $subs );
				else
					$body = l10n_gTxt( 'l10n-email_body_other' , $subs );

				$body.= join( "\r\n" , $links ) . "\r\n" . l10n_gTxt( 'l10n-email_end' , $subs );
				$subject = l10n_gTxt( 'l10n-email_xfer_subject' , $subs );

				@txpMail($email, $subject, $body, $replyto);
				//echo br,"Sent email to $email",br,"Reply to: $replyto",br,"Subject: $subject",br,br,"Body: $body",br,br;
				}
			}
		}
	function delete_article()
		{
		#
		#	Deletes an article (multiple translations) from the DB.
		#
		$vars = array( 'article' );
		extract( gpsa( $vars ) );

		#
		#	Read the translation from the master table, extracting Group and Lang...
		#
		$translations = safe_rows( '*' , 'textpattern' , "`Group`='$article'" );

		#
		#	Delete from the master table...
		#
		$master_deleted = safe_delete( 'textpattern' , "`Group`='$article'" );

		#
		#	Delete from the translation tables...
		#
		foreach( $translations as $translation )
			{
			$lang = $translation['Lang'];
			$translation_table = GroupManager::make_textpattern_name( array( 'long'=>$lang ) );
			safe_delete( $translation_table , "`Group`='$article'" );
			}

		#
		#	Delete from the articles table...
		#
		GroupManager::destroy_group( $article );
		}

	function delete_translation()
		{
		$vars = array( 'translation' );
		extract( gpsa( $vars ) );

		#
		#	Read the translation from the master table, extracting Group and Lang...
		#
		$details = safe_row( '*' , 'textpattern' , "`ID`='$translation'" );
		$lang = $details['Lang'];
		$article = $details['Group'];

		#
		#	Delete from the master table...
		#
		$master_deleted = safe_delete( 'textpattern' , "`ID`='$translation'" );

		#
		#	Delete from the correct language translation table...
		#
		$translation_table = GroupManager::make_textpattern_name( array( 'long'=>$lang ) );
		$translation_deleted = safe_delete( $translation_table , "`ID`='$translation'" );

		#
		#	Delete from the article table...
		#
		$article_updated = GroupManager::remove_article( $article , $translation , $lang );

		if( $master_deleted and $translation_deleted and $article_updated )
			$this->parent->message = 'Translation: '.$translation.' deleted OK.';
		else
			{
			$results = GroupManager::check_groups();
			if( !empty( $results ) )
				{
				$this->parent->message = $results[0][3];
				'Groups rebuilt.';
				}
			else
				{
				$this->parent->message = 'Groups ok.';
				}
			}
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

	function render_article_table()
		{
		$event = $this->parent->event;
		//$d_art_title = gTxt( 'l10n-delete_article_title' );
		//$d_trn_title = gTxt( 'l10n-delete_trans_title' );
		//$clone_title = gTxt( 'l10n-clone_title' );

		#
		#	Pager calculations...
		#
		extract( get_prefs() );				#	Keep the articles/page count in sync.
		extract(gpsa(array('page')));
		$total = GroupManager::get_total();
		$limit = max(@$article_list_pageby, 15);
		list($page, $offset, $numPages) = pager($total, $limit, $page);

		#
		#	User permissions...
		#
		$can_delete = has_privs( 'article.delete' );
		$can_clone  = has_privs( 'l10n.clone' );

		#
		#	Get the statuses array...
		#
		$statuses = array(
			1 => gTxt('draft'),
			2 => gTxt('hidden'),
			3 => gTxt('pending'),
			4 => gTxt('live'),
			5 => gTxt('sticky'),
			);

		#
		#	Init language related vars...
		#
		$langs = LanguageHandler::get_site_langs();
		$full_lang_count = count( $langs );
		$default_lang = LanguageHandler::get_site_default_lang();

		#
		#	Link our css file and start building the table...
		#
		$o[] = n . '<link href="lib/mlp.css" rel="Stylesheet" type="text/css" />' . n;
		$o[] = startTable( /*id*/ 'l10n_articles_table' , /*align*/ '' , /*class*/ '' , /*padding*/ '5px' );
		$o[] = '<caption><strong>'.gTxt('articles').'</strong></caption>';

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

		#
		#	Process the articles (textpattern_groups) table...
		#
		#	Use values from the pager to grab the right sections of the table.
		#
		$articles = GroupManager::get_articles( '1=1' , 'ID' , $offset , $limit );
		if( count( $articles ) )
			{
			while( $article = nextRow($articles) )
				{
				$num_visible = 0;		# 	Holds a count of Sticky/Live translations of this article.
				$trclass = '';			#	Class for the row (=article)
				$counts['article']+= 1;	#	Increment the article count.
				$cells = array();		#	List of table cells (=translations) in this row
				$sections = array();	#	Holds a list of the unique sections used by translations in this article.

				#
				#	Pull out the article (NB: Not translation!)...
				#
				extract( $article );
				$members = unserialize( $members );
				$n_translations_expected = count( $members );


				if( $can_delete )
					$delete_art_link = '<a href="'. $this->parent->url( array('page'=>$page,'step'=>'delete_article', 'article'=>$ID), true ) .
										'" class="clone-link" onclick="return verify(\'' .
										doSlash(gTxt('confirm_delete_popup')) .
										'\')"><img src="txp_img/l10n_delete.png" /></a>';
				else
					$delete_art_link = '';

				#
				#	Compose the leading (article) cell...
				#
				$cells[] = td( $delete_art_link . $ID , '' , 'id' );

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
				for( $i=0 ; $i < $n_translations ; $i++ )
					{
					$lang = $translations[$i]['Lang'];
					if( in_array( $lang , $langs ) )
						{
						$n_valid_translations++;
						$index[$lang] = $i;
						}
					else
						continue;

					#
					#	Check that the translation is recorded in the article members!
					#
					if( !array_key_exists( $lang , $members ) )
						{
						$this->parent->message = l10n_gTxt( 'l10n-missing_translation' , array( '{id}'=>$ID ) );
						$members[$lang] = $translations[$i]['ID'];
						GroupManager::_update_group( $ID , $names , $members );
						$n_valid_translations++;
						}
					}

				#
				#	Does the article info disagree with the translation set?
				#
				//if( $n_valid_translations !== $n_translations_expected )
				//	{
				//	echo br , "Warning: article $ID recorded $n_translations_expected but there are $n_translations of which $n_valid_translations are valid!";
				//	}

				#
				#	Are all expected translations present?
				#
				$all_translations_present = ($n_valid_translations === $full_lang_count);


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
						$status = !empty($status_no) ? $statuses[$status_no] : '';
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
							$clone_link = 	'<a href="' . $this->parent->url( array('page'=>$page,'step'=>'start_clone','translation'=>$id,'article'=>$ID), true ) .
											'" class="clone-link"><img src="txp_img/l10n_clone.png" /></a>';

						#
						#	Make the delete link...
						#
						if( $can_delete )
							$delete_trans_link = 	'<a href="' . $this->parent->url( array('page'=>$page,'step'=>'delete_translation', 'translation'=>$id), true ) .
													'" class="delete-link" onclick="return verify(\'' .
													doSlash(gTxt('confirm_delete_popup')) .
													'\')"><img src="txp_img/l10n_delete.png" /></a>';
						else
							$delete_trans_link = '';

						$content = 	$delete_trans_link . strong( $title ) . br . $section . ' &#8212; ' . $author .
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
		$l[] = $this->add_legend_item( 'status_1' , $statuses[1] );
		$l[] = $this->add_legend_item( 'status_2' , $statuses[2] . '/'. gTxt('none') );
		$l[] = $this->add_legend_item( 'status_3' , $statuses[3] );
		$l[] = $this->add_legend_item( 'status_4' , $statuses[4] );
		$l[] = $this->add_legend_item( 'status_5' , $statuses[5] );
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

		$o[] = tag( join( '' , $body) , 'tbody' );
		$o[] = endTable();
		$o[] = n.nav_form( $event, $page, $numPages, '', '', '', '');
		$o[] = n.pageby_form( $event, $article_list_pageby );

		echo join( '' , $o );
		}

	function render_start_clone()
		{
		$vars = array( 'translation' , 'page' );
		extract( gpsa( $vars ) );

		#
		#	Get the un-translated languages for the article that owns this translation...
		#
		$details = safe_row( '*' , 'textpattern' , "`ID`='$translation'" );
		$title   = $details['Title'];
		$article = $details['Group'];
		$author  = $details['AuthorID'];
		$to_do = GroupManager::get_remaining_langs( $article );
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

		#
		#	Link our css file and start building ...
		#
		$o[] = n . '<link href="lib/mlp.css" rel="Stylesheet" type="text/css" />' . n;
		$o[] = startTable( /*id*/ 'l10n_clone_table' , /*align*/ '' , /*class*/ '' , /*padding*/ '5px' );
		$o[] = '<caption><strong>'.l10n_gTxt('l10n-clone_and_translate' , array( '{article}'=>$title ) ).'</strong></caption>';

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
		$s .= hInput( 'translation' , $translation );
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
		'1' => array('setup' => 'Extend the txp_lang.data field from TINYTEXT to TEXT'),
		'2' => array(
			'setup' => 'Insert the strings for this plugin',
			'cleanup' => 'Remove plugin strings'),
		'3' => array(
			'setup' => 'Add `Lang` and `Group` fields to textpattern table',
			'cleanup' => 'Drop the `Lang` and `Group` fields from the textpattern table'),
		'4' => array(
			'setup' => 'Add the substitutions table',
			'cleanup' => 'Drop the substitutions table'),
		'5' => array(
			'setup' => 'Add the articles table',
			'cleanup' => 'Drop the articles table'),
		'6' => array('setup' => 'Process articles'),
		'7' => array(
			'setup' => 'Add the language native textpattern tables',
			'cleanup' => 'Drop the language native textpattern tables'),
		'8' => array ( 'cleanup' => 'Delete cookies' ),
		);

	function installed()
		{
		$result = getThing( 'show tables like \''.PFX.L10N_ARTICLES_TABLE.'\'' );
		return ($result);
		}

	function setup_1()
		{
		# Extend the txp_lang table to allow text instead of tinytext in the data field.
		$sql = ' CHANGE `data` `data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
		$ok = @safe_alter( 'txp_lang' , $sql );
		$this->add_report_item( 'Extend the txp_lang.data field from TINYTEXT to TEXT' , $ok );
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
			$sql[] = " ADD `Lang` VARCHAR( 8 ) CHARACTER SET utf8 COLLATE utf8_general_ci ";
			$sql[] = " NOT NULL DEFAULT '-' AFTER `LastModID` , ";
			$sql[] = " ADD `Group` INT( 11 ) NOT NULL DEFAULT '0' AFTER `Lang`";
		$ok = @safe_alter( 'textpattern' , join('', $sql) );
		$this->add_report_item( 'Add `Lang` and `Group` fields to textpattern table' , $ok );
		}

	function setup_4()
		{
		$sql = array();
			$sql[] = 'CREATE TABLE IF NOT EXISTS `'.PFX.L10N_SUBS_TABLE.'` (';
			$sql[] = '`id` int(11) NOT NULL AUTO_INCREMENT, ';
			$sql[] = '`table` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , ';
			$sql[] = '`language` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , ';
			$sql[] = '`entry_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci default NULL, ';
			$sql[] = '`entry_column` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci default NULL, ';
			$sql[] = '`entry_value` text CHARACTER SET utf8 COLLATE utf8_general_ci, ';
			$sql[] = '`entry_value_html` text CHARACTER SET utf8 COLLATE utf8_general_ci, ';
			$sql[] = 'PRIMARY KEY (`id`)';
			$sql[] = ') TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=1';
		$ok = safe_query(join('', $sql));
		$this->add_report_item( 'Add the "'.L10N_SUBS_TABLE.'" table' , $ok );
		}

	function setup_5()
		{
		$ok = GroupManager::create_table();
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
			$table_name = GroupManager::make_textpattern_name( $code );
			$indexes = "(PRIMARY KEY  (`ID`), KEY `categories_idx` (`Category1`(10),`Category2`(10)), KEY `Posted` (`Posted`), FULLTEXT KEY `searching` (`Title`,`Body`))";
			$sql = "create table `".PFX."$table_name` $indexes select * from `".PFX."textpattern` where Status>=4";
			$ok = @safe_query( $sql );
			$this->add_report_item( 'Add the '. LanguageHandler::get_native_name_of_lang( $lang ) .' ['.$table_name.'] table' , $ok , true );
			}
		}

	function cleanup_2()
		{
		# Remove the strings...
		$this->parent->strings = array_merge( $this->parent->strings , $this->parent->perm_strings );
		$ok = StringHandler::remove_strings_by_name( $this->parent->strings , 'admin' );
		$this->add_report_item( ($ok===true)?'Remove plugin strings':"Removed $ok strings" , true );
		}

	function cleanup_3()
		{
		$drop = 'true';
		//$drop = gps( 'l10n_drop_textpattern_extensions' );
		if( !empty($drop) )
			{
			$sql = "drop `Lang`, drop `Group`";
			$ok = @safe_alter( 'textpattern' , $sql );
			$this->add_report_item( 'Drop the `Lang` and `Group` fields from the textpattern table' , $ok );
			}
		else
			$this->add_report_item( 'Skipped dropping the `Lang` and `Group` fields from the textpattern table' , $ok );
		}

	function cleanup_4()
		{
		$sql = 'drop table `'.PFX.L10N_SUBS_TABLE.'`';
		$ok = @safe_query( $sql );
		$this->add_report_item( 'Delete the "'.L10N_SUBS_TABLE.'" table' , $ok );
		}

	function cleanup_5()
		{
		$ok = GroupManager::destroy_table();
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
			$table_name = GroupManager::make_textpattern_name( $code );
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
			$time = time() - 3600;
			$ok = setcookie( $lang , $lang , $time );
			$this->add_report_item( 'Delete the '. LanguageHandler::get_native_name_of_lang( $lang ) . ' cookie' , $ok , true );
			}
		}
	# TODO : get user choice as to which language to revert the site to...
	# revert_content( form_table , field_list , validated_language_choice );
		# revert_content will ...
		# 	remove l10n_localise tags,
		#	revert any found snippets with their language replacement (if any)
		#	leave only blocks marked with matching if_lang statements
		#	change all get_lang tags to the chosen lang
	# revert_content( page_table , field_list , validated_language_choice );
	# revert_content( article_table , field_list , validated_language_choice );

	function _import_cat1_lang()
		{
		#
		#	Scan cat1 to find the language for an article.
		# Probably need a mapping from cat1 lang -> iso language unless we are lucky.
		#
		}

	function _import_cat2_lang()
		{
		}

	function _import_section_lang()
		{
		}

	function _import_fixed_lang()
		{
		# 	Scans the articles, creating a group for each and adding it and setting the
		# language to the site default...

		$where = "1";
		$rs = safe_rows_start( 'ID , Title' , 'textpattern' , $where );
		$count = @mysql_num_rows($rs);

		//$lang = $this->pref('l10n-languages');
		$i = 0;
		if( $rs && $count > 0 )
			{
			while ( $a = nextRow($rs) )
				if( GroupManager::create_group_and_add( $a ) )
					$i++;
			}
		if( $i === $count )
			return true;

		return "$i of $count";
		}

	}

global $l10n_view, $prefs;
$l10n_view = new LocalisationView( 'l10n-localisation' , L10N_NAME, 'content' );
$prefs['db_redirect_func'] = array(&$l10n_view, '_redirect_textpattern');

class LanguageHandler
	{
	/*
	class LanguageHandler implements ISO-693-1 language support.
	*/
	function do_fleshout_names( $langs , $append_code = false , $append_default=false )
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
				if( $append_default and ($code === LanguageHandler::get_site_default_lang() ) )
					$tmp .= ' - ' . gTxt('default');
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
		# This is a subset of the full array, limited to known TxP admin-side translations.
		# If you need a language not listed here, cut and paste from the help section of this plugin.
		static $iso_693_1_langs = array(
		'ca'=>array( 'ca'=>'Català' ) ,
		'cs'=>array( 'cs'=>'Čeština' ) ,
		'da'=>array( 'da'=>'Dansk' ) ,
		'de'=>array( 'de'=>'Deutsch' ) ,
		'el'=>array( 'el'=>'Ελληνικά' ) ,
		'en'=>array( 'en'=>'English' , 'en-gb'=>'English (GB)' , 'en-us'=>'English (US)' ),
		'es'=>array( 'es'=>'Español' ),
		'et'=>array( 'et'=>'Eesti Keel' ),
		'eu'=>array( 'eu'=>'Euskera' ),
		'fi'=>array( 'fi'=>'Suomi' ),
		'fr'=>array( 'fr'=>'Français' ),
		'he'=>array( 'he'=>'עברית / עִבְרִית' ,'dir'=>'rtl' ),
		'hu'=>array( 'hu'=>'Magyar' ),
		'id'=>array( 'id'=>'Bahasa Indonesia' ),
		'is'=>array( 'is'=>'Íslenska' ),
		'it'=>array( 'it'=>'Italiano' ),
		'ja'=>array( 'ja'=>'日本語' ),
		'lv'=>array( 'lv'=>'Latviešu' ),
		'nl'=>array( 'nl'=>'Nederlands' ),
		'no'=>array( 'no'=>'Norsk' ),
		'pl'=>array( 'pl'=>'Polski' ),
		'pt'=>array( 'pt'=>'Português' ),
		'ro'=>array( 'ro'=>'Română' ),
		'ru'=>array( 'ru'=>'Русский' ),
		'sk'=>array( 'sk'=>'Slovenčina' ),
		'sv'=>array( 'sv'=>'Svenska' ),
		'th'=>array( 'th'=>'ภาษาไทย' ),
		'uk'=>array( 'uk'=>"Українська" ),
		'zh'=>array( 'zh'=>'中文(简体)' , 'zh-cn'=>'中文(简体)' , 'zh-tw'=>'中文(國語)'  ),
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
		$dir = '';
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
		Returns an array of the ISO-693-1 languages the site supports.
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

	}

class SnippetHandler
	{
	/*
	class SnippetHandler implements localised "snippets" within page and
	form templates. Uses the services of the string_handler to localise the
	strings therein.
	*/

	function  get_pattern( $name )
		{
		# Use the first snippet detection pattern for a simple snippet format that is visible when the substitution fails.
		# Use the second snippet detection pattern if you want unmatched snippets as xhtml comments.
		static $snippet_pattern = "/##([\w|\.|\-]+)##/";
		//	var $snippet_pattern = "/\<\!--##([\w|\.|\-]+)##--\>/";

		# The following pattern is used to match any l10n_snippet tags in pages and forms.
		static $snippet_tag_pattern = "/\<txp:l10n_snippet name=\"([\w|\.|\-]+)\"\s*\/\>/";

		# The following are the localise tag pattern(s)...
		static $tag_pattern = '/\<\/*txp:l10n_localise\s*\>/';

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
			return '<txp:l10n_localise>'.$thing.'</txp:l10n_localise>';
			break;

			default:
			case 'check':
			return SnippetHandler::has_localisation_tags( $thing );
			break;
			}
		}

	function find_snippets_in_block( &$thing , $merge = false , $get_data = false )
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
		array_shift( $tags );
		$tags = $tags[0];
		$tags = doArray( $tags , 'strtolower' );
		$out = array_merge( $out , $tags );

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
						if( $get_data)
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

		//	$where = " `event`='snippet' AND `name` IN ($name_set)";
		$where = " `name` IN ($name_set)";
		$rs = safe_rows_start( 'lang, name', 'txp_lang', $where );

		return array_merge( $result , StringHandler::get_strings( $rs , $stats ) );
		}
	}

class StringHandler
	{
	function make_legend( $title , $args = null )
		{
		$title = l10n_gTxt( $title , $args );
		$title = mb_convert_case( $title , MB_CASE_TITLE , 'utf-8' );
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

	function if_plugin_registered( $plugin , $lang , $count = 0 )
		{
		global $prefs;
		$name = StringHandler::do_prefs_name( $plugin );
		if( $name and ( $details = @$prefs[$name] ) )
			return $details;
		return false;
		}

	function register_plugin( $plugin , $pfx , $string_count , $lang , $event )
		{
		$name = StringHandler::do_prefs_name( $plugin );
		$vals = array( 'pfx'=>doSlash($pfx) , 'num'=>$string_count , 'lang'=>$lang , 'event'=>doSlash($event) );
		return set_pref( doSlash($name) , serialize($vals) , L10N_NAME , 2 );
		}

	function unregister_plugin( $plugin )
		{
		global $prefs;
		$name = doSlash( StringHandler::do_prefs_name( $plugin ) );
		@safe_delete( 'txp_prefs' , "`name`='$name' AND `event`='".L10N_NAME.'\'' );
		unset( $prefs[$name] );
		}

	function insert_strings( $pfx , $strings , $lang , $event='' , $plugin='' , $override = false )
		{
		/*
		PLUGIN SUPPORT ROUTINE
		Plugin authors: CALL THIS FROM THE IMMEDIATE PROCESSING SECTION OF YOUR PLUGIN'S ADMIN CODE.
		Adds the given array of prefixed, aliased, strings to txp_lang
		*/
		global	$txp_current_plugin;
		//echo br , "insert_strings( $pfx , $strings , $lang , $event , $plugin , $override )";
		if( empty($strings) or !is_array($strings) or empty($lang) or empty($pfx) )
			return null;

		if( empty( $plugin) )
			$plugin = $txp_current_plugin;

		# If needed, register the plugin...
		$num = count($strings);
		if( false === StringHandler::if_plugin_registered($plugin , $lang , $num ) )
			StringHandler::register_plugin( $plugin , $pfx , $num , $lang , $event );
		elseif( !$override )
			return false;
		//echo "... plugin now registered";
		# If the prefix doesn't end with the required sep character, add it...
		$pfx_len = strlen( $pfx );
		if( $pfx[$pfx_len-1] != L10N_SEP )
			{
			$pfx .= L10N_SEP;
			$pfx_len += 1;
			}

			# if the plugin is known, store it as a suffix to any strings stored...
		if( !empty($plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$plugin;
		//echo "... prefix is $pfx, event is $event";
		#	Iterate over the $strings and, for each that is not present, enter them into the sql table...
		$lastmod 	= date('YmdHis');
		$lang 		= doSlash( $lang );
		$event 		= doSlash( $event );
		foreach( $strings as $name=>$data )
			{
			$data = doSlash($data);

			# If the name isn't prefixed yet, add the prefix...
			if( substr( $name , 0 , $pfx_len ) !== $pfx )
				$name = doSlash($pfx . $name);
			else
				$name = doSlash( $name );

			$set 	= "`lang`='$lang', `lastmod`='$lastmod', `event`='$event', `data`='$data'";
			$where 	= ", `name`='$name'";
			@safe_insert( 'txp_lang' , $set . $where );
			if( $override )
				@safe_update( 'txp_lang' , $set , $where );
			}

		# Cleanup empty strings.
		@safe_delete( 'txp_lang', "`data`=''");
		return true;
		}

	function store_translation_of_string( $name , $event , $new_lang , $translation , $id='' )
		{
		/*
		ADMIN SUPPORT ROUTINE
		For use by the localisation plugin.
		Can create, delete or update a row in the DB depending upon the calling arguments.
		*/
		global	$txp_current_plugin;

		if( empty($name) or empty($event) or empty($new_lang) )
			return null;

		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;

		$name = doSlash( $name );
		$event = doSlash( $event );
		$new_lang = doSlash( $new_lang );
		$translation = doSlash( $translation );
		$id = doSlash( $id );

		$lastmod 		= date('YmdHis');
		$set 	= " `lang`='$new_lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$translation'" ;

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

	function store_translation_by_id( $id , $new_lang , $translation )
		{
		/*
		ADMIN SUPPORT ROUTINE
		For use by the localisation plugin. Clones the entry with the given id and stores the
		translation in the data and sets the lang and date as given.
		*/
		# 	Check we have valid arguments...
		if( empty($id) or empty($translation) or empty($new_lang) )
			return null;

		#	Does the row to copy exist?
		$id = doSlash( $id );
		$row = safe_row( '*' , 'txp_lang' , "`id`=$id" );
		if( !$row )
			return false;

		extract( $row );
		$translation	= doSlash( $translation );
		$new_lang 		= doSlash( $new_lang );
		$lastmod 		= date('YmdHis');
		$set 			= " `lang`='$new_lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$translation'" ;

		@safe_insert( 'txp_lang' , $set );
		}

	function remove_strings( $plugin , $remove_lang , $debug = '' )
		{
		/*
		PLUGIN SUPPORT ROUTINE
		Either: Removes all the occurances of plugin and snippet strings in the given langs...
		OR:		Removes all of the named plugin's strings.
		*/
		if( $remove_lang and !empty( $remove_lang ) )
			{
			$where = "(`lang` IN ('$remove_lang')) AND (`event` LIKE \"common.%\" OR `event` LIKE \"public.%\" OR `event` LIKE \"admin.%\" OR `event`='snippet')";
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			}
		elseif( $plugin and !empty( $plugin ) )
			{
			$where = "`event`=\"common.$plugin\" OR `event`=\"public.$plugin\" OR `event`=\"admin.$plugin\"";
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			StringHandler::unregister_plugin( $plugin );
			}
		}

	function remove_strings_by_name( $strings , $event = '' )
		{
		/*
		PLUGIN SUPPORT ROUTINE
		Plugin authors: CALL THIS FROM THE IMMEDIATE PROCESSING SECTION OF YOUR PLUGIN'S ADMIN CODE.
		Removes all of the named strings in ALL languages. (It uses the keys of the strings array).
		*/
		global	$txp_current_plugin , $prefs;
		if( !$strings or !is_array( $strings ) or empty( $strings ) )
			return null;

		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;
		$event 	= doSlash( $event );

		$result = false;
		$n_strings = count( $strings );
		if( $n_strings )
			{
			$deletes = 0;
			foreach( $strings as $name=>$data )
				{
				$name 	= doSlash($name);
				$where 	= " `name`='$name'";
				if( !empty($event) )
					$where .= " AND `event`='$event'";
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

		if( $txp_current_plugin )
			StringHandler::unregister_plugin( $txp_current_plugin );

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

	function load_strings( $lang , $filter='' )
		{
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Loads all strings of the given language into an array and returns them.
		*/
		$extras = array();
		$where  = ' AND ( event=\'snippet\' OR event LIKE "public.%" OR event LIKE "common.%" ';
		$close = ')';
		if( @txpinterface == 'admin' )
			$close = 'OR event LIKE "admin.%" )';

		$rs = safe_rows_start('name, data','txp_lang','lang=\''.doSlash($lang).'\'' . $where . $close . $filter );
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
					'plugin'	=> $owner,	#	Name the plugin these strings are for.
					'prefix'	=> $prefix,	#	Its unique string prefix
					'lang'		=> $lang,	#	The language of the initial strings.
					'event'		=> $event,	#	public/admin/common = which interface the strings will be loaded into
					);

		$filter = ' AND `name` LIKE "'.doSlash($prefix).L10N_SEP.'%"';
		$r['strings'] = StringHandler::load_strings( $lang, $filter );
		$result = chunk_split( base64_encode( serialize($r) ) , 64 );
		//	echo br, "serialize_strings( $lang , $owner , $prefix , $event ) ... \$filter=$filter", br, var_dump( $r ), br, var_dump( $result ), br;
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
		$where = ' `name` LIKE "'.$prefix.L10N_SEP.'%"';
		$rs = safe_rows_start( 'lang, name', 'txp_lang', $where );
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

	function get_string_set( $string_name )
		{
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Given a string name, will extract an array of the matching translations.
		translation_lang => string_id , event , data
		*/
		$result = array();

		$where = ' `name` = "'.doSlash($string_name).'"';
		$rs = safe_rows_start( 'lang, id, event, data', 'txp_lang', $where );
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

	function gTxt( $alias, $args=null )
		{
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Given a string name, will pull the string out of the $textarray and perform any argument replacements needed.
		*/
		global $textarray;
		global $l10n_language;

		$lang = $l10n_language;
		if( !$lang )
			$lang = LanguageHandler::get_site_default_lang();

		$out = @$textarray[ $alias ];
		if( !$out or ($out === $alias) )
			$out = "($lang) $alias";

		if( isset( $args ) and is_array( $args ) and count($args) )
			{
			foreach( $args as $pattern=>$value )
				$out = preg_replace( '/\\'.$pattern.'/' , $value , $out );
			}

		return $out;
		}
	} // End class StringHandler

#	PUBLIC/ADMIN WRAPPER ROUTINES...
function l10n_gTxt( $name , $args = null )
	{
	/*
	Plugin authors can define strings with embedded variables that get preg_replaced
	based on the the argument array.

	So a string : 'plugin_name_hello' => 'Hello there $name.'

	could be replaced like this from within the plugin...
	l10n_gTxt( 'plugin_name_hello' , array( '$name'=>$name ) );
	*/
	return StringHandler::gTxt( $name , $args );
	}

?>
