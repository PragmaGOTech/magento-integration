<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\Method\Adapter;
use Magento\Quote\Model\Quote;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\AvailabilityCheckerInterface;

class IsPragmaPayActiveObserver implements ObserverInterface
{
    private AvailabilityCheckerInterface $availabilityChecker;

    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
    }

    public function execute(Observer $observer): void
    {
        /** @var DataObject $checkResult */
        $checkResult = $observer->getData('result');
        /** @var Adapter $methodInstance */
        $methodInstance = $observer->getData('method_instance');
        /** @var Quote $quote */
        $quote = $observer->getData('quote');
        if (
            $quote === null ||
            $methodInstance->getCode() !== PragmaConnectionConfigProviderInterface::METHOD_NAME ||
            $checkResult->getData('is_available') === false
        ) {
            return;
        }
        $checkResult->setData(
            'is_available',
            $this->availabilityChecker->execute($quote)
        );
    }
}
