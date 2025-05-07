<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Pragma\PragmaPayCore\Api\ApiClientInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Exception\ApiException;
use Psr\Log\LoggerInterface;

class ApiClient implements ApiClientInterface
{
    public function __construct(
        private readonly PragmaConnectionConfigProviderInterface $connectionConfigProvider,
        private readonly Json $json,
        private readonly ClientFactory $clientFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function submit(string $actionUri, array $params, array $headers = [], string $method = 'POST'): string
    {
        $validMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];
        if (!in_array(strtoupper($method), $validMethods, true)) {
            throw new InvalidArgumentException('Invalid HTTP method');
        }
        $storeId = (int)$this->storeManager->getStore()->getId();
        $client = $this->getClient($storeId);
        try {
            if ($this->connectionConfigProvider->isLogCartRequest($storeId)) {
                $this->logger->notice(
                    sprintf(
                        "Request Method: %s. Request URI: %s. Request Date: %s.",
                        $method,
                        $actionUri,
                        (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                    )
                );
            }
            $result = $client->request(
                $method,
                $actionUri,
                [
                    'headers' => $headers,
                    'json' => $params
                ]
            );

            if (!$this->isValidStatusCode($result->getStatusCode())) {
                $response = $this->json->unserialize($result->getBody()->getContents());
                $this->logger->error(
                    sprintf(
                        'Request URI: %s. Request Failed %s. Operation Errors: %s',
                        $actionUri,
                        $response['message'] ?? '',
                        $response['errors'] ?? ''
                    )
                );
                throw new ApiException(
                    (string)__('Something went wrong with your request. Please try again later.')
                );
            }

            $responseBody = $result->getBody()->getContents();

            // Validate JSON response
            try {
                $this->json->unserialize($responseBody);
            } catch (InvalidArgumentException) {
                throw new ApiException('Invalid response format');
            }

            return $responseBody;
        } catch (ClientException|GuzzleException $e) {
            if (str_contains($e->getMessage(), 'Timeout')) {
                throw new ApiException('Request timed out');
            }

            $this->logger->notice($e->getMessage());
            throw new ApiException(
                (string)__('Something went wrong with your request. Please try again later.')
            );
        }
    }

    private function isValidStatusCode(int $statusCode): bool
    {
        return match ($statusCode) {
            200, 201, 202 => true,
            default => false
        };
    }

    private function getClient(int $storeId): Client
    {
        return $this->clientFactory->create([
            'config' => [
                'base_uri' => $this->connectionConfigProvider->getApiUrl($storeId),
                'verify' => true,
                'connect_timeout' => 30,
                'timeout' => 30
            ]
        ]);
    }
}
