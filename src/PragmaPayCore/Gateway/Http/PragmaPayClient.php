<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Http;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Pragma\PragmaPayCore\Api\ApiClientInterface;
use Pragma\PragmaPayCore\Exception\ApiException;

class PragmaPayClient implements ClientInterface
{
    public function __construct(
        private readonly ApiClientInterface $apiClient,
        private readonly Json $json
    ) {
    }

    public function placeRequest(TransferInterface $transferObject): array
    {
        try {
            $responseRaw = $this->apiClient->submit(
                $transferObject->getUri(),
                $transferObject->getBody(),
                $transferObject->getHeaders(),
                $transferObject->getMethod()
            );
        } catch (ApiException) {
            return [];
        }

        return $this->json->unserialize($responseRaw);
    }
}
