<?php

namespace Ibraimv\SolrIntegration\Facades;

use Illuminate\Support\Facades\Facade;

class Solr extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \Ibraimv\SolrIntegration\SolrClient::class;
	}
}
