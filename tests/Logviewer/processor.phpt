<?php
use logviewer\Config;
use logviewer\LogProcessor;

require_once '../bootstrap.php';

$config = new Config('../../conf/config.php');
$processor = new \logviewer\LogProcessor($config);

$logs = array(
	'<span class="error">[ERROR]</span>' => '[ERROR]',
	'<span class="warning">[WARNING]</span>' => '[WARNING]',
	'<span class="ok">[OK]</span>' => '[OK]',
	'<span class="warning">Operation not permitted</span>' => 'Operation not permitted',
	'<span class="warning">File does not exist: </span>' => 'File does not exist: ',


	'<span class="info">PHP Notice: </span>' => 'PHP Notice: ',
	'<span class="faild">Exception: aaa.html</span>' => 'Exception: aaa.html',
	'<span class="faild">PHP Parse error: </span>' => 'PHP Parse error: ',
);

foreach ($logs as $expected => $log) {
	\Tester\Assert::same($expected, $processor->highlight($log));
}