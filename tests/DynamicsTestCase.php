<?php

namespace Microsoft\Dynamics\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Microsoft\Dynamics\Core\Log;

class DynamicsTestCase extends TestCase
{
	protected $log;
    protected $http;
    protected $accessToken;
    private $username;
    private $password;
    protected $instanceUrl;

    const AUTHORITY_URL      = 'https://login.microsoftonline.com/common';
    const AUTHORIZE_ENDPOINT = '/oauth2/authorize';
    const TOKEN_ENDPOINT     = '/oauth2/token';

    public function setUp()
    {
        $this->log = Log::get_instance();
        $this->http = new Client();
        $this->getTestConfig();
        $this->getAccessToken();
    }

    public function getTestConfig()
    {
        $configFileLocation = __DIR__ . '/testConfig.json';
        if ( ! file_exists($configFileLocation)) {
            $configFileLocation = __DIR__ . '/testConfig.example.json';
        }

        $testConfigFile = file_get_contents($configFileLocation);
        
        $testConfig = json_decode($testConfigFile, true);

        $this->clientId     = $testConfig['test_client_id_v1'];
        $this->clientSecret = $testConfig['test_client_secret_v1'];
        $this->username     = $testConfig['test_username'];
        $this->password     = $testConfig['test_password'];
        $this->instanceUrl  = $testConfig['test_instance_url'] ?? '';
    }

    public function getAccessToken()
    {
        // $tokenEndpoint = Constants::AUTHORITY_URL . '/oauth2/token';
        // $body = http_build_query(
        //     array(
        //         'grant_type'    => 'password',
        //         'resource'      => 'https://graph.microsoft.com',
        //         'client_id'     => $this->clientId,
        //         'client_secret' => $this->clientSecret,
        //         'username'      => $this->username,
        //         'password'      => $this->password
        //     ));
        // $headers = array(
        //     'content-type' => 'application/x-www-form-urlencoded',
        //     'content-length' => strlen($body)
        //     );
        
        // // Send a POST request to the token endpoint to retrieve tokens.
        // // Token endpoint is:
        // // https://login.microsoftonline.com/common/oauth2/token
        // $response = RequestManager::sendPostRequest(
        //     $tokenEndpoint,
        //     $headers,
        //     $body
        // );

        // // Store the raw response in JSON format.
        // $jsonResponse = json_decode($response, true);
        
        // $this->accessToken = $jsonResponse['access_token'];
        
        $uri = self::AUTHORITY_URL . self::TOKEN_ENDPOINT;

        //$this->log->info($uri);

        $form_params = [
            'grant_type'    => 'password',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username'      => $this->username,
            'password'      => $this->password,
            'resource'      => $this->instanceUrl,
        ];

        //$this->log->info(print_r($form_params, true));

        $response = $this->http->request('POST', $uri, [
            'form_params' => $form_params
        ]);

        $json = $response->getBody();

        //$this->log->info($json);

        $token = json_decode( $json, true );

        $this->accessToken = $token['access_token'];
    }
}
