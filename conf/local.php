<?php

return array(

	'isMulti' => '/.*\d+\..*\.wikidi\.\w+/',

	'highlights' => array(
		'admin', 'wikidi', 'default-wikidi', 'develcz', 'kinohled', 'zdrojak',
		'flow', 'magictable', 'topicleaders', 'testomato', 'devops', 'blogs',
		'www', 'deploy', 'php', 'cron', 'nginx', 'mysql', 'mongo', 'sphinx', 'rserver'
	),

	'filelist' => array(
		// normal
		'/var/www/*/logs/*/*.log',
		'/var/www/*/logs/*/*.html',

		// novejsi projekty to copou tady
		'/var/www/*/htdocs/log/*.log',
		'/var/www/*/htdocs/log/*.html',
		'/var/www/*/htdocs/log/*/*.log',
		'/var/www/*/htdocs/log/*/*.html',
		'/var/www/*/mass/*/log/*/*.html', // hash
		'/var/www/*/mass/*/log/*/*.log',

		// nektere starsi projekty to cpou sem
		'/var/www/*/htdocs/logs/*.log',
		'/var/www/*/htdocs/logs/*.html',
		'/var/www/*/htdocs/logs/*/*.log',
		'/var/www/*/htdocs/logs/*/*.html',
		'/var/www/*/mass/*/logs/*/*.html',
		'/var/www/*/mass/*/logs/*/*.log',

		// a sem par exotu
		'/var/www/*/htdocs/tmp/*.log',
		'/var/www/*/htdocs/tmp/*.html',

		// ostatni
		'/var/log/rserver/*.log',
		'/var/log/sphinxsearch/*.log',
		'/var/log/mongo.log',
		'/var/log/php.log'
	)
);

