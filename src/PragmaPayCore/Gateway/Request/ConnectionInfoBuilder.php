<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use Magento\Framework\Url;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class ConnectionInfoBuilder implements BuilderInterface
{
    public function __construct(
        private readonly PragmaConnectionConfigProviderInterface $configProvider,
        private readonly Url $urlBuilder,
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
            'body' => [
                'notificationUrl' => $this->urlBuilder->getUrl('', ['_direct' => $this->configProvider->getNotificationUrl($storeId)]),
                'returnUrl' => $this->urlBuilder->getUrl($this->configProvider->getReturnUrl($storeId)),
                'cancelUrl' => $this->urlBuilder->getUrl($this->configProvider->getCancelUrl($storeId)),
            ],
        ];
    }
}
