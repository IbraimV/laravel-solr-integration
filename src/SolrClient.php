<?php

namespace Ibraimv\SolrIntegration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SolrClient
{
	protected $client;
	protected $baseUri;
	protected $validParams = [
		'q', 'fq', 'sort', 'rows', 'start', 'fl',
		'defType', 'q.op', 'debugQuery', 'wt', 'facet',
		'spellcheck', 'dismax', 'boost', 'group',
		'hl', 'spatial', 'stats', 'terms', 'timeAllowed',
		'cursorMark'
	];

	protected $defaultCollection;

	protected $defaultParams;

	public function __construct(Client $client = null, array $config = [])
	{

		$this->baseUri = $config['base_uri']
			?? config('solr.base_uri');

		$this->defaultCollection = $config['default_collection']
			?? config('solr.default_collection');

		$this->defaultParams = $config['default_params']
			?? config('solr.default_params');

		$this->client = $client ?? new Client(['base_uri' => $this->baseUri]);
	}

	public function addDocument(array $document): array
	{
		return $this->makePostRequest('/update', [$document], ['commit' => 'true']);
	}

	public function bulkAddDocuments(array $documents): array
	{
		return $this->makePostRequest('/update', $documents, ['commit' => 'true']);
	}

	public function bulkDeleteDocuments(array $criteria): array
	{
		$deletePayload = [];

		if (isset($criteria['q'])) {
			$deletePayload['delete'] = ['query' => $criteria['q']];
		}

		elseif (isset($criteria['field']) && isset($criteria['values'])) {
			$field = $criteria['field'];
			$ids = $criteria['values'];

			$deletePayload['delete'] = array_map(fn($id) => [$field => $id], $ids);
		} else {
			throw new \InvalidArgumentException("Invalid delete criteria provided.");
		}

		return $this->makePostRequest('/update', $deletePayload, ['commit' => 'true']);
	}

	public function deleteSingleDocument(string $value, string $field = 'id'): array
	{
		$deletePayload = ['delete' => [$field => $value]];
		return $this->makePostRequest('/update', $deletePayload, ['commit' => 'true']);
	}

	public function search(string $query, array $options = []): array
	{
		$this->validateParams($options);
		$defaultParams = ['q' => $query, 'wt' => 'json'];
		$defaultParams = array_merge($this->defaultParams, $defaultParams);
		$params = array_merge($defaultParams, $options);

		return $this->makeGetRequest('/select', $params);
	}

	private function validateParams(array $params): void
	{
		foreach (array_keys($params) as $key) {
			if (!in_array($key, $this->validParams, true)) {
				throw new \InvalidArgumentException("Invalid Solr parameter: {$key}");
			}
		}
	}

	private function makeGetRequest(string $path, array $params): array
	{
		try {

			$response = $this->client->get($this->defaultCollection . $path, ['query' => $params]);
			return json_decode($response->getBody()->getContents(), true);
		} catch (RequestException $e) {
			$this->logError('GET request', $e);
			throw new \Exception("GET request failed in Solr: " . $e);
		}
	}

	private function makePostRequest(string $path, array $data, array $queryParams = []): array
	{
		try {
			$response = $this->client->post($this->defaultCollection . $path, [
				'json' => $data,
				'query' => $queryParams,
			]);

			return json_decode($response->getBody()->getContents(), true);
		} catch (RequestException $e) {
			$this->logError('POST request', $e);
			throw new \Exception("POST request failed in Solr: " . $e);
		}
	}

	private function logError(string $action, RequestException $e)
	{
		$errorMessage = $e->hasResponse()
			? $e->getResponse()->getBody()->getContents()
			: $e->getMessage();
		error_log("Solr {$action} error: {$errorMessage}");
	}
}
