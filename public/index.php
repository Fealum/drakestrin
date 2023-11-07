<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

$url = isset($_GET['url']) ? $_GET['url'] : 'index';

require_once ROOT.DS.'vendor'.DS.'autoload.php';
require_once ROOT.DS.'vendor'.DS.'phayes'.DS.'geophp'.DS.'geoPHP.inc';

require_once ROOT.DS.'library'.DS.'bootstrap.php';