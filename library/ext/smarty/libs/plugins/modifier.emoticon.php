<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty spacify modifier plugin
 * 
 * Type:	modifier<br>
 * Name:	emoticon<br>
 * Purpose:	insert emoticons into string
 * 
 * @author Fabian
 * @param string $string		input string
 * @param array $emoticons		Emoticons to be replaced
 * @return string
 */
function smarty_modifier_emoticon($string, $emoticons) {
	global $config;
	foreach ($emoticons as $emoticon) {
		$string = str_replace($emoticon->text, '<img src="'.$config->url.'/img/emoticon/'.$emoticon->path.'" title="'.$emoticon->name.'">', $string);
	}
	return $string;
} 

?>