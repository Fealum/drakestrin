<?php

/** Configuration Variables **/

define('DEVELOPMENT_ENVIRONMENT', true);

define('STDSOURCE', 'mysql1');

$config = array('title' => 'Kaiserreich Drachenstein', 'url' => 'http://drakestrin.de', 'niceurl' => 'drakestrin.de', 'email' => 'admin@drakestrin.de', 'datetime' => '%d.%m.%Y, %R', 'date' => '%d.%m.%Y', 'time' => '%R', 'emailvalidfor' => 172800, 'timeout' => 900, 'pageentries' => 20, 'avatarsize' => 100000, 'salt' => '');
$config = (object) $config;

$tables = array(
	0 => 'user',
	1 => 'thread',
	2 => 'company',
	3 => 'board',
	4 => 'group',
	5 => 'encyclopedia'
);
