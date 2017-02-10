<?php

namespace SaintSystems\OData;

/**
 * The base request class.
 */
class ODataRequest implements IODataRequest
{
    protected $sdkVersionHeaderName;
    protected $sdkVersionHeaderValue;

    /**
     * Constructs a new <see cref="BaseRequest"/>.
     *
     * <param name="requestUrl">The URL for the request.</param>
     * <param name="client">The <see cref="IBaseClient"/> for handling requests.</param>
     * <param name="options">The header and query options for the request.</param>
     */
    public function __construct(
        string $requestUrl,
        string $method,
        IODataClient $client,
        string $returnType = null,
        array $options = null)
    {
        $this->method = $method;
        $this->client = $client;
        $this->headers = $this->getDefaultHeaders();
        $this->queryOptions = [];

        $this->requestUrl = $requestUrl;//$this->initializeUrl($requestUrl);

        $this->returnType = $returnType;

        //$this->sdkVersionHeaderName = CoreConstants.Headers.SdkVersionHeaderName;
        //$this->SdkVersionHeaderPrefix = "Graph";

        if ($options != null)
        {
            $headerOptions = array_filter($options, function($item) {
                return is_a($item, class_basename(HeaderOption::class));
            });
            if ($headerOptions != null)
            {
                $this->headers[] = $headerOptions;
            }

            $queryOptions = array_filter($options, function($item) {
                return is_a($item, class_basename(QueryOption::class));
            });
            if ($queryOptions != null)
            {
                $this->queryOptions[] = $queryOptions;
            }
        }
    }

    /**
     * Gets or sets the content type for the request.
     */
    public $contentType;

    /**
     * Gets the <see cref="HeaderOption"/> collection for the request.
     */
    public $headers;

    /**
     * Gets the <see cref="IGraphServiceClient"/> for handling requests.
     */
    public $client;

    /**
     * Gets or sets the HTTP method string for the request.
     */
    public $method;

    /**
     * Gets the <see cref="QueryOption"/> collection for the request.
     */
    public $queryOptions;

    /**
     * Gets the URL for the request, without query string.
     */
    public $requestUrl;
    
    /**
     * Gets or sets the telemetry header prefix for requests.
     */
    protected $sdkVersionHeaderPrefix;

    public function getQueryOptions()
    {
        return $this->queryOptions;
    }

    /**
    * Get a list of headers for the request
    *
    * @return array The headers for the request
    */
    private function getDefaultHeaders()
    {
        $headers = [
            //RequestHeader::HOST => $this->client->getBaseUrl(),
            RequestHeader::CONTENT_TYPE => 'application/json',
            RequestHeader::ODATA_MAX_VERSION => Constants::MAX_ODATA_VERSION,
            RequestHeader::ODATA_VERSION => Constants::ODATA_VERSION,
            RequestHeader::PREFER => Constants::ODATA_MAX_PAGE_SIZE_DEFAULT,
            RequestHeader::USER_AGENT => 'odata-sdk-php-' . Constants::SDK_VERSION,
            //RequestHeader::AUTHORIZATION => 'Bearer ' . $this->accessToken
        ];
        return $headers;
    }

    /**
     * Sends the request.
     *
     * <typeparam name="T">The expected response object type for deserialization.</typeparam>
     * <param name="serializableObject">The serializable object to send.</param>
     * <param name="cancellationToken">The <see cref="CancellationToken"/> for the request.</param>
     * <param name="completionOption">The <see cref="HttpCompletionOption"/> to pass to the <see cref="IHttpProvider"/> on send.</param>
     * <returns>The <see cref="HttpResponseMessage"/> object.</returns>
     */
    public function send($serializableObject = null)
    {
        if (empty($this->requestUrl))
        {
            throw new ODataException(Constants::REQUEST_URL_MISSING);
        }

        // if ($this->client->getAuthenticationProvider() == null)
        // {
        //     throw new ODataException('AuthenticationProviderMissing');
        // }

        $request = $this->getHttpRequestMessage();
        
        $this->authenticateRequest($request);

        // Attach streams & JSON automatically
        if (is_string($serializableObject) || is_a($serializableObject, 'GuzzleHttp\\Psr7\\Stream')) {
            $request->content = $serializableObject;
        } // JSON-encode the model object's property dictionary
        else if (method_exists($serializableObject, 'getProperties')) {
            $class = get_class($serializableObject);
            $class = explode("\\", $class);
            $model = strtolower(end($class));
            
            $body = $this->flattenDictionary($serializableObject->getProperties());
            $request->content = "{" . $model . ":" . json_encode($body) . "}";
        } 
        // By default, JSON-encode (i.e. arrays)
        else {
            $request->content = json_encode($serializableObject);
        }

        // if ($serializableObject != null)
        // {
        //     var inputStream = serializableObject as Stream;

        //     if (inputStream != null)
        //     {
        //         request->content = new StreamContent($inputStream);
        //     }
        //     else
        //     {
        //         $request->content = new StringContent($this->Client.HttpProvider.Serializer.SerializeObject(serializableObject));
        //     }

        //     if (!empty($this->contentType))
        //     {
        //         $request.Content.Headers.ContentType = new MediaTypeHeaderValue($this->ContentType);
        //     }
        // }

        return $this->client->getHttpProvider()->send($request, $this->returnType);

    }

    /**
     * Gets the <see cref="HttpRequestMessage"/> representation of the request.
     *
     * <returns>The <see cref="HttpRequestMessage"/> representation of the request.</returns>
     */
    public function getHttpRequestMessage()
    {
        $queryString = $this->buildQueryString();
        $request = new HttpRequestMessage(new HttpMethod($this->method), $this->requestUrl.$queryString);

        $this->addHeadersToRequest($request);

        return $request;
    }

    /// <summary>
    /// Builds the query string for the request from the query option collection.
    /// </summary>
    /// <returns>The constructed query string.</returns>
    private function buildQueryString()
    {
        $queryOptions = $this->getQueryOptions();
        if ($queryOptions != null)
        {
            return '?' . http_build_query($queryOptions, null, '&');
        }

        return null;
    }

    /**
     * Adds all of the headers from the header collection to the request.
     * @param \Microsoft\Core\Http\HttpRequestMessage $request The HttpRequestMessage representation of the request.
     */
    private function addHeadersToRequest(HttpRequestMessage $request)
    {
        $request->headers = array_merge($this->headers, $request->headers);

        // if (string.IsNullOrEmpty($this->sdkVersionHeaderValue))
        // {
        //     var assemblyVersion = $this->GetType().GetTypeInfo().Assembly.GetName().Version;
        //     $this->sdkVersionHeaderValue = string.Format(
        //         CoreConstants.Headers.SdkVersionHeaderValueFormatString,
        //         $this->SdkVersionHeaderPrefix,
        //         assemblyVersion.Major,
        //         assemblyVersion.Minor,
        //         assemblyVersion.Build);
        // }

        // Append SDK version header for telemetry
        // request.Headers.Add(
        //     $this->sdkVersionHeaderName,
        //     $this->sdkVersionHeaderValue);
    }

    /**
     * Adds the authentication header to the request.
     *
     * <param name="request">The <see cref="HttpRequestMessage"/> representation of the request.</param>
     * <returns>The task to await.</returns>
     */
    private function authenticateRequest(HttpRequestMessage $request)
    {
        $authenticationProvider = $this->client->getAuthenticationProvider();
        if ( ! is_null($authenticationProvider) && is_callable($authenticationProvider)) {
            return $authenticationProvider($request);
        }
    }

    /**
     * Initializes the request URL for the request, breaking it into query options and base URL.
     *
     * <param name="requestUrl">The request URL.</param>
     * <returns>The request URL minus query string.</returns>
     */
    private function initializeUrl(string $requestUrl)
    {
        if (empty($requestUrl))
        {
            throw new ODataException(Constants::BASE_URL_MISSING);
        }

        $uri = new Uri($requestUrl);
        
        if (!empty($uri->query))
        {
            $queryString = $uri->query;

            $queryOptions = [];

            $queryStringParts = explode('&', $queryString);

            $queryOptions = array_map(function($item) {
                // We want to split on the first occurrence of = since there are scenarios where a query option can 
                // have 'sub-query' options on navigation properties for $expand scenarios. This way we can properly
                // split the query option name/value into the QueryOption object. Take this for example:
                // $expand=extensions($filter=Id%20eq%20'SMB'%20)
                // We want to get '$expand' as the name and 'extensions($filter=Id%20eq%20'SMB'%20)' as the value
                // for QueryOption object.
                // OData URL conventions 5.1.2 System Query Option $expand
                // http://docs.oasis-open.org/odata/odata/v4.0/errata03/os/complete/part2-url-conventions/odata-v4.0-errata03-os-part2-url-conventions-complete.html#_Toc453752359
                $segments = explode('=', $item, 2);
                return new QueryOption($segments[0], count($segments) > 1 ? $segments[1] : '');
            }, $queryStringParts);

            $this->queryOptions = array_merge($this->queryOptions, $queryOptions);

        }

        //return new UriBuilder($uri) { Query = '' }.ToString();
        return http_build_url($uri, ['query' => '']);
    }

    /**
    * Flattens the property dictionaries into 
    * JSON-friendly arrays
    *
    * @param mixed $obj the object to flatten
    *
    * @return array flattened object
    */
    protected function flattenDictionary($obj) {
        foreach ($obj as $arrayKey => $arrayValue) {
            if (method_exists($arrayValue, 'getProperties')) {
                $data = $arrayValue->getProperties();
                $obj[$arrayKey] = $data;
            } else {
                $data = $arrayValue;
            }
            if (is_array($data)) {
                $newItem = $this->flattenDictionary($data);
                $obj[$arrayKey] = $newItem;
            }
        }
        return $obj;
    }
}
