<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class CreatePaymentUriBuilder implements BuilderInterface
{
    private const PAYMENT_URI = 'api/v2/partner/payment';
    private const SANDBOX_PAYMENT_URI = 'api/v2/partner/payment';

    public function __construct(
        private readonly PragmaConnectionConfigProviderInterface $configProvider,
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
            'uri' => $this->getPaymentUri($storeId),
        ];
    }
    private function getPaymentUri(int $storeId): string
    {
        return $this->configProvider->isSandbox($storeId)
            ? self::SANDBOX_PAYMENT_URI
            : self::PAYMENT_URI;
    }
}
