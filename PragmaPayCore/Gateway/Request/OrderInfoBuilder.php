<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use LogicException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Pragma\PragmaPayCore\Client\RequestManager;
use Pragma\PragmaPayCore\Service\GenerateUuid5;

class OrderInfoBuilder implements BuilderInterface
{
    public function __construct(
        private readonly RequestManager $requestManager,
        private readonly GenerateUuid5 $generateUuid5
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

        $orderData = [
            'partnerOrderId' => $this->generateUuid5->execute($order->getOrderIncrementId()),
            'items' => [
                array_merge(
                    [
                        'partnerItemId' => $this->generateUuid5->execute($order->getOrderIncrementId()),
                        'value' => [
                            'amount' => $this->requestManager->formatAmount($order->getGrandTotalAmount()),
                            'currency' => $order->getCurrencyCode(),
                        ],
                    ],
                )
            ]
        ];
        if ($order->getCustomerId()) {
            $orderData['partnerCustomerId'] = $this->generateUuid5->execute((string)$order->getCustomerId());
        }
        return [
            'body' => $orderData,
        ];
    }
}
