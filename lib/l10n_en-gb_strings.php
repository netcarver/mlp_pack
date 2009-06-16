<?php

if( !defined( 'txpinterface' ) ) exit;

#
#	The language of the strings in this file...
#
global $l10n_default_strings_lang;
$l10n_default_strings_lang = 'en-gb';	# English (GB)


#
#	These strings are always needed, they will get installed in the language array...
#
global $l10n_default_strings_perm;
$l10n_default_strings_perm = array(
	'l10n-localisation'			=> 'MLP',
	'l10n-toggle'				=> 'Toggle',
	'l10n-snippets_tab'			=> 'Snippets',

	# Strings for the setup wizard...
	'l10n-wizard'				=> 'Wizards',
	'l10n-done'					=> 'Done',
	'l10n-skipped'				=> 'Skipped',
	'l10n-failed'				=> 'Failed',
	'l10n-setup_1_main'			=> 'Extend the `txp_lang.data` field from TINYTEXT to TEXT and add the `{field}` field',
	'l10n-setup_1_title'		=> 'Change the txp_lang table&#8230;',
	'l10n-setup_1_extend'		=> 'Extend `txp_lang.data` from TINYTEXT to TEXT',
	'l10n-setup_2_main'			=> 'Insert the strings for the MLP Pack',
	'l10n-setup_2_langs'		=> 'Set the languages to: {langs}',
	'l10n-setup_3_main'			=> 'Add `{lang}` and `{group}` fields to the textpattern table',
	'l10n-setup_3_title'		=> 'Add fields to the "textpattern" table',
	'l10n-setup_4_main'			=> 'Localise fields in content tables',
	'l10n-setup_6_main'			=> 'Process {count} articles',
	'l10n-setup_11_main'		=> 'Insert MLP tags into pages',
	'l10n-setup_12_main'		=> 'Set site slogan to reflect the browse language',
	'l10n-setup_13_main'		=> 'Upgrade pages and forms from gbp_l10n v0.5 tags',
	'l10n-op_table'				=> '{op} the {table} table',
	'l10n-op_tables'			=> '{op} the {tables} tables',
	'l10n-comment_op'			=> '{op} the default comment invitation',
	'l10n-missing_thing'		=> 'missing {thing}',
	'l10n-missing_all_privs'	=> ' privs - or grants on DB (`{db}` as `{escaped_db}`) cannot be evaluated',
	'l10n-add_field'			=> 'Add the `{table}.{field}` field',
	'l10n-report_privs'			=> 'Potentially missing {name}.<br />User \'{user}\' seems to be missing the privilege(s)&#8230;<br /><strong class="failure">{missing}</strong><br />&#8230; for database \'{db}\' on \'{host}\'.<br /><br />These are necessary for the MLP Pack.<br /><br /><strong>This might be a false result.</strong><br />If you are sure you have these privileges&#8230;<br />{privs}<br />&#8230;then you can continue.<br /><br />If you don\'t know, please check with your database administrator.<br />',
	'l10n-show_debug'			=> '(Show details.)',
	'l10n-skip_field'			=> 'Skip the `{table}.{field}` field - it already exists',
	'l10n-skip_priv_check'		=> "I have all the needed privileges.",
	'l10n-try_again'			=> 'Check again.',
	'l10n-copy_defaults'		=> 'Copy defaults to `{table}.{field}` field',
	'l10n-version_errors'		=> 'MLP Pack Detected Version Problems',
	'l10n-version_reason'		=> 'The MLP Pack cannot operate in this installation because&#8230;',
	'l10n-version_item'			=> 'It requires <strong class="failure">{name} {min}</strong> or above, current install is {current}.',
	'l10n-setup' 				=> 'MLP Pack Setup',
	'l10n-setup_steps' 			=> 'The following steps will be taken to install configure the MLP Pack&#8230;',
	'l10n-setup_report'			=> 'MLP Pack Setup Report&#8230;',
	);


#
#	These are the regular mlp pack strings that will get installed into the txp_lang table...
#
global $l10n_default_strings;
$l10n_default_strings = array(
	'l10n-add_tags'				=> 'Add localisation tags to this window?' ,
	'l10n-add_missing_rend'		=> 'Added missing rendition ({rendition}) to article {ID}',
	'l10n-add_string_rend'		=> '* Incomplete.<br>Add renditions in the missing {side} side languages.',
	'l10n-all_languages'		=> 'Any language',
	'l10n-allow_search_delete' => 'Allow strings to be totally deleted on the snippet > search tab?',
	'l10n-allow_writetab_changes' => "Power users can change a rendition's language or article?",
	'l10n-article_fully_populated' => 'Article "{title}" (number {article}) is fully poplated.',
	'l10n-article_table_ok'		=> 'Article table ok.',
	'l10n-by'					=> 'by',
	'l10n-by_content'			=> 'by content',
	'l10n-by_name'				=> 'by name',
	'l10n-cannot_clone'			=> 'Cannot clone:',
	'l10n-cannot_export'		=> 'Cannot export {lang} as it is not installed on the site.',
	'l10n-clean_feeds'			=> 'Keep Txp\'s normal feed behaviour (don\'t inject language markers in feeds)?',
	'l10n-clone'				=> 'Clone',
	'l10n-clone_and_translate'	=> 'Clone "{article}" for translation',
	'l10n-clone_all_from'		=> 'Clone all {lang} from...',
	'l10n-cannot_delete_all'	=> 'Must have 1+ rendition(s).',
	'l10n-del_phantom' 			=> 'Deleted phantom rendition ({rendition}) from article {ID}',
	'l10n-delete_plugin'		=> 'This will remove ALL strings for this plugin.',
	'l10n-delete_whole_lang'	=> 'Delete all ({var2}) strings in {var1}?',
	'l10n-edit_resource'		=> 'Edit {type}: {owner} ',
	'l10n-email_xfer_subject'	=> '[{sitename}] Notice: {count} rendition{s} transferred to you.',
	'l10n-email_body_other'		=> "{txp_username} has transferred the following rendition{s} to you...\r\n\r\n",
	'l10n-email_body_self'		=> "You transferred the following rendition{s} to yourself...\r\n\r\n",
	'l10n-email_end'			=> "Please don't forget to clear the url-only-title when you translate the rendition{s}!\r\n\r\nThank you,\r\n--\r\n{txp_username}.",
	'l10n-empty'				=> 'empty',
	'l10n-empty_rend_id' 		=> 'Empty rendition id.',
	'l10n-explain_extra_lang'	=> '<p>* These languages are not specified in the public site preferences but may be in use for the admin interface.</p><p>If they are not needed for your site you can delete them.</p>',
	'l10n-explain_specials'		=> 'A list of snippets that appear in the TxP system but not on any page or form.',
	'l10n-export'				=> 'Export',
	'l10n-export_title'			=> '<h2>Export {type} Strings {help}</h2><br/><p>Select languages you wish to include and then click the button.</p>',
	'l10n-filter_label'			=> 'Live Filter&#8230;',
	'l10n-import'				=> 'Import',
	'l10n-import_count'			=> 'Imported {count} {type} strings.',
	'l10n-import_title'			=> '<h2>Import {type} Strings</h2><br/><p>Paste exported file into the box below and click the button.</p>',
	'l10n-import_warning'		=> 'This will insert or OVERWRITE all of the displayed strings.',
	'l10n-inline_editing'		=> 'Inline editing of pages and forms?',
	'l10n-into'					=> 'into',
	'l10n-inout'				=> 'Export/Import',
	'l10n-invalid_import_file'	=> '<p><strong>This is not a valid string file.</strong></p>',
	'l10n-invalid_rend_id'		=> 'Invalid rendition id (needs to be an integer)',
	'l10n-lang_remove_warning'	=> 'This will remove ALL plugin strings in {var1}. ',
	'l10n-lang_remove_warning2'=> 'Delete ALL strings in {var1}. You will need to re-install all strings for {var1} if you want to use it again, even in the admin interface. ',
	'l10n-language_not_supported' => 'Skipping: Language not supported.',
	'l10n-languages' 			=> 'Languages ',
	'l10n-legend_warning'		=> 'Warning/Error',
	'l10n-legend_fully_visible'	=> 'Visible in all languages',
	'l10n-localised'			=> 'Localised',
	'l10n-ltr'					=> 'LTR&nbsp;>',
	'l10n-missing'				=> ' missing.',
	'l10n-missing_rendition'	=> 'Article: {id} missing a rendition.',
	'l10n-no_langs_selected' 	=> 'No languages selected for clone.',
	'l10n-no_plugin_heading'	=> 'Notice&#8230;',
	'l10n-no_rend_matching_id' => 'No matching rendition',
	'l10n-pageform-markup'		=> '<p>[#] = snippet count.</p>',
	'l10n-plugin'				=> 'Plugin',
	'l10n-registered_plugins'	=> 'Registered Plugins.' ,
	'l10n-remove_plugin'		=> "This plugin is no longer installed or may be running from the cache directory.<br/><br/>If this plugin's strings are no longer needed you can remove them.",
	'l10n-renditions'			=> 'Renditions',
	'l10n-clone_by_rend_id'		=> 'Clone rendition (Enter rendition id)',
	'l10n-rendition_delete_ok'	=> 'Rendition {rendition} deleted.',
	'l10n-renditions_for'		=> 'Renditions for ',
	'l10n-rtl'					=> '<&nbsp;RTL',
	'l10n-sbn_rubrik'			=> 'Type your search phrase above.',
	'l10n-sbn_title'			=> 'Type your search term in here.',
	'l10n-search_for_strings'	=> 'Search For {interface} Strings',
	'l10n-search_public_strings_only' => 'Limit string searches to publicly available strings?',
	'l10n-send_notifications'	=> 'Email user when you assign them a rendition?',
	'l10n-send_notice_to_self'	=> '&#8230; even when assigning to yourself?',
	'l10n-send_notice_on_changeauthor' => '&#8230; even when author changed in content > renditions list?',
	'l10n-show_clone_by_id'		=> 'Allow cloning by rendition ID in the article table?',
	'l10n-show_langs'			=> 'Show languages&#8230;',
	'l10n-show_legends' 		=> 'Show article table legend?',
	'l10n-skip_rendition'		=> 'Skipped rendition ({rendition}) while processing article ({ID}) as it uses unsupported language {lang}',
	'l10n-snippet'				=> 'Snippet',
	'l10n-snippets'				=> ' snippets.',
	'l10n-special'				=> 'Special',
	'l10n-specials'				=> 'Specials',
	'l10n-statistics'			=> 'Show Statistics ',
	'l10n-strings'				=> ' strings',
	'l10n-strings_match'		=> ' {interface} strings match&#8230;',
	'l10n-summary'				=> 'Statistics.',
	'l10n-table_rebuilt'		=> 'Article table corrected, try again.',
	'l10n-textbox_title'		=> 'Type in the text here.',
	'l10n-total'				=> 'Total',
	'l10n-unlocalised'			=> 'Unlocalised',
	'l10n-url_exclusions' 		=> 'Exclude these sections/areas from URL re-writes?',
	'l10n-use_browser_languages'	=> 'Use browser "accept-language" headers?',
	'l10n-verify_clone_all'		=> 'Are you sure you want to clone all non-empty source renditions to empty {targ_lang} renditions? This will not overwrite anything but it could take some time.',
	'l10n-view_site'			=> 'View localised site',
	'l10n-warn_section_mismatch' => 'Section mismatch',
	'l10n-warn_lang_mismatch'	=> 'Language mismatch',
	'l10n-xlate_to'				=> 'Translating into: ',

	# Strings for the cleanup wizard...
	'l10n-drop_field'			=> 'Drop the `{table}.{field}` field',
	'l10n-clean_2_main'			=> 'Remove all MLP strings and unregister plugins',
	'l10n-clean_2_unreg'		=> 'Unregistered plugin \'{name}\'',
	'l10n-clean_2_remove_all'	=> 'Remove plugin strings',
	'l10n-clean_2_remove_count'	=> 'Removed {count} strings',
	'l10n-clean_3a_main'		=> 'Drop the `{lang}` and `{group}` fields from the textpattern table',
	'l10n-clean_3a_main_2'		=> 'Check this if you do not want to re-install the MLP Pack',
	'l10n-clean_4a_main'		=> 'Remove Localised content from tables',
	'l10n-clean_8_main'			=> 'Delete cookies',
	'l10n-delete_cookie'		=> 'Delete the {lang} cookie',
	'l10n-cleanup' 				=> 'MLP Pack Cleanup',
	'l10n-cleanup_report'		=> 'MLP Pack Cleanup Report&#8230;',
	'l10n-cleanup_next' 		=> 'The MLP Pack l10n plugin can now be disabled and/or uninstalled.',
	'l10n-cleanup_steps'		=> 'The following steps will be taken to cleanup the MLP Pack&#8230;',
	);
