<?php

/**
 * Create a path out of an element and return it 
 * @param array $array
 * @return string
 **/

function createPath($array) {
	return ROOT.DS.implode(DS, $array);
}

/**
 * Check if environment is development and display errors
 **/

function setReporting() {
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	}
	else {
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', createPath(array('tmp', 'logs', 'error.log')));
	}
}

/**
 * Check for Magic Quotes and remove them
 * @param unknown_type $value
 * @return Ambigous <multitype:, string>
 **/

function stripSlashesDeep($value) {
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}

function removeMagicQuotes() {
	if (get_magic_quotes_gpc()) {
		$_GET = stripSlashesDeep($_GET);
		$_POST = stripSlashesDeep($_POST);
		$_COOKIE = stripSlashesDeep($_COOKIE);
	}
}

/** Check register globals and remove them **/

function unregisterGlobals() {
	if (ini_get('register_globals')) {
		$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		foreach ($array as $value) {
			foreach ($GLOBALS[$value] as $key => $var) {
				if ($var === $GLOBALS[$key]) 
					unset($GLOBALS[$key]);
			}
		}
	}
}

/** Returns object/controller/model Name **/

function getObjectNameFromModel($value) {
	return substr(strtolower($value), 0, -5);
}

function getObjectNameFromController($value) {
	return substr(strtolower($value), 0, -10);
}

function getControllerNameFromObject($value) {
	return ucwords($value).'Controller';
}

function getModelNameFromObject($value) {
	return ucwords($value).'Model';
}

function utf8ify($str){
  $strlen = strlen($str);
  for($i=0; $i<$strlen; $i++){
    $ord = ord($str[$i]);
    if($ord < 0x80) continue; // 0bbbbbbb
    elseif(($ord&0xE0)===0xC0 && $ord>0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
    elseif(($ord&0xF0)===0xE0) $n = 2; // 1110bbbb
    elseif(($ord&0xF8)===0xF0 && $ord<0xF5) $n = 3; // 11110bbb (exkl F5-FF)
    else return utf8_encode($str); // ungültiges UTF-8-Zeichen
    for($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
      if(++$i===$strlen || (ord($str[$i])&0xC0)!==0x80)
        return utf8_encode($str); // ungültiges UTF-8-Zeichen
  }
  return $str; // kein ungültiges UTF-8-Zeichen gefunden
}

/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function validEmail($email) {
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex) return false;
	else {
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		// local part length exceeded
		if ($localLen < 1 || $localLen > 64) return false;
		// domain part length exceeded
		elseif ($domainLen < 1 || $domainLen > 255) return false;
		// local part starts or ends with '.'
		elseif ($local[0] == '.' || $local[$localLen-1] == '.') return false;
		// local part has two consecutive dots
		elseif (preg_match('/\\.\\./', $local)) return false;
		// character not valid in domain part
		elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) return false;
		// domain part has two consecutive dots
		elseif (preg_match('/\\.\\./', $domain)) return false;
		elseif (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
			// character not valid in local part unless local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) return false;
		}
		// domain not found in DNS
		if($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) return false;
	}
	return $isValid;
}

/** Main Call Function **/

function callHook() {
	global $url;

	$urlArray = array();
	$urlArray = explode("/", $url);

	$object = $urlArray[0];
	array_shift($urlArray);
	$action = (isset($urlArray[0]) && $urlArray[0] != '') ? $urlArray[0] : 'std';
	array_shift($urlArray);
	$queryString = $urlArray;

	$controller = getControllerNameFromObject($object);
	$dispatch = new $controller(getModelNameFromObject($object), $object, $action);

	if ((int)method_exists($controller, $action)) 
		call_user_func_array(array($dispatch, $action), $queryString);
	else {
		/* TODO: Error Generation Code Here */
	}
}

/** Autoload any classes that are required **/

function autoload($className) {
	if (file_exists(createPath(array('library', 'class', strtolower($className).'.php'))))
		require_once createPath(array('library', 'class', strtolower($className).'.php'));
	elseif (file_exists(createPath(array('application', 'controller', getObjectNameFromController($className).'.php'))))
		require_once createPath(array('application', 'controller', getObjectNameFromController($className).'.php'));
	elseif (file_exists(createPath(array('application', 'model', getObjectNameFromModel($className).'.php'))))
		require_once createPath(array('application', 'model', getObjectNameFromModel($className).'.php'));
	else {
		/* TODO: Error Generation Code Here */
	}
}

spl_autoload_register("autoload");

setReporting();
removeMagicQuotes();
unregisterGlobals();
callHook();