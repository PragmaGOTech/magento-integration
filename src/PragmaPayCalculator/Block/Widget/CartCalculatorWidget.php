<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCalculator\Block\Widget;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;
use Pragma\PragmaPayCalculator\Model\CalculatorApiConfig;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Throwable;

class CartCalculatorWidget extends AbstractCalculator implements BlockInterface
{
    public function __construct(
        Context $context,
        private readonly CalculatorApiConfig $calculatorApiConfig,
        StoreManagerInterface $storeManager,
        PragmaConnectionConfigProviderInterface $connectionConfigProvider,
        private readonly CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $storeManager, $connectionConfigProvider, $data);
    }

    public function getCartTotal(): ?int
    {
        try {
            return $this->calculatorApiConfig->prepareAmount(
                (float)$this->checkoutSession->getQuote()->getGrandTotal()
            );
        } catch (Throwable) {
            return null;
        }
    }
}
