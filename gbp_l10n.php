<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Uncomment and edit this line to override:
# $plugin['name'] = 'abc_plugin';

$plugin['version'] = '0.5';
$plugin['author'] = 'Graeme Porteous';
$plugin['author_uri'] = 'http://porteo.us/projects/textpattern/gbp_l10n/';
$plugin['description'] = 'Textpattern content localization.';
$plugin['type'] = '1';

@include_once('../zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---
h1. Instructions

Under the content tab, there is a new localisation subtab. Here you can find a list of every article, category title and section titles which needs tobe localised.

To see your localised content you need to surround *everything* in all of your page and form templates with @<txp:gbp_localize>@ ... @</txp:gbp_localize>@

You can also use @<txp:gbp_localize section="foo" />@ or @<txp:gbp_localize category="bar" />@ to output localised sections and categories
# --- END PLUGIN HELP ---
<?php
}
# --- BEGIN PLUGIN CODE ---

// Constants
if (!defined('gbp_language'))
	define('gbp_language', 'language');

// require_plugin() will reset the $txp_current_plugin global
global $txp_current_plugin;
$gbp_current_plugin = $txp_current_plugin;
require_plugin('gbp_admin_library');
$txp_current_plugin = $gbp_current_plugin;

class LocalizationView extends GBPPlugin {
	
	var $gp = array(gbp_language);
	var $preferences = array(
		'languages' => array('value' => array('fr', 'de'), 'type' => 'gbp_array_text'),

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

	);

	function preload() {

		global $gbp, $txp_current_plugin, $_GBP;
		$gbp[$txp_current_plugin] = &$this;
		$_GBP[0] = &$this;

		if ($this->preferences['articles']['value'])
			new LocalisationTabView('articles', 'article', $this);
		if ($this->preferences['categories']['value'])
			new LocalisationTabView('categories', 'category', $this);
		// if ($this->preferences['links']['value'])
		// 	new LocalisationTabView('links', 'link', $this);
		if ($this->preferences['sections']['value'])
			new LocalisationTabView('sections', 'section', $this);
		new GBPPreferenceTabView('preferences', 'preference', $this);

		$sql[] = 'CREATE TABLE IF NOT EXISTS `'.PFX.'gbp_l10n` (';
		$sql[] = '`id` int(11) NOT NULL AUTO_INCREMENT, ';
		$sql[] = '`table` varchar(64) NOT NULL , ';
		$sql[] = '`language` varchar(16) NOT NULL , ';
		$sql[] = '`entry_id` varchar(128) default NULL, ';
		$sql[] = '`entry_column` varchar(128) default NULL, ';
		$sql[] = '`entry_value` text, ';
		$sql[] = '`entry_value_html` text, ';
		$sql[] = 'PRIMARY KEY (`id`)';
		$sql[] = ') TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=1';
		
		safe_query(join('', $sql));
	}

	function main() {

		foreach ($this->preferences['languages']['value'] as $key)
			$languages['value'][$key] = gTxt($key);
		
		if (!gps(gbp_language))
			$_GET[gbp_language] = $this->preferences['languages']['value'][0];

		setcookie(gbp_language, gps(gbp_language), time() + 3600 * 24 * 365);

		$out[] = '<div style="padding-bottom: 3em; text-align: center; clear: both;">';
		$out[] = form(
			fLabelCell('Language: ').
			selectInput(gbp_language, $languages['value'], gps(gbp_language), 0, 1).
			'<br /><a href="'.hu.gps(gbp_language).'/">view localised site</a>'.
			$this->form_inputs()
		);
		$out[] = '</div>';

		echo join('', $out);
	}
}

class LocalisationTabView extends GBPAdminTabView {

	function preload() {

		if (gps('step'))
			$this->save_post();
	}

	function main() {

		switch ($this->event)
		{
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

	function render_list($key, $value, $table, $where) {

		$out[] = '<div style="float: left; width: 50%;" class="gbp_i18n_list">';

		// SQL used in both queries
		$sql = "FROM ".PFX."$table AS source, ".PFX."gbp_l10n AS l10n WHERE source.$key = l10n.entry_id AND l10n.entry_value != '' AND l10n.table = '".PFX."$table' AND l10n.language = '".gps(gbp_language)."' AND $where";

		// Localised
		$rows = startRows("SELECT DISTINCT source.$key as k, source.$value as v ".$sql);
		if ($rows) {

			$out[] = '<ul><h3>Localised</h3>';
			while ($row = nextRow($rows))
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_id.'='.$row['k'].'">'.$row['v'].'</a></li>';

			$out[] = '</ul>';
		}

		// Unlocalised
		$rows = startRows("SELECT DISTINCT $key as k, $value as v FROM ".PFX."$table WHERE $key NOT IN (SELECT DISTINCT source.$key $sql) AND $where");
		if ($rows) {

			$out[] = '<ul><h3>Unlocalised</h3>';
			while ($row = nextRow($rows))
				$out[] = '<li><a href="'.$this->parent->url().'&#38;'.gbp_id.'='.$row['k'].'">'.$row['v'].'</a></li>'.n;

			$out[] = '</ul>';
		}

		$out[] = '</div>';
		echo join('', $out);
	}

	function render_edit($vars, $hidden_vars, $table, $where, $entry_id) {
		global $_GBP;
		
		$fields = trim(join(',', array_merge($vars, $hidden_vars)), ' ,');

		if ($rs1 = safe_row($fields, $table, $where)) {
			$out[] = '<div style="float: right; width: 50%;" class="gbp_l10n_edit">';

			foreach($rs1 as $field => $value) {

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

				if (in_array($field_type, array('blob'))) {

					$out[] = '<p class="gbp_l10n_field">'.ucwords($field).'</p>';
					$out[] = '<div class="gbp_l10n_value_disable">'.text_area('" readonly class="', 200, 420, $value).'</div>';
					$out[] = '<div class="gbp_l10n_value">'.text_area($field, 200, 420, $entry_value).'</div>';

				} else if (in_array($field_type, array('string'))) {

					$out[] = '<p class="gbp_l10n_field">'.ucwords($field).'</p>';
					$out[] = '<div class="gbp_l10n_value_disable">'.fInput('text', '', $value, 'edit" readonly title="', '', '', 60).'</div>';
					$out[] = '<div class="gbp_l10n_value">'.fInput('text', $field, $entry_value, 'edit', '', '', 60).'</div>';

				} else
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

	function save_post() {

		global $txpcfg;
		extract(get_prefs());

		extract(gpsa($this->parent->preferences[$this->event.'_hidden_vars']['value']));
		$vars = gpsa($this->parent->preferences[$this->event.'_vars']['value']);

		$table = PFX.$_POST['gbp_table'];
		$language = $_POST[gbp_language];
		$entry_id = $_POST[gbp_id];

		include_once $txpcfg['txpath'].'/lib/classTextile.php';
		$textile = new Textile();

		foreach($vars as $field => $value) {

			if ($field == 'Body') {

				if (!isset($textile_body))
				$textile_body = $use_textile;

				if ($use_textile == 0 or !$textile_body)
					$value_html = trim($value);

				else if ($use_textile == 1)
					$value_html = nl2br(trim($value));

				else if ($use_textile == 2 && $textile_body)
					$value_html = $textile -> TextileThis($value);

			}

			if ($field == 'Title')
				$value = $textile->TextileThis($value, '', 1);

			if ($field == 'Excerpt') {

				if (!isset($textile_excerpt))
					$textile_excerpt = 1;

				if ($textile_excerpt) {
					$value_html = $textile -> TextileThis($value);
				} else {
					$value_html = $textile -> TextileThis($value, 1);
				}
			}

			if (!isset($id))
				$id = '';

			if (!isset($value_html))
				$value_html = '';

			if (phpversion() >= "4.3.0") {

				$value = mysql_real_escape_string($value);
				$value_html = mysql_real_escape_string($value_html);

			} else {

				$value = mysql_escape_string($value);
				$value_html = mysql_escape_string($value_html);
			}

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
 
if (@txpinterface == 'admin') {

	// We are admin-side.
	new LocalizationView('localisation', 'l10n', 'content');

}
else {

	// We are publish-side.
	global $prefs, $gbp_language;

	if (!defined('rhu'))
		define("rhu", preg_replace("/http:\/\/.+(\/.*)\/?$/U", "$1", hu));

	$path = explode('/', trim(str_replace(trim(rhu, '/'), '', $_SERVER['REQUEST_URI']), '/'));
	if (!array_key_exists('gbp_l10n_languages', $prefs))
		$prefs['gbp_l10n_languages'] = 'fr,de';

	$lang_codes = explode(',', $prefs['gbp_l10n_languages']);
	foreach($lang_codes as $code)
	{
		if ($path[0] == $code)
			$gbp_language = $code;
	}

	function gbp_localize($atts, $thing = '') {
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

			return parse($thing);
		}

		return null;
	}
}

# --- END PLUGIN CODE ---

?>
