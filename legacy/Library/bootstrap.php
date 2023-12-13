<?php

/** Configuration Variables **/

define('DEVELOPMENT_ENVIRONMENT', true);

/**
 * Create a path out of an element and return it 
 * @param array $array
 * @return string
 **/

function createPath($array)
{
    return ROOT . DS . implode(DS, $array);
}

/** Check register globals and remove them **/

function unregisterGlobals()
{
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

function getModelNameFromObject($value)
{
    return ucwords($value) . 'Model';
}

function utf8ify($str)
{
    $strlen = strlen($str);
    for ($i = 0; $i < $strlen; $i++) {
        $ord = ord($str[$i]);
        if ($ord < 0x80) continue; // 0bbbbbbb
        elseif (($ord & 0xE0) === 0xC0 && $ord > 0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
        elseif (($ord & 0xF0) === 0xE0) $n = 2; // 1110bbbb
        elseif (($ord & 0xF8) === 0xF0 && $ord < 0xF5) $n = 3; // 11110bbb (exkl F5-FF)
        else return mb_convert_encoding($str, 'utf8'); // ung�ltiges UTF-8-Zeichen
        for ($c = 0; $c < $n; $c++) // $n Folgebytes? // 10bbbbbb
            if (++$i === $strlen || (ord($str[$i]) & 0xC0) !== 0x80)
                return mb_convert_encoding($str, 'utf8'); // ungültiges UTF-8-Zeichen
    }
    return $str; // kein ungültiges UTF-8-Zeichen gefunden
}

/** Main Call Function **/

function callHook($object, $action, $queryString)
{
    $config = (object) array(
        'title' => 'Kaiserreich Drachenstein',
        'url' => $_ENV['APP_URL'],
        'niceurl' => $_ENV['NICEURL'],
        'email' => $_ENV['APP_ADMIN_MAIL'],
        'datetime' => '%d.%m.%Y, %R',
        'date' => '%d.%m.%Y',
        'time' => '%R',
        'timezone' => 'Europe/Berlin',
        'timeout' => 900,
        'pageentries' => 20,
        'avatarsize' => 500000,
        'salt' => $_ENV['SALT']
    );

    $tables = array(
        0 => 'user',
        1 => 'thread',
        2 => 'company',
        3 => 'board',
        4 => 'group',
        5 => 'encyclopedia',
        6 => 'character',
        8 => 'company_worker'
    );

    $controller = '\Legacy\App\Controller\\' . ucwords($object) . 'Controller';

    if (method_exists($controller, $action)) {
        $dispatch = new $controller(getModelNameFromObject($object), $object, $action, $config, $tables);

        call_user_func_array(array($dispatch, $action), $queryString);
    } else {
        /* TODO: Error Generation Code Here */
        echo "Error 404: Page not found :( Controller: " . $controller . " Action: " . $action . class_exists('\Legacy\Library\Class\_list');
        exit;
    }
}

unregisterGlobals();
callHook($object, $action, $queryString);
