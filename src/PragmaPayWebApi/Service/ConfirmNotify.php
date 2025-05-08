<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Service;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Pragma\PragmaPayWebApi\Api\ConfirmNotifyInterface;
use Pragma\PragmaPayWebApi\Api\Data\NotificationObjectInterface;
use Pragma\PragmaPayWebApi\Api\NotifyOrderProcessorInterface;
use Psr\Log\LoggerInterface;

class ConfirmNotify implements ConfirmNotifyInterface
{

    public function __construct(
        private readonly NotifyOrderProcessorInterface $notifyOrderProcessor,
        private readonly Json $json,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param string $id
     * @param \Pragma\PragmaPayWebApi\Api\Data\NotificationObjectInterface $object
     * @param string $type
     * @param string $date
     * @param string $timestamp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(
        string $id,
        NotificationObjectInterface $object,
        string $type,
        string $date,
        string $timestamp
    ): string {
        try {
            $paymentId = $object->getPaymentId();
            $repaymentPeriod = $object->getRepaymentPeriod();
            $items = $object->getItems();

            $this->logger->notice('Received webhook PAYMENT_CHANGED', [
                'paymentId' => $paymentId,
                'repaymentPeriodType' => $repaymentPeriod->getType(),
                'repaymentPeriodValue' => $repaymentPeriod->getValue(),
                'itemsCount' => count($items)
            ]);

            foreach ($items as $item) {
                $this->logger->notice('Webhook Item', [
                    'partnerItemId' => $item->getPartnerItemId(),
                    'status' => $item->getStatus(),
                    'amount' => $item->getValue()->getAmount(),
                    'currency' => $item->getValue()->getCurrency()
                ]);

                $this->notifyOrderProcessor->execute(
                    $item->getStatus(),
                    $paymentId,
                    $item->getValue()->getAmount(),
                    $item->getPartnerItemId()
                );
            }
            return $this->json->serialize(['status' => 'success', 'message' => 'Webhook processed successfully.']);
        } catch (Exception $e) {
            $this->logger->error('Webhook processing error: ' . $e->getMessage());
            throw new LocalizedException(__('Webhook processing failed'));
        }
    }
}
