<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		'../../extensions/CirrusSearch',
		'../../extensions/Scribunto',
		'../../extensions/Wikibase',
		'../../extensions/WikibaseLexeme',
		'../../extensions/WikibaseCirrusSearch',
	]
);

// Exclude Wikibase stubs for Cirrus
$cfg['exclude_file_list'] = array_merge(
	$cfg['exclude_file_list'],
	[
		'../../extensions/Wikibase/repo/tests/phpunit/includes/Search/Elastic/DispatchingQueryBuilderTest.php',
	]
);

$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[
		'../../extensions/CirrusSearch',
		'../../extensions/Scribunto',
		'../../extensions/Wikibase',
		'../../extensions/WikibaseLexeme',
		'../../extensions/WikibaseCirrusSearch',
	]
);

return $cfg;
