<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class PragmaPayCreatePayment implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDataObject = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDataObject->getPayment();
        $payment
            ->setTransactionId($response[PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD])
            ->setIsTransactionPending(true)
            ->setIsTransactionClosed(false);

        $payment->setAdditionalInformation(
            PragmaConnectionConfigProviderInterface::ADDITION_KEY_REDIRECT_URL,
            $response[PragmaConnectionConfigProviderInterface::API_RESPONSE_REDIRECT_URL_FIELD]
        );
        $payment->setAdditionalInformation(
            PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT,
            $response[PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD]
        );
        if (empty($response[PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD])) {
            return;
        }
        foreach ($response[PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD] as $item) {
            if (!array_key_exists(PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEM_ID_FIELD, $item)) {
                continue;
            }
            $payment->setAdditionalInformation(
                PragmaConnectionConfigProviderInterface::ADDITION_KEY_ITEM_ID,
                $item[PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEM_ID_FIELD]
            );
        }
    }
}
