<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCalculator\Block\Widget;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;
use Pragma\PragmaPayCalculator\Model\CalculatorApiConfig;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class ProductCalculatorWidget extends AbstractCalculator implements BlockInterface
{
    private ProductRepositoryInterface $productRepository;

    private CalculatorApiConfig $calculatorApiConfig;

    private Registry $registry;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        CalculatorApiConfig $calculatorApiConfig,
        StoreManagerInterface $storeManager,
        PragmaConnectionConfigProviderInterface $connectionConfigProvider,
        Registry $registry,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->calculatorApiConfig = $calculatorApiConfig;
        $this->registry = $registry;
        parent::__construct($context, $storeManager, $connectionConfigProvider, $data);
    }

    public function getProductPrice(): ?int
    {
        return $this->calculatorApiConfig->prepareAmount((float)(($nullsafeVariable1 = $this->getProduct()) ? $nullsafeVariable1->getFinalPrice() : null));
    }

    private function getProduct(): ?ProductInterface
    {
        $productId = $this->getData('product_id');
        if ($productId) {
            try {
                return $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $exception) {
                return null;
            }
        }

        return $this->registry->registry('current_product');
    }
}
