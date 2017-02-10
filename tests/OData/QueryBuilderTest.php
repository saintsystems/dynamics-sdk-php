<?php

namespace SaintSystems\OData\Tests;

use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Core\Log;

use Illuminate\Support\Collection;
use SaintSystems\OData\ODataClient;
use SaintSystems\OData\Query\Builder;

class BuilderTest extends TestCase
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

    public function getBuilder()
    {
        return new Builder(
            $this->client, $this->client->getQueryGrammar(), $this->client->getPostProcessor()
        );
    }

    public function testConstructor()
    {
        $builder = $this->getBuilder();

        $this->assertNotNull($builder);
    }

    public function testEntitySet()
    {
        $builder = $this->getBuilder();

        $entitySet = 'People';

        $builder->entitySet($entitySet);

        $expected = $entitySet;
        $actual = $this->readAttribute($builder, 'entitySet');
        
        $this->assertEquals($expected, $actual);

        $request = $builder->toRequest();
        $this->assertEquals($expected, $request);
    }

    public function testEntitySetGet()
    {
        $builder = $this->getBuilder();

        $entitySet = 'People';

        $people = $builder->entitySet($entitySet)->get();

        //dd($people);
        $this->assertInstanceOf(Collection::class, $people);
        //$this->assertEquals($expected, $request);
    }

    public function testEntityKeyString()
    {
        $builder = $this->getBuilder();

        $entityId = 'russellwhyte';

        $builder->entityKey($entityId);

        $expected = $entityId;
        $actual = $this->readAttribute($builder, 'entityKey');

        $this->assertEquals($expected, $actual);

        $expectedUri = "('$entityId')";
        $actualUri = $builder->toRequest();
        
        $this->assertEquals($expectedUri, $actualUri);
    }

    public function testEntityKeyNumeric()
    {
        $builder = $this->getBuilder();

        $entityId = '1';

        $builder->entityKey($entityId);

        $expected = $entityId;
        $actual = $this->readAttribute($builder, 'entityKey');

        $this->assertEquals($expected, $actual);

        $expectedUri = "($entityId)";
        $actualUri = $builder->toRequest();
        
        $this->assertEquals($expectedUri, $actualUri);
    }

    public function testEntityKeyUuid()
    {
        $builder = $this->getBuilder();

        $entityId = 'c78ae94b-0983-e511-80e5-3863bb35ddb8';

        $builder->entityKey($entityId);

        $expected = $entityId;
        $actual = $this->readAttribute($builder, 'entityKey');

        $this->assertEquals($expected, $actual);

        $expectedUri = "($entityId)";
        $actualUri = $builder->toRequest();
        
        $this->assertEquals($expectedUri, $actualUri);
    }

    public function testEntityKeyUuidNegative()
    {
        $builder = $this->getBuilder();

        $entityId = 'k78ae94b-0983-t511-80e5-3863bb35ddb8';

        $builder->entityKey($entityId);

        $expected = $entityId;
        $actual = $this->readAttribute($builder, 'entityKey');

        $this->assertEquals($expected, $actual);

        $expectedUri = "('$entityId')";
        $actualUri = $builder->toRequest();
        
        $this->assertEquals($expectedUri, $actualUri);
    }

    public function testTake()
    {
        $builder = $this->getBuilder();

        $take = 1;

        $builder->take($take);

        $expected = $take;
        $actual = $this->readAttribute($builder, 'take');

        $this->assertEquals($expected, $actual);
    }

    public function testSkip()
    {
        $builder = $this->getBuilder();

        $skip = 5;

        $builder->skip($skip);

        $expected = $skip;
        $actual = $this->readAttribute($builder, 'skip');

        $this->assertEquals($expected, $actual);
    }

}
