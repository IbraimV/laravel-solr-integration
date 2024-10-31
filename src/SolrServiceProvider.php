<?php

namespace Ibraimv\SolrIntegration;

use Illuminate\Support\ServiceProvider;
use Ibraimv\SolrIntegration\SolrClient;

class SolrServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom(
            __DIR__ . '/config/solr.php',
            'solr'
        );

		$this->app->singleton(SolrClient::class, function ($app) {
            return new SolrClient(null, $app['config']->get('solr'));
        });
	}

	public function boot()
	{
		$this->publishes([
			__DIR__ . '/config/solr.php' => config_path('solr.php'),
		], 'config');
	}
}
