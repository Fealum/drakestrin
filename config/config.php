<?php

use Dotenv\Dotenv;

/** Configuration Variables **/

$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();

define('DEVELOPMENT_ENVIRONMENT', true);

define('STDSOURCE', 'mysql1');

$config = array(
	'title' => 'Kaiserreich Drachenstein',
	'url' => $_ENV['URL'],
	'niceurl' => $_ENV['NICEURL'],
	'email' => $_ENV['ADMIN_MAIL'],
	'datetime' => '%d.%m.%Y, %R', 
	'date' => '%d.%m.%Y',
	'time' => '%R',
	'timezone' => 'Europe/Berlin',
	'emailvalidfor' => 172800,
	'timeout' => 900,
	'pageentries' => 20,
	'avatarsize' => 100000,
	'salt' => $_ENV['SALT']
);

$config = (object) $config;

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
