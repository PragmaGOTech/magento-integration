<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pragma\PragmaPayCore\Api\AuthorizationTokenProviderInterface;

class AuthorizationHeaderBuilder implements BuilderInterface
{
    public function __construct(
        private readonly AuthorizationTokenProviderInterface $authorizationTokenProvider,
    ) {
    }

    public function build(array $buildSubject): array
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new InvalidArgumentException('Payment data object should be provided');
        }
        $buildPayment = $buildSubject['payment'];
        $storeId = (int)$buildPayment->getOrder()->getStoreId();
        return [
            'headers' => [
                'authorization' => $this->getBearerToken($storeId),
            ],
        ];
    }

    private function getBearerToken(int $storeId): string
    {
        return 'Bearer ' . $this->authorizationTokenProvider->getAccessToken($storeId);
    }
}
