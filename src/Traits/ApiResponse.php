<?php

namespace Victorlopezalonso\LaravelUtils\Traits;

const HTTP_CODE_200_OK = 200;
const HTTP_CODE_201_OK_CREATED = 201;
const HTTP_CODE_202_OK_ACCEPTED = 202;
const HTTP_CODE_204_OK_NO_CONTENT = 204;
const HTTP_CODE_400_BAD_REQUEST = 400;
const HTTP_CODE_401_UNAUTHORIZED = 401;
const HTTP_CODE_402_PAYMENT_REQUIRED = 402;
const HTTP_CODE_403_FORBIDDEN = 403;
const HTTP_CODE_404_NOT_FOUND = 404;
const HTTP_CODE_409_CONFLICT = 409;
const HTTP_CODE_422_UNPROCESSABLE_ENTITY = 422;
const HTTP_CODE_426_UPGRADE_REQUIRED = 426;
const HTTP_CODE_429_TOO_MANY_REQUESTS = 429;
const HTTP_CODE_500_INTERNAL_SERVER_ERROR = 500;
const HTTP_CODE_503_SERVICE_UNAVAILABLE = 503;

trait ApiResponse
{
    protected $response = [];
    protected $status = HTTP_CODE_200_OK;

    public function withData($data)
    {
        $this->response['data'] = $data;
        return $this;
    }

    public function withMessage($message)
    {
        $this->response['message'] = $message;
        return $this;
    }

    public function withError($message, $code = 0)
    {
        $this->response['error'] = [
            'code' => $code,
            'message' => $message,
        ];
        return $this;
    }

    public function withValidations($validations)
    {
        $this->response['validations'] = $validations;
        return $this;
    }

    public function withStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function ok()
    {
        return $this->withStatus(HTTP_CODE_200_OK)->response();
    }

    public function okCreated()
    {
        return $this->withStatus(HTTP_CODE_201_OK_CREATED)->response();
    }

    public function okAccepted()
    {
        return $this->withStatus(HTTP_CODE_202_OK_ACCEPTED)->response();
    }

    public function okNoContent()
    {
        return $this->withStatus(HTTP_CODE_204_OK_NO_CONTENT)->response();
    }

    public function badRequest()
    {
        return $this->withStatus(HTTP_CODE_400_BAD_REQUEST)->response();
    }

    public function unauthorized()
    {
        return $this->withStatus(HTTP_CODE_401_UNAUTHORIZED)->response();
    }

    public function paymentRequired()
    {
        return $this->withStatus(HTTP_CODE_402_PAYMENT_REQUIRED)->response();
    }

    public function forbidden()
    {
        return $this->withStatus(HTTP_CODE_403_FORBIDDEN)->response();
    }

    public function notFound()
    {
        return $this->withStatus(HTTP_CODE_404_NOT_FOUND)->response();
    }

    public function conflict()
    {
        return $this->withStatus(HTTP_CODE_409_CONFLICT)->response();
    }

    public function unprocessableEntity()
    {
        return $this->withStatus(HTTP_CODE_422_UNPROCESSABLE_ENTITY)->response();
    }

    public function upgradeRequired()
    {
        return $this->withStatus(HTTP_CODE_426_UPGRADE_REQUIRED)->response();
    }

    public function tooManyRequests()
    {
        return $this->withStatus(HTTP_CODE_429_TOO_MANY_REQUESTS)->response();
    }

    public function internalServerError()
    {
        return $this->withStatus(HTTP_CODE_500_INTERNAL_SERVER_ERROR)->response();
    }

    public function serviceUnavailable()
    {
        return $this->withStatus(HTTP_CODE_503_SERVICE_UNAVAILABLE)->response();
    }

    private function response()
    {
        return response()->json($this->response, $this->status, [JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK]);
    }
}
