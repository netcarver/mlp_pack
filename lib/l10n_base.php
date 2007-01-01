<?php

#
#	This file contains functions/classes needed on both the admin and public side
# of the plugin.
#

if( !defined( 'L10N_COL_OWNER' ) )
	define( 'L10N_COL_OWNER' , L10N_NAME.'_owner' );
if( !defined( 'L10N_COL_LANG' ) )
	define( 'L10N_COL_LANG' , L10N_NAME.'_lang' );
if( !defined( 'L10N_COL_GROUP' ) )
	define( 'L10N_COL_GROUP' , L10N_NAME.'_group' );

global $txpcfg , $event;
include_once $txpcfg['txpath'].'/lib/l10n_langs.php';

function _l10n_redirect_textpattern($table)
	{
	if( @txpinterface !== 'public' )
		return $table;

	if( 'textpattern' === $table )
		{
		global $l10n_language;

		$language_set 	= isset( $l10n_language );
		$language_ok	= true;
		if( $language_set and $language_ok )
			{
			$table = _l10n_make_textpattern_name( $l10n_language );
			}
		}
	elseif ( L10N_MASTER_TEXTPATTERN === $table )
		{
		$table = 'textpattern';
		}
	return $table;
	}

function _l10n_remap_fields( $thing , $table , $get_mappings=false )
	{
	static $interfaces = array( 'public' , 'admin' );
	static $mappings = array	(
		'txp_category'	=> array(
			'title' 		=> array(
				'sql' 			=> "varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''" ,
				'e' 			=> 'category',
				'paint_steps'	=> array( 'cat_article_edit', 'cat_link_edit', 'cat_image_edit', 'cat_file_edit' ),
				'paint' 		=> '_l10n_category_paint',
				'save_steps'	=> array( 'cat_article_create', 'cat_article_save', 'cat_link_create', 'cat_link_save', 'cat_image_create', 'cat_image_save', 'cat_file_create', 'cat_file_save', ),
				'save'			=> '_l10n_category_save',
				),
			),
		'txp_file' 		=> array(
			'description'	=> array(
				'sql'			=> "text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL" ,
				'e' 			=> 'file',
				'paint_steps'	=> array( 'file_edit' ),
				'paint' 		=> '_l10n_file_paint',
				'save_steps'	=> array( 'file_save' ),
				'save'			=> '_l10n_file_save',
				),
			),
		'txp_image'		=> array(
			'alt'			=> array(
				'sql'			=> "varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",
				'e' 			=> 'image',
				'paint_steps'	=> array( 'image_edit' ),
				'paint' 		=> '_l10n_image_paint',
				'save_steps'	=> array( 'image_save' ),
				'save'			=> '_l10n_image_save',
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
		'txp_link' 		=> array(
			'description'	=> array(
				'sql'			=> "text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",
				'e' 			=> 'link',
				'save_steps'	=> array( 'link_post', 'link_save' ),
				'save'			=> '_l10n_link_save',
				'paint_steps'	=> '',
				'paint' 		=> '_l10n_link_paint',
				),
			),
		'txp_section'	=> array(
			'title' 		=> array(
				'sql'			=> "varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''",
				'e' 			=> 'section',
				'paint_steps'	=> array( '' ),
				'paint' 		=> '_l10n_section_paint',
				'save_steps'	=> array( 'section_save', 'section_create' ),
				'save'			=> '_l10n_section_save',
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
		$lang = MLPLanguageHandler::get_site_default_lang();
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
		$localised_field = _l10n_make_field_name( $field , $lang );
		$r = "`$localised_field` as `$field`";
		//$r = '`'.$lang."-$field` as `$field`";

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
function _l10n_walk_mappings( $fn , $atts='' )
	{
	if( !is_callable( $fn ) )
		return false;

	global $l10n_mappings;
	if( !is_array( $l10n_mappings ) )
		$l10n_mappings = _l10n_remap_fields( '' , '' , true );

	foreach( $l10n_mappings as $table=>$fields )
		{
		foreach( $fields as $field=>$attributes )
			{
			//	The user function must create a safe table name by calling safe_pfx() on the table name
			call_user_func( $fn , $table , $field , $attributes , $atts );
			}
		}

	return true;
	}

function _l10n_make_field_name( $column , $lang )
	{
	$tmp = _l10n_clean_sql_name( L10N_NAME.'_'.$lang.'_'.$column );
	return $tmp;
	}

function _l10n_clean_sql_name( $name )
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
function _l10n_make_textpattern_name( $full_code )
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
			$error = "_l10n_make_textpattern_name() given an invalid input value $full_code";
			trigger_error( $error , E_USER_ERROR );
			}
		}

	if( strlen( $code ) < 2 )
		{
		trigger_error( "$code is too short!" , E_USER_ERROR );
		}

	$result = _l10n_clean_sql_name( L10N_RENDITION_TABLE_PREFIX . $code );

	return $result;
	}



class MLPLanguageHandler
	{
	#	class MLPLanguageHandler implements ISO-693-1 language support.
	function do_fleshout_names( &$langs , $suffix='' , $append_code = true , $append_default=false , $use_long=true )
		{
		$result = array();
		if( is_array($langs) and !empty($langs) )
			{
			foreach( $langs as $code )
				{
				$code = trim( $code );
				$tmp = MLPLanguageHandler::get_native_name_of_lang( $code );
				if( $append_code )
					$tmp .= ' [' . $code . ']';
				if( !$use_long )
					$code = substr( $code , 0 , 2 );
				if( !empty( $suffix ) )
					$tmp .= ' ' . $suffix;
				if( $append_default and ($code === MLPLanguageHandler::get_site_default_lang() ) )
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
				$tmp = MLPLanguageHandler::get_lang_direction( $code );
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
			trigger_error( 'Invalid type passed to MLPLanguageHandler::compact_code()', E_USER_ERROR);
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

		if( empty( $short_code ) )
			{
			//echo br, "expand_code( $short_code ) rejecting empty \$short_code!";
			return null;
			}

		$langs = MLPLanguageHandler::get_site_langs();
		foreach( $langs as $code )
			{
			$code = trim( $code );
			$r = MLPLanguageHandler::compact_code( $code );
			if( $short_code === $r['short'] )
				$result[] = $code;
			}
		if( count( $result ) )
			return $result[0];
		return NULL;
		}

	function iso_693_1_langs ( $input, $to_return='lang' )
		{
		global $iso_693_1_langs;

		switch ( $to_return )
			{
			default:
			case 'lang':
				$r = MLPLanguageHandler::compact_code( $input );
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
				$r = MLPLanguageHandler::compact_code( $input );
				return $r['short'];
			break;

			case 'short2long':
				return MLPLanguageHandler::expand_code( $input );
			break;

			case 'dir':
				extract( MLPLanguageHandler::compact_code( $input ) );
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
		$lang = MLPLanguageHandler::compact_code( $code );
		$short = $lang['short'];
		if( isset( $short ) )
			return MLPLanguageHandler::is_valid_short_code($short);

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
			$result = ( MLPLanguageHandler::iso_693_1_langs( $code ) );
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
			$out = MLPLanguageHandler::iso_693_1_langs( $name, 'code' );
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
		if( !empty($lang) and ('rtl' == MLPLanguageHandler::iso_693_1_langs( $lang, 'dir' ) ) )
			$dir = ' dir="rtl"';
		return $dir;
		}

	function get_lang_direction( $lang )
		{
		/*
		Builds the xhtml direction markup needed based upon the directionality of the language requested.
		*/
		$dir = 'ltr';
		if( !empty($lang) and ('rtl' == MLPLanguageHandler::iso_693_1_langs( $lang, 'dir' ) ) )
			$dir = 'rtl';
		return $dir;
		}

	function get_native_name_of_lang( $code )
		{
		/*
		Returns the native name of the given language code.
		*/
		return (MLPLanguageHandler::iso_693_1_langs( $code )) ? MLPLanguageHandler::iso_693_1_langs( $code ) : MLPLanguageHandler::iso_693_1_langs( 'en' ) ;
		}

	function get_site_langs( $set_if_empty = false )
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
		$lang_codes = MLPLanguageHandler::get_site_langs();
		return $lang_codes[0];
		}
	function get_installation_langs( $limit = 400 )
		{
		/*
		Returns an array of all the languages in this TXP installation with more
		than the limit number of strings in that lang...
		*/
		$installation_langs = array( LANG );
		$langs = safe_column('lang','txp_lang',"1=1 GROUP BY 'lang'");
		foreach( $langs as $lang )
			{
			$count = safe_count( 'txp_lang' , "`lang`='$lang'" );
			if( ($count >= $limit) && ($lang !== LANG) )
				$installation_langs[] = $lang;
			}
		unset( $langs );

		return $installation_langs;
		}

	}

?>
