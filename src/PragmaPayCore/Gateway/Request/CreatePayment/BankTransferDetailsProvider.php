<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request\CreatePayment;

use Pragma\PragmaPayCore\Api\DetailsConfigProviderInterface;
use Pragma\PragmaPayCore\Client\RequestManager;

class BankTransferDetailsProvider
{
    public function __construct(
        private readonly DetailsConfigProviderInterface $configProvider,
        private readonly RequestManager $requestManager,
    ) {
    }

    public function execute(int $storeId): array
    {
        $payee = [
            'email' => $this->configProvider->getEmail($storeId),
        ];

        $registrationNumber = $this->requestManager->formatRegistrationNumber(
            $this->configProvider->getRegistrationNumberCountry($storeId),
            $this->configProvider->getRegistrationNumber($storeId)
        );
        if ($registrationNumber !== null) {
            $payee['registrationNumber'] = $registrationNumber;
        }


        $phoneData = $this->requestManager->formatPhone($this->configProvider->getPhone($storeId));
        if ($phoneData) {
            $payee['phone'] = $phoneData;
        }
        return [
            'bankTransferDetails' => [
                'payee' => $payee,
                'payeeBankAccount' => [
                    'iban' => $this->configProvider->getIban($storeId),
                ]
            ],
            'merchant' => $payee,
        ];
    }
}
