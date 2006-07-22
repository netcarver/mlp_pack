<?php

$plugin['name'] = 'gbp_l10n';
$plugin['version'] = '0.5';
$plugin['author'] = 'Graeme Porteous';
$plugin['author_uri'] = 'http://porteo.us/projects/textpattern/gbp_l10n/';
$plugin['description'] = 'Textpattern content localisation.';
$plugin['type'] = '1';

@include_once('../zem_tpl.php');

if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. Instructions

Under the content tab, there is a new localisation subtab. Here you can find a list of every article, category title and section titles which needs tobe localised.

To see your localised content you need to surround *everything* in all of your page and form templates with @<txp:gbp_localise>@ ... @</txp:gbp_localise>@

You can also use @<txp:gbp_localise section="foo" />@ or @<txp:gbp_localise category="bar" />@ to output localised sections and categories

h2. Snippets

To add snippets to pages or forms...

# Make sure the page/form is wrapped with the @<txp:gbp_localise>@ ... @</txp:gbp_localise>@ statements.
# Within those statements type a string starting and ending with two hash characters, like this "##my_first_snippet##" (no need for the quotation marks.)
# On the *content > localise* tab, look for your page or form on the pages or form subtab.
# Click on the page/form name to bring up a list of all snippets therein.
# You should see your snippet "my_first_snippet" listed with no translations.
# Click on the name of your snippet to bring up the edit boxes. 
# Supply appropriate translations and hit the save button.
# Now looking at your site should give you the correct translation according to the url you type.

# --- END PLUGIN HELP ---
-->
<?php
}
# --- BEGIN PLUGIN CODE ---

// Constants
if (!defined('gbp_language'))
	define('gbp_language', 'language');
if (!defined('gbp_plugin'))
	define('gbp_plugin', 'plugin');

// require_plugin() will reset the $txp_current_plugin global
global $txp_current_plugin;
$gbp_current_plugin = $txp_current_plugin;
require_plugin('gbp_admin_library');
$txp_current_plugin = $gbp_current_plugin;

if( !defined( 'GBP_PREFS_LANGUAGES' ))
	define( 'GBP_PREFS_LANGUAGES', $gbp_current_plugin.'_languages' );

class LocalisationView extends GBPPlugin 
	{
	
	var $gp = array(gbp_language);
	var $preferences = array(
		'languages' => array('value' => array( 'en' , 'fr' , 'de' ), 'type' => 'gbp_array_text'),

		'articles' => array('value' => 1, 'type' => 'yesnoradio'),
		'article_vars' => array('value' => array('Title', 'Body', 'Excerpt'), 'type' => 'gbp_array_text'),
		'article_hidden_vars' => array('value' => array('textile_body', 'textile_excerpt'), 'type' => 'gbp_array_text'),

		'categories' => array('value' => 1, 'type' => 'yesnoradio'),
		'category_vars' => array('value' => array('title'), 'type' => 'gbp_array_text'),
		'category_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		// 'links' => array('value' => 0, 'type' => 'yesnoradio'),
		// 'link_vars' => array('value' => array('linkname', 'description'), 'type' => 'gbp_array_text'),
		// 'link_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		'sections' => array('value' => 1, 'type' => 'yesnoradio'),
		'section_vars' => array('value' => array('title'), 'type' => 'gbp_array_text'),
		'section_hidden_vars' => array('value' => array(), 'type' => 'gbp_array_text'),

		'forms'	=> array('value' => 1, 'type' => 'yesnoradio'),

		'pages'	=> array('value' => 1, 'type' => 'yesnoradio'),

		'plugins'	=> array('value' => 1, 'type' => 'yesnoradio'),
		);

	var $strings_lang = 'en';
	var $perm_strings = array( # These strings are always needed.
	'gbp_l10n_localisation'			=> 'localisation',
	);
	var $strings = array(	
	'gbp_l10n_cleanup_verify'		=> "This will totally remove all l10n tables, strings and translations and the operation cannot be undone. Plugins that require or load l10n will stop working.",
	'gbp_l10n_cleanup_wiz_text'		=> 'This allows you to remove the custom table and almost all of the strings that were inserted.',
	'gbp_l10n_cleanup_wiz_title'	=> 'Cleanup Wizard',
	'gbp_l10n_delete_plugin'		=> 'This will remove ALL strings for this plugin.',
	'gbp_l10n_edit_resource'		=> 'Edit $type: $owner ',
	'gbp_l10n_explain_extra_lang'	=> '<p>* These languages are not specified in the site preferences.</p><p>If they are not needed for your site you can delete them.</p>',
	'languages' 					=> 'Languages ',
	'gbp_l10n_lang_remove_warning'	=> 'This will remove ALL plugin strings/snippets in $var1. ',
	'gbp_l10n_localised'			=> 'Localised',
	'gbp_l10n_missing'				=> ' missing.', 
	'gbp_l10n_no_plugin_heading'	=> 'Notice&#8230;',
	'gbp_l10n_plugin_not_installed'	=> '<strong>*</strong> These plugins have registered strings but are not installed.<br/><br/>If you have removed the plugin and will not be using it again, you can strip the strings out.',
	'gbp_l10n_registered_plugins'	=> 'Registered Plugins.' ,
	'gbp_l10n_remove_plugin'		=> "This plugin is not installed.<br/><br/>If this plugin's strings are no longer needed you can remove them.",
	'gbp_l10n_setup_verify'			=> 'This will add a table called gbp_l10n to your Database. It will also insert a lot of new strings into your txp_lang table and change the `data` field of that table from type TINYTEXT to type TEXT.',
	'gbp_l10n_setup_wiz_text'		=> 'This allows you to install the custom table and all of the strings definitions needed (in English). You will be able to edit and translate the strings once this plugin is setup.',
	'gbp_l10n_setup_wiz_title'		=> 'Setup Wizard',
	'gbp_l10n_snippets'				=> ' snippets.',
	'gbp_l10n_statistics'			=> 'Show Statistics ',
	'gbp_l10n_strings'				=> ' strings.',
	'gbp_l10n_summary'				=> 'Statistics.',
	'gbp_l10n_textbox_title'		=> 'Type in the text here.',
	'gbp_l10n_translations_for'		=> 'Translations for ',
	'gbp_l10n_unlocalised'			=> 'Unlocalised',
	'gbp_l10n_view_site'			=> 'View localised site', 
	'gbp_l10n_wizard'				=> 'Wizards',
	);

	// Constructor
	function LocalisationView( $title_alias , $event , $parent_tab = 'extensions' ) 
		{
		global $textarray;
		$lang = explode( '-' , LANG );

		#	Merge the string that is always needed for the localisation tab title...
		$textarray = array_merge( $textarray , $this->perm_strings );

		#	Only merge and load the rest of the strings if this view's event is active. 
		$txp_event = gps('event');
		if( $txp_event === $event )
			{
			if( !$this->installed() or ($this->strings_lang != $lang[0]) )
				{
				# Merge the default language strings into the textarray so that non-English 
				# users at least see an English message in the plugin. If the user adds translations later 
				# the translations will override these merged strings.
				$textarray = array_merge( $textarray , $this->strings );
				}

			# Pull the language from the TxP language variable and use that to load the strings from 
			# the store to the $textarray. This will override the strings inserted above, if they have
			# been translated or edited.
			StringHandler::load_strings_into_textarray( $lang[0] );
			}

		# Be sure to call the parent constructor *after* the strings it needs are added and loaded!
		GBPPlugin::GBPPlugin( gTxt($title_alias) , $event , $parent_tab );
		}

	function preload() 
		{
		global $gbp, $txp_current_plugin, $_GBP;
		$gbp[$txp_current_plugin] = &$this;
		$_GBP[0] = &$this;

		#	NB: This event processing must occur before the installed() check below to
		# prevent the creation of tabs if the content is not yet installed...
		$step = gps('step');
		if( $step )	
			{
			switch( $step ) 
				{
				case 'cleanup':		
					$this->cleanup();
				break;

				case 'setup':		
					$this->setup();
				break;
				}
			}

		if( $this->installed() )
			{
			if ($this->preferences['articles']['value'])
				new LocalisationTabView( gTxt('articles'), 'article', $this);
			if ($this->preferences['categories']['value'])
				new LocalisationTabView( gTxt('categories'), 'category', $this);
			// if ($this->preferences['links']['value'])
			// 	new LocalisationTabView('links', 'link', $this);
			if ($this->preferences['sections']['value'])
				new LocalisationTabView( gTxt('sections'), 'section', $this);
			if ($this->preferences['forms']['value'])
				new LocalisationStringView( gTxt('forms') , 'form' , $this );
			if ($this->preferences['pages']['value'])
				new LocalisationStringView( gTxt('pages') , 'page' , $this );
			if ($this->preferences['plugins']['value'])
				new LocalisationStringView( gTxt('plugins'), 'plugin', $this );
			new GBPPreferenceTabView( gTxt('tab_preferences'), 'preference', $this);
			}

		new LocalisationTabView( gTxt('gbp_l10n_wizard'), 'wizard', $this);

		}	# end preload()
		
	function installed() 
		{
		$result = getThing( "show tables like '".PFX."gbp_l10n'" );
		return ($result);
		}
	
	function setup() 
		{
		/*
		One-shot installation code goes in here...
		*/

		# Adds the strings this class needs. These lines makes them editable via the "plugins" string tab.
		StringHandler::insert_strings( $this->perm_strings , $this->strings_lang , 'admin' );
		StringHandler::insert_strings( $this->strings , $this->strings_lang , 'admin' );

		# Create the l10n table...
		$sql[] = 'CREATE TABLE IF NOT EXISTS `'.PFX.'gbp_l10n` (';
		$sql[] = '`id` int(11) NOT NULL AUTO_INCREMENT, ';
		$sql[] = '`table` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , ';
		$sql[] = '`language` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , ';
		$sql[] = '`entry_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci default NULL, ';
		$sql[] = '`entry_column` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci default NULL, ';
		$sql[] = '`entry_value` text CHARACTER SET utf8 COLLATE utf8_general_ci, ';
		$sql[] = '`entry_value_html` text CHARACTER SET utf8 COLLATE utf8_general_ci, ';
		$sql[] = 'PRIMARY KEY (`id`)';
		$sql[] = ') TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=1';

		safe_query(join('', $sql));
		
		/*
		SED: Extend the txp_lang table to allow text instead of tinytext in the data field.
		This adds one byte per entry but gives much more flexibility to the strings/snippets for uses such as full 
		paragraphs of static text with some xhtml markup.
		*/
		$sql = ' CHANGE `data` `data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
		safe_alter( 'txp_lang' , $sql );
		
		$this->redirect( array( 'event'=>'l10n' , gbp_tab=>'preference' ) );
		}	# end setup()
	
	function cleanup() 
		{
		# Cleanup code follows...
		$sql = 'drop table `'.PFX.'gbp_l10n`';
		@safe_query( $sql );
		
		# Delete the perm_strings, the defaults will, however, be merged into textarray by the constructor...
		StringHandler::remove_strings_by_name( $this->perm_strings , 'admin' );
		
		# These get totally removed and don't get re-inserted by the setup routine...
		StringHandler::remove_strings_by_name( $this->strings , 'admin' );
	
		# Now the cleanup is complete, redirect to the plugin page for the delete.
		# Not strictly necessary, but a convenience for the user.
		$this->redirect( array( 'event'=>'plugin' ) );
		}	# end cleanup()

	function main() 
		{
		$out[] = '<div style="padding-bottom: 3em; text-align: center;">';
		if( $this->installed() )
			{
			# Only render the common area at the head of the tabs if the table is installed ok.
			foreach ($this->preferences['languages']['value'] as $key)
				$languages['value'][$key] = gTxt($key);

			if (!gps(gbp_language))
				$_GET[gbp_language] = $this->preferences['languages']['value'][0];

			setcookie(gbp_language, gps(gbp_language), time() + 3600 * 24 * 365);

			#	Render the top of page div.
			$out[] = form(
				fLabelCell( gTxt('language').': ' ).
				selectInput(gbp_language, $languages['value'], gps(gbp_language), 0, 1).
				'<br /><a href="'.hu.gps(gbp_language).'/">'.gTxt('gbp_l10n_view_site').'</a>'.
				$this->form_inputs()
				);
			}
		$out[] = '</div>';

		echo join('', $out);
		}	# end main()
	
	function end()
		{
		$step = gps('step');
		if( $step )	
			{
			switch( $step ) 
				{
				case 'prefs_save':
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
				case 'gbp_save_strings' :
				$this->save_strings();
				break;

				# Called if the user chooses to delete the string set for a removed plugin.
				case 'gbp_remove_stringset' :	
				$this->remove_strings();
				break;

				# Called if the user chooses to remove a specific languages' strings.
				# eg if they entered some french translations but later drop french from the site.
				case 'gbp_remove_languageset' :	
				$this->remove_strings();
				break;
				
				case 'gbp_save_pageform':
				$this->save_pageform();
				break;
				}
			}
		}
	
	function main()
		{
		switch ($this->event)
			{
			case 'page':
			$this->render_owner_list('page');
			if ($owner = gps('owner'))
				{
				$id = gps(gbp_id);
				$this->render_string_list( 'txp_page' , 'user_html' , $owner , $id );
				if( $id )
					$this->render_string_edit( 'page', $owner , $id );
				elseif( $step = gps('step') and $step == 'edit_pageform' )
					{
					$this->render_pageform_edit( 'txp_page' , 'name' , 'user_html' , $owner );
					}
				}
			break;

			case 'form':
			$this->render_owner_list('form');
			if ($owner = gps('owner'))
				{
				$id = gps(gbp_id);
				$this->render_string_list( 'txp_form' , 'Form' , $owner , $id );
				if( $id )
					$this->render_string_edit( 'form' , $owner , $id );
				elseif( $step = gps('step') and $step == 'edit_pageform' )
					{
					$this->render_pageform_edit( 'txp_form' , 'name' , 'Form' , $owner );
					}
				}
			break;

			case 'plugin':
			$this->render_owner_list('plugin');
			if ($owner = gps(gbp_plugin))
				{
				$id = gps(gbp_id);
				$this->render_plugin_string_list( $owner , $id );
				if( $id )
					$this->render_string_edit( 'plugin', $owner , $id );
				}
			break;
			}
		}

	function _generate_list( $table , $fname )	# left pane subroutine
		{
		$rs = safe_rows_start( "$fname as name", $table, '1=1' ) ;
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				$out[] = '<li><a href="'.$this->parent->url().'&#38;owner='.$a['name'].'">'.$a['name'].'</a></li>';
			}
		else
			{
			$out[] = '<li>'.gTxt('none').'</li>'.n;
			}
		return join('', $out);
		}

	function _generate_plugin_list()	# left pane subroutine
		{
		$registered_plugins = StringHandler::discover_registered_plugins();
		if( count( $registered_plugins ) )
			{
			//	Get an array of installed plugins. Not all of them will have registered for 
			// string support...
			global $plugins;
			
			foreach( $registered_plugins as $plugin )
				{
				//	Display marker if the plugin isn't installed anymore.
				$marker = ( !array_search( $plugin, $plugins ) )
					? ' <strong>*</strong>' : '';
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_plugin.'='.$plugin.'">'.$plugin.$marker.'</a></li>';
				}
			}
		else
			{
			$out[] = '<li>'.gTxt('none').'</li>'.n;
			}
		return join('', $out);
		}

	function render_owner_list( $type )	#	Render the left pane
		{
		/*
		Renders a list of resource owners for the left-hand pane.
		*/
		$out[] = '<div style="float: left; width: 20%;" class="gbp_i18n_owner_list">';

		switch( $type )
			{
			case 'plugin':
			$out[] = '<h3>'.gTxt('gbp_l10n_registered_plugins').'</h3>'.n.'<ol>'.n;
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

	function _render_string_list( $strings , $owner_label , $owner_name )	# Center pane string render subroutine
		{
		$strings_exist 	= ( count( $strings ) > 0 );
		if( !$strings_exist )
			return '';

		$site_langs 	= LanguageHandler::get_site_langs();

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
				$out[]= '<li><a href="'.$this->parent->url().'&#38;'.$owner_label.'='.$owner_name.'&#38;'.gbp_id.'='.$string.'">' . 
						$guts .
						'</a></li>';
				}
			}
		else
			{
			$out[] = '<li>'.gTxt('none').'</li>'.n;
			}

		$out[] = '</ol>';
		return join('', $out);
		}

	function _render_string_stats( $string_name , &$stats )	# Right pane stats render subroutine
		{
		$site_langs 	= LanguageHandler::get_site_langs();

		//
		//	Render stats summary for the strings...
		//
		$out[] = '<h3>'.gTxt('gbp_l10n_summary').'</h3>'.n;
		$out[] = '<ul>';
		$extras_found = false;
		foreach( $stats as $iso_code=>$count )
			{
			$name = LanguageHandler::get_native_name_of_lang( $iso_code );
			$guts = $count . ' ' . $name;
			$remove = '';
			if( !in_array( $iso_code , $site_langs ) )
				{
				$extras_found = true;
				$remove[] = '<span class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</span>';
				$remove[] = sInput( 'gbp_remove_languageset');
				$remove[] = $this->parent->form_inputs();
				$remove[] = hInput( 'lang_code' , $iso_code );
				$guts = form( $guts . ' * ' . join( '' , $remove ) , 
								'' ,
								"verify('" . doSlash(gbp_gTxt('gbp_l10n_lang_remove_warning' , array('$var1'=>$name)) ) . 
								 doSlash(gTxt('are_you_sure')) . "')");
				}
			$out[]= '<li>'.$guts.'</li>';
			}
		$out[]= '<li style="border-top: 1px solid gray; margin-right: 1em;">'.array_sum($stats).' '.gTxt('gbp_l10n_strings').'</li>';
		$out[] = '</ul>';
		if( $extras_found )
			$out[] = gTxt('gbp_l10n_explain_extra_lang');

		return join( '' , $out );
		}
		
	function render_plugin_string_list( $plugin , $string_name )	# Center pane plugin wrapper
		{
		/*
		Show all the strings and localisations for the given plugin.
		*/
		$stats 			= array();
		$strings 		= StringHandler::get_plugin_strings( $plugin , $stats );
		$strings_exist 	= ( count( $strings ) > 0 );

		$out[] = '<div style="float: left; width: 25%;" class="gbp_i18n_plugin_list">';
		$out[] = '<h3>'.$plugin.' '.gTxt('gbp_l10n_strings').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' . 
				 $this->parent->url( array( gbp_plugin => $plugin ) , true ) . '">' . 
				 gTxt('gbp_l10n_statistics') . '&#187;</a></span>' . br . n;
		
		$out[] = $this->_render_string_list( $strings , gbp_plugin , $plugin );
		$out[] = '</div>';
		
		//
		//	Render default view details in right hand pane...
		//
 		if( empty( $string_name ) )
			{
			$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
			$out[] = $this->_render_string_stats( $string_name , $stats );
			
			//
			//	If the plugin is not present start with a box offering to delete the lot!
			//
			global $plugins;
			if( !array_search( $plugin, $plugins ) )
				{
				$out[] = '<h3>'.gTxt('gbp_l10n_no_plugin_heading').'</h3>'.n;
				$del[] = graf( gTxt('gbp_l10n_remove_plugin') );
				$del[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</div>';
				$del[] = sInput('gbp_remove_stringset');
				$del[] = $this->parent->form_inputs();
				$del[] = hInput(gbp_plugin, $plugin);

				$out[] = form(	join('', $del) , 
								'border: 1px solid grey; padding: 0.5em; margin: 1em;' ,
								"verify('".doSlash(gTxt('gbp_l10n_delete_plugin')).' '.doSlash(gTxt('are_you_sure'))."')");
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

		$out[] = '<div style="float: left; width: 25%;" class="gbp_i18n_string_list">';
		$out[] = '<h3>'.$owner.' '.gTxt('gbp_l10n_snippets').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' . 
				 $this->parent->url( array( 'owner' => $owner ) , true ) . '">' . 
				 gTxt('gbp_l10n_statistics') . '&#187;</a></span>' . br . n;
		$out[] = '<span style="float:right;"><a href="' . 
				 $this->parent->url( array( 'owner'=>$owner , 'step'=>'edit_pageform' ) , true ) . '">' . 
				 gbp_gTxt('gbp_l10n_edit_resource' , array('$type'=>$this->event,'$owner'=>$owner) ) . 
				 '&#187;</a></span>' . br . br . n;

		#	Render the list... 
		$out[] = $this->_render_string_list( $strings , 'owner', $owner );
		$out[] = '</div>';

		//
		//	Render default view details in right hand pane...
		//
		$step = gps('step');
 		if( empty( $id ) and empty( $step ) )
			{
			$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
			$out[] = $this->_render_string_stats( $id , $stats );
			$out[] = '</div>';
			}

		echo join('', $out);
		}

	function render_pageform_edit( $table , $fname, $fdata, $owner )	# Right pane page/form edit textarea.
		{
		$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
		$out[] = '<h3>'.gbp_gTxt('gbp_l10n_edit_resource' , array('$type'=>$this->event,'$owner'=>$owner) ).'</h3>'.n.'<form action="index.php" method="post">';
		
		$data = safe_field( $fdata , $table , '`'.$fname.'`=\''.doSlash($owner).'\'' );
		$out[] = '<p><textarea name="data" cols="70" rows="20" title="'.gTxt('gbp_l10n_textbox_title').'">' . 
				 $data . 
				 '</textarea></p>'.br.n;
		$out[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$out[] = sInput('gbp_save_pageform');
		$out[] = $this->parent->form_inputs();
		$out[] = hInput('owner', $owner);
		$out[] = '</form></div>';
		echo join('', $out);
		}
		
	function render_string_edit( $type , $owner , $id ) # Right pane string edit routine
		{
		/*
		Render the edit controls for all localisations of the chosen string.
		*/
		$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
		$out[] = '<h3>'.gTxt('gbp_l10n_translations_for').$id.'</h3>'.n.'<form action="index.php" method="post"><dl>';
		
		$string_event = 'snippet';
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

			$out[] = '<dt>'.$lang.' ('.$code.').'.((empty($data['data'])) ? ' *' . gTxt('gbp_l10n_missing') : '' ).'</dt>';
			$out[] = '<dd><p>'.
						'<textarea name="' . $code . '-data" cols="60" rows="1" title="' . 
						gTxt('gbp_l10n_textbox_title') . '">' . $data['data'] . '</textarea>' .
						hInput( $code.'-id' , $data['id'] ) . 
						'</p></dd>';
			}
		
		$out[] = '</dl>';
		$out[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$out[] = sInput('gbp_save_strings');
		$out[] = $this->parent->form_inputs();
		$out[] = hInput('codes', trim( join( ',' , $final_codes ) , ', ' ) );
		$out[] = hInput(gbp_language, gps(gbp_language));
		if( $type === 'plugin' )
			$out[] = hInput(gbp_plugin, $owner);
		else
			$out[] = hInput('owner', $owner);
		$out[] = hInput('gbp_type', $type );
		$out[] = hInput('string_event', $string_event);
		$out[] = hInput(gbp_id, $id);
		$out[] = '</form></div>';
		echo join('', $out);

		}

	function remove_strings()
		{
		$remove_langs 	= gps('lang_code');
		$plugin 		= gps(gbp_plugin);

		StringHandler::remove_strings( $plugin , $remove_langs );
		}

	function save_strings() 
		{
		$string_name 	= gps( gbp_id );
		$event       	= gps( 'string_event' );
		$codes			= gps( 'codes' );
		$lang_codes		= explode( ',' , $codes );
		
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
			{
			@safe_update( 'txp_form' , "`Form`='$data'" , "`name`='$owner'" );
			}
		elseif( $tab === 'page' )
			{
			@safe_update( 'txp_page' , "`user_html`='$data'" , "`name`='$owner'" );
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
				case 'gbp_save':
				case 'gbp_post':
					$this->save_post();
				break;
				}
			}
		}

	function main() 
		{
		switch ($this->event)
			{
			case 'wizard':
				$this->render_wizard();
			break;
			case 'article':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['article_vars']['value'], $this->parent->preferences['article_hidden_vars']['value'], 'textpattern', "id = '$id'", $id);
				$this->render_list('ID', 'Title', 'textpattern', '1 order by Title asc');
			break;
			case 'category':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['category_vars']['value'], $this->parent->preferences['category_hidden_vars']['value'], 'txp_category', "id = '$id'", $id);
				$this->render_list('id', 'title', 'txp_category', "name != 'root' order by title asc");
			break;
			// case 'link':
			// 	if ($id = gps(gbp_id))
			// 		$this->render_edit($this->parent->preferences['link_vars']['value'], $this->parent->preferences['link_hidden_vars']['value'], 'txp_link', "id = '$id'", $id);
			// 	$this->render_list('id', 'linkname', 'txp_link', '1 order by linkname asc');
			// break;
			case 'section':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['section_vars']['value'], $this->parent->preferences['section_hidden_vars']['value'], 'txp_section', "name = '$id'", $id);
				$this->render_list('name', 'title', 'txp_section', "name != 'default' order by name asc");
			break;
			}
		}

	function render_wizard()
		{
		$out[] = '<div style="border: 1px solid gray; width: 50em; text-align: center; margin: 1em auto; padding: 1em; clear: both;">';

		if( $this->parent->installed() )
			{
			$out[] = '<h1>'.gTxt('gbp_l10n_cleanup_wiz_title').'</h1>';
			$out[] = graf( gTxt('gbp_l10n_cleanup_wiz_text') );

			$out[] = form(
				fInput('submit', '', gTxt('cleanup'), '') . $this->parent->form_inputs() . sInput( 'cleanup' ) , 
				'' ,
				"verify('".doSlash(gTxt('are_you_sure')).' '.doSlash( gTxt('gbp_l10n_cleanup_verify'))."')"
						 );
			}
		else
			{
			$out[] = '<h1>'.gTxt('gbp_l10n_setup_wiz_title').'</h1>';
			$out[] = graf( gTxt('gbp_l10n_setup_wiz_text') );
			$out[] = form(
				fInput('submit', '', gTxt('Setup'), '') . $this->parent->form_inputs() . sInput( 'setup' ) , 
				'' ,
				"verify('".doSlash(gTxt('are_you_sure')).' '.doSlash(gTxt('gbp_l10n_setup_verify'))."')"
						 );
			}
			
		$out[] = '</div>';		
		echo join('', $out);
		}
		
	function render_list($key, $value, $table, $where) 
		{
		$out[] = '<div style="float: left; width: 50%;" class="gbp_i18n_list">';

		// SQL used in both queries
		$sql = "FROM ".PFX."$table AS source, ".PFX."gbp_l10n AS l10n WHERE source.$key = l10n.entry_id AND l10n.entry_value != '' AND l10n.table = '".PFX."$table' AND l10n.language = '".gps(gbp_language)."' AND $where";

		// Localised
		$rows = startRows("SELECT DISTINCT source.$key as k, source.$value as v ".$sql);
		if ($rows) 
			{
			$out[] = '<ul><h3>'.gTxt('gbp_l10n_localised').'</h3>';
			while ($row = nextRow($rows))
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_id.'='.$row['k'].'">'.$row['v'].'</a></li>';

			$out[] = '</ul>';
			}

		// Unlocalised
		$rows = startRows("SELECT DISTINCT $key as k, $value as v FROM ".PFX."$table WHERE $key NOT IN (SELECT DISTINCT source.$key $sql) AND $where");
		if ($rows) 
			{
			$out[] = '<ul><h3>'.gTxt('gbp_l10n_unlocalised').'</h3>';
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
			$out[] = '<div style="float: right; width: 50%;" class="gbp_l10n_edit">';

			foreach($rs1 as $field => $value) 
				{
				$rs2 = safe_row(
					'id, entry_value',
					'gbp_l10n',
					"`language` = '".gps(gbp_language)."' AND `entry_id` = '$entry_id' AND `entry_column` = '$field' AND `table` = '".PFX."$table'"
					);

				$field_type = mysql_field_type(mysql_query("SELECT $field FROM ".PFX.$table), 0);

				if ($rs2)
					extract($rs2);

				if (!isset($entry_value))
					$entry_value = '';

				if (in_array($field_type, array('blob'))) 
					{
					$out[] = '<p class="gbp_l10n_field">'.gTxt($field).'</p>';
					$out[] = '<div class="gbp_l10n_value_disable">'.text_area('" readonly class="', 200, 420, $value).'</div>';
					$out[] = '<div class="gbp_l10n_value">'.text_area($field, 200, 420, $entry_value).'</div>';
					} 
				else if (in_array($field_type, array('string'))) 
					{
					$out[] = '<p class="gbp_l10n_field">'.gTxt($field).'</p>';
					$out[] = '<div class="gbp_l10n_value_disable">'.fInput('text', '', $value, 'edit" readonly title="', '', '', 60).'</div>';
					$out[] = '<div class="gbp_l10n_value">'.fInput('text', $field, $entry_value, 'edit', '', '', 60).'</div>';
					} 
				else
					$out[] = hInput($field, $value);
				}

			$out[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
			$out[] = '</div>';

			$out[] = $this->parent->form_inputs();
			$out[] = sInput(((isset($id)) || (gps('step') == 'gbp_save')) ? 'gbp_save' : 'gbp_post');

			$out[] = hInput('gbp_table', $table);
			$out[] = hInput(gbp_language, gps(gbp_language));
			$out[] = hInput(gbp_id, $entry_id);

			echo form(join('', $out));
			}
		}

	function save_post() 
		{
		global $txpcfg;
		extract(get_prefs());

		extract(gpsa($this->parent->preferences[$this->event.'_hidden_vars']['value']));
		$vars = gpsa($this->parent->preferences[$this->event.'_vars']['value']);

		$table = PFX.$_POST['gbp_table'];
		$language = $_POST[gbp_language];
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
				case 'gbp_post':
					$rs = safe_insert('gbp_l10n', "`id` = '$id', `table` = '$table', `language` = '$language', `entry_id` = '$entry_id', `entry_column` = '$field', `entry_value` = '$value', `entry_value_html` = '$value_html'");
				break;
				case 'gbp_save':
					$rs = safe_update('gbp_l10n', "`entry_value` = '$value', `entry_value_html` = '$value_html'",
						"`table` = '$table' AND `language` = '$language' AND `entry_id` = '$entry_id' AND `entry_column` = '$field'"
					);
				break;
				}
			}
		}

	}

new LocalisationView( 'gbp_l10n_localisation' , 'l10n', 'content');
if (@txpinterface == 'public')
	{

	// We are publish-side.
	global $prefs, $gbp_language;

	if (!defined('rhu'))
		define("rhu", preg_replace("/http:\/\/.+(\/.*)\/?$/U", "$1", hu));

	$path = explode('/', trim(str_replace(trim(rhu, '/'), '', $_SERVER['REQUEST_URI']), '/'));

	$lang_codes = LanguageHandler::get_site_langs();
	foreach($lang_codes as $code)
		{
		if ($path[0] == $code)
			{
			$gbp_language = $code;
			break;	# Stop on first match.
			}
		}

	/*
	SED:	Load the localised set of strings based on the selected language...	
		Our localise routine should now have all the strings it needs to do snippet localisation
		Plugins should be able to call gTxt() or gbp_gTxt() to output localised content.
	*/
	StringHandler::load_strings_into_textarray( $gbp_language );

	/* ====================================================
	TAG HANDLERS FOLLOW
	==================================================== */
	function gbp_snippet($atts)
		{
		/*
		Tag handler: Outputs a localised snippet. This is a strict alternative to using 
		direct snippets in pages/forms.
		Atts: 'name' the name of the snippet to output.
		*/
		$out = '';
		if( array_key_exists('name', $atts) )
			{
			global $gbp_language;

			$out = gTxt( $atts['name'] );
			if( $out === $atts['name'] )
				$out = '('.(($gbp_language)?$gbp_language:'??').')'.$out;
			}
		return $out;
		}

	// -----------------------------------------------------
	function gbp_if_lang( $atts , $thing )
	    {
		/*
		Basic markup tag. Use this to wrap blocks of content you only want to appear 
		when the specified language is set or if the direction of the selected language matches
		what you want. (Output different css files for rtl layouts for example).
		*/
		global $gbp_language;
		$out = '';
		
		if( !$gbp_language )
			return $out;
		
		extract(lAtts(array(
							'lang' => $gbp_language ,
							'dir'  => '',
							'wraptag' => 'div' ,
							),$atts));

		if( !empty($dir) and in_array( $dir , array( 'rtl', 'ltr') ) )
			{
			#	Does the direction of the currently selected site language match that requested?
			#	If so, parse the contained content.
			if( $dir == LanguageHandler::get_lang_direction( $gbp_language ) )
				$out = parse($thing) . n;
			}
		elseif( $lang == $gbp_language )
			{
			#	If the required language matches the site language, output a suitably marked up block of content.
			$dir = LanguageHandler::get_lang_direction_markup( $lang );
			$out = "<$wraptag lang=\"$lang\"$dir/>" . parse($thing) . "</$wraptag>" . n;
			}

		return $out;
	    }

	// ----------------------------------------------------
	function gbp_render_lang_list( $atts )
		{
		/*
		Renders a list of links that can be used to switch this page to another supported language.
		*/
		global $gbp_language;
	
		$result = array();
		
		$site_langs = LanguageHandler::get_site_langs();
		if( !empty($site_langs) )
			{
			foreach( $site_langs as $code ) 
				{
				$native_name = LanguageHandler::get_native_name_of_lang( $code );
				$dir = LanguageHandler::get_lang_direction_markup( $code );
				$class = ($gbp_language === $code) ? 'gbp_current_language' : '';
				$native_name = doTag( $native_name , 'span' , $class , ' lang="'.$code.'"'.$dir );
				$result[] = '<a href="'.hu.$code.$_SERVER['REQUEST_URI'].'">'.$native_name.'</a>'.n;
				}
			}
		
		return doWrap( $result , 'ul' , 'li' );
		}

	// -----------------------------------------------------
	function gbp_get_language( $atts )
		{
		/*
		Outputs the current language. Use in page/forms to output the language needed by the doctype/html decl.
		*/
		global $gbp_language;

		if( !$gbp_language )
			return '';
		return $gbp_language;
		}

	// -----------------------------------------------------
	function gbp_get_lang_dir( $atts )
		{
		/*
		Outputs the direction (rtl/ltr) of the current language. 
		Use in page/forms to output the direction needed by xhtml elements.
		*/
		global $gbp_language;
		
		$lang = $gbp_language; 
		if( !$gbp_language )
			$lang = LanguageHandler::get_site_default_lang();

		$dir = LanguageHandler::get_lang_direction( $lang );
		return $dir;
		}
	
	// ----------------------------------------------------
	function gbp_localise($atts, $thing = '') 
		{
		/*
		Graeme's original localisation container tag. Still very much needed.
		Some mods to include direct snippet localisation for any contained content.
		*/
		global $gbp_language, $thisarticle, $thislink;

		if ($gbp_language) {
			if (array_key_exists('category', $atts)) {
				$id = $atts['category'];
				$table = PFX.'txp_category';
				$rs = safe_field('entry_value', 'gbp_l10n', "`entry_id` = '$id' AND `entry_column` = 'title' AND `table` = '$table' AND `language` = '$gbp_language'");

				if ($rs && !empty($rs))
					return $rs;
				else
					return ucwords($atts['category']);

			} else if (array_key_exists('section', $atts)) {

				$id = $atts['section'];
				$table = PFX.'txp_section';
				$rs = safe_field('entry_value', 'gbp_l10n', "`entry_id` = '$id' AND `entry_column` = 'title' AND `table` = '$table' AND `language` = '$gbp_language'");

				if ($rs && !empty($rs))
					return $rs;
				else
					return ucwords($atts['section']);

			} else if ($thing) {

				# SED: Process the direct snippet substitutions needed in the contained content.
				$thing = SnippetHandler::substitute_snippets( $thing );
				
				if (isset($thisarticle)) {
					$rs = safe_rows('entry_value, entry_value_html, entry_column', 'gbp_l10n', "`language` = '$gbp_language' AND `entry_id` = '".$thisarticle['thisid']."' AND `table` = '".PFX."textpattern'");

					if ($rs) foreach($rs as $row) {
						if ($row['entry_value'])
							$thisarticle[strtolower($row['entry_column'])] = ($row['entry_value_html']) ? parse($row['entry_value_html']) : $row['entry_value'];
					}
				}
				$html = parse($thing);
				$html = preg_replace('#((href|src)=")(?!\/?(https?|ftp|download|images|'.$gbp_language.'))\/?#', '$1'.$gbp_language.'/', $html);
				return $html;
			}
		}

		if (array_key_exists('category', $atts)) {

			$rs = safe_field('title', 'txp_category', '`name` = "'.$atts['category'].'"');
			if ($rs && !empty($rs))
				return $rs;
			else
				return ucwords($atts['category']);

		} else if (array_key_exists('section', $atts)) {

			$rs = safe_field('title', 'txp_section', '`name` = "'.$atts['section'].'"');
			if ($rs && !empty($rs))
				return $rs;
			else
				return ucwords($atts['section']);

		} else if ($thing) {

			# SED: Process and string substitutions needed in the contained content.
			$thing = SnippetHandler::substitute_snippets( $thing );
			return parse($thing);
		}

		return null;
		}
	}


/* ----------------------------------------------------------------------------
class LanguageHandler implements ISO-693-1 language support.
---------------------------------------------------------------------------- */
class LanguageHandler
	{
	function iso_693_1_langs ( $input, $to_return='lang' )
		{
		# Comment out as much as you feel you will never need. (Lightens up the memory needed a little.)
		static $iso_693_1_langs = array( 
		'aa'=>array( 'aa'=>'Afaraf' ),	//	'en'=>'Afar'
		'ab'=>array( 'ab'=>'аҧсуа бызшәа' ),	//	'en'=>'Abkhazian' 
		'af'=>array( 'af'=>'Afrikaans' ),	//	'en'=>'Afrikaans' 
		'am'=>array( 'am'=>'አማርኛ' ),	//	'en'=>'Amharic' 
		'ar'=>array( 'ar'=>'العربية' , 'dir'=>'rtl' ),	//	'en'=>'Arabic' 
		'as'=>array( 'as'=>'অসমীয়া' ),	//	'en'=>'Assamese' 
		'ay'=>array( 'ay'=>'Aymar aru' ),	//	'en'=>'Aymara' 
		'az'=>array( 'az'=>'Azərbaycan dili' ),	//	'en'=>'Azerbaijani' 
		'ba'=>array( 'ba'=>'башҡорт теле' ),	//	'en'=>'Bashkir' 
		'be'=>array( 'be'=>'Беларуская мова' ),	//	'en'=>'Byelorussian' 
		'bg'=>array( 'bg'=>'Български' ),	//	'en'=>'Bulgarian' 
		'bh'=>array( 'bh'=>'भोजपुरी' ),	//	'en'=>'Bihari',
		'bi'=>array( 'bi'=>'Bislama' ),	//	'en'=>'Bislama' 
		'bn'=>array( 'bn'=>'বাংলা' ),	//	'en'=>'Bengali; Bangla'
		'bo'=>array( 'bo'=>'Bod Skad' ) ,	//	'en'=>'Tibetan' 
		'br'=>array( 'br'=>'ar Brezhoneg' ) ,	//	'en'=>'Breton' 
		'ca'=>array( 'ca'=>'Català' ) ,	//	'en'=>'Catalan' 
		'co'=>array( 'co'=>'Corsu' ) ,	//	'en'=>'Corsican' 
		'cs'=>array( 'cs'=>'Čeština' ) ,	//	'en'=>'Czech' 
		'cy'=>array( 'cy'=>'Cymraeg' ) ,	//	'en'=>'Welsh' 
		'da'=>array( 'da'=>'Dansk' ) ,	//	'en'=>'Danish' 
		'de'=>array( 'de'=>'Deutsch' ) ,	//	'en'=>'German' 
		'dz'=>array( 'dz'=>'Dzongkha' ) ,	//	'en'=>'Bhutani'
		'el'=>array( 'el'=>'Ελληνικά' ) ,	//	'en'=>'Greek' 
		'en'=>array( 'en'=>'English' ),
		'eo'=>array( 'eo'=>'Esperanto' ),	//	'en'=>'Esperanto' 
		'es'=>array( 'es'=>'Español' ),	//	'en'=>'Spanish' 
		'et'=>array( 'et'=>'Eesti Keel' ),	//	'en'=>'Estonian' 
		'eu'=>array( 'eu'=>'Euskera' ),	//	'en'=>'Basque' 
		'fa'=>array( 'fa'=>'Fārsī' ),	//	'en'=>'Persian' 
		'fi'=>array( 'fi'=>'Suomi' ),	//	'en'=>'Finnish' 
		'fj'=>array( 'fj'=>'vaka-Viti' ),	//	'en'=>'Fiji' 
		'fo'=>array( 'fo'=>'Føroyska' ),	//	'en'=>'Faroese' 
		'fr'=>array( 'fr'=>'Français' ),	//	'en'=>'French' 
		'fy'=>array( 'fy'=>'Frysk' ),	//	'en'=>'Frisian' 
		'ga'=>array( 'ga'=>'Gaeilge' ),	//	'en'=>'Irish' 
		'gd'=>array( 'gd'=>'Gàidhlig' ),	//	'en'=>'Scots Gaelic'
		'gl'=>array( 'gl'=>'Galego' ),	//	'en'=>'Galician' 
		'gn'=>array( 'gn'=>"Avañe'ẽ" ),	//	'en'=>'Guarani' 
		'gu'=>array( 'gu'=>'ગુજરાતી' ),	//	'en'=>'Gujarati' 
		'ha'=>array( 'ha'=>'حَوْسَ حَرْش۪' , 'dir'=>'rtl' ),	//	'en'=>'Hausa' 
		'he'=>array( 'he'=>'עברית / עִבְרִית' ,'dir'=>'rtl' ),	//	'en'=>'Hebrew' 
		'hi'=>array( 'hi'=>'हिन्दी' ),	//	'en'=>'Hindi' 
		'hr'=>array( 'hr'=>'Hrvatski' ),	//	'en'=>'Croatian' 
		'hu'=>array( 'hu'=>'Magyar' ),	//	'en'=>'Hungarian' 
		'hy'=>array( 'hy'=>'Հայերէն' ),	//	'en'=>'Armenian' 
		'ia'=>array( 'ia'=>'Interlingua' ),	//	'en'=>'Interlingua' 
		'id'=>array( 'id'=>'Bahasa Indonesia' ),	//	'en'=>'Indonesian' 
		'ie'=>array( 'ie'=>'Interlingue' ),	//	'en'=>'Interlingue' 
		'ik'=>array( 'ik'=>'Iñupiak' ),	//	'en'=>'Inupiak' 
		'is'=>array( 'is'=>'Íslenska' ),	//	'en'=>'Icelandic' 
		'it'=>array( 'it'=>'Italiano' ),	//	'en'=>'Italian' 
		'iu'=>array( 'iu'=>'ᐃᓄᒃᑎᑐᑦ' ),	//	'en'=>'Inuktitut' 
		'ja'=>array( 'ja'=>'日本語' ),	//	'en'=>'Japanese' 
		'jw'=>array( 'jw'=>'basa Jawa' ),	//	'en'=>'Javanese' 
		'ka'=>array( 'ka'=>'ქართული' ),	//	'en'=>'Georgian' 
		'kk'=>array( 'kk'=>'Қазақ' ),	//	'en'=>'Kazakh' 
		'kl'=>array( 'kl'=>'Kalaallisut' ),	//	'en'=>'Greenlandic' 
		'km'=>array( 'km'=>'ភាសាខ្មែរ' ),	//	'en'=>'Cambodian' 
		'kn'=>array( 'kn'=>'ಕನ್ನಡ' ),	//	'en'=>'Kannada' 
		'ko'=>array( 'ko'=>'한국어' ),	//	'en'=>'Korean' 
		'ks'=>array( 'ks'=>'काऽशुर' ),	//	'en'=>'Kashmiri' 
		'ku'=>array( 'ku'=>'Kurdí' ),	//	'en'=>'Kurdish' 
		'ky'=>array( 'ky'=>'Кыргызча' ),	//	'en'=>'Kirghiz' 
		'la'=>array( 'la'=>'Latine' ),	//	'en'=>'Latin' 
		'ln'=>array( 'ln'=>'lokótá ya lingála' ),	//	'en'=>'Lingala' 
		'lo'=>array( 'lo'=>'ລາວ' ),	//	'en'=>'Laothian' 
		'lt'=>array( 'lt'=>'Lietuvių Kalba' ),	//	'en'=>'Lithuanian' 
		'lv'=>array( 'lv'=>'Latviešu' ),	//	'en'=>'Latvian'
		'mg'=>array( 'mg'=>'Malagasy fiteny' ),	//	'en'=>'Malagasy' 
		'mi'=>array( 'mi'=>'te Reo Māori' ),	//	'en'=>'Maori' 
		'mk'=>array( 'mk'=>'Македонски' ),	//	'en'=>'Macedonian' 
		'ml'=>array( 'ml'=>'മലയാളം' ),	//	'en'=>'Malayalam' 
		'mn'=>array( 'mn'=>'Монгол' ),	//	'en'=>'Mongolian' 
		'mo'=>array( 'mo'=>'лимба молдовеняскэ' ),	//	'en'=>'Moldavian' 
		'mr'=>array( 'mr'=>'मराठी' ),	//	'en'=>'Marathi' 
		'ms'=>array( 'ms'=>'Bahasa Melayu' ),	//	'en'=>'Malay' 
		'mt'=>array( 'mt'=>'Malti' ),	//	'en'=>'Maltese' 
		'my'=>array( 'my'=>'ဗမာစကား' ),	//	'en'=>'Burmese' 
		'na'=>array( 'na'=>'Ekakairũ Naoero' ),	//	'en'=>'Nauru' 
		'ne'=>array( 'ne'=>'नेपाली' ),	//	'en'=>'Nepali' 
		'nl'=>array( 'nl'=>'Nederlands' ),	//	'en'=>'Dutch' 
		'no'=>array( 'no'=>'Norsk' ),	//	'en'=>'Norwegian' 
		'oc'=>array( 'oc'=>'lenga occitana' ),	//	'en'=>'Occitan' 
		'om'=>array( 'om'=>'Afaan Oromo' ),	//	'en'=>'(Afan) Oromo'
		'or'=>array( 'or'=>'ଓଡ଼ିଆ' ),	//	'en'=>'Oriya' 
		'pa'=>array( 'pa'=>'ਪੰਜਾਬੀ' ),	//	'en'=>'Punjabi' 
		'pl'=>array( 'pl'=>'Polski' ),	//	'en'=>'Polish' 
		'ps'=>array( 'ps'=>'پښتو' , 'dir'=>'rtl' ),	//	'en'=>'Pashto' 
		'pt'=>array( 'pt'=>'Português' ),	//	'en'=>'Portuguese' 
		'qu'=>array( 'qu'=>'Runa Simi/Kichwa' ),	//	'en'=>'Quechua' 
		'rm'=>array( 'en'=>'Rhaeto-Romance' ),
		'rn'=>array( 'rn'=>'Kirundi' ),	//	'en'=>'Kirundi' 
		'ro'=>array( 'ro'=>'Română' ),	//	'en'=>'Romanian' 
		'ru'=>array( 'ru'=>'Русский' ),	//	'en'=>'Russian' 
		'rw'=>array( 'rw'=>'Kinyarwandi' ),	//	'en'=>'Kinyarwanda' 
		'sa'=>array( 'sa'=>'संस्कृतम्' ),	//	'en'=>'Sanskrit' 
		'sd'=>array( 'sd'=>'سنڌي' , 'dir'=>'rtl' ),	//	'en'=>'Sindhi' 
		'sg'=>array( 'sg'=>'yângâ tî sängö' ),	//	'en'=>'Sangho' 
		'sh'=>array( 'sh'=>'Српскохрватски' ),	//	'en'=>'Serbo-Croatian'
		'si'=>array( 'si'=>'(siṁhala bʰāṣāva)' ),	//	'en'=>'Sinhalese' 
		'sk'=>array( 'sk'=>'Slovenčina' ),	//	'en'=>'Slovak' 
		'sl'=>array( 'sl'=>'Slovenščina' ),	//	'en'=>'Slovenian' 
		'sm'=>array( 'sm'=>"gagana fa'a Samoa" ),	//	'en'=>'Samoan' 
		'sn'=>array( 'sn'=>'chiShona' ),	//	'en'=>'Shona' 
		'so'=>array( 'so'=>'af Soomaali' ),	//	'en'=>'Somali' 
		'sq'=>array( 'sq'=>'Shqip' ),	//	'en'=>'Albanian' 
		'sr'=>array( 'sr'=>'Srpski' ),	//	'en'=>'Serbian' 
		'ss'=>array( 'ss'=>'siSwati' ),	//	'en'=>'Siswati' 
		'st'=>array( 'st'=>'seSotho' ),	//	'en'=>'Sesotho' 
		'su'=>array( 'su'=>'basa Sunda' ),	//	'en'=>'Sundanese' 
		'sv'=>array( 'sv'=>'Svenska' ),	//	'en'=>'Swedish' 
		'sw'=>array( 'sw'=>'Kiswahili' ),	//	'en'=>'Swahili' 
		'ta'=>array( 'ta'=>'தமிழ்' ),	//	'en'=>'Tamil' 
		'te'=>array( 'te'=>'తెలుగు' ),	//	'en'=>'Telugu' 
		'tg'=>array( 'tg'=>'زبان تاجکی' , 'dir'=>'rtl' ),	//	'en'=>'Tajik' 
		'th'=>array( 'th'=>'ภาษาไทย' ),	//	'en'=>'Thai' 
		'ti'=>array( 'ti'=>'ትግርኛ' ),	//	'en'=>'Tigrinya' 
		'tk'=>array( 'tk'=>'Türkmençe' ),	//	'en'=>'Turkmen' 
		'tl'=>array( 'tl'=>'Tagalog' ),	//	'en'=>'Tagalog' 
		'tn'=>array( 'tn'=>'Setswana' ),	//	'en'=>'Setswana' 
		'to'=>array( 'to'=>'Faka-Tonga' ),	//	'en'=>'Tonga' 
		'tr'=>array( 'tr'=>'Türkçe' ),	//	'en'=>'Turkish' 
		'ts'=>array( 'ts'=>'xiTsonga' ),	//	'en'=>'Tsonga' 
		'tt'=>array( 'tt'=>'تاتارچا' , 'dir'=>'rtl' ),	//	'en'=>'Tatar' 
		'tw'=>array( 'tw'=>'Twi' ),	//	'en'=>'Twi' 
		'ug'=>array( 'ug'=>'uyghur tili' ),	//	'en'=>'Uighur' 
		'uk'=>array( 'uk'=>"Українська" ),	//	'en'=>'Ukrainian' 
		'ur'=>array( 'ur'=>'اردو', 'dir'=>'rtl' ),	//	'en'=>'Urdu' 
		'uz'=>array( 'uz'=>"Ўзбек (o'zbek)" ),	//	'en'=>'Uzbek' 
		'vi'=>array( 'vi'=>'Tiếng Việt' ),	//	'en'=>'Vietnamese' 
		'vo'=>array( 'vo'=>"vad'd'a tšeel" ),	//	'en'=>'Volapuk' 
		'wo'=>array( 'wo'=>'Wollof' ),	//	'en'=>'Wolof' 
		'xh'=>array( 'xh'=>'isiXhosa' ),	//	'en'=>'Xhosa' 
		'yi'=>array( 'yi'=>'ײִדיש' , 'dir'=>'rtl' ),	//	'en'=>'Yiddish' 
		'yo'=>array( 'yo'=>'Yorùbá' ),	//	'en'=>'Yoruba' 
		'za'=>array( 'za'=>'Sawcuengh' ),	//	'en'=>'Zhuang' 
		'zh'=>array( 'zh'=>'中文(國語)' ),	//	'en'=>'Chinese' 
		'zu'=>array( 'zu'=>'isiZulu' ),	//	'en'=>'Zulu' 
		);
		
		switch ( $to_return )
			{
			default:
			case 'lang':
				return (array_key_exists( $input, $iso_693_1_langs ))
					?	$iso_693_1_langs[$input][$input]
					:	NULL;
			break;

			case 'dir':
				return (array_key_exists( $input, $iso_693_1_langs ) and array_key_exists('dir', $iso_693_1_langs[$input]))
					?	$iso_693_1_langs[$input]['dir']
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
	
	// ----------------------------------------------------------------------------
	function is_valid_code($code)
		{
		/*
		LANGUAGE SUPPORT ROUTINE
		Check the given string is a valid 2-digit language code from the ISO-693-1 table.
		*/
		$result = false;
		if( 2 == strlen( $code ) )
			{
			$result = ( LanguageHandler::iso_693_1_langs( $code ) );
			}
		return $result;
		}
	// ----------------------------------------------------------------------------
	function find_code_for_lang( $name )
		{
		/*
		LANGUAGE SUPPORT ROUTINE
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
	// ----------------------------------------------------------------------------
	function get_lang_direction_markup( $lang )
		{
		/*
		LANGUAGE SUPPORT ROUTINE
		Builds the xhtml direction markup needed based upon the directionality of the language requested.
		*/
		$dir = '';
		if( !empty($lang) and ('rtl' == LanguageHandler::iso_693_1_langs( $lang, 'dir' ) ) )
			$dir = ' dir="rtl"';
		return $dir;
		}
	// ----------------------------------------------------------------------------
	function get_lang_direction( $lang )
		{
		/*
		LANGUAGE SUPPORT ROUTINE
		Builds the xhtml direction markup needed based upon the directionality of the language requested.
		*/
		$dir = 'ltr';
		if( !empty($lang) and ('rtl' == LanguageHandler::iso_693_1_langs( $lang, 'dir' ) ) )
			$dir = 'rtl';
		return $dir;
		}
	// ----------------------------------------------------------------------------
	function get_native_name_of_lang( $code )
		{
		/*
		LANGUAGE SUPPORT ROUTINE
		Returns the native name of the given language code.
		*/
		return (LanguageHandler::iso_693_1_langs( $code )) ? LanguageHandler::iso_693_1_langs( $code ) : LanguageHandler::iso_693_1_langs( 'en' ) ;
		}
	// ----------------------------------------------------------------------------
	function get_site_langs()
		{
		/*
		LANGUAGE SUPPORT ROUTINE
		Returns an array of the ISO-693-1 languages the site supports.
		*/
		global $prefs;
		
		if (!array_key_exists(GBP_PREFS_LANGUAGES, $prefs))
			$prefs[GBP_PREFS_LANGUAGES] = array('en', 'el');
		
		$lang_codes = $prefs[GBP_PREFS_LANGUAGES];
		return $lang_codes;
		}
	// ----------------------------------------------------------------------------
	function get_site_default_lang()
		{
		/*
		LANGUAGE SUPPORT ROUTINE
		Returns a string containing the ISO-693-1 language to be used as the site's default.
		*/
		$lang_codes = LanguageHandler::get_site_langs();
		return $lang_codes[0];
		}

	} // End of LanguageHandler

/* ----------------------------------------------------------------------------
class SnippetHandler implements localised "snippets" within page and
form templates. Uses the services of the string_handler to localise the
strings therein.
---------------------------------------------------------------------------- */
class SnippetHandler
	{
	function  get_pattern( $name )
		{
		# Use the first snippet detection pattern for a simple snippet format that is visible when the substitution fails.
		# Use the second snippet detection pattern if you want unmatched snippets as xhtml comments.
		static $snippet_pattern = "/##([\w|\.|\-]+)##/";
		//	var $snippet_pattern = "/\<\!--##([\w|\.|\-]+)##--\>/";

		# The following pattern is used to match any gbp_snippet tags in pages and forms.
		static $snippet_tag_pattern = "/\<txp:gbp_snippet name=\"([\w|\.|\-]+)\"\s*\/\>/";
		
		switch( $name )
			{
			case 'snippet' :	
				return $snippet_pattern;
			break;
			default :
			case 'snippet_tag' :
				return $snippet_tag_pattern;
			break;
			}
		}
	// ----------------------------------------------------------------------------
	function substitute_snippets( &$thing )
		{
		/*
		PUBLIC LOCALISATION SUPPORT ROUTINE for use by localisation plugin.
		Replaces all snippets within the contained block with their text from the global textarray.
		Allows TxP devs to include snippets* in their forms and page templates.
		
		*A Snippet is a specially formatted marker in the page/form template that gets substituted by
		the localisation routine.
		*/
		$out = preg_replace_callback( 	SnippetHandler::get_pattern('snippet') , 
										create_function(
							           '$match',
								       'global $gbp_language;
										global $textarray;
										if( $gbp_language )
											$lang = $gbp_language;
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
	// ----------------------------------------------------------------------------
	function find_snippets_in_block( &$thing , $merge = false , $get_data = false )
		{
		/*
		ADMIN SUPPORT ROUTINE
		Scans the given block ($thing) for snippets and returns their names as the values of an array.
		If merge is true then these values are expanded to contain whatever data is found in the txp_lang table for 
		that snippet.
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
			#	The admin side can then manipulate and expand the returned array and stash it back in the 
			#	language table for future use.

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
	// ----------------------------------------------------------------------------
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

		$where = " `event`='snippet' AND `name` IN ($name_set)";
		$rs = safe_rows_start( 'lang, name', 'txp_lang', $where );
		
		return array_merge( $result , StringHandler::get_strings( $rs , $stats ) );
		}
	} // End of SnippetHandler
	
/* ----------------------------------------------------------------------------
class StringHandler implements localised string storage support.
---------------------------------------------------------------------------- */
class StringHandler	
	{
	// ----------------------------------------------------------------------------
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
	// ----------------------------------------------------------------------------
	function insert_strings( $strings , $lang , $event='' )
		{
		/*
		PLUGIN SUPPORT ROUTINE
		Plugin authors: CALL THIS FROM THE IMMEDIATE PROCESSING SECTION OF YOUR PLUGIN'S ADMIN CODE.
		Adds the given array of aliased strings to an additional string table.
		*/
		global	$txp_current_plugin;

		# 	Check we have valid arguments...
		if( empty($strings) or empty($lang) )
			return null;

		# if the plugin is known, store it as a suffix to any strings stored...
		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;

		#	Iterate over the $strings and, for each that is not present, enter them into the sql table...
		$lastmod 	= date('YmdHis');
		$lang 		= doSlash( $lang );
		$event 		= doSlash( $event );
		foreach( $strings as $name=>$data )
			{
			$data = doSlash($data);
			$name = doSlash($name);
			mysql_query("INSERT INTO `".PFX."txp_lang` SET `lang`='$lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$data'");
			}
			
		# Possible TO DO... stop deleting empty entries. We might have to do this once a proper registration routine is
		# in place.
		mysql_query("DELETE FROM `".PFX."txp_lang` WHERE `data`=''");
//		mysql_query("FLUSH TABLE `".PFX."txp_lang`");
		}
	// ----------------------------------------------------------------------------
	function store_translation_of_string( $name , $event , $new_lang , $translation , $id='' )
		{
		/*
		ADMIN SUPPORT ROUTINE
		For use by the localisation plugin. 
		Can create or update row in the DB depending upon the calling arguments.
		*/
		# 	Check we have valid arguments...
		if( empty($name) or empty($event) or empty($new_lang) )
			{
//			echo br, " Aborting Translation Storage -- Missing paramenter!";
			return null;
			}

		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;

		#	Escape the lot for mySQL.
		$id 			= doSlash( $id );
		$event 			= doSlash( $event );
		$name  			= doSlash( $name );
		$translation	= doSlash( $translation );
		$new_lang 		= doSlash( $new_lang );
		$lastmod 		= date('YmdHis');

		$set 	= " `lang`='$new_lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$translation'" ;

		if( !empty( $id ) )
			{
			#	This is an update...
			$where	= " `id`='$id'";
			$result = @safe_update( 'txp_lang' , $set , $where );
			}
		else
			{
			#	Insert new row...
			$result = @safe_insert( 'txp_lang' , $set );
			}

		return $result;
		}
	// ----------------------------------------------------------------------------
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
			
//		echo br.br.br.br , "String [id=$id] found.";
			
		extract( $row );
		$translation	= doSlash( $translation );
		$new_lang 		= doSlash( $new_lang );
		$lastmod 		= date('YmdHis');
		$set 			= " `lang`='$new_lang', `name`='$name', `lastmod`='$lastmod', `event`='$event', `data`='$translation'" ;

//		echo " Calling safe_insert($set)." , br;

		@safe_insert( 'txp_lang' , $set );
		}
	// ----------------------------------------------------------------------------
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
//echo br, 'Langs', $where;
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			}
		elseif( $plugin and !empty( $plugin ) )
			{
			$where = "`event`=\"common.$plugin\" OR `event`=\"public.$plugin\" OR `event`=\"admin.$plugin\"";
//echo br, 'plugin', $where;
			@safe_delete( 'txp_lang' , $where , $debug );
			@safe_optimize( 'txp_lang' , $debug );
			}
		}
	// ----------------------------------------------------------------------------
	function remove_strings_by_name( $strings , $event = '' )
		{
		/*
		PLUGIN SUPPORT ROUTINE
		Plugin authors: CALL THIS FROM THE IMMEDIATE PROCESSING SECTION OF YOUR PLUGIN'S ADMIN CODE.
		Removes all of the named strings in ALL languages. (It uses the keys of the strings array).
		*/
		global	$txp_current_plugin;

		if( !$strings or !is_array( $strings ) )
			return null;

		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;
		$event 	= doSlash( $event );

//		echo br.br.br.br, "In remove_strings. Event($event). ";

		if( count( $strings ) )
			{
			foreach( $strings as $name=>$data )
				{
				$name 	= doSlash($name);
				$where 	= " `name`='$name'";
				if( !empty($event) )
					$where .= " AND `event`='$event'";
//				echo br , "Deleting entry where($where).";
				@safe_delete( 'txp_lang' , $where );
				}
			@safe_optimize( 'txp_lang' , $debug );
			}		
		}
	// ----------------------------------------------------------------------------
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
	// ----------------------------------------------------------------------------
	function load_strings( $lang )	
		{
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Loads all strings of the given language into an array and returns them.
		*/
		$extras = array();

		if( @txpinterface == 'admin' )
			$rs = safe_rows_start('name, data','txp_lang',"lang='".doSlash($lang)."'");
		else
			$rs = safe_rows_start(
				'name, data', 
				'txp_lang', 
				"lang='" . doSlash($lang) . "' AND ( event='snippet' OR event LIKE \"public.%\" OR event LIKE \"common.%\" )");

		if( $rs && mysql_num_rows($rs) > 0 )
			{
			while ( $a = nextRow($rs) )
				{
				$extras[$a['name']] = $a['data'];
				}
			}
		return $extras;
		}
	// ----------------------------------------------------------------------------
	function discover_registered_plugins()	
		{
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Gets an array of the names of plugins that have registered strings in the correct format. 
		No repeats!
		*/
		$result = array();

		$rs = safe_rows_start( 	
							'distinct event', 
							'txp_lang', 
							' `event` like "public.%" or `event` like "admin.%" or `event` like "common.%"'
							);
		if( $rs && mysql_num_rows($rs) > 0 )
			{
			$set = array();
			while ( $a = nextRow($rs) )
				{
				$plugin = StringHandler::strip_leading_section($a['event']);			
				$set[$plugin] = $plugin;
				}
			foreach( $set as $plugin )
				{
				$result[] = $plugin;
				}
			sort( $result );
			}
		return $result;		
		}
	// ----------------------------------------------------------------------------
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
	// ----------------------------------------------------------------------------
	function get_plugin_strings( $plugin , &$stats )	
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
		$where = ' `event` = "public.'.$plugin.'" or `event` = "admin.'.$plugin.'" or `event` = "common.'.$plugin.'"';
		$rs = safe_rows_start( 'lang, name', 'txp_lang', $where );
		return StringHandler::get_strings( $rs , $stats );
		}
	// ----------------------------------------------------------------------------
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
	// ----------------------------------------------------------------------------
	function get_string_set( $string_name )	
		{
		/*
		ADMIN INTERFACE SUPPORT ROUTINE
		Given a string name, will extract an array of the matching translations.
		translation_lang => string_id , event , data
		*/
		$result = array();

		$where = ' `name` = "'.$string_name.'"';
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
	// ----------------------------------------------------------------------------
	function gTxt( $alias, $args=null )
		{
		/*
		PUBLIC/ADMIN INTERFACE SUPPORT ROUTINE
		Given a string name, will pull the string out of the $textarray and perform any argument replacements needed.
		*/
		global $textarray;
		global $gbp_language;
		
		$lang = $gbp_language;
		if( !$lang )
			$lang = LanguageHandler::get_site_default_lang();
			
		$out = @$textarray[ $alias ];
		if( !$out or ($out === $alias) )
			$out = "($lang) $alias";
			
		if( isset( $args ) and is_array( $args ) and count($args) )
			{
//			echo br , "Doing replacements ...";
			foreach( $args as $pattern=>$value )
				{
//				echo "$pattern -> $value ... ";
				$out = preg_replace( '/\\'.$pattern.'/' , $value , $out );
				}
			}
			
		return $out;
		}
	} // End class StringHandler


/* ----------------------------------------------------------------------------
PUBLIC/ADMIN WRAPPER ROUTINES...
---------------------------------------------------------------------------- */
function gbp_gTxt( $name , $args = null )	
	{
	/*
	Extension to the gTxt routine to allow an optional parameter list.
	Plugin authors can define strings with embedded variables that get preg_replaced
	based on the the argument array.
	
	So a string : 'plugin_name_hello' => 'Hello there $name.'
	
	could be replaced like this from within the plugin...
		gbp_gTxt( 'plugin_name_hello' , array( '$name'=>$name ) );
	*/
	return StringHandler::gTxt( $name , $args );
	}
// ----------------------------------------------------------------------------

# --- END PLUGIN CODE ---

?>
