<?php

return [
	'base_uri' => env('SOLR_BASE_URI', ''),
	'default_collection' => env('SOLR_DEFAULT_COLLECTION', ''),
	'default_params' => json_decode(env('SOLR_DEFAULT_PARAMS', '{"defType":"edismax","sort":"score desc"}'), true),
];