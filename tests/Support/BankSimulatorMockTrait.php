<?php

namespace App\Tests\Support;

use Illuminate\Http\Response;

trait BankSimulatorMockTrait
{
    use BaseMockTrait;

    protected function kycCall(array $requestData, array $responseData, int $statusCode = Response::HTTP_OK): array
    {
        return $this->bankSimulatorRequest('/kyc', $requestData, $responseData, $statusCode);
    }

    protected function bankSimulatorRequest(
        string $uri,
        array $requestData = [],
        array $responseData = [],
        int $statusCode = Response::HTTP_OK,
    ): array {
        return $this->request(
            type: 'POST',
            url: "https://api.dev.banksystemsimulator.ronas.cloud{$uri}",
            data: $requestData,
            headers: [
                'authorization' => 'token',
                'content-type' => 'application/json',
                'accept' => 'application/json',
            ],
            options: [
                'http_errors' => false,
            ],
            responseData: $responseData,
            statusCode: $statusCode,
        );
    }
}
