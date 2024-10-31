<?php

namespace Ibraimv\SolrIntegration\Facades;

use Illuminate\Support\Facades\Facade;

class Solr extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \IbraimV\SolrIntegration\SolrClient::class;
	}
}
