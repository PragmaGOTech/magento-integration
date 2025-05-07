<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use LogicException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class PragmaPaymentIdBuilder implements BuilderInterface
{
    public function build(array $buildSubject): array
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new InvalidArgumentException('Payment data object should be provided');
        }
        $buildPayment = $buildSubject['payment'];
        if (!$buildPayment->getPayment() instanceof OrderPaymentInterface) {
            throw new LogicException('Order payment should be provided.');
        }
        $pragmaPaymentId = $buildPayment
            ->getPayment()
            ->getAdditionalInformation(PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT);

        if (!$pragmaPaymentId) {
            throw new LogicException('Pragma payment id should be provided.');
        }
        return [
            'body' => [
                'paymentId' => $pragmaPaymentId
            ],
        ];
    }
}
