<?php

namespace Victorlopezalonso\LaravelUtils\Exceptions;

use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Victorlopezalonso\LaravelUtils\Traits\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApiExceptionHandler
{
    use ApiResponse;

    public static function parse(Throwable $exception)
    {
        $instance = new self();

        switch ($exception) {
            case $exception instanceof AuthenticationException:
            case $exception instanceof AuthorizationException:
                return $instance->withTranslation('server.unauthorized')->unauthorized();
                break;
            case $exception instanceof NotFoundHttpException:
            case $exception instanceof MethodNotAllowedHttpException:
                $instance->withTranslation('server.route_or_method_not_found')->notFound();
                break;
            case $exception instanceof ValidationException:
                return $instance->withValidations($exception->validator)->badRequest();
                break;
            case $exception instanceof ApiException:
                return $instance->withTranslation($exception->getMessage())->withStatus($exception->getCode())->response();
                break;
            default:
                return $instance->withErrorMessage($exception->getMessage())->internalServerError();
        }
    }
}
