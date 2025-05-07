<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use Magento\SalesSequence\Model\Manager as SequenceManager;

class CreditMemoIncrementIdGenerator
{
    public function __construct(
        private readonly SequenceManager $sequenceManager
    ) {
    }

    public function getNextCreditMemoIncrementId(int $storeId): string
    {
        // Get the sequence for the credit memo entity
        $sequence = $this->sequenceManager->getSequence('creditmemo', $storeId);

        // Generate the next increment ID
        return $sequence->getNextValue();
    }
}
