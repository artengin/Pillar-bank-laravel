<?php

namespace App\Exceptions;

use App\Enums\ApiServiceEnum;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExternalApiHttpException extends HttpException
{
    public function __construct(
        public readonly ApiServiceEnum $serviceName,
        public readonly int $responseCode,
        public readonly string|array $responseData,
    ) {
        parent::__construct(
            statusCode: Response::HTTP_FAILED_DEPENDENCY,
            message: __('validation.exceptions.external_api_http_exception', [
                'service' => $serviceName->value,
                'response_code' => $responseCode,
                'response_data' => (is_array($responseData)) ? json_encode($responseData) : $responseData,
            ]),
        );
    }
}
