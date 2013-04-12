<?php
$private = array(

	'version' => 'v1.4.3',

	// zacatky cest k logum - je to kvuli odstraneni zacatku cesty pro setrideni do skupin, jinak spadne do vychozi
	'stripPaths' => array('/var/log/'),

	// muzu provadet multi?
	'isMulti' => '/^https?:\/\/\d+/',

	// masky adresaru s logy
	'filelist' => array(
		'/var/log/*/*.log'
	),

	// regexps for replaces in log content
	'replace' => array(
		// ANSI escape chars
		'/\x1b(\[|\(|\))[;?0-9]*[0-9A-Za-z]/' => "",
		'/\x1b(\[|\(|\))[;?0-9]*[0-9A-Za-z]/' => "",
		'/[\x03|\x1a]/' => "",

		// BUILD
		'/(BUILD FAILED)/i' => '<span class="faild">$1</span>',
		'/(BUILD SUCCESSFUL)/i' => '<span class="success">$1</span>',

		// PHP
		'/(.*PHP Parse error:[^\n]*)/' => '<span class="faild">$1</span>',
		'/(.*PHP Fatal error:[^\n]*)/' => '<span class="faild">$1</span>',
		'/(.*PHP Warning:[^\n]*)/' => '<span class="warning">$1</span>',
		'/(.*PHP Notice:[^\n]*)/' => '<span class="info">$1</span>',
		'/(.*Exception:.*.html[^\n]*)/' => '<span class="faild">$1</span>',

		// permissions
		'/(Permission denied)/i' => '<span class="faild">$1</span>',

		// git
		'/(^fatal:[^\n]+)/im' => '<span class="faild">$1</span>', // git fatal
		'/((^[\s]?error:|.*-[\s]+error:)[^\n]+)/im' => '<span class="error">$1</span>', // git errro

		// common errors
		'/(FAILURES!)/i' => '<span class="error">$1</span>',
		'/(Failed command:[^\n]+)/i' => '<span class="error">$1</span>',
		'/(Catchable fatal error: [^\n]+)/i' => '<span class="error">$1</span>',
		'/: (ERROR - Parse error[^\n]+)/i' => ': <span class="error">$1</span>',

		// warnings
		'/(File does not exist:[^\n]+)/i' => '<span class="warning">$1</span>',
		'/(Operation not permitted)/i' => '<span class="warning">$1</span>',
		'/([1-9]+ warning\(s\))/i' => '<span class="warning">$1</span>',
		'/(Warnings?: [^\n]?)/i' => '<span class="warning">$1</span>',

		// build
		'/(HEAD is now at )([[:alnum:]]+)( [^\n]+)/i' => '$1<span class="info">$2</span><em>$3</em>',
		'/(\[info\] [^\n]+)/i' => '<span class="info">$1</span>',
		'/(\[init\] [^\n]+)/i' => '<span class="info">$1</span>',
		'/(\[OK\])/i' => '<span class="ok">$1</span>',
		'/(\[FAILED?\])/' => '<span class="error">$1</span>',
		'/(\[ERRORS?\])/' => '<span class="error">$1</span>',
		'/(\[WARNINGS?\])/' => '<span class="warning">$1</span>',

		// selenium, JS Hint, CS
		'/(Tests: [1-9]+, Assertions: [1-9]+, Errors: [1-9].)/i' => '<span class="error">$1</span>',
		'/(OK, but incomplete or skipped tests!)/i' => '<span class="ok">$1</span>',
		'/([1-9]+ error\(s\))/i' => '<span class="error">$1</span>',
		'/(E [^\n]+\.html)/' => '<span class="error">$1</span>',
		'/(\. [^\n]+\.html)/i' => '<span class="ok">$1</span>',
		'/(\[JSHint\] OK)/i' => '<span class="ok">$1</span>',
		'/(\[JSHint\] [1-9]+ ERRORS!)/i' => '<span class="error">$1</span>',
		'/(\[PHP CodeSniffer\] OK)/i' => '<span class="ok">$1</span>',
		'/(\[PHP CodeSniffer\] [1-9]+ ERROR!)/i' => '<span class="error">$1</span>',

		// success
		'/(Warnings?: 0)/' => '<span class="ok">$1</span>',
		'/(0 warning\(s\))/' => '<span class="ok">$1</span>',
		'/(0 error\(s\))/' => '<span class="ok">$1</span>',
		'/(OK \([0-9]+ tests, [0-9]+ assertions\))/i' => '<span class="ok">$1</span>',

		// tracy
		'/(.*Fatal error:[^\n]*)/' => '<span class="faild">$1</span>',
		'/(\S+\/exception-[\-0-9]*-[[:alnum:]]*\.html)/' => '<a href="{url}&action=show&log=$1">$1</a>',
		'/(exception-[\-0-9]*-[[:alnum:]]*\.html)/' => '<a href="{url}&action=show&log={dirname}/$1">$1</a>',

		'/([^\n]+------+\n)/' => '<hr>',
		'/([^\n]+======+\n)/' => '</pre><pre>',
		'/https?:\/\/[^<>[:space:]]+[[:alnum:]|\/]/i' => '<a href="$0" target="_blank">$0</a>',
	)
);

// ---------------------------------------------------------------------------------------------------------------------

$public = array(
	'lines' => 150, // kolik radku
	'reverse' => true, // obracene poradi radku
	'tail' => true, // cist od konce
	'multi' => false, // logy ze vsech stroju (napr:wikidi-admin.1.web.srv.wikidi.net:8080) - meni se cislo[1-254]
	'merge' => false, // obracene poradi radku
	'showmachine' => false,
	'header' => true, // mam zobrazit hlavicku view ?
	'pin' => false, // pripnout hlavicku
	'raw' => false, // ciste textovy vysput
);

if (file_exists(__DIR__ . '/local.php')) require_once 'local.php'; // prepisuje cokoliv

return array('private' => $private, 'public' => $public);
