<?php

namespace SWRetail\Http;

use Psr\Http\Message\ResponseInterface;

use function GuzzleHttp\json_decode;

/**
 * Response for Api Requests.
 */
class Response // implements ResponseInterface by magic
{
    /**
     * Original response.
     *
     * @var ResponseInterface
     */
    public $response;

    /**
     * Parsed JSON body.
     *
     * @var object
     */
    public $json;

    /**
     * Load original response.
     *
     * @param ResponseInterface $response Original response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Transparent proxy for calls to the original response.
     *
     * @param string $name      Method name
     * @param mixed  $arguments Method parameters
     *
     * @return mixed
     *
     * @todo Write out all possible methods to really implement ResponseInterface
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->response, $name], $arguments);
    }

    /**
     * Parse the JSON body into PHP variables.
     */
    public function parseJsonBody()
    {
        $body = (string) $this->response->getBody();
        if (! empty($body)) {
            try {
                $this->json = json_decode($body);
            } catch (\InvalidArgumentException $e) {
                $this->json = (object) [
                    'error' => $e->getMessage(),
                    'body'  => $body,
                ];
            }
        }
    }
}
