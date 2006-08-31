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
	# register a routine to handle URLs until the permanent_links plugin is integrated.
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
			getlocale( $gbp_language['long'] );
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
				getlocale( $gbp_language['long'] );
				$result = (!empty($tmp));
				}
			}
		if( $debug )
			echo br , "Input='$short_code', Site Language set to " , var_dump( $gbp_language ) , " Returning ", var_dump($result),  br;

		return $result;
		}

	function _l10n_pretext()
		{
		function load_localised_pref( $name )
			{
			global $prefs , $gbp_language;
			//	echo br ,br ,br ,br , "pretext: load_localised_pref( $name ) ... language = '{$gbp_language['long']}' ... ";
			$r = StringHandler::load_strings( $gbp_language['long'] , " AND `name`='snip-$name'" );
			if( !empty( $r ) )
				{
				$v = $r['snip-'.$name];
				$GLOBALS[$name] = $v;
				$GLOBALS['prefs'][$name] = $v;
				$prefs[$name] = $v;
				//	echo " \$prefs[$name] = " , $prefs[$name] , br , var_dump( $v );
				}
			}
		global $prefs, $gbp_language;

		if (!defined('rhu'))
			define("rhu", preg_replace("/http:\/\/.+(\/.*)\/?$/U", "$1", hu));
		$path = explode('/', trim(str_replace(trim(rhu, '/'), '', $_SERVER['REQUEST_URI']), '/'));

		//		echo br.br.br.br , "_l10n_pretext() ... ";
		$tmp = array_shift($path);
		//		echo " ... first item=$tmp ";
		if( gbp_l10n_set_browse_language( $tmp ) )
			{
			#	Reset the URL, removing the language component...
			$new_uri = '/' . join( '/' , $path );
			$_SERVER['REQUEST_URI'] = $new_uri;
			}

		//		echo ' setting $_SERVER[\'REQUEST_URI\'] to ', $_SERVER['REQUEST_URI'] , br;

		#	Load the site name and slogan into the $prefs[] array in the right place...
		load_localised_pref( 'sitename' );
		load_localised_pref( 'site_slogan' );

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
							'lang' => $gbp_language['short'] ,
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

		if( !$gbp_language )
			$lang = LanguageHandler::compact_code( LanguageHandler::get_site_default_lang() );
		else
			$lang = $gbp_language;

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

				if (isset($thisarticle))
					{
					$rs = safe_rows('entry_value, entry_value_html, entry_column', 'gbp_l10n', '`language` = \''.$gbp_language['long'].'\' AND `entry_id` = \''.$thisarticle['thisid']."' AND `table` = '".PFX."textpattern'");
					if( $rs )
						foreach( $rs as $row )
							{
							if( $row['entry_value'] )
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


# --- END PLUGIN CODE ---
?>

