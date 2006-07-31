<?php

$plugin['name'] = 'gbp_l10n';
$plugin['version'] = '0.6';
$plugin['author'] = 'Graeme Porteous';
$plugin['author_uri'] = 'http://porteo.us/projects/textpattern/gbp_l10n/';
$plugin['description'] = 'Textpattern content localisation.';
$plugin['type'] = '1';

$plugin['url'] = '$HeadURL$';
$plugin['date'] = '$LastChangedDate$';
$plugin['revision'] = '$LastChangedRevision$';

@include_once('../zem_tpl.php');

if (0) {
?>
<!-- CSS SECTION
# --- BEGIN PLUGIN CSS ---
<style type="text/css">
div#l10n_help td { vertical-align:top; }
div#l10n_help code { font-weight:bold; font: 105%/130% "Courier New", courier, monospace; background-color: #FFFFCC;}
div#l10n_help code.code_tag { font-weight:normal; border:1px dotted #999; background-color: #f0e68c; display:block; margin:10px 10px 20px; padding:10px; }
div#l10n_help a:link, div#l10n_help a:visited { color: blue; text-decoration: none; border-bottom: 1px solid blue; padding-bottom:1px;}
div#l10n_help a:hover, div#l10n_help a:active { color: blue; text-decoration: none; border-bottom: 2px solid blue; padding-bottom:1px;}
div#l10n_help h1 { color: #369; font: 20px Georgia, sans-serif; margin: 0; text-align: center; }
div#l10n_help h2 { border-bottom: 1px solid black; padding:10px 0 0; color: #369; font: 17px Georgia, sans-serif; }
div#l10n_help h3 { color: #693; font: bold 12px Arial, sans-serif; letter-spacing: 1px; margin: 10px 0 0;text-transform: uppercase;}
</style>
# --- END PLUGIN CSS ---
-->
<!-- HELP SECTION
# --- BEGIN PLUGIN HELP ---
<div id="l10n_help">

h1. l10n Internationalisation Plugin Instructions.

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

h2. Full ISO-693-1 Array

Here is the full array for the 2-character ISO-693 part 1 language codes.

Cut and paste the rows you need into the iso_693_1_langs() function in the language handler...

 <code><pre>static $iso_693_1_langs = array( 
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
	'en'=>array( 'en'=>'English' , 'en-gb'=>'British English' , 'en-us'=>'American English' ),
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
	'zh'=>array( 'zh'=>'中文(简体)' , 'zh-cn'=>'中文(简体)' , 'zh-tw'=>'中文(國語)'  ),	// 'en'=>'Chinese'
	'zu'=>array( 'zu'=>'isiZulu' ),	//	'en'=>'Zulu' 
	);</pre></code>

</div>
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
if( !defined( 'L10N_SEP' ))
	define( 'L10N_SEP' , '-' );
if( !defined( 'L10N_NAME' ) )
	define( 'L10N_NAME' , 'l10n' );

// require_plugin() will reset the $txp_current_plugin global
global $txp_current_plugin;
$gbp_current_plugin = $txp_current_plugin;
require_plugin('gbp_admin_library');
$txp_current_plugin = $gbp_current_plugin;

if( !defined( 'GBP_PREFS_LANGUAGES' ))
	define( 'GBP_PREFS_LANGUAGES', $gbp_current_plugin.'_l10n-languages' );

class LocalisationView extends GBPPlugin 
	{
	
	var $gp = array(gbp_language);
	var $preferences = array(
		'l10n-languages' => array('value' => array(), 'type' => 'gbp_array_text'),

		'articles' => array('value' => 1, 'type' => 'yesnoradio'),
		'l10n-article_vars' => array('value' => array('Title', 'Body', 'Excerpt'), 'type' => 'gbp_array_text'),
		'l10n-article_hidden_vars' => array('value' => array('textile_body', 'textile_excerpt'), 'type' => 'gbp_array_text'),

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
	var $perm_strings = array( # These strings are always needed.
	'l10n-localisation'			=> 'localisation',
	);
	var $strings = array(	
	'l10n-add_tags'				=> 'Add localisation tags to this window?' ,
	'l10n-article_vars'			=> 'Article variables ',
	'l10n-article_hidden_vars'	=> 'Hidden article variables ',
	'l10n-category_vars'		=> 'Category variables ',
	'l10n-category_hidden_vars'	=> 'Hidden category variables ',
	'l10n-section_vars'			=> 'Section variables ',
	'l10n-section_hidden_vars'	=> 'Hidden section variables ',
	'l10n-cleanup_verify'		=> "This will totally remove all l10n tables, strings and translations and the operation cannot be undone. Plugins that require or load l10n will stop working.",
	'l10n-cleanup_wiz_text'		=> 'This allows you to remove the custom table and almost all of the strings that were inserted.',
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
	'l10n-setup_verify'			=> 'This will add a table called gbp_l10n to your Database. It will also insert a lot of new strings into your txp_lang table and change the `data` field of that table from type TINYTEXT to type TEXT.',
	'l10n-setup_wiz_text'		=> 'This allows you to install the custom table and all of the strings definitions needed (in English). You will be able to edit and translate the strings once this plugin is setup.',
	'l10n-setup_wiz_title'		=> 'Setup Wizard',
	'l10n-snippets'				=> ' snippets.',
	'l10n-statistics'			=> 'Show Statistics ',
	'l10n-strings'				=> ' strings.',
	'l10n-summary'				=> 'Statistics.',
	'l10n-textbox_title'		=> 'Type in the text here.',
	'l10n-translations_for'		=> 'Translations for ',
	'l10n-unlocalised'			=> 'Unlocalised',
	'l10n-view_site'			=> 'View localised site', 
	'l10n-wizard'				=> 'Wizards',
	);

	// Constructor
	function LocalisationView( $title_alias , $event , $parent_tab = 'extensions' ) 
		{
		global $textarray;

		if( @txpinterface == 'admin' )
			{
			#	Register callbacks to get plugins' strings registered. 
			register_callback(array(&$this, '_initiate_callbacks'), 'l10n' , '' , 0 );

			# First run, setup the languages array to the currently installed admin side languages...
			$langs = LanguageHandler::get_site_langs( false );
			if( NULL === $langs )
				{
				# Make sure the currently selected admin-side language is the site default...
				$this->preferences['l10n-languages']['value'][] = LANG;

				# Get the remaining admin-side langs...
				$installed_langs = safe_column('lang','txp_lang',"1 GROUP BY 'lang'");
				foreach( $installed_langs as $lang )
					{
					if( !in_array( $lang , $this->preferences['l10n-languages']['value'] ) )
						$this->preferences['l10n-languages']['value'][] = $lang;
					}
				}

			$textarray = array_merge( $textarray , $this->perm_strings );

			#	Merge the string that is always needed for the localisation tab title...
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

				# Load the strings from the store to the $textarray. This will override the 
				# strings inserted above, if they have been translated or edited.
				StringHandler::load_strings_into_textarray( LANG );
				}
			}
		else
			#	Register callbacks to get plugins' strings registered. 
			register_callback(array(&$this, '_initiate_callbacks'), 'pretext' , '' , 0 );

		# Be sure to call the parent constructor *after* the strings it needs are added and loaded!
		GBPPlugin::GBPPlugin( gTxt($title_alias) , $event , $parent_tab );
		}

	function preload() 
		{
		global $gbp, $txp_current_plugin, $_GBP;
		$gbp[$txp_current_plugin] = &$this;
		$_GBP[0] = &$this;

		add_privs($this->event, '1,2,3,6');

		#	NB: Process step before the installed() check below
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
			if ($this->preferences['articles']['value'] and has_privs('article.edit') )
				new LocalisationTabView( gTxt('articles'), 'article', $this);
			if ($this->preferences['categories']['value'] and has_privs('category') )
				new LocalisationTabView( gTxt('categories'), 'category', $this);
			// if ($this->preferences['links']['value'] and has_privs('link') )
			// 	new LocalisationTabView('links', 'link', $this);
			if ($this->preferences['sections']['value'] and has_privs('section') )
				new LocalisationTabView( gTxt('sections'), 'section', $this);
			if ($this->preferences['forms']['value'] and has_privs('form') )
				new LocalisationStringView( gTxt('forms') , 'form' , $this );
			if ($this->preferences['pages']['value'] and has_privs('page') )
				new LocalisationStringView( gTxt('pages') , 'page' , $this );
			if ($this->preferences['plugins']['value'] and has_privs('plugin') )
				new LocalisationStringView( gTxt('plugins'), 'plugin', $this );
			if( has_privs('prefs') )
				new GBPPreferenceTabView( gTxt('tab_preferences'), 'preference', $this);
			}

		if( has_privs('admin.edit') )
			new LocalisationTabView( gTxt('l10n-wizard'), 'wizard', $this);

		}

	function installed() 
		{
		$result = getThing( "show tables like '".PFX."gbp_l10n'" );
		return ($result);
		}
	
	function setup() 
		{
		# One-shot installation code goes in here...


		# Adds the strings this class needs. These lines makes them editable via the "plugins" string tab.
		# Make sure we only call insert_strings() once!
		$this->strings = array_merge( $this->strings , $this->perm_strings );
		StringHandler::insert_strings( $this->strings_prefix , $this->strings , $this->strings_lang , 'admin' , 'gbp_l10n' );

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

		# SED: Extend the txp_lang table to allow text instead of tinytext in the data field.
		$sql = ' CHANGE `data` `data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
		safe_alter( 'txp_lang' , $sql );

		$this->redirect( array( 'event'=>L10N_NAME , gbp_tab=>'preference' ) );
		}
	
	function cleanup() 
		{
		$sql = 'drop table `'.PFX.'gbp_l10n`';
		@safe_query( $sql );
		
		# These get totally removed and don't get re-inserted by the setup routine...
		$this->strings = array_merge( $this->strings , $this->perm_strings );
		StringHandler::remove_strings_by_name( $this->strings , 'admin' );
	
		$this->redirect( array( 'event'=>'plugin' ) );
		}

	function _process_string_callbacks( $event , $step , $pre , $func )
		{
		#	May need to move this to base class when the string handler moves to Admin Lib.
		if( !is_callable($func , false , $key) )
			return "Cannot call function '$key'.";

		$r = call_user_func($func, $event, $step);
		if( !is_array( $r ) )
			return "Call of '$key' returned a non-array value.";

//echo br , "function _process_string_callbacks( $event , $step , $pre , $func ) ";
//echo br , "Call of $key returned ... " , var_dump( $r ) , br;

		extract( $r );
		
		$result = "Skipped insertion of strings for '$key'.";
		if( $plugin and $prefix and $strings and $lang and $event and (count($strings)) )
			{
			if( StringHandler::insert_strings( $prefix , $strings , $lang , $event , $plugin ) )
				$result = true;
			}
		
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
		$data = str_split( $data , 64*1024 );
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
			foreach( $this->preferences['l10n-languages']['value'] as $key )
				$languages['value'][$key] = gTxt($key);

			if (!gps(gbp_language))
				$_GET[gbp_language] = $this->preferences['l10n-languages']['value'][0];

			setcookie(gbp_language, gps(gbp_language), time() + 3600 * 24 * 365);

			#	Render the top of page div.
			$out[] = form(
				fLabelCell( gTxt('language').': ' ).
				selectInput(gbp_language, $languages['value'], gps(gbp_language), 0, 1).
				'<br /><a href="'.hu.gps(gbp_language).'/">'.gTxt('l10n-view_site').'</a>'.
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
				
				case 'gbp_localise_pageform':
				$this->localise_pageform();
				break;
				
				case 'gbp_export_languageset':
				$this->export_languageset();
				break;
				
				case 'gbp_import_languageset':
				$this->import_languageset();
				break;
				}
			}
		}
	
	function main()
		{
		$id = gps(gbp_id);
		$step = gps('step');
		$pf_steps = array('gbp_save_pageform', 'edit_pageform', 'gbp_localise_pageform');
		$pl_steps = array('gbp_import_languageset');
		$can_edit = $this->parent->preferences['l10n-inline_editing']['value'];

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
			elseif( $owner = gps(gbp_plugin) and $prefix = gps('prefix') )
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
					$guts = doTag( $guts , 'strong' );
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
				$out[] = '<li><a href="' . $this->parent->url( array(gbp_plugin=>$plugin,'prefix'=>$pfx) , true ) . '">' .
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
		$out[] = '<div style="float: left; width: 20%;" class="gbp_i18n_owner_list">';

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
				$remove[] = '<span class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</span>';
				$remove[] = sInput( 'gbp_remove_languageset');
				$remove[] = $this->parent->form_inputs();
				$remove[] = hInput( 'lang_code' , $iso_code );
				$remove = form( join( '' , $remove ) , 
								'' ,
								"verify('" . doSlash(gbp_gTxt('l10n-lang_remove_warning' , array('$var1'=>$name)) ) . 
								 doSlash(gTxt('are_you_sure')) . "')");
				}

			$details =  StringHandler::if_plugin_registered( $string_name , $iso_code );
			if( false !== $details )
				{
				$details = unserialize( $details );
				$export[] = '<span class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('l10n-export'), '').'</span>';
				$export[] = sInput( 'gbp_export_languageset');
				$export[] = $this->parent->form_inputs();
				$export[] = hInput( 'language' , $iso_code );
				$export[] = hInput( 'prefix' , $details['pfx'] );
				$export[] = hInput( 'plugin' , $string_name );
				$export = form( join( '' , $export ) );
				}
			
			$out[]= tr( '<td align="right">'.($extras_found ? ' * ' : '').$name.'</td>'.n.'<td align="right">&nbsp;&nbsp;'.$count.'&nbsp;</td>'.n.td($export).td($remove) );
			}
		$out[] = '<tr style="border: 1px solid #ccc;">'.td('').'<td align="right">&nbsp;&nbsp;'.array_sum($stats).'&nbsp;</td>'.n.td('').n.td('').'</tr>'.n;
		$out[] = '</tbody></table>';

		if( $extras_found )
			$out[] = gTxt('l10n-explain_extra_lang');

		if( !empty( $string_name ) )
			{
			$import[] = gTxt('l10n-import_title') . br;
			$import[] = '<textarea name="data" cols="60" rows="2" id="gbp_l10n_string_import">';
			$import[] = '</textarea>' .br . br;
			$import[] = '<span class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('l10n-import'), '').'</span>';
			$import[] = sInput( 'gbp_import_languageset');
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
		$stats 			= array();
		$strings 		= StringHandler::get_plugin_strings( $plugin , $stats , $prefix );
		$strings_exist 	= ( count( $strings ) > 0 );

		$out[] = '<div style="float: left; width: 25%;" class="gbp_i18n_plugin_list">';
		$out[] = '<h3>'.$plugin.' '.gTxt('l10n-strings').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' . 
				 $this->parent->url( array( gbp_plugin => $plugin, 'prefix'=>$prefix ) , true ) . '">' . 
				 gTxt('l10n-statistics') . '&#187;</a></span>' . br . n;
		
		$out[] = br . n . $this->_render_string_list( $strings , gbp_plugin , $plugin , $prefix );
		$out[] = '</div>';
		
		# Render default view details in right hand pane...
 		if( empty( $string_name ) )
			{
			$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
			$out[] = $this->_render_string_stats( $plugin , $stats );

			# If the plugin is not present offer to delete the lot
			global $plugins;
			if( !array_search( $plugin, $plugins ) )
				{
				$out[] = '<h3>'.gTxt('l10n-no_plugin_heading').'</h3>'.n;
				$del[] = graf( gTxt('l10n-remove_plugin') );
				$del[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('delete'), '').'</div>';
				$del[] = sInput('gbp_remove_stringset');
				$del[] = $this->parent->form_inputs();
				$del[] = hInput(gbp_plugin, $plugin);

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
		$can_edit = $this->parent->preferences['l10n-inline_editing']['value'];
		
		$out[] = '<div style="float: left; width: 25%;" class="gbp_i18n_string_list">';
		$out[] = '<h3>'.$owner.' '.gTxt('l10n-snippets').'</h3>'.n;
		$out[] = '<span style="float:right;"><a href="' . 
				 $this->parent->url( array( 'owner' => $owner ) , true ) . '">' . 
				 gTxt('l10n-statistics') . '&#187;</a></span>' . br . n;
		if( $can_edit )
			 $out[] = '<span style="float:right;"><a href="' . 
					 $this->parent->url( array( 'owner'=>$owner , 'step'=>'edit_pageform' ) , true ) . '">' . 
					 gbp_gTxt('l10n-edit_resource' , array('$type'=>$this->event,'$owner'=>$owner) ) . 
					 '&#187;</a></span>' . br . n;

		#	Render the list... 
		$out[] = br . n . $this->_render_string_list( $strings , 'owner', $owner , '' ) . n;
		$out[] = '</div>';

		#	Render default view details in right hand pane...
		$step = gps('step');
 		if( empty( $id ) and empty( $step ) )
			{
			$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
			$out[] = $this->_render_string_stats( '' , $stats );
			$out[] = '</div>';
			}

		echo join('', $out);
		}

	function render_pageform_edit( $table , $fname, $fdata, $owner )	# Right pane page/form edit textarea.
		{
		$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
		$out[] = '<h3>'.gbp_gTxt('l10n-edit_resource' , array('$type'=>$this->event,'$owner'=>$owner) ).'</h3>' . n;

		$data = safe_field( $fdata , $table , '`'.$fname.'`=\''.doSlash($owner).'\'' );
		$localised = SnippetHandler::do_localise( $data );

		if( !$localised )
			{
			$l[] = '<p>'.gTxt('l10n-add_tags').n;
			$l[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('add'), '').'</div></p>';
			$l[] = sInput('gbp_localise_pageform').n;
			$l[] = $this->parent->form_inputs();
			$l[] = hInput('owner', $owner);
			$l[] = hInput('data', $data);
			$out[] = form( join('', $l) , 'border: 1px solid grey; padding: 0.5em; margin: 1em;' );
			}

		$f[] = '<p><textarea name="data" cols="70" rows="20" title="'.gTxt('l10n-textbox_title').'">' . 
			 $data . 
			 '</textarea></p>'.br.n;
		$f[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$f[] = sInput('gbp_save_pageform');
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
		$out[] = '<div style="float: right; width: 50%;" class="gbp_i18n_values_list">';
		$out[] = '<h3>'.gTxt('l10n-translations_for').$id.'</h3>'.n.'<form action="index.php" method="post"><dl>';
		
		$string_event = 'snippet';
		if( $type == gbp_plugin )
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
		$out[] = '<div class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</div>';
		$out[] = sInput('gbp_save_strings');
		$out[] = $this->parent->form_inputs();
		$out[] = hInput('codes', trim( join( ',' , $final_codes ) , ', ' ) );
		$out[] = hInput(gbp_language, gps(gbp_language));
		$out[] = hInput('prefix', gps('prefix'));
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
			$f1[] = sInput( 'gbp_import_languageset');
			$f1[] = hInput( 'commit', 'true' );
			$f1[] = $this->parent->form_inputs();
			
			foreach( $d['strings'] as $k=>$v )
				$l[] = tr( '<td style="text-align: right;">'.$k.' : </td>' . n . td("<input type=\"text\" readonly size=\"100\" value=\"$v\"/>") ) .n ;

			$f2[] = '<span class="gbp_l10n_form_submit">'.fInput('submit', '', gTxt('save'), '').'</span>';
			$content = join( '' , $f1 ) . doTag( join( '' , $l ) , 'table' ) . join( '' , $f2 );
			$o[] = form( $content , '' ,
						"verify('" . doSlash( gTxt('l10n-import_warning') ) . ' ' . doSlash(gTxt('are_you_sure')) . "')");
			}
		$o[] = '</div>';
		echo join( '' , $o );
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

//echo br , "Storing $string_name($code) as [$translation] using id:'$id' and event:'$event'";
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
					$this->render_edit($this->parent->preferences['l10n-article_vars']['value'], $this->parent->preferences['l10n-article_hidden_vars']['value'], 'textpattern', "id = '$id'", $id);
				$this->render_list('ID', 'Title', 'textpattern', '1 order by Title asc');
			break;
			case 'category':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['l10n-category_vars']['value'], $this->parent->preferences['l10n-category_hidden_vars']['value'], 'txp_category', "id = '$id'", $id);
				$this->render_list('id', 'title', 'txp_category', "name != 'root' order by title asc");
			break;
			// case 'link':
			// 	if ($id = gps(gbp_id))
			// 		$this->render_edit($this->parent->preferences['link_vars']['value'], $this->parent->preferences['link_hidden_vars']['value'], 'txp_link', "id = '$id'", $id);
			// 	$this->render_list('id', 'linkname', 'txp_link', '1 order by linkname asc');
			// break;
			case 'section':
				if ($id = gps(gbp_id))
					$this->render_edit($this->parent->preferences['l10n-section_vars']['value'], $this->parent->preferences['l10n-section_hidden_vars']['value'], 'txp_section', "name = '$id'", $id);
				$this->render_list('name', 'title', 'txp_section', "name != 'default' order by name asc");
			break;
			}
		}

	function render_wizard()
		{
		$out[] = '<div style="border: 1px solid gray; width: 50em; text-align: center; margin: 1em auto; padding: 1em; clear: both;">';

		if( $this->parent->installed() )
			{
			$out[] = '<h1>'.gTxt('l10n-cleanup_wiz_title').'</h1>';
			$out[] = graf( gTxt('l10n-cleanup_wiz_text') );

			$out[] = form(
				fInput('submit', '', gTxt('cleanup'), '') . $this->parent->form_inputs() . sInput( 'cleanup' ) , 
				'' ,
				"verify('".doSlash(gTxt('are_you_sure')).' '.doSlash( gTxt('l10n-cleanup_verify'))."')"
						 );
			}
		else
			{
			$out[] = '<h1>'.gTxt('l10n-setup_wiz_title').'</h1>';
			$out[] = graf( gTxt('l10n-setup_wiz_text') );
			$out[] = form(
				fInput('submit', '', gTxt('Setup'), '') . $this->parent->form_inputs() . sInput( 'setup' ) , 
				'' ,
				"verify('".doSlash(gTxt('are_you_sure')).' '.doSlash(gTxt('l10n-setup_verify'))."')"
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

		$hidden_vars = @gpsa($this->parent->preferences['l10n-'.$this->event.'_hidden_vars']['value']);
		$vars = @gpsa($this->parent->preferences['l10n-'.$this->event.'_vars']['value']);
		if( !empty( $hidden_vars ) )
			extract( $hidden_vars );

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

new LocalisationView( 'l10n-localisation' , L10N_NAME, 'content');

if (@txpinterface == 'public')
	{
	register_callback( '_l10n_pretext' , 'pretext' );

	function gbp_l10n_set_browse_language( $short_code , $debug=0 )
		{
		# Call this function with the short language code.
		global $gbp_language;
		$result = false;

		$site_langs = LanguageHandler::get_site_langs();
		$tmp = LanguageHandler::expand_code( $short_code );

		if( $debug )
			echo br, "gbp_l10n_set_browse_language( $short_code ) ... \$site_langs=", var_dump($site_langs),", \$tmp='$tmp'";

		if( isset( $tmp ) and in_array( $tmp , $site_langs ) )
			{
			if( $debug )
				echo " ... in IF() ... " ;
			$gbp_language = LanguageHandler::compact_code($tmp);
			$result = true;
			if( $debug )
				echo "\$tmp [$tmp] used to set \$gbp_language to " , var_dump($gbp_language['long']) , " returning TRUE", br ;
			}
		else
			{
			if( $debug )
				echo " ... in ELSE ... " ;
			if( !isset($gbp_language) or !in_array( $gbp_language['long'] , $site_langs ))
				{
				$gbp_language = LanguageHandler::compact_code( LanguageHandler::get_site_default_lang() );
				$result = (!empty($tmp));
				}
			}
		if( $debug )
			echo br , "Input='$short_code', Site Language set to " , var_dump( $gbp_language ) , " Returning ", var_dump($result),  br;
		
		return $result;
		}
	
	function _l10n_pretext()
		{
		global $prefs, $gbp_language;

		if (!defined('rhu'))
			define("rhu", preg_replace("/http:\/\/.+(\/.*)\/?$/U", "$1", hu));
		$path = explode('/', trim(str_replace(trim(rhu, '/'), '', $_SERVER['REQUEST_URI']), '/'));

//echo br.br.br.br , "_l10n_pretext() ... ";
		$tmp = array_shift($path);
//echo " ... first item=$tmp ";
		if( gbp_l10n_set_browse_language( $tmp ) )
			{
			#	Reset the URL, removing the language component...
			$new_uri = '/' . join( '/' , $path );
			$_SERVER['REQUEST_URI'] = $new_uri;
			}

//echo ' setting $_SERVER[\'REQUEST_URI\'] to ', $_SERVER['REQUEST_URI'] , br;

		# Load the localised set of strings based on the selected language...	
		StringHandler::load_strings_into_textarray( $gbp_language['long'] );
		}

	/*
	TAG HANDLERS FOLLOW
	*/
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
				$out = '('.(($gbp_language['long'])?$gbp_language['long']:'??').')'.$out;
			}
		return $out;
		}

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
			if( $dir == LanguageHandler::get_lang_direction( $gbp_language['short'] ) )
				$out = parse($thing) . n;
			}
		elseif( $lang == $gbp_language['short'] or $lang == $gbp_language['long'] )
			{
			#	If the required language matches the site language, output a suitably marked up block of content.
			$dir = LanguageHandler::get_lang_direction_markup( $lang );
			$out = "<$wraptag lang=\"$lang\"$dir/>" . parse($thing) . "</$wraptag>" . n;
			}

		return $out;
	    }

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
				extract( LanguageHandler::compact_code($code) );
				$native_name = LanguageHandler::get_native_name_of_lang( $code );
				$dir = LanguageHandler::get_lang_direction_markup( $code );
				$class = ($gbp_language === $code) ? 'gbp_current_language' : '';
				$native_name = doTag( $native_name , 'span' , $class , ' lang="'.$code.'"'.$dir );
				
				$result[] = '<a href="'.hu.$short.$_SERVER['REQUEST_URI'].'">'.$native_name.'</a>'.n;
				}
			}
		
		return doWrap( $result , 'ul' , 'li' );
		}

	function gbp_get_language( $atts )
		{
		/*
		Outputs the current language. Use in page/forms to output the language needed by the doctype/html decl.
		*/
		global $gbp_language;

		extract( lAtts( array( 'type'=>'short' ) , $atts ) );
		
		if( !$gbp_language )
			return '';
		return $gbp_language[$type];
		}

	function gbp_get_lang_dir( $atts )
		{
		/*
		Outputs the direction (rtl/ltr) of the current language. 
		Use in page/forms to output the direction needed by xhtml elements.
		*/
		global $gbp_language;
		
		extract( lAtts( array( 'type'=>'short' ) , $atts ) );
		
		$lang = $gbp_language; 
		if( !$gbp_language )
			$lang = LanguageHandler::compact_code( LanguageHandler::get_site_default_lang() );

		$dir = LanguageHandler::get_lang_direction( $lang[$type] );
		return $dir;
		}
	
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
					$rs = safe_rows('entry_value, entry_value_html, entry_column', 'gbp_l10n', '`language` = \''.$gbp_language['long'].'\' AND `entry_id` = \''.$thisarticle['thisid']."' AND `table` = '".PFX."textpattern'");

					if ($rs) foreach($rs as $row) {
						if ($row['entry_value'])
							$thisarticle[strtolower($row['entry_column'])] = ($row['entry_value_html']) ? parse($row['entry_value_html']) : $row['entry_value'];
					}
				}
				$html = parse($thing);
				$html = preg_replace('#((href|src)=")(?!\/?(https?|ftp|download|images|))\/?#', $gbp_language['short'].'/'.'$1', $html);
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


class LanguageHandler
	{
	/* 
	class LanguageHandler implements ISO-693-1 language support.
	*/
	
	function compact_code( $long_code )
		{
		/*
		Pull apart a long form language code into components.
		Output = {short , COUNTRY , [long]}	So, en-gb=> {en , GB , en-gb}
		*/

		# Cache the results as they are probably going to get used many times per tab...
		static $code_mappings;
		if( isset( $code_mappings[$long_code] ) )
			return $code_mappings[$long_code];

		$result = array();
		$result['short'] 	= @substr( $long_code , 0 , 2 );
		$result['country']  = @substr( $long_code , 3 , 2 );

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
		$langs = LanguageHandler::get_site_langs();
		foreach( $langs as $code )
			{
			extract( LanguageHandler::compact_code( $code ) );
			if( $short_code === $short )
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
				extract( LanguageHandler::compact_code( $input ) );
				
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
				extract( LanguageHandler::compact_code( $input ) );
				return $short;
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
		extract( LanguageHandler::compact_code( $code ) );
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
		
		$exists = array_key_exists(GBP_PREFS_LANGUAGES, $prefs);
		if( $set_if_empty and !$exists )
			{
			$prefs[GBP_PREFS_LANGUAGES] = LANG;
			$exists = true;
			}

		if( $exists )
			$lang_codes = $prefs[GBP_PREFS_LANGUAGES];
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

		# The following pattern is used to match any gbp_snippet tags in pages and forms.
		static $snippet_tag_pattern = "/\<txp:gbp_snippet name=\"([\w|\.|\-]+)\"\s*\/\>/";
		
		# The following are the localise tag pattern(s)...
		static $tag_pattern = '/\<\/*txp:gbp_localise\s*\>/';
		
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
								       'global $gbp_language;
										global $textarray;
										if( $gbp_language )
											$lang = $gbp_language[\'long\'];
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
		$r = preg_match_all( $p , $thing , $matches );
		if( $r !== false )
			$i += $r;
		return ($i > 1);
		}

	function do_localise( &$thing , $action = 'check' )
		{
		if( !$thing or empty( $thing ) )
			return NULL;
		
//echo br , "do_localise( \$thing , $action )";

		switch( $action )
			{
			case 'remove' :
			$p = SnippetHandler::get_pattern( 'tag_localise' );
			$thing = trim( preg_replace( $p , '' , $thing , -1 , $count ) );
			return $count;
			break;

			case 'insert' :
			return '<txp:gbp_localise>'.n.n.$thing.n.n.'</txp:gbp_localise>';
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

//		$where = " `event`='snippet' AND `name` IN ($name_set)";
		$where = " `name` IN ($name_set)";
		$rs = safe_rows_start( 'lang, name', 'txp_lang', $where );
		
		return array_merge( $result , StringHandler::get_strings( $rs , $stats ) );
		}
	}

class StringHandler	
	{

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
		
		extract( doSlash( func_get_args() ) );
		
		if( empty($name) or empty($event) or empty($new_lang) )
			return null;

		if( !empty($txp_current_plugin) and ($event=='public' or $event=='admin' or $event=='common') )
			$event = $event.'.'.$txp_current_plugin;

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

		if( count( $strings ) )
			{
			foreach( $strings as $name=>$data )
				{
				$name 	= doSlash($name);
				$where 	= " `name`='$name'";
				if( !empty($event) )
					$where .= " AND `event`='$event'";
				@safe_delete( 'txp_lang' , $where );
				}
			@safe_optimize( 'txp_lang' , $debug );
			}

		if( $txp_current_plugin )
			StringHandler::unregister_plugin( $txp_current_plugin );
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
		$count = mysql_num_rows($rs);
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
//echo br, "serialize_strings( $lang , $owner , $prefix , $event ) ... \$filter=$filter", br, var_dump( $r ), br, var_dump( $result ), br;
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
		global $gbp_language;
		
		$lang = $gbp_language;
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
function gbp_gTxt( $name , $args = null )	
	{
	/*
	Plugin authors can define strings with embedded variables that get preg_replaced
	based on the the argument array.
	
	So a string : 'plugin_name_hello' => 'Hello there $name.'
	
	could be replaced like this from within the plugin...
		gbp_gTxt( 'plugin_name_hello' , array( '$name'=>$name ) );
	*/
	return StringHandler::gTxt( $name , $args );
	}

# --- END PLUGIN CODE ---
?>
