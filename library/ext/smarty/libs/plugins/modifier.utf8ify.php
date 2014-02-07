<?php
/**
 * Smarty plugin
 * 
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty utf8ify modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     uft8ify<br>
 * Purpose:  utf8ify words in the string
 *
 * {@internal {$string|uft8ify} is the fastest option for MBString enabled systems }}
 *
 * @param string  $string    string to capitalize
 * @return string capitalized string
 * @author Fabian Müller <mail at feals dot de> 
 */
function smarty_modifier_utf8ify($string)
{
  $strlen = strlen($string);
  for($i=0; $i<$strlen; $i++){
    $ord = ord($string[$i]);
    if($ord < 0x80) continue; // 0bbbbbbb
    elseif(($ord&0xE0)===0xC0 && $ord>0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
    elseif(($ord&0xF0)===0xE0) $n = 2; // 1110bbbb
    elseif(($ord&0xF8)===0xF0 && $ord<0xF5) $n = 3; // 11110bbb (exkl F5-FF)
    else return utf8_encode($string); // ungültiges UTF-8-Zeichen
    for($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
      if(++$i===$strlen || (ord($string[$i])&0xC0)!==0x80)
        return utf8_encode($string); // ungültiges UTF-8-Zeichen
  }
  return $string; // kein ungültiges UTF-8-Zeichen gefunden
}