<?php

namespace Victorlopezalonso\LaravelUtils\Exceptions;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Victorlopezalonso\LaravelUtils\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Auth\AuthenticationException;
use Exception;
use Throwable;

class ApiException extends Exception implements Responsable
{
    use ApiResponse;

    public function __construct(Throwable $exception)
    {
        parent::__construct($exception);

        $this->setMessageByException($exception);
    }

    public function setMessageByException(Throwable $exception)
    {
        switch ($exception) {
            case $exception instanceof AuthenticationException:
            case $exception instanceof AuthorizationException:
                $this->unauthorized();
                $message = trans('SERVER_UNAUTHORIZED');
                break;
            case $exception instanceof NotFoundHttpException:
            case $exception instanceof MethodNotAllowedHttpException:
                $this->notFound();
                $message = trans('SERVER_ROUTE_OR_METHOD_NOT_FOUND_EXCEPTION');
                break;
            case $exception instanceof ValidationException:
                $this->unprocessableEntity();
                $errors = $exception->errors();
                $firstKey = array_keys($errors)[0];
                $message = $errors[$firstKey][0];
                $validations = $errors;
                break;
            default:
                $this->internalServerError();
                $message = trans('SERVER_INTERNAL_ERROR');
        }

        $this->withMessage($message)->withError($exception->getMessage(), $exception->getCode());

        if (isset($validations)) {
            if ($validations) {
                $this->withValidations($validations);
            }
        }
    }

    public function toResponse($request)
    {
        return $this->response();
    }
}
