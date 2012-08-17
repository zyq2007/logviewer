<?php
namespace logviewer;
header('Cache-Control: no-cache, must-revalidate');

// include a autoload
$basepath = realpath(__DIR__);
include_once 'Autoloader.php';
spl_autoload_register('\AutoLoader::load', true, false);

// nacist config a logviewer
$config = new Config($basepath . '/conf/config.php');
$logviewer = new Logviewer($config);

// co se bude dit
if (!empty($_GET['action'])) {

	switch ($_GET['action']) {
		case 'resolved':
			$logviewer->resolved();
			break;
		case 'show':
			$logviewer->show();
			break;

		default:
			$logviewer->getList();
			break;
	}
} else {
	$logviewer->getList();
}
