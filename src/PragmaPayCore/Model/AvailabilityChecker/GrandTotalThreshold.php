<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\AvailabilityChecker;

use Magento\Quote\Model\Quote;
use Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface;

class GrandTotalThreshold implements AvailabilityCheckerInterface
{
    private PragmaPayCartConfigProviderInterface $cartConfigProvider;

    public function __construct(PragmaPayCartConfigProviderInterface $cartConfigProvider)
    {
        $this->cartConfigProvider = $cartConfigProvider;
    }

    public function execute(Quote $quote): bool
    {
        $storeId = (int)$quote->getStoreId();
        $grandTotal = (float)$quote->getBaseGrandTotal();
        $min = $this->cartConfigProvider->getMinOrderTotal($storeId);
        $max = $this->cartConfigProvider->getMaxOrderTotal($storeId);

        return $grandTotal >= $min && $grandTotal <= $max;
    }
}
