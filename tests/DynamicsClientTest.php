<?php

namespace Microsoft\Dynamics\Tests;

use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Core\Log;

use Microsoft\Dynamics\DynamicsClient;
use Microsoft\Dynamics\Http\BaseRequest;
use Microsoft\Dynamics\Tests\DynamicsTestCase;

class DynamicsClientTest extends DynamicsTestCase
{
    public function testDynamicsClientConstructor()
    {
        $dynamicsClient = new DynamicsClient('https://contoso.crm.dynamics.com/', function($requestMessage) {
            $accessToken = 'abc';
            $requestMessage->headers['Authorization'] = 'Bearer ' . $accessToken;
        });
        $this->assertNotNull($dynamicsClient);
        $baseUrl = $this->readAttribute($dynamicsClient, 'baseUrl');
        $this->assertEquals('https://contoso.crm.dynamics.com', $baseUrl);
    }

    public function testBaseRequest()
    {
        $dynamicsClient = new DynamicsClient('https://contoso.crm.dynamics.com/', function($requestMessage) {
            $accessToken = 'abc';
            $requestMessage->headers['Authorization'] = 'Bearer ' . $accessToken;
        });
        $baseRequest = new BaseRequest('https://contoso.crm.dynamics.com/api/data/v8.2/', $dynamicsClient);
    }

    public function testLeadRequest()
    {
         $dynamicsClient = new DynamicsClient($this->instanceUrl.'/api/data/v8.2', function($requestMessage) {
            $requestMessage->headers['Authorization'] = 'Bearer ' . $this->accessToken;
            // $this->log->info($this->accessToken);
        });

        $id = 'c78ae94b-0983-e511-80e5-3863bb35ddb8';
        $lead = $dynamicsClient->Lead[$id]->request()->get();

        // $leads = $dynamicsClient->leads->find($id);
        // $leads = $dynamicsClient->Lead
        //                             ->select($id)
        //                             ->where()
        //                             ->expand()
        //                             ->get();
        // die(print_r($leads, true));
        $this->log->info(print_r($lead, true));
        //$lead = $dynamicsClient->leads($id)->request()->get();
        //die(print_r($lead, true));
        //$this->assertEquals(true, $exists);
    }
}
