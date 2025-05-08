<?php
declare(strict_types=1);

namespace Pragma\PragmaPayAdminUi\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Pragma\PragmaPayCore\Api\DetailsConfigProviderInterface;

class DetailsConfigProvider implements DetailsConfigProviderInterface
{
    public const DETAILS_TITLE = 'pragma_payment/general/title';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function getTitle(int $storeId): string
    {
        return (string)$this->scopeConfig->getValue(
            self::DETAILS_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
