<?php

namespace Ibraimv\SolrIntegration;

use Illuminate\Support\ServiceProvider;

class SolrServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom(
            __DIR__ . '/config/solr.php',
            'solr'
        );
	}

	public function boot()
	{
		$this->publishes([
			__DIR__ . '/config/solr.php' => config_path('solr.php'),
		], 'config');
	}
}
