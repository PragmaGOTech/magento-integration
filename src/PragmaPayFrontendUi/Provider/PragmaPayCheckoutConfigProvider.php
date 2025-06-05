<?php
declare(strict_types=1);

namespace Pragma\PragmaPayFrontendUi\Provider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class PragmaPayCheckoutConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private readonly PragmaConnectionConfigProviderInterface $connectionConfigProvider,
        private readonly StoreManagerInterface $storeManager,
        private readonly Repository $assetRepository,
        private readonly ResolverInterface $resolver
    ) {
    }

    public function getConfig(): array
    {
        try {
            $isActive = $this->connectionConfigProvider->isActive(
                (int)$this->storeManager->getStore()->getId()
            );
        } catch (NoSuchEntityException) {
            $isActive = false;
        }

        return [
            'payment' => [
                'pragmaPayment' => [
                    'isActive' => $isActive,
                    'logoSrc' => $this->assetRepository->getUrl('Pragma_PragmaPayFrontendUi::images/logo.png'),
                    'logoDarkSrc' => $this->assetRepository->getUrl('Pragma_PragmaPayFrontendUi::images/logo-dark.png'),
                    'language' => $this->getLanguage(),
                    'agreementText' => __('I consent to the ongoing transfer of my personal, commercial, and transactional account data in the store to PragmaGO S.A. by the seller (operating the online store <strong>%1</strong>), which is necessary for entering into and performing financing agreements with PragmaGO S.A.', $this->storeManager->getStore()->getName()),
                ]
            ]
        ];
    }

    private function getLanguage(): string
    {
        return current(explode('_', $this->resolver->getLocale()));
    }
}
