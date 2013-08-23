<?php
/** $conf */
namespace logviewer;

use Slim\Slim;

header('Cache-Control: no-cache, must-revalidate');
require 'vendor/autoload.php';

$app = new Slim(array('view' => new View()));

$conditions = array(
	'lines' => 'all|[0-9]+',
	'read' => 'tail|head',
	'type' => 'single|multi',
	'direction' => 'reverse|normal',
);

// homepage
$app->get(
	'/(:lines)(/:read)(/:type)(/:direction)',
	function ($lines = 150, $read = 'tail', $type = 'single', $direction = 'reverse') use ($app) {
		$logs = Utils::glob(Config::filelist());

		$app->view->params = array(
			'format' => null,
			'lines' => intval($lines),
			'read' => $read,
			'type' => $type,
			'direction' => $direction
		);

		// logs
		$app->view->logs = Utils::prepareLogs($logs);
		$app->view->display('list.phtml');
	}
)->conditions($conditions);


// read log
$conditions['format'] = 'view|raw';
$app->get(
	'/:format(/:lines)(/:read)(/:type)(/:direction)',
	function ($format, $lines = 150, $read = 'tail', $type = 'single', $direction = 'reverse') use ($app) {
		// configure reader
		$logReader = new LogReader();
		$logReader->log = $log = array_key_exists('log', $_GET) ? htmlspecialchars($_GET['log']) : null;
		$logReader->mime = $extension = pathinfo($log, PATHINFO_EXTENSION);
		$logReader->lines = intval($lines);
		$logReader->read = $read;
		$logReader->type = $type;
		$logReader->direction = $direction;
		$content = $logReader->display();

		if ($format === 'raw') {
			// download rew
			$mime = array(
				'html' => 'text/html',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
			);

			header('Content-Type: ' . (array_key_exists($extension, $mime) ? $mime[$extension] : 'text/plain'));
			header('Content-Type: application/force-download');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="' . basename($log) . '"');
			die(is_array($content) ? implode(PHP_EOL, $content) : $content);

		} else {
			$app->view->log = $log;
			$app->view->logProcessor = new LogProcessor();
			$app->view->output = $content;
			$app->view->logType = $extension;
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