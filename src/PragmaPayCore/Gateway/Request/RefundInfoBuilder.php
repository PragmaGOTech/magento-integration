<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Client\RequestManager;
use Pragma\PragmaPayCore\Service\CreditMemoIncrementIdGenerator;

class RefundInfoBuilder implements BuilderInterface
{

    public function __construct(
        private readonly RequestManager $requestManager,
        private readonly CreditMemoIncrementIdGenerator $creditMemoIncrementIdGenerator,
    ) {
    }

    public function build(array $buildSubject): array
    {
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();

        $creditMemo = $payment->getCreditmemo();

        if (!$creditMemo) {
            throw new LocalizedException(__('Credit memo is required for refund.'));
        }

        return [
            'body' => [
                'itemId' => $payment->getAdditionalInformation(
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_ITEM_ID
                ),
                'partnerUpdateId' => $creditMemo->getIncrementId() ?? $this->creditMemoIncrementIdGenerator->getNextCreditMemoIncrementId((int)$creditMemo->getStoreId()),
                'value' => [
                    'amount' => $this->requestManager->formatAmount($creditMemo->getGrandTotal()),
                    'currency' => $creditMemo->getOrderCurrencyCode(),
                ],
            ]
        ];
    }
}
