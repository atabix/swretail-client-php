<?php

namespace SWRetail\Exceptions;

use SWRetail\Http\Response;

/**
 * Generic Exception for Api related requests.
 */
class ApiException extends \Exception
{
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
        $data = $response->json;

        $exception = new static($data->errorstring . ' (code: ' . $data->errorcode . ')', $data->errorcode);
        $exception->apiResponse = $response;

        return $exception;
    }
}
