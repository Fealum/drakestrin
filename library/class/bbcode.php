<?php
require_once(createPath(array('library', 'ext', 'stringparser_bbcode', 'stringparser_bbcode.class.php')));
class BBCode {
	public static $instance;

	private function __construct() {
	}
	
	// singleton pattern
	static function getInstance() {
		if(!isset(self::$instance)) {
			
			// create uniform line breaks
			function convertlinebreaks ($text) {
				return preg_replace ("/\015\012|\015|\012/", "\n", $text);
			}
			
			// strip everything but line breaks
			function bbcode_stripcontents ($text) {
				return preg_replace ("/[^\n]/", '', $text);
			}
			
			function bbcode_typography($text) {
				$text = preg_replace ("/\.\.\./", '&hellip;', $text);
				$text = preg_replace ("/\s-\s/", '&nbsp;&ndash;&nbsp;', $text);
				$text = preg_replace ("/\&quot;(.*?)\&quot;/", '&raquo;$1&laquo;', $text);
				return $text;
			}
			
			function bbcode_emoticon($text) {
				global $config;
				$emoticons = array(
					":))" => array("31.gif", "glücklich lächelnd"),
					":)" => array("30.gif", "lächelnd"),
					":]" => array("32.gif", "selbstzufrieden lächelnd"),
					":D" => array("33.gif", "lachend"),
					":rofl:" => array("34.gif", "auf dem Boden rollend vor Lachen"),
					";)" => array("35.gif", "zwinkernd"),
					":(" => array("36.gif", "traurig"),
					";o" => array("37.gif", "erschöpft"),
					";(" => array("38.gif", "weinend"),
					"X(" => array("39.gif", "wütend"),
					"X/" => array("40.gif", "fuchsteufelswild"),
					":teufel:" => array("41.gif", "teuflisch"),
					":o" => array("54.gif", "geplagt"),
					"?(" => array("55.gif", "fragend"),
					"8o" => array("56.gif", "schockiert"),
					"8)" => array("57.gif", "cool"),
					":P" => array("58.gif", "die Zunge rausstreckend"),
					":tongue:" => array("59.gif", "fröhlich lachend"),
					":rolleyes:" => array("63.gif", "augenrollend"),
					":baby:" => array("64.gif", "Baby"),
					":hallo:" => array("65.gif", ""),
					":genial:" => array("66.gif", ""),
					"<3" => array("67.gif", ""),
					":>" => array("69.gif", ""),
					":3" => array("72.gif", ""),
					":adel:" => array("42.gif", ""),
					":koenig:" => array("43.gif", ""),
					":kaiser:" => array("44.gif", ""),
					":veuxin:" => array("45.gif", ""),
					":trinken" => array("46.gif", ""),
					":party:" => array("47.gif", ""),
					":ritter:" => array("48.gif", ""),
					":angriff:" => array("49.gif", ""),
					":fechter:" => array("50.gif", ""),
					":spam:" => array("51.gif", ""),
					":dito:" => array("52.gif", ""),
					":urlaub:" => array("53.gif", ""),
					":richter:" => array("60.gif", ""),
					":hero:" => array("61.gif", ""),
					":ritter2:" => array("62.gif", ""),
					":tinos:" => array("68.gif", ""),
					":met:" => array("70.gif", ""),
					"=)" => array("31.gif", ""),
					":evil:" => array("40.gif", ""),
					":birth:" => array("47.gif", ""),
					":cool:" => array("57.gif", ""),
					":verliebt:" => array("67.gif", "")
				);
				foreach ($emoticons as $code => $path) {
					$text = str_replace(htmlspecialchars($code), '<img src="'.$config->url.'/img/emoticon/'.$path[0].'" title="'.htmlentities($path[1]).'">', $text);
				}
				return $text;
			}
			
			// convert size
			function do_bbcode_size($action, $attributes, $content, $params, $node_object) {
				if ($action == 'validate') {
					if (!isset ($attributes['default']) || !intval($attributes['default'])) {
						return false;
					}
					return true;
				}
				$size = (int) $attributes['default'];
				return '<font size="'.$size.'">'.$content.'</font>';
			}
			
			// convert color
			function do_bbcode_color($action, $attributes, $content, $params, $node_object) {
				if ($action == 'validate') {
					if (!isset ($attributes['default']) || !ctype_alnum(substr($attributes['default'], 1))) {
						return false;
					}
					return true;
				}
				return '<font color="'.$attributes['default'].'">'.$content.'</font>';
			}
			
			// convert urls
			function do_bbcode_url($action, $attributes, $content, $params, $node_object) {
				if (!isset ($attributes['default'])) {
					$url = $content;
					$text = htmlspecialchars ($content);
				} else {
					$url = $attributes['default'];
					$text = $content;
				}
				if ($action == 'validate') {
					if (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:'
							|| substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') {
						return false;
					}
					return true;
				}
				return '<a href="'.htmlspecialchars ($url).'">'.$text.'</a>';
			}
			
			// convert images
			function do_bbcode_img($action, $attributes, $content, $params, $node_object) {
				if ($action == 'validate') {
					if (substr ($content, 0, 5) == 'data:' || substr ($content, 0, 5) == 'file:'
							|| substr ($content, 0, 11) == 'javascript:' || substr ($content, 0, 4) == 'jar:') {
						return false;
					}
					return true;
				}
				return '<img src="'.htmlspecialchars($content).'" alt="">';
			}
			
			// convert code
			function do_bbcode_code($action, $attributes, $content, $params, $node_object) {
				if ($action == 'validate') {
					return true;
				}
				return "<code>".$content."</code>";
			}
			
			$bbcode = new StringParser_BBCode();
			
			$bbcode->addFilter (STRINGPARSER_FILTER_PRE, 'convertlinebreaks');
			
			$bbcode->addParser (array ('block', 'inline', 'listitem', 'handlung'), 'htmlspecialchars');
			$bbcode->addParser (array ('block', 'inline', 'link', 'listitem', 'code', 'handlung'), 'nl2br');
			$bbcode->addParser (array ('block', 'inline', 'link', 'listitem', 'handlung'), 'bbcode_typography');
			$bbcode->addParser (array ('block', 'inline', 'link', 'listitem', 'handlung'), 'bbcode_emoticon');
			$bbcode->addParser ('list', 'bbcode_stripcontents');
			
			$bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'),
					'inline', array ('listitem', 'block', 'inline', 'link', 'handlung'), array ());
			$bbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<em>', 'end_tag' => '</em>'),
					'inline', array ('listitem', 'block', 'inline', 'link', 'handlung'), array ());
			$bbcode->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'),
					'inline', array ('listitem', 'block', 'inline', 'link', 'handlung'), array ());
			$bbcode->addCode ('size', 'callback_replace', 'do_bbcode_size', array ('callback_replace_param' => 'default'),
					'inline', array ('listitem', 'block', 'inline', 'link', 'handlung'), array ());
			$bbcode->addCode ('color', 'callback_replace', 'do_bbcode_color', array ('callback_replace_param' => 'default'),
					'inline', array ('listitem', 'block', 'inline', 'link', 'handlung'), array ());
			$bbcode->addCode ('url', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'),
					'link', array ('listitem', 'block', 'inline', 'handlung'), array ('link'));
			$bbcode->addCode ('img', 'usecontent', 'do_bbcode_img', array (),
					'image', array ('listitem', 'block', 'inline', 'handlung', 'link'), array ());
			$bbcode->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
					'list', array ('block', 'list', 'listitem'), array ());
			$bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
					'listitem', array ('list'), array ());
			$bbcode->addCode ('ul', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
					'list', array ('block', 'list', 'listitem'), array ());
			$bbcode->addCode ('li', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
					'listitem', array ('list'), array ());
			$bbcode->addCode ('h', 'simple_replace', null, array ('start_tag' => '<div class="action post">', 'end_tag' => '</div>'),
					'handlung', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('handlung', 'simple_replace', null, array ('start_tag' => '<div class="action post">', 'end_tag' => '</div>'),
					'handlung', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('s', 'simple_replace', null, array ('start_tag' => '<div class="simoff post">', 'end_tag' => '</div>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('simoff', 'simple_replace', null, array ('start_tag' => '<div class="simoff post">', 'end_tag' => '</div>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('sl', 'simple_replace', null, array ('start_tag' => '<div class="sl post">', 'end_tag' => '</div>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('q', 'simple_replace', null, array ('start_tag' => '<blockquote>', 'end_tag' => '</blockquote>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('quote', 'simple_replace', null, array ('start_tag' => '<blockquote>', 'end_tag' => '</blockquote>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('code', 'usecontent', 'do_bbcode_code', array ('start_tag' => '<p>', 'end_tag' => '</p>'),
					'code', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('left', 'simple_replace', null, array ('start_tag' => '<p style="text-align:left;">', 'end_tag' => '</p>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('center', 'simple_replace', null, array ('start_tag' => '<p style="text-align:center;">', 'end_tag' => '</p>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('right', 'simple_replace', null, array ('start_tag' => '<p style="text-align:right;">', 'end_tag' => '</p>'),
					'block', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
			$bbcode->setCodeFlag ('*', 'paragraphs', true);
			$bbcode->setCodeFlag ('h', 'paragraphs', true);
			$bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
			$bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('q', 'paragraph_type', BBCODE_PARAGRAPH_ALLOW_INSIDE);
			$bbcode->setCodeFlag ('q', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('q', 'opentag.after.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('q', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('q', 'closetag.after.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('q', 'paragraphs', true);
			$bbcode->setCodeFlag ('quote', 'paragraphs', true);
			$bbcode->setCodeFlag ('quote', 'paragraph_type', BBCODE_PARAGRAPH_ALLOW_INSIDE);
			$bbcode->setCodeFlag ('quote', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('quote', 'opentag.after.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('quote', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setCodeFlag ('quote', 'closetag.after.newline', BBCODE_NEWLINE_DROP);
			$bbcode->setGlobalCaseSensitive(false);
			$bbcode->setRootParagraphHandling (true);


			self::$instance = $bbcode;
		}
		return self::$instance;
	}
}