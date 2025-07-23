<?php
declare(strict_types=1);

namespace Pragma\PragmaPayFrontendUi\Controller\Data;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order\Payment;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface AS PragmaConfig;

class GetPostPlaceOrderData implements HttpGetActionInterface
{
    private const SUCCESS_FIELD = 'success';


    private ResultFactory $resultFactory;

    private Session $checkoutSession;

    private UrlInterface $url;

    public function __construct(ResultFactory $resultFactory, Session $checkoutSession, UrlInterface $url)
    {
        $this->resultFactory = $resultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->url = $url;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            /** @var $payment Payment */
            $payment = $this->checkoutSession->getLastRealOrder()->getPayment();
            $paymentInformation = $payment->getAdditionalInformation();
            if (is_array($paymentInformation) &&
                array_key_exists(PragmaConfig::ADDITION_KEY_REDIRECT_URL, $paymentInformation)) {
                $returnData = [
                    self::SUCCESS_FIELD => true,
                    PragmaConfig::REDIRECT_URI_FIELD => $paymentInformation[PragmaConfig::ADDITION_KEY_REDIRECT_URL]
                ];
            } else {
                $returnData = [
                    self::SUCCESS_FIELD => true,
                    PragmaConfig::REDIRECT_URI_FIELD => $this->url->getUrl('checkout/onepage/success')
                ];
            }
        } catch (Exception $exception) {
            $returnData = [
                self::SUCCESS_FIELD => false,
                'message' => $exception->getMessage()
            ];
        }
        return $result->setData($returnData);
    }
}
