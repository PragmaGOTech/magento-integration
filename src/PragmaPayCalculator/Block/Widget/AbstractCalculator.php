<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCalculator\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

abstract class AbstractCalculator extends Template implements BlockInterface
{
    public function __construct(
        Context $context,
        protected StoreManagerInterface $storeManager,
        protected PragmaConnectionConfigProviderInterface $connectionConfigProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getPartnerKey(): string
    {
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException) {
            $storeId = 0;
        }
        return $this->connectionConfigProvider->getPartnerKey($storeId);
    }
}
