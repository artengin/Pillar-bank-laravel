<?php

namespace App\Support;

use App\Enums\ApiServiceEnum;
use App\Enums\User\StatusEnum;
use App\Exceptions\ExternalApiHttpException;
use Illuminate\Support\Facades\Hash;
use RonasIT\Support\Services\HttpRequestService;
use Symfony\Component\HttpFoundation\Response;

class BankSimulatorApiService
{
    protected string $url;
    protected string $token;

    public function __construct(
        protected HttpRequestService $httpRequestService,
    ) {
        $this->url = config('defaults.kyc_url');
        $this->token = Hash::make(config('defaults.kyc_secret_key'));
    }

    public function verifyUser(array $data): StatusEnum
    {
        $response = $this->apiCall(
            method: 'post',
            path: '/kyc',
            data: [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'ssn' => $data['ssn'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ],
        );

        return StatusEnum::tryFrom($response['status']);
    }

    protected function apiCall(string $method, string $path, array $data = []): array
    {
        $request = $this
            ->httpRequestService
            ->set('http_errors', false)
            ->send(
                method: $method,
                url: $this->generateUrl($path),
                data: $data,
                headers: $this->prepareHeaders(),
            );

        $response = $request->getResponse();

        if ($response->getStatusCode() > Response::HTTP_MULTIPLE_CHOICES) {
            throw new ExternalApiHttpException(
                serviceName: ApiServiceEnum::KYC,
                responseCode: $response->getStatusCode(),
                responseData: json_decode($response->getBody(), true),
            );
        }

        return $request->json();
    }

    protected function generateUrl(string $path): string
    {
        return "{$this->url}{$path}";
    }

    protected function prepareHeaders(array $additionalHeaders = []): array
    {
        return array_merge([
            'authorization' => $this->token,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ], $additionalHeaders);
    }
}
