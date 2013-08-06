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
	dirname(__DIR__) . '/app/log/*.log',
	dirname(__DIR__) . '/app/log/*.jpg',
	dirname(__DIR__) . '/app/log/*.png',
	dirname(__DIR__) . '/app/log/*.html',
);

