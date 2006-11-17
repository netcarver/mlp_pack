<?php

$plugin['name'] 		= 'zem_contact_lang_mlp';
$plugin['version'] 		= '4.0.3-MLP';
$plugin['author'] 		= 'Stephen Dickinson, based on work by the TXP Community';
$plugin['author_uri'] 	= 'http://forum.textpattern.com/viewtopic.php?id=12956';
$plugin['description'] 	= 'MLP strings plug-in for Zem Contact Reborn';
$plugin['type'] 		= 0; 	# No longer has to be an admin plugin to register the strings!

$plugin['url'] = '$HeadURL$';
$plugin['date'] = '$LastChangedDate$';
$plugin['revision'] = '$LastChangedRevision$';

@include_once('../zem_tpl.php');

if (0) {
?>
<!-- CSS SECTION
# --- BEGIN PLUGIN CSS ---
# --- END PLUGIN CSS ---
-->
<!-- HELP SECTION
# --- BEGIN PLUGIN HELP ---

<div style="text-align:center;font-weight:bold;font-size:24px;text-decoration:underline;">Zem Contact Lang</div>

This is a separate language plug-in for use with Zem Contact Reborn. Both plug-ins need to be installed and activated in order to work properly.

Separating the language in this way will enable non-english users to update the main plug-in without affecting their &#8220;localisation&#8221;.

<div id="local" style="text-align:center;font-weight:bold;font-size:24px;text-decoration:underline;">Localisation</div>

Throughout the <code>zem_contact_reborn</code> plug-in, use has been made of a separate <code>gTxt</code> function which you can see in this plug-in&#8217;s code by clicking on the &#8220;Edit&#8221; button.

If you are using the plug-in for a non-english site you can make use of this to localise text outputs for your preferred language.

You should only edit text that appears after the <code>=&gt;</code> sign.

If you have a dual-language site and the languages use separate &#8220;sections&#8221;, you can use the &#60;txp:if&#95;section&#62; tag to enable different translations. An example of this usage is shown in the <strong><a href="http://forum.textpattern.com/viewtopic.php?id=13416">forum thread</a></strong>. Our thanks to Els (doggiez) for this example.

# --- END PLUGIN HELP ---

-->
<?php
}

# --- BEGIN PLUGIN CODE ---

#	Define a (hopefully) unique prefix for our strings...
if( !defined( 'ZCRL_PREFIX' ) )
	define( 'ZCRL_PREFIX' , 'zem_crl' );

# 	Here are the strings. Note that they all use the single quote format.
# The variable substitutions will be done as the strings are used.
global $zem_crl_strings;
$zem_crl_strings = array(
	'checkbox'			=> 'Checkbox',
	'contact'			=> 'Contact',
	'email'				=> 'Email',
	'email_subject'		=> '$var1 > Inquiry',
	'email_thanks'		=> 'Thank you, your message has been sent.',
	'field_missing'		=> 'Required field, &#8220;<strong>$var1</strong>&#8221;, is missing.',
	'form_expired'		=> 'The form has expired, please try again.',
	'general_inquiry'	=> 'General inquiry',
	'invalid_email'		=> '&#8220;<strong>$var1</strong>&#8221; is not a valid email address.',
	'invalid_host'		=> '&#8220;<strong>$var1</strong>&#8221; is not a valid email host.',
	'invalid_value'		=> 'Invalid value for &#8220;<strong>$var1</strong>&#8221;, &#8220;<strong>$var2</strong>&#8221; is not one of the available options.',
	'mail_sorry'		=> 'Sorry, unable to send email.',
	'message'			=> 'Message',
	'min_warning'		=> '&#8220;<strong>$var1</strong>&#8221; must contain at least $var2 characters.',
	'option'			=> 'Option',
	'radio'				=> 'Radio',
	'receiver'			=> 'Receiver',
	'send'				=> 'Send',
	'spam'				=> 'We do not accept spam thankyou!',
	'text'				=> 'Text',
	'to'				=> 'No &#8220;<strong>to</strong>&#8221; email address specified.',
	'to_missing'		=> '&#8220;<strong>To</strong>&#8221; address is missing.'
	);


#	Register the callback for the enumerate string event.
# If the MLP pack is not present and active this will NOT get called.
if( @txpinterface=='public' )
	{
	register_callback( 'zem_crl_enumerate_strings' , 'l10n.enumerate_strings' );

	#	Here's a callback routine used to register the above strings with
	# the MLP Pack (if installed).
	function zem_crl_enumerate_strings($event , $step='' , $pre=0)
		{
		global $zem_crl_strings;
		$r = array	(
					'owner'		=> 'zem_contact_lang_mlp',	#	Name the plugin these strings are for.
					'prefix'	=> ZCRL_PREFIX,				#	Its unique string prefix
					'lang'		=> 'en-gb',					#	The language of the initial strings.
					'event'		=> 'public',				#	public/admin/common = which interface the strings will be loaded into
					'strings'	=> $zem_crl_strings,		#	The strings themselves.
					);
		return $r;
		}

	}

#
#	Here's the local gTxt routine.
#
#	Need to make this fallback to the local array in case this is not being used with the MLP pack.
#
function zem_contact_gTxt($what, $var1 = '', $var2 = '')
	{
	global $zem_crl_strings;

	#	Build an array of substitutions (if any)...
	$args = array();
	if( !empty( $var1 ) )
		$args['$var1'] = $var1;
	if( !empty( $var2 ) )
		$args['$var2'] = $var2;

	#	Build the correct key (note the dash--it's required) and grab from the $textarray strings...
	$key = ZCRL_PREFIX . '-' . $what;
	$val = gTxt( $key );

	#	If the gTxt fetch failed to pull the string, go direct to the default lang as a backup...
	if( $val === $key )
		$val = $zem_crl_strings[ $what ];

	#	Substitute values as needed...
	$val = strtr( $val , $args );
	return $val;
	}

# --- END PLUGIN CODE ---
?>
