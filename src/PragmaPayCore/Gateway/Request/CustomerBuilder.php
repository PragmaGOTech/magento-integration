<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use LogicException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Pragma\PragmaPayCore\Client\RequestManager;

class CustomerBuilder implements BuilderInterface
{
    public function __construct(
        private readonly RequestManager $requestManager,
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
        $billingAddress = $buildPayment->getOrder()->getBillingAddress();
        $customerData = [
            'email' => $billingAddress->getEmail(),
            'firstName' => $billingAddress->getFirstname(),
            'lastName' => $billingAddress->getLastname(),
        ];

        $registrationNumber = $this->requestManager->formatRegistrationNumber(
            (string)$billingAddress->getCountryId(),
            (string)$billingAddress->getVatId()
        );
        if ($registrationNumber) {
            $customerData['registrationNumber'] = $registrationNumber;
        }

        $phoneData = $this->requestManager->formatPhone($billingAddress->getTelephone());
        if ($phoneData) {
            $customerData['phone'] = $phoneData;
        }

        return [
            'body' => [
                'customer' => $customerData
            ],
        ];
    }
}
