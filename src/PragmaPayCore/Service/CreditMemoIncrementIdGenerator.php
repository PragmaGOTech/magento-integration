<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use Magento\SalesSequence\Model\Manager as SequenceManager;

class CreditMemoIncrementIdGenerator
{
    private SequenceManager $sequenceManager;

    public function __construct(SequenceManager $sequenceManager)
    {
        $this->sequenceManager = $sequenceManager;
    }

    public function getNextCreditMemoIncrementId(int $storeId): string
    {
        // Get the sequence for the credit memo entity
        $sequence = $this->sequenceManager->getSequence('creditmemo', $storeId);

        // Generate the next increment ID
        return $sequence->getNextValue();
    }
}
