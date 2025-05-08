<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Data\Order;

use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactoryInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Data\Quote\QuoteAdapterFactory;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;

class PaymentDataObjectFactory implements PaymentDataObjectFactoryInterface
{
    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param Order\OrderAdapterFactory $orderAdapterFactory
     * @param Quote\QuoteAdapterFactory $quoteAdapterFactory
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly OrderAdapterFactory $orderAdapterFactory,
        private readonly QuoteAdapterFactory $quoteAdapterFactory
    ) {
    }

    /**
     * Creates Payment Data Object
     *
     * @param InfoInterface $paymentInfo
     * @return PaymentDataObjectInterface
     */
    public function create(InfoInterface $paymentInfo): PaymentDataObjectInterface
    {
        if ($paymentInfo instanceof Payment) {
            $data['order'] = $this->orderAdapterFactory->create(
                ['order' => $paymentInfo->getOrder()]
            );
        } elseif ($paymentInfo instanceof \Magento\Quote\Model\Quote\Payment) {
            $data['order'] = $this->quoteAdapterFactory->create(
                ['quote' => $paymentInfo->getQuote()]
            );
        }
        $data['payment'] = $paymentInfo;

        return $this->objectManager->create(
            PaymentDataObject::class,
            $data
        );
    }
}
