# Solr Integration for Laravel

This package provides an easy-to-use Solr client for Laravel, allowing you to interact with an Apache Solr server. It facilitates document addition, bulk operations, and search queries, making it simple to integrate Solr with Laravel applications.

## Installation

### Step 1: Install the Package

Install via Composer:

```bash
composer require ibraimv/solr-integration
```

### Step 2: Publish Configuration
Publish the Solr configuration file:

```bash
php artisan vendor:publish --provider="Ibraimv\SolrIntegration\SolrServiceProvider" --tag="config"
```

### Step 3: Configure Environment Variables
Publish the Solr configuration file:

```bash
SOLR_BASE_URI="http://localhost:8983/solr"
SOLR_DEFAULT_COLLECTION="my_collection"
SOLR_DEFAULT_PARAMS='{"defType":"edismax","sort":"score desc"}'
```

## Usage

### Basic usage
```bash
use Ibraimv\SolrIntegration\Facades\Solr;

// Add a document
Solr::addDocument([
    'id' => '123',
    'title' => 'Sample Document',
    'description' => 'This is a sample document.',
]);

// Bulk add documents
Solr::bulkAddDocuments([
    ['id' => '124', 'title' => 'Second Document'],
    ['id' => '125', 'title' => 'Third Document'],
]);

// Search documents
$response = Solr::search('title:Sample', [
    'fq' => 'description:document',
    'sort' => 'score desc',
    'rows' => 10,
]);

// Delete a document by ID
Solr::deleteSingleDocument('123');

// Delete a document by a different field
Solr::deleteSingleDocument('unique_value', 'custom_field');


// Bulk delete by query or field criteria
Solr::bulkDeleteDocuments([
    'q' => 'description:document'
]);

// Bulk delete by specific field and array of values
Solr::bulkDeleteDocuments([
    'field' => 'custom_field',
    'values' => ['value1', 'value2', 'value3']
]);
```

### Overriding Default Configuration Values

When creating an instance of SolrClient, you can override the default values for base\_uri, default\_collection, and default\_params. This is useful if you need to use a separate Solr configuration for specific methods without affecting the global configuration.

```dash
use Ibraimv\SolrIntegration\SolrClient;

$customSolrClient = new SolrClient(null, [
    'base_uri' => 'http://custom-solr-server:8983/solr',
    'default_collection' => 'custom_collection',
    'default_params' => ['defType' => 'edismax', 'sort' => 'date desc']
]);

// Now you can use $customSolrClient with the specified configuration.
$customSolrClient->addDocument([
    'id' => '200',
    'title' => 'Custom Document',
    'description' => 'This document uses a custom Solr configuration.',
]);
```

### Methods

*   **addDocument(array $document)**: Add a single document to Solr.
    
*   **bulkAddDocuments(array $documents)**: Add multiple documents at once.
    
*   **deleteSingleDocument(string $value, string $field = 'id')**: Delete a document by a specific field.
    
*   **bulkDeleteDocuments(array $criteria)**: Delete multiple documents based on query criteria or field values.
    
*   **search(string $query, array $options = \[\])**: Perform a search query with customizable options.
    

### Configuration Options

*   **base\_uri**: The base URL of the Solr server.
    
*   **default\_collection**: The default Solr collection to use.
    
*   **default\_params**: Default search parameters.

Exception Handling
------------------

All requests are wrapped in error handling. In case of failure, exceptions will be logged, and the error message can be found in the Laravel error logs.

License
-------

This package is open-source and licensed under the MIT License.
