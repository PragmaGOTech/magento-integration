<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

class PragmaPayRefundPayment implements HandlerInterface
{
    public function __construct(
        private readonly CreditmemoRepositoryInterface $creditMemoRepository,
    ) {
    }

    public function handle(array $handlingSubject, array $response)
    {
        $paymentDataObject = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDataObject->getPayment();

        /** @var \Magento\Sales\Model\Order\Creditmemo| null $creditMemo */
        $creditMemo = $payment->getCreditmemo();
        if (!$creditMemo) {
            return;
        }


        $order = $creditMemo->getOrder();
        $creditMemo->setTransactionId($response['id']);
        $refundMessage = __(
            'The refund transaction %1 valid till %2 was processed on PragmaPay side',
            $response['id'],
            $response['validTill'],
        );
        $creditMemo->addComment($refundMessage);
        $order->addCommentToStatusHistory($refundMessage);

        if (isset($response['link'])) {
            $linkMessage = __(
                'Link for refunding: %1',
                $response['link'],
            );
            $creditMemo->addComment($linkMessage);
            $order->addCommentToStatusHistory($linkMessage);
        }

        $bankAccountMessage = __('Bank account for refunding: %1', $response['bankAccount']);
        $creditMemo->addComment($bankAccountMessage);
        $order->addCommentToStatusHistory($bankAccountMessage);


        $payment->setAdditionalInformation('pragma_refund_id', $response['id']);
        $payment->setAdditionalInformation('pragma_refund_link', $response['link'] ?? '');
        $payment->setAdditionalInformation('pragma_refund_title', $response['title'] ?? '');
        $payment->setAdditionalInformation('pragma_refund_valid_till', $response['validTill']);
        $payment->setAdditionalInformation('pragma_refund_bank_account', $response['bankAccount']);

        $payment->setShouldCloseParentTransaction(true);

        $this->creditMemoRepository->save($creditMemo);
    }
}
