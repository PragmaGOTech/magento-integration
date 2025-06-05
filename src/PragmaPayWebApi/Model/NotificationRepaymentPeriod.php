<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Model;

use Magento\Framework\DataObject;
use Pragma\PragmaPayWebApi\Api\Data\NotificationRepaymentPeriodInterface;

class NotificationRepaymentPeriod extends DataObject implements NotificationRepaymentPeriodInterface
{
    public function getType(): string
    {
        return $this->getData('type');
    }

    public function setType(string $value): void
    {
        $this->setData('type', $value);
    }

    public function getValue(): string
    {
        return $this->getData('value');
    }

    public function setValue(string $value): void
    {
        $this->setData('value', $value);
    }
}
