<?php

namespace Victorlopezalonso\LaravelUtils\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    /**
     * ApiException constructor.
     *
     * @param string $message It should be an existing key in copies.json file
     * @param int $code This will be used as the http status of the response.
     * @param null|Throwable $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
