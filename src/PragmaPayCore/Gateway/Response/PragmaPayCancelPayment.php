<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Response;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class PragmaPayCancelPayment implements HandlerInterface
{
    public function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }

    public function handle(array $handlingSubject, array $response)
    {
        $paymentDataObject = SubjectReader::readPayment($handlingSubject);

        $orderAdapter = $paymentDataObject->getOrder();
        if (!$orderAdapter) {
            return;
        }

        try {
            $order = $this->orderRepository->get($orderAdapter->getId());
            $message = __(
                'Transaction was canceled on PragmaPay side',
            );
            $order->addCommentToStatusHistory($message);
        } catch (NoSuchEntityException) {
            return;
        }
    }
}
