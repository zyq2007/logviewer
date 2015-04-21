<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */

// or use our example

return [

	'dir' => '/', # logviewer install dir

	'isMulti' => '/.*\d+\..*\.wikidi\.\w+/',

	'highlights' => ['log'],

	'filelist' => [

		dirname(__DIR__) . '/app/log/*.log',
		dirname(__DIR__) . '/app/log/*.jpg',
		dirname(__DIR__) . '/app/log/*.png',
		dirname(__DIR__) . '/app/log/*.html',

		//dirname(__DIR__) . '/../**/*.*',
	]
];