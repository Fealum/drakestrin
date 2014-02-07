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
 * Name:	bbcode<br>
 * Purpose:	convert bbcode to html
 * 
 * @author Fabian
 * @param string $string		input string
 * @param array $codes			BBcodes to be replaced
 * @return string
 */
function smarty_modifier_bbcode($string, $codes) {
	foreach ($codes as $bbcode) {
		$string = preg_replace('/'.$bbcode->pregsearch.'/is', $bbcode->pregreplace, $string);
	}
	return $string;
} 

?>