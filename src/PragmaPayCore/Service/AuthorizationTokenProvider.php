<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Pragma\PragmaPayCore\Api\ApiClientInterface;
use Pragma\PragmaPayCore\Api\AuthorizationTokenProviderInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Exception\ApiException;
use Pragma\PragmaPayCore\Gateway\Request\ClientConfigDataBuilder;
use Psr\Log\LoggerInterface;

class AuthorizationTokenProvider implements AuthorizationTokenProviderInterface
{
    private const AUTHORIZATION_URI = 'api/partner/authorize';
    private const SANDBOX_AUTHORIZATION_URI = 'api/partner/authorize';

    private ApiClientInterface $apiClient;

    private PragmaConnectionConfigProviderInterface $connectionConfigProvider;

    private Json $json;

    private LoggerInterface $logger;

    private ?string $accessToken = null;

    public function __construct(ApiClientInterface $apiClient, PragmaConnectionConfigProviderInterface $connectionConfigProvider, Json $json, LoggerInterface $logger)
    {
        $this->apiClient = $apiClient;
        $this->connectionConfigProvider = $connectionConfigProvider;
        $this->json = $json;
        $this->logger = $logger;
    }

    public function getAccessToken(?int $storeId = null): ?string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }
        try {
            $partnerKey = $this->connectionConfigProvider->getPartnerKey($storeId);
            $partnerSecret = $this->connectionConfigProvider->getPartnerSecret($storeId);

            $response = $this->apiClient->submit(
                $this->connectionConfigProvider->isSandbox($storeId)
                    ? self::SANDBOX_AUTHORIZATION_URI
                    : self::AUTHORIZATION_URI,
                [
                    'key' => $partnerKey,
                    'secret' => $partnerSecret,
                ],
                ClientConfigDataBuilder::CLIENT_HEADERS
            );

            $this->accessToken = $this->json->unserialize($response)['token'] ?? null;
            if (!$this->accessToken) {
                throw new LocalizedException(__('Token not found in response.'));
            }

            return $this->accessToken;
        } catch (ClientException | ApiException $e) {
            $this->logger->error('HTTP request to API failed.', ['exception' => $e]);
            throw new LocalizedException(__('Communication error with API.'), $e);
        }
    }
}
