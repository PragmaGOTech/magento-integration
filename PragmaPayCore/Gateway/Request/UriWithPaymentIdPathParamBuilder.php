<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use LogicException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class UriWithPaymentIdPathParamBuilder implements BuilderInterface
{
    public const CANCEL_PAYMENT_URI = 'api/v2/partner/payment/%s/cancel';
    public const CANCEL_SANDBOX_PAYMENT_URI = 'api/v2/partner/payment/%s/cancel';
    public const REFUND_PAYMENT_URI = 'api/v2/partner/payment/%s/update';
    public const REFUND_SANDBOX_PAYMENT_URI = 'api/v2/partner/payment/%s/update';
    public const GET_PAYMENT_URI = 'api/v2/partner/payment/%s';
    public const GET_SANDBOX_PAYMENT_URI = 'api/v2/partner/payment/%s';

    public function __construct(
        private readonly PragmaConnectionConfigProviderInterface $configProvider,
        private readonly string $paymentUri,
        private readonly string $sandboxPaymentUri,
    ) {
    }

    public function build(array $buildSubject): array
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new InvalidArgumentException('Payment data object should be provided');
        }
        $buildPayment = $buildSubject['payment'];
        if (!$buildPayment->getPayment() instanceof OrderPaymentInterface) {
            throw new LogicException('Order payment should be provided.');
        }

        $order = $buildPayment->getOrder();
        if (!$order instanceof OrderAdapterInterface) {
            throw new LogicException('Order should be provided.');
        }

        $storeId = (int)$order->getStoreId();
        $pragmaPaymentId = $buildPayment
            ->getPayment()
            ->getAdditionalInformation(PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT);

        if (!$pragmaPaymentId) {
            throw new LogicException('Pragma payment id should be provided.');
        }
        return [
            'uri' => $this->getPaymentUri($storeId, (string)$pragmaPaymentId),
        ];
    }
    private function getPaymentUri(int $storeId, string $paymentId): string
    {
        $uri = $this->configProvider->isSandbox($storeId)
            ? $this->sandboxPaymentUri
            : $this->paymentUri;

        return sprintf($uri, $paymentId);
    }
}
