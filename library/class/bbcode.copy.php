<?php

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
					":))" => "31.gif",
					":)" => "30.gif",
					":]" => "32.gif",
					":D" => "33.gif",
					":rofl:" => "34.gif",
					";)" => "35.gif",
					":(" => "36.gif",
					";o" => "37.gif",
					";(" => "38.gif",
					"X(" => "39.gif",
					"X/" => "40.gif",
					":teufel:" => "41.gif",
					":o" => "54.gif",
					"?(" => "55.gif",
					"8o" => "56.gif",
					"8)" => "57.gif",
					":P" => "58.gif",
					":tongue:" => "59.gif",
					":rolleyes:" => "63.gif",
					":baby:" => "64.gif",
					":hallo:" => "65.gif",
					":genial:" => "66.gif",
					"<3" => "67.gif",
					":>" => "69.gif",
					":3" => "72.gif",
					":adel:" => "42.gif",
					":koenig:" => "43.gif",
					":kaiser:" => "44.gif",
					":veuxin:" => "45.gif",
					":trinken" => "46.gif",
					":party:" => "47.gif",
					":ritter:" => "48.gif",
					":angriff:" => "49.gif",
					":fechter:" => "50.gif",
					":spam:" => "51.gif",
					":dito:" => "52.gif",
					":urlaub:" => "53.gif",
					":richter:" => "60.gif",
					":hero:" => "61.gif",
					":ritter2:" => "62.gif",
					":tinos:" => "68.gif",
					":met:" => "70.gif",
					"=)" => "31.gif",
					":evil:" => "40.gif",
					":birth:" => "47.gif",
					":cool:" => "57.gif",
					":verliebt:" => "67.gif"
				);
				foreach ($emoticons as $code => $path) {
					$text = str_replace(htmlspecialchars($code), '<img src="'.$config->url.'/img/emoticon/'.$path.'" title="'.htmlentities($code).'">', $text);
				}
				return $text;
			}

			$bbcode = new ChrisKonnertz\BBCode\BBCode();
			$bbcode->addTag('handlung', function($tag, &$html, $openingTag) {
				if ($tag->opening) {
					return '<div class="action post">';
				} else {
					return '</div>';
				}
			});
			$bbcode->addTag('h', function($tag, &$html, $openingTag) {
				if ($tag->opening) {
					return '<div class="action post">';
				} else {
					return '</div>';
				}
			});
			self::$instance = $bbcode;
		}
		return self::$instance;
	}
}