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
    private ObjectManagerInterface $objectManager;

    private OrderAdapterFactory $orderAdapterFactory;

    private QuoteAdapterFactory $quoteAdapterFactory;

    public function __construct(ObjectManagerInterface $objectManager, OrderAdapterFactory $orderAdapterFactory, QuoteAdapterFactory $quoteAdapterFactory)
    {
        $this->objectManager = $objectManager;
        $this->orderAdapterFactory = $orderAdapterFactory;
        $this->quoteAdapterFactory = $quoteAdapterFactory;
    }

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
