<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Ibraimv\SolrIntegration\SolrClient;
use Mockery;
use PHPUnit\Framework\TestCase;

class SolrClientTest extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	private function getClientWithMock(Client $mockClient = null)
	{
		$config = [
			'base_uri' => 'http://localhost:8983/solr/',
			'default_collection' => 'core_name',
			'default_params' => ['defType' => 'edismax', 'sort' => 'score desc']
		];

		return new SolrClient($mockClient, $config);
	}

	public function testAddDocumentSuccessfully()
	{
		$mockClient = Mockery::mock(Client::class);
		$mockClient->shouldReceive('post')
			->once()
			->andReturn(new Response(200, [], json_encode(['response' => 'success'])));

		$solrClient = $this->getClientWithMock($mockClient);
		$response = $solrClient->addDocument(['id' => '1', 'title' => 'Test Document']);

		$this->assertArrayHasKey('response', $response);
		$this->assertEquals('success', $response['response']);
	}

	public function testBulkAddDocumentsSuccessfully()
	{
		$mockClient = Mockery::mock(Client::class);
		$mockClient->shouldReceive('post')
			->once()
			->andReturn(new Response(200, [], json_encode(['response' => 'bulk success'])));

		$solrClient = $this->getClientWithMock($mockClient);
		$documents = [
			['id' => '1', 'title' => 'Document 1'],
			['id' => '2', 'title' => 'Document 2']
		];
		$response = $solrClient->bulkAddDocuments($documents);

		$this->assertArrayHasKey('response', $response);
		$this->assertEquals('bulk success', $response['response']);
	}

	public function testSearchSuccessfully()
	{
		$mockClient = Mockery::mock(Client::class);
		$mockClient->shouldReceive('get')
			->once()
			->andReturn(new Response(200, [], json_encode([
				'response' => ['numFound' => 1, 'docs' => [['id' => '1', 'title' => 'Test Document']]]
			])));

		$solrClient = $this->getClientWithMock($mockClient);
		$results = $solrClient->search('title:Test');

		$this->assertEquals(1, $results['response']['numFound']);
		$this->assertEquals('Test Document', $results['response']['docs'][0]['title']);
	}

	public function testDeleteSingleItemSuccessfully()
	{
		$mockClient = Mockery::mock(Client::class);
		$mockClient->shouldReceive('post')
			->once()
			->andReturn(new Response(200, [], json_encode(['response' => 'deleted'])));

		$solrClient = $this->getClientWithMock($mockClient);
		$response = $solrClient->deleteSingleDocument('1', 'product_id');

		$this->assertArrayHasKey('response', $response);
		$this->assertEquals('deleted', $response['response']);
	}

	public function testBuildDeleteItemsByQuery()
	{
		$mockClient = Mockery::mock(Client::class);
		$mockClient->shouldReceive('post')
			->once()
			->andReturn(new Response(200, [], json_encode(['response' => 'query deleted'])));

		$solrClient = $this->getClientWithMock($mockClient);
		$response = $solrClient->bulkDeleteDocuments(['q' => 'category:Books']);

		$this->assertArrayHasKey('response', $response);
		$this->assertEquals('query deleted', $response['response']);
	}

	public function testBuildDeleteItemsByFieldMapping()
	{
		$mockClient = Mockery::mock(Client::class);
		$mockClient->shouldReceive('post')
			->once()
			->andReturn(new Response(200, [], json_encode(['response' => 'field deleted'])));

		$solrClient = $this->getClientWithMock($mockClient);
		$response = $solrClient->bulkDeleteDocuments([
			'field' => 'product_id',
			'values' => ['1', '2']
		]);

		$this->assertArrayHasKey('response', $response);
		$this->assertEquals('field deleted', $response['response']);
	}

	public function testSearchThrowsException()
	{
		$mockClient = Mockery::mock(Client::class);
		$mockClient->shouldReceive('get')
			->once()
			->andThrow(new RequestException('Error Communicating with Solr', new Request('GET', '/select')));

		$solrClient = $this->getClientWithMock($mockClient);

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('GET request failed in Solr');

		$solrClient->search('title:Test');
	}
}
