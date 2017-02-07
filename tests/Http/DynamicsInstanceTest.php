<?php

use Microsoft\Dynamics\Dynamics;
use Microsoft\Dynamics\Model;
use Microsoft\Dynamics\Tests\DynamicsTestCase;
use Microsoft\Dynamics\Core\DynamicsConstants;
use Microsoft\Dynamics\Http\DynamicsRequest;

class DynamicsInstanceTest extends DynamicsTestCase
{
    public function setUp()
    {
        parent::setUp();
        if ( ! $this->canRun()) {
            $this->markTestSkipped(
              'Valid testConfig.json values must be present to test against a real Dynamics instance.'
            );
        }
    }

    public function testDynamicsLeadCollectionRequest()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl($this->instanceUrl)
                 ->setAccessToken($this->accessToken);

        $leads = $dynamics->createCollectionRequest('GET', '/leads')
                      ->setReturnType(Model\Lead::class)
                      ->execute();

        $this->assertTrue(is_array($leads));

        if (count($leads) > 0) {
            $lead = $leads[0];
            // $this->log->info(sprintf('%s %s (%s)', $lead->getFirstName(), $lead->getLastName(), $lead->id);
            $this->assertInstanceOf(Microsoft\Dynamics\Model\Lead::class, $lead);
        }
        
    }

    public function testDynamicsLeadRequest()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl($this->instanceUrl)
                 ->setAccessToken($this->accessToken);

        $leadId = 'c78ae94b-0983-e511-80e5-3863bb35ddb8';

        $lead = $dynamics->createRequest('GET', "/leads($leadId)")
                      ->setReturnType(Model\Lead::class)
                      ->execute();

        $this->assertEquals($leadId, $lead->id);

    }

    public function testDynamicsLeadCollectionRequestFilterByEmail()
    {
        $dynamics = new Dynamics();
        $dynamics->setInstanceUrl($this->instanceUrl)
                 ->setAccessToken($this->accessToken);

        $leadId = 'c78ae94b-0983-e511-80e5-3863bb35ddb8';

        $leads = $dynamics->createCollectionRequest('GET', '/leads?$filter=emailaddress1 eq \'adam@anderly.com\'')
                      ->setReturnType(Model\Lead::class)
                      ->execute();

        $lead = $leads[0];

        // $this->log->info(print_r($lead, true));

        $this->assertEquals($leadId, $lead->id);
        $this->assertEquals('Adam', $lead->firstname);
        $this->assertEquals('adam@anderly.com', $lead->emailaddress1);

    }
}