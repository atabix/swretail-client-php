<?php

namespace SWRetail\Exceptions;

use SWRetail\Http\Response;

/**
 * Generic Exception for Api related requests.
 */
class ApiException extends \Exception 
{

    /**
     * Errors from the JSON body of the original response.
     *
     * @var array
     */
    public $apiErrors;

    /**
     * Original response.
     *
     * @var Response
     */
    public $apiResponse;

    /**
     * Create an Exception with data from a response.
     *
     * @param Response $response Originating response with "errors" in Json body.
     *
     * @return self
     */
    public static function fromResponse(Response $response) : self
    {
        $firstError = reset($response->json->errors);

        $exception = new static(
            ($firstError->title ?? '') . ': ' . ($firstError->detail ?? ''),
            $response->getStatusCode()
        );
        $exception->apiResponse = $response;
        $exception->apiErrors = $response->json->errors;

        return $exception;
    }

    /**
     * Get the first error.
     *
     * @return object
     */
    public function getFirstError() : object
    {
        if (! empty($this->apiErrors)) {
            return reset($this->apiErrors);
        }

        return \json_decode(\json_encode([
            'title'  => 'Error',
            'detail' => $this->getMessage(),
        ]));
    }
}
