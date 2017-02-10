<?php

namespace SaintSystems\OData\Tests;

use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Core\Log;

use SaintSystems\OData\ODataClient;

class ODataClientTest extends TestCase
{
    protected $log;
    private $baseUrl;

    public function setUp()
    {
        $this->log = Log::get_instance();
        $this->baseUrl = 'http://services.odata.org/V4/TripPinService';
        $this->crmUrl = 'https://saintsystems.crm.dynamics.com/api/data/v8.2';
    }

    public function testODataClientConstructor()
    {
        $odataClient = new ODataClient($this->baseUrl);
        $this->assertNotNull($odataClient);
        $baseUrl = $this->readAttribute($odataClient, 'baseUrl');
        $this->assertEquals('http://services.odata.org/V4/TripPinService/', $baseUrl);
    }

    // public function testEntitySetCollectionRequest()
    // {
    //     $odataClient = new ODataClient($this->crmUrl, function($requestMessage) {
    //         $requestMessage->headers['Authorization'] = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6Il9VZ3FYR190TUxkdVNKMVQ4Y2FIeFU3Y090YyIsImtpZCI6Il9VZ3FYR190TUxkdVNKMVQ4Y2FIeFU3Y090YyJ9.eyJhdWQiOiJodHRwczovL3NhaW50c3lzdGVtcy5jcm0uZHluYW1pY3MuY29tIiwiaXNzIjoiaHR0cHM6Ly9zdHMud2luZG93cy5uZXQvMWEyY2QzNWUtNWEzOC00OWRiLWE4OGYtYmEzYzRjOGI1MmZkLyIsImlhdCI6MTQ4NjY3MjI4NCwibmJmIjoxNDg2NjcyMjg0LCJleHAiOjE0ODY2NzYxODQsImFjciI6IjEiLCJhaW8iOiJBUUFCQUFFQUFBRFJOWVJRM2RoUlNybS00Sy1hZHBDSmQ2SDhHdEdDQWFCS2V5RzgwTkk0X1JncEEyRjhKTTJnRW02dmNEUXJwdEptUmh1YjE0V0hKTzZhMHhrNGZsX2J0aFFNbnhFMl9HTXpqSmJZaXRDcFFtQjk1TzlGN0YtTHQzdEh3X1IyR3NCM09LRUN3QWVKN3l0OGFzU0NxeXo1SUFBIiwiYW1yIjpbInB3ZCJdLCJhcHBpZCI6ImQ4NzBjMGZmLTc3OGEtNGI2MC1iN2ViLTZmMDYyN2VmODlkOSIsImFwcGlkYWNyIjoiMSIsImZhbWlseV9uYW1lIjoiQW5kZXJseSIsImdpdmVuX25hbWUiOiJBZGFtIiwiaXBhZGRyIjoiMTA0LjEzLjI0Ny4yMDciLCJuYW1lIjoiQWRhbSBBbmRlcmx5Iiwib2lkIjoiNTNjY2YwMzAtYThjYy00Zjc5LTg3YjItOGMyMjY3MTA0ZTZmIiwicGxhdGYiOiI1IiwicHVpZCI6IjEwMDNCRkZEOTM1MTE5OTYiLCJzY3AiOiJ1c2VyX2ltcGVyc29uYXRpb24iLCJzdWIiOiJncHdOS1lxZkRQN0JOWlVuTFdxNi1NWlJscEptWndoRDB4QmdweTlXSWlNIiwidGlkIjoiMWEyY2QzNWUtNWEzOC00OWRiLWE4OGYtYmEzYzRjOGI1MmZkIiwidW5pcXVlX25hbWUiOiJhZGFtLmFuZGVybHlAc2FpbnRzeXN0ZW1zLmNvbSIsInVwbiI6ImFkYW0uYW5kZXJseUBzYWludHN5c3RlbXMuY29tIiwidmVyIjoiMS4wIiwid2lkcyI6WyI2MmU5MDM5NC02OWY1LTQyMzctOTE5MC0wMTIxNzcxNDVlMTAiXX0.hidZCVj4DJceYXUIagGJyBwpSgb4eAGnFFlavyeUCskbJzADKMpvNcKH5hVBogusvM8kVQlk8OrtuenwB2B_0fR3rxeAr_EGw64g4NUsIVX5iAibBr1VLb1FTGm_n_qiCPYujRnHV8RnWrvXc_xVdLq4CJehciK83QqpdkvwM8wlMKwlSzzyJnoACPBTFU-rVn-C3azXhAgqfPmjhrWdi_rl5MmHGMQYhBRTWAcXrzBKGA_7z91OdNX1uFztkjestNm12SoAmYY0wG2G3yKcEDm1lAcfkYGKG9-jcYcbX-U90Dh04sD7c6l0oamtxHoLvnnCWPVzc_hpqbigGFJTEw';
    //     });

    //     //$people = $odataClient->entitySet('leads')->get();
    //     //dd($people->count());

    //     $leadId = 'c78ae94b-0983-e511-80e5-3863bb35ddb8';
    //     $lead = $odataClient->entitySet('leads')->find($leadId);
    //     //$this->log->info(print_r($people, true));
    //     //die();
    //     dd($lead);

    //     $this->log->info(print_r($people, true));

    // }
}
