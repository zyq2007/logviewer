<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
$private['filelist'] = array(
	'/var/log/mongo.log',
  '/var/log/php.log',
	'/Users/roman/workspace/*/*.log',
	'/Users/roman/workspace/*/*/bluescreen/*.log',
	'/Users/roman/workspace/*/*/bluescreen/*.html',
	'/Users/roman/workspace/*/testbuilder/reports/selenium-screens/*.png',
	'/Users/roman/workspace/*/testbuilder/reports/selenium-screens/*.html',
);

// or use our example

$private['filelist'] = array(
	__DIR__ . '/../example/*.log',
	__DIR__ . '/../example/*.jpg',
	__DIR__ . '/../example/*.png',
	__DIR__ . '/../example/*.html',
);



