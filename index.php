<?php
namespace logviewer;

use Slim\Slim;

header('Cache-Control: no-cache, must-revalidate');
require 'vendor/autoload.php';


$app = new Slim(
	array(
		'view' => new View()
	)
);

$config = new Config(__DIR__ . '/conf/config.php');

$conditions = array(
	'lines' => 'all|[0-9]+',
	'read' => 'tail|head',
	'type' => 'single|multi',
	'direction' => 'reverse|normal',
);


// homepage
$app->get(
	'/(:lines)(/:read)(/:type)(/:direction)',
	function ($lines = 150, $read = null, $type = null, $direction = null) use ($app, $config) {
		$logs = Utils::glob($config->_filelist);

		$app->view->params = array(
			'format' => null,
			'lines' => $lines,
			'read' => $read,
			'type' => $type,
			'direction' => $direction
		);

		// logs
		$app->view->logs = Utils::prepareLogs($logs);
		$app->view->config = $config;
		$app->view->display('list.phtml');
	}
)->conditions($conditions);


// read log
$conditions['format'] = 'view|raw';
$app->get(
	'/:format(/:lines)(/:read)(/:type)(/:direction)',
	function ($format, $lines = 150, $read = null, $type = null, $direction = null) use ($app, $config) {

		// configure reader
		$logReader = new LogReader($config);
		$logReader->log = $log = array_key_exists('log', $_GET) ? htmlspecialchars($_GET['log']) : null;
		$logReader->logType = $logType = pathinfo($log, PATHINFO_EXTENSION);
		$logReader->multi = $type === 'multi';
		$logReader->lines = intval($lines);
		$logReader->tail = $read === 'tail' || $read === null;
		$logReader->reverse = $direction === 'reverse';
		$content = $logReader->display();

		if ($format === 'raw') {
			// download rew
			$mime = array(
				'html' => 'text/html',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
			);

			header('Content-Type: ' . array_key_exists($logType, $mime) ? $mime[$logType] : 'text/plain');
			header('Content-Type: application/force-download');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="' . basename($log) . '"');
			die(is_array($content) ? implode(PHP_EOL, $content) : $content);

		} else {
			$app->view->log = $log;
			$app->view->logProcessor = new LogProcessor($config);
			$app->view->output = $content;
			$app->view->logType = $logType;
			$app->view->config = $config;
			$app->view->url = '';
			$app->view->params = array(
				'format' => $format,
				'lines' => $lines,
				'read' => $read,
				'type' => $type,
				'direction' => $direction
			);

			//var_dump($app->view->all());
			$app->view->display('show.phtml');
		}
	}
)->conditions($conditions);

$app->run();