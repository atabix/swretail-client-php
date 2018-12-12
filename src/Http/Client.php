<?php

namespace SWRetail\Http;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions as Options;
use Psr\Http\Message\ResponseInterface;
use SWRetail\Exceptions\ApiException;

use function GuzzleHttp\choose_handler;

class Client extends HttpClient
{
    /**
     * Request options for Guzzle.
     *
     * @var array
     */
    private $requestOptions = [];

    /**
     * Latest thrown Exception.
     *
     * @var \Exception
     */
    public $requestException;

    /**
     * Initialise HTTP Client.
     *
     * @param array $parameters Extra parameters
     */
    public function __construct($parameters = [])
    {
        $parameters = \array_replace_recursive([
            'base_uri' => swconfig('api_base_uri'),
            'auth'     => [
                swconfig('api_auth_username'),
                swconfig('api_auth_password'),
            ],
            'handler'  => $this->getHandlerStack(),
        ], $parameters);

        parent::__construct($parameters);

        // $this->header('Content-Type', 'application/vnd.api+json');
        // $this->header('Accept', 'application/vnd.api+json');
    }

    /**
     * Request Handler.
     *
     * @return HandlerStack
     */
    private function getHandlerStack() : HandlerStack
    {
        $stack = new HandlerStack();
        $stack->setHandler(choose_handler());

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $apiResponse = new Response($response);
            $apiResponse->parseJsonBody();

            return $apiResponse;
        }));

        return $stack;
    }

    /**
     * Make the request with configured parameters.
     *
     * @param string $method HTTP method
     * @param string $path   Relative path to the base_uri.
     *
     * @return Response
     */
    public function apiRequest(string $method, string $path) : Response
    {
        $method = \strtoupper($method);

        return $this->request($method, $path, $this->requestOptions);
    }

    /**
     * Do a request with the given parameters.
     *
     * @param string $method HTTP method
     * @param string $path   Relative path to the base_ur
     * @param array  $query  Querystring
     * @param array  $data   JSON data for HTTP body.
     *
     * @throws RequestException
     *
     * @return Response
     */
    public function getApiResponse(string $method, string $path, $query = null, $data = null) : Response
    {
        if ($query) {
            // $this->query($query);
            $this->setOption(Options::QUERY, $query);
        }
        if ($data) {
            // $this->json($data);
            $this->setOption(Options::JSON, $data);
        }

        // $this->logRequest($method, $path, $query, $data); // TEMP

        return $this->apiRequest($method, $path);
    }

    /**
     * Create a new Client, do the request with the given parameters, and handle common exceptions.
     *
     * @param string $method HTTP method
     * @param string $path   Relative path to the base_uri
     * @param mixed  $query  Querystring
     * @param mixed  $data   JSON data for HTTP body.
     *
     * @throws ApiException
     *
     * @return Response
     */
    public static function requestApi(string $method, string $path, $query = null, $data = null) : Response
    {
        $client = new self();
        try {
            $response = $client->getApiResponse($method, $path, $query, $data);
        } catch (RequestException $e) {
            $response = $client->handleRequestException($e); // TODO
        }

        // throw when data has "errors".
        $client->handleResponseErrors($response); // TODO

        return $response;
    }

    /**
     * Get the response from a RequestException.
     *
     * @param RequestException $exception
     *
     * @throws ApiException
     * @throws RequestException
     *
     * @return Response
     */
    public function handleRequestException(RequestException $exception)
    {
        $this->requestException = $exception;

        $class = \get_class($exception);
        switch ($class) {
            case ConnectException::class:
                throw new ApiException($exception->getMessage(), 0, $exception);
            case ServerException::class:
            case ClientException::class:
                $response = $exception->getResponse();
                break;
            default:
                throw $exception; /* RequestException */
        }

        return $response;
    }

    /**
     * Throw an exception when response has errors.
     *
     * @throws ApiException
     */
    public function handleResponseErrors(Response $response)
    {
        // TODO check for SWRetail API.
        if (! empty($response->json->errors)) {
            throw ApiException::fromResponse($response);
        }
    }
}
