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
    protected StoreManagerInterface $storeManager;

    protected PragmaConnectionConfigProviderInterface $connectionConfigProvider;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        PragmaConnectionConfigProviderInterface $connectionConfigProvider,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->connectionConfigProvider = $connectionConfigProvider;
        parent::__construct($context, $data);
    }

    public function getPartnerKey(): string
    {
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $exception) {
            $storeId = 0;
        }
        return $this->connectionConfigProvider->getPartnerKey($storeId);
    }
}
