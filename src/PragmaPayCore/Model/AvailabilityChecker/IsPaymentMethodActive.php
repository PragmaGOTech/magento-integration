<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\AvailabilityChecker;

use Magento\Quote\Model\Quote;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class IsPaymentMethodActive implements AvailabilityCheckerInterface
{
    private PragmaConnectionConfigProviderInterface $configProvider;

    public function __construct(PragmaConnectionConfigProviderInterface $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function execute(Quote $quote): bool
    {
        return $this->configProvider->isActive((int)$quote->getStoreId());
    }
}
