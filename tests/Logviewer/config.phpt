<?php
use logviewer\Config;

require_once '../bootstrap.php';

// exception

try {
	$config = new Config('not exists file');
	\Tester\Assert::fail('Expected exception');
} catch (\Exception $e) {
	\Tester\Assert::true(true);
}

$config = new Config('../../conf/config.php');
\Tester\Assert::true(is_array($config->_replace));

