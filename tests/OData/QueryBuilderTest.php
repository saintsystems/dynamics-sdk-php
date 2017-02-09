<?php

namespace Microsoft\OData\Tests;

use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Core\Log;

use Microsoft\OData\ODataClient;
use Microsoft\OData\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    protected $log;
    protected $baseUrl;
    protected $client;

    public function setUp()
    {
        $this->log = Log::get_instance();
        $this->baseUrl = 'http://services.odata.org/V4/TripPinService';
        $this->client = new ODataClient($this->baseUrl);
    }

    public function testQueryBuilderConstructor()
    {
        $builder = new QueryBuilder(
            $this->client, $this->client->getQueryGrammar(), $this->client->getPostProcessor()
        );

        $this->assertNotNull($builder);
    }

}
