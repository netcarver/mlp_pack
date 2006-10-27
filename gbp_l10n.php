<?php

$plugin['name'] = 'l10n';
$plugin['version'] = '0.6';
$plugin['author'] = 'Graeme Porteous and Stephen Dickinson';
$plugin['author_uri'] = 'http://porteo.us/projects/textpattern/gbp_l10n/';
$plugin['description'] = 'Textpattern Multi-Lingual Publishing.';
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

Under the content tab, there is a new *MLP* subtab.

h2. Terminology

|_. Term |_. Definition |
| Work | A collection of an author's (or authors') ideas/opinions/thoughts. |
| Rendition | The expression of an authors _work_ in a single language. |
| Article | The *set* of _renditions_ of a given author's _work_. An article always has at least one _rendition_. |
| Translate/Translation | The act of translating one rendition into a new rendition. This also covers the process of conversion of the initial _work_ into its first _rendition_. |
| Translator | The person or persons doing the translation (could be the author of the original _work_ but doesn't have to be.) |

 <br>

To avoid confusion, the noun 'translation' *always* refers to the act of translating, *never* to the result of translating something.

A 'rendition' *always* refers to the result of translating a work (or an existing rendition of a work) into a language.

Plain Textpattern makes no differentiation between articles and renditions because it only supports a single rendition of any work and has no need to distinguish between multiple renditions of a work -- or any need to manage the set of renditions of that work (the _article_) as a whole.

In effect this means that the old 'Articles' tab on the contents page has been renamed 'Renditions' and a new tab (under the MLP tab) is introduced to allow display and manipulation of articles (sets of renditions of a work).

The content > write tab still allows the editing of renditions.

h2. What the MLP(Multi-Lingual Publishing) Plugin provides.

On the admin side...
* Support for localisation of plugin strings via the admin interface (at last, no editing of source files!)
* Support for 'snippets' to simplify page/form editing and writing.
* Import/export of your plugin strings or snippets so you can upload to live sites or share with others.
* Support for Articles (groups of renditions).
* Support for cloning of renditions and their translation into other languages using the existing write tab.
* Email notifications sent to translators when articles are cloned or have their author changed.
* Extra filtering of the list of renditions by language.
* No hijacking of existing fields (sections/categories/custom fields) to store language information, so you are free to use the section/categories/custom fields in your application.
* Setup and Cleanup wizards.

On the public side...
* Detection of the language the user wants to view a site in via the url or browser headers.
* Persistance of the langauge selection so that the urls don't need re-writing.
* Automatic selection of the correct renditions of snippets in pages and forms.
* Fully functional search/commenting/feeds for each language the site supports.
* 404 support for finding renditions that are not available in the requested language.
* A tag listing available renditions of a given article and allowing switching between them.
* Tags for accessing language codes and direction information.
* Conditional tag for testing the visitor's language or the directionality of the language.
* Localised (and direction adjusted) feeds.


h2. Translation of Renditions.

Originally I wanted to allow the creation of new renditions by showing an exisiting rendition on one side of the screen and then allowing a translator to do the translating on the other side of the screen. However, this would mean big changes to the existing write tab, or replacing the write tab with a complicated substitute.

However, I happened upon Mary's 'Save New' plugin and that inspired the current solution that allows the write tab to remain virtually untouched and yet still allow translation. This is done by 'cloning' a source rendition and then translating the clone *in situ* in the write tab.

This means that the translator edits the clone, replacing the source text as they go, until it is all replaced with the target language. At that point the clone is a new rendition of the original author's work.

It's much easier on the translators as they get to keep the interface they are used to.


h2. Snippets

To add snippets to pages or forms...

# Make sure the page/form is wrapped with the @<txp:l10n_localise>@ ... @</txp:l10n_localise>@ statements.
# Within those statements type a string starting and ending with two hash characters, like this "##my_first_snippet##" (no need for the quotation marks.)
# On the *content > MLP* tab, look for your page or form on the pages or form subtab.
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

if( !defined( 'L10N_SUBS_TABLE' ) )
	define( 'L10N_SUBS_TABLE' , 'l10n_substitutions' );

global $txpcfg;



# -- The base classes are always needed so load them in...
include_once $txpcfg['txpath'].'/lib/l10n_base.php';



# -- Include the admin file only if needed...
if( @txpinterface == 'admin' )
	{
	include_once $txpcfg['txpath'].'/lib/l10n_admin.php';
	}



# -- Public code section follows...
if (@txpinterface == 'public')
	{
	global $l10n_view;

	$installed = $l10n_view->installed();
	if( !$installed )
		{
		return '';
		}

	# register a routine to handle URLs until the permanent_links plugin is integrated.
	register_callback( '_l10n_pretext' 					, 'pretext' );
	register_callback( '_l10n_textpattern_comment_submit'	, 'textpattern' );
	register_callback( '_l10n_tag_feeds'					, 'rss_entry' );
	register_callback( '_l10n_tag_feeds'					, 'atom_entry' );

	function _l10n_tag_feeds()
		{
		global $l10n_language , $thisarticle;

		$syndicate_body_or_excerpt = $GLOBALS['prefs']['syndicate_body_or_excerpt'];

		$dir = LanguageHandler::get_lang_direction_markup( $l10n_language['short'] );
		$content = $thisarticle['body'];
		$summary = $thisarticle['excerpt'];

		if ($syndicate_body_or_excerpt)
			{
			# short feed: use body as summary if there's no excerpt
			if( !trim($summary) )
				$summary = $content;
			$content = '';
			}

		$thisarticle['excerpt'] = tag( $summary , 'div' , $dir );
		$thisarticle['body']    = (!empty($content)) ? tag( $content , 'div' , $dir ) : '';
		}

	function _l10n_set_browse_language( $short_code , $debug=0 )
		{
		#
		#	Call this function with the SHORT language code.
		#
		#	Takes care of storing the global language variable and also tries to do extra stuff like
		#	setting up the correct locale for the requested language.
		#
		global $l10n_language;
		$result = false;

		$site_langs = LanguageHandler::get_site_langs();
		$tmp = LanguageHandler::expand_code( $short_code );

		if( $debug )
			echo br, "_l10n_set_browse_language( $short_code ) ... \$site_langs=", var_dump($site_langs),", \$tmp='$tmp'";

		if( isset( $tmp ) and in_array( $tmp , $site_langs ) )
			{
			if( $debug )
				echo " ... in IF() ... " ;
			$l10n_language = LanguageHandler::compact_code($tmp);
			$result = true;
			getlocale( $l10n_language['long'] );
			if( $debug )
				echo "\$tmp [$tmp] used to set \$l10n_language to " , var_dump($l10n_language['long']) , " returning TRUE", br ;
			}
		else
			{
			if( $debug )
				echo " ... in ELSE ... " ;
			if( !isset($l10n_language) or !in_array( $l10n_language['long'] , $site_langs ))
				{
				$l10n_language = LanguageHandler::compact_code( LanguageHandler::get_site_default_lang() );
				getlocale( $l10n_language['long'] );
				$result = (!empty($tmp));
				}
			}
		if( $debug )
			echo br , "Input='$short_code', Site Language set to " , var_dump( $l10n_language ) , " Returning ", var_dump($result),  br;

		return $result;
		}


	function _l10n_process_url()
		{
		global $l10n_language;

		$new_first_path = '';

		@session_start();
		//$l10n_language = @$_SESSION['lang'];
		$site_langs = LanguageHandler::get_site_langs();

		if (!defined('rhu'))
			define("rhu", preg_replace("/http:\/\/.+(\/.*)\/?$/U", "$1", hu));
		$path = explode('/', trim(str_replace(trim(rhu, '/'), '', $_SERVER['REQUEST_URI']), '/'));

		//echo br.br.br.br.br , "_l10n_process_url() ...";
		//echo "site langs = " , var_dump( $site_langs );

		if( !empty( $path ) )
			{
			#
			#	Examine the first path entry for the language request.
			#
			$tmp = array_shift( $path );
			$temp = LanguageHandler::expand_code( $tmp );
			$reduce_uri = true;
			$new_first_path = (isset($path[0])) ? $path[0] : '' ;

			//echo br , " ... first item=$tmp [$temp] ";
			if( !empty($temp) and in_array( $temp , $site_langs ) )
				{
				#
				#	Hit! We can serve this language...
				#
				//echo " ... setting lang=$tmp from path." , br;
				$_SESSION['lang'] = $tmp;
				$_SESSION['llang'] = $temp;
				}
			else
				{
				#
				#	Not a language this site can serve...
				#
				if( !LanguageHandler::is_valid_short_code( $tmp ) )
					{
					#
					#	And not a known language so don't reduce the uri and use
					# the original part of the path...
					#
					$reduce_uri = false;
					$new_first_path = $tmp;
					}
				}

			if( $reduce_uri )
				{
				$new_uri = '/' . join( '/' , $path );
				$_SERVER['REQUEST_URI'] = $new_uri;
				}
			}

		if( !isset($_SESSION['lang']) or empty($_SESSION['lang']) )
			{
			#
			#	If we are still missing a language for the session, try to get the prefered selection
			# from the user agent's HTTP header.
			#
			//echo br,br,br,br , "Processing user agent request...";
			$req_lang = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '' ;
			if( isset( $req_lang ) and !empty( $req_lang ) )
				{
				$chunks = split( ',' , $req_lang );
				//echo br, "Agent request: " , var_dump($req_lang) , br , " chunks: " , var_dump($chunks);
				if( count( $chunks ) )
					{
					foreach( $chunks as $chunk )
						{
						$info = split( ';' , $chunk );
						if( false === $info )
							{
							//echo br,br, "Could not split on ';' boundaries!";
							$info[] = $chunk;
							}
						$code = $info[0];
						if( $code )
							{
							$len = strlen( $code );
							if( $len === 2 )
								{
								$lang = LanguageHandler::expand_code( $info[0] );
								$lang = LanguageHandler::compact_code( $lang );
								}
							elseif( $len === 5 )
								$lang = LanguageHandler::compact_code( $info[0] );
							else
								continue;

							//echo br,br , "trying " , var_dump($lang), " in " , var_dump( $site_langs );
							if( in_array( $lang['long'] , $site_langs ) )
								{
								$_SESSION['lang'] = $lang['short'];
								$_SESSION['llang'] = $lang['long'];
								//echo br,br," ... Setting language to {$_SESSION['lang']} [",var_dump($lang),"], from user-agent request." , br;
								break;
								}
							}
						}
					}
				}
			}

		#
		#	If we are still missing a language for the session, use the site default...
		#
		if( !isset($_SESSION['lang']) or empty($_SESSION['lang']) )
			{
			$def = $site_langs[0];
			//echo " ... setting to default lang: $def " , br;
			$_SESSION['lang'] = $def;
			}

		_l10n_set_browse_language( $_SESSION['lang'] );

		//echo br , "\$l10n_language = " , var_dump($l10n_language);
		//echo br , ' setting $_SERVER[\'REQUEST_URI\'] to ', $_SERVER['REQUEST_URI'] , br , br;

		return $new_first_path;
		}


	function _l10n_textpattern_comment_submit()
		{
		global $pretext, $l10n_language;

		#
		#	Detect comment submission and update master textpattern table...
		#
		$commented = gps( 'commented' );
		if( $commented === '1' )
			{
			$id = isset($pretext['id']) ? $pretext['id'] : '' ;
			if( !empty($id) )
				{
				$thecount = safe_field('count(*)','txp_discuss','parentid='.doSlash($id).' and visible='.VISIBLE);

				#
				#	Update the l10n master table (which simply maps to the underlying 'textpattern' table)...
				#
				$updated = safe_update('l10n_master_textpattern',"comments_count='".doSlash($thecount)."'","ID='".doSlash($id)."'");
				}
			}
		}
	function _l10n_pretext()
		{
		function load_localised_pref( $name )
			{
			global $prefs,$pretext;
			$k = "snip-$name";
			$r = gTxt( $k );
			if( $r !== $k )
				{
				$GLOBALS[$name] = $r;
				$GLOBALS['prefs'][$name] = $r;
				$prefs[$name] = $r;
				$pretext[$name] = $r;
				}
			}
		global $l10n_language , $textarray , $prefs;

		$first_chunk = _l10n_process_url();
		//echo br,br,br,br,br,"First chunk=",$first_chunk;

		#
		#	Now we know what language this user is browsing in.
		# If it is NOT the site's currently selected language then we need to re-load
		# the textarray with the right language (otherwise some strings used in comment forms
		# and older/newer tags will be wrong!
		#
		if( LANG !== $l10n_language['long'] and LANG !== $l10n_language['short'] )
			{
			//echo br , 'Re-loading $textarray.';

			$textarray = load_lang( $l10n_language['long'] );
			$prefs['language'] = $l10n_language['long'];
			}

		load_localised_pref( 'site_slogan' );
		@$GLOBALS['prefs']['comments_default_invite'] = gTxt('comment');

		#
		#	Don't know why, but there seems to be some whitespace getting into the
		# output buffer. XHTML can cope but it causes a parse error in the feed xml
		#
		#	Simple solution is to make sure the output buffer is empty before
		# continuing the processing of rss or atom requests...
		#
		$ob_cleaning = array( 'rss' , 'atom' );
		if( in_array( $first_chunk , $ob_cleaning) )
			while( @ob_end_clean() );
		}

	/*
	TAG HANDLERS FOLLOW
	*/
	function l10n_lang_list( $atts )
		{
		global $thisarticle , $l10n_language, $is_article_list , $pretext;

		extract(lAtts(array(
							'title'				=> '',					#	Title will be prepended as a paragraph.
							'on404'				=> '', 					#	Article id to lookup. If explicitly defined
							'current_class'		=> 'l10n_current',		#	Literal class markup for the current language
							'language_class'	=> 'long',				#	How the class of the list item is marked up
																		#	'long' => long lang eg: en-gb | 'short' eg. 'en'
							'list_class'		=> 'l10n_lang_list',	#	Literal class markup for entire list
							'show_empty'  		=> '',					#	show all langs, even ones with no translation?
							'link_current'		=> '',					#	make the current language an active hyperlink?
							'display'			=> 'native',			# 	How the language is displayed on the web page
																		#	'native++' | 'native+' | 'native' | 'long' | 'short'
							),$atts));

		$on404			= !empty($on404);
		$show_empty		= !empty($show_empty);
		$link_current	= !empty($link_current);

		$list = array();
		$alangs = array();
		$slangs = LanguageHandler::get_site_langs();
		$article_list = $is_article_list;
		$section = empty($pretext['s']) ? '' : '/'.$pretext['s'];

		if( $on404 )
			{
			#
			#	Find the section and id of the faulting article (if possible)...
			#
			$url = trim($_SERVER['REQUEST_URI'] , '/');
			$parts = chopUrl($url);
			$section = '/'.$parts['u0'];
			$id = $parts['u1'];
			if( !empty($id) and is_numeric($id) )
				{
				$article_list = false;
				}
			else
				{
				return '';
				}
			#
			#	Make sure we show all alternatives, even if they are in the current language...
			#
			$link_current = true;
			}
		else
			{
			#
			#	Not on a 404 page, so use the one of the current article (if any).
			#
			$id = (isset($thisarticle['thisid'])) ? $thisarticle['thisid'] : '' ;
			}

		$show_title = !empty( $title );

		if( !$article_list )
			{
			//echo br , var_dump( $thisarticle ) , br , br;
			$alangs = ArticleManager::get_alternate_mappings( $id , 'nothing' , true );

			if( $show_title )
				$show_title = !empty( $alangs );
			}

		if( $show_title )
			$title = tag( $title , 'p' ) . n;
		else
			$title = '';

		foreach( $slangs as $lang )
			{
			$codes = LanguageHandler::compact_code($lang);
			$short = $codes['short'];
			$long  = $codes['long'];
			$dir   = LanguageHandler::get_lang_direction_markup($lang);
			//$dir   = '';

			switch( $display )
				{
				case 'short':
					$lname = $short;
				break;
				case 'long':
					$lname = $long;
				break;
				case 'native+':
					$lname = LanguageHandler::get_native_name_of_lang( $lang )." [$short]";
				break;
				case 'native++':
					$lname = LanguageHandler::get_native_name_of_lang( $lang )." [$long]";
				break;
				default:
					$lname = LanguageHandler::get_native_name_of_lang( $lang );
				break;
				}

			if( $article_list )
				{
				#
				#	No individual ID but we should be able to serve all the languages
				# so use the current url and inject the language component into each one...
				#
				$current = ($l10n_language['long'] === $lang);
				$text    = tag( $lname , 'span' , $dir);

				#
				#	Prep the line class...
				#
				$class = ('short'===$language_class) ? $short : $lang ;
				if( $current )
					$class .= ' '.$current_class;
				$class = ' class="'.$class.'"';

				if( !$current or $link_current )
					$line = '<a href="'.hu.$short.$_SERVER['REQUEST_URI'].'">'.$text.'</a>';
				else
					$line = $text;

				$list[] = tag( $line , 'li' , $class );
				}
			else
				{
				#
				#	If a translation exists for that language then we
				# build a valid url to it and make it active in the list, otherwise include it in the
				# list but wihtout the hyper-link.
				#
				#	The active page is marked up with a css class.
				#
				if( array_key_exists( $lang , $alangs ) )
					{
					$current = ($l10n_language['long'] === $lang);
					$text    = tag( $lname , 'span' , $dir);

					#
					#	Prep the line class...
					#
					$class = ('short'===$language_class) ? $short : $lang ;
					if( $current )
						$class .= ' '.$current_class;
					$class = ' class="'.$class.'"';

					if( !$current or $link_current )
						$line = '<a href="'.hu.$short.$section.'/'.$alangs[$lang].'">'.$text.'</a>';
					else
						$line = $text;

					$list[] = tag( $line , 'li' , $class );
					}
				else
					{
					//echo 'no translation.';
					if( $show_empty )
						$list[] = tag( $lname , 'li' );
					}
				}
			}


		$list = tag( join( "\n\t" , $list ) , 'ul' , " class=\"$list_class\"" );
		return $title . $list;
		}

	function l10n_if_lang( $atts , $thing )
	    {
		/*
		Basic markup tag. Use this to wrap blocks of content you only want to appear
		when the specified language is set or if the direction of the selected language matches
		what you want. (Output different css files for rtl layouts for example).
		*/
		global $l10n_language;
		$out = '';

		if( !$l10n_language )
			return $out;

		extract(lAtts(array(
							'lang' => $l10n_language['short'] ,
							'dir'  => '',
							'wraptag' => 'div' ,
							),$atts));

		if( !empty($dir) and in_array( $dir , array( 'rtl', 'ltr') ) )
			{
			#	Does the direction of the currently selected site language match that requested?
			#	If so, parse the contained content.
			if( $dir == LanguageHandler::get_lang_direction( $l10n_language['short'] ) )
				$out = parse($thing) . n;
			}
		elseif( $lang == $l10n_language['short'] or $lang == $l10n_language['long'] )
			{
			#	If the required language matches the site language, output a suitably marked up block of content.
			$dir = LanguageHandler::get_lang_direction_markup( $lang );
			$out = "<$wraptag lang=\"$lang\"$dir/>" . parse($thing) . "</$wraptag>" . n;
			}

		return $out;
	    }

	function l10n_get_language( $atts )
		{
		/*
		Outputs the current language. Use in page/forms to output the language needed by the doctype/html decl.
		*/
		global $l10n_language;

		extract( lAtts( array( 'type'=>'short' ) , $atts ) );

		if( !$l10n_language )
			return '';

		$result = $l10n_language['short'];
		if( strtolower($type) === 'long' )
			$result = $l10n_language['long'];
		return $result;
		}

	function _l10n_feed_link_cb( $matches )
		{
		global $l10n_feed_link_lang;

		#
		#	$matches[0] is the entire pattern...
		#	$matches[1] is the href...
		#
		$path = trim( str_replace( trim(hu, '/'), '', $matches[1] ), '/' );
		$path = $l10n_feed_link_lang['short'].'/'.$path;
		$path = ' href="'.hu.$path.'"';
		return $path;
		}
	function l10n_feed_link( $atts )
		{
		global $l10n_language, $l10n_feed_link_lang;
		$l10n_feed_link_lang = $l10n_language;

		if( isset($atts['code']) )
			{
			$code = $atts['code'];
			unset( $atts['code'] );

			if( $code === 'none' )
				return feed_link( $atts );

			$l10n_feed_link_lang = LanguageHandler::compact_code( $code );
			}

		#
		#	Get the standard result...
		#
		$result = feed_link( $atts );

		#
		#	Inject the language code into the url...
		#
		$pattern = '/ href="(.*)" /';
		$result = preg_replace_callback( $pattern , '_l10n_feed_link_cb' , $result );

		return $result;
		}

	function l10n_get_lang_dir( $atts )
		{
		/*
		Outputs the direction (rtl/ltr) of the current language.
		Use in page/forms to output the direction needed by xhtml elements.
		*/
		global $l10n_language;

		extract( lAtts( array( 'type'=>'short' ) , $atts ) );

		if( !$l10n_language )
			$lang = LanguageHandler::compact_code( LanguageHandler::get_site_default_lang() );
		else
			$lang = $l10n_language;

		$dir = LanguageHandler::get_lang_direction( $lang[$type] );
		return $dir;
		}

	function l10n_localise($atts, $thing = '')
		{
		global $l10n_language, $thisarticle, $thislink;

		if ($l10n_language)
			{
			if (array_key_exists('category', $atts))
				{
				$id = $atts['category'];
				$table = PFX.'txp_category';
				$rs = safe_field('entry_value', L10N_SUBS_TABLE, "`entry_id` = '$id' AND `entry_column` = 'title' AND `table` = '$table' AND `language` = '{$l10n_language['long']}'");

				if ($rs && !empty($rs))
					return $rs;
				else
					return ucwords($atts['category']);

				}
			else if (array_key_exists('section', $atts))
				{
				$id = $atts['section'];
				$table = PFX.'txp_section';
				$rs = safe_field('entry_value', L10N_SUBS_TABLE, "`entry_id` = '$id' AND `entry_column` = 'title' AND `table` = '$table' AND `language` = '{$l10n_language['long']}'");

				if ($rs && !empty($rs))
					return $rs;
				else
					return ucwords($atts['section']);
				}
			else if ($thing)
				{
				# SED: Process the direct snippet substitutions needed in the contained content.
				$thing = SnippetHandler::substitute_snippets( $thing );
				$html = parse($thing);
				$html = preg_replace('#((href|src)=")(?!\/?(https?|ftp|download|images|))\/?#', $l10n_language['short'].'/'.'$1', $html);
				return $html;
			}
		}

		if (array_key_exists('category', $atts))
			{
			$rs = safe_field('title', 'txp_category', '`name` = "'.$atts['category'].'"');
			if ($rs && !empty($rs))
				return $rs;
			else
				return ucwords($atts['category']);

			}
		else if (array_key_exists('section', $atts))
			{
			$rs = safe_field('title', 'txp_section', '`name` = "'.$atts['section'].'"');
			if ($rs && !empty($rs))
				return $rs;
			else
				return ucwords($atts['section']);
			}
		else if ($thing)
			{
			# SED: Process and string substitutions needed in the contained content.
			$thing = SnippetHandler::substitute_snippets( $thing );
			return parse($thing);
		}

		return null;
		}
	}

# --- END PLUGIN CODE ---
?>

