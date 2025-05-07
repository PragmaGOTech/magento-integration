<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Model;

use Magento\Framework\DataObject;
use Pragma\PragmaPayWebApi\Api\Data\NotificationItemValueInterface;

class NotificationItemValue extends DataObject implements NotificationItemValueInterface
{
    public function getFormat(): string
    {
        return $this->getData('format');
    }

    public function setFormat(string $value): void
    {
        $this->setData('format', $value);
    }

    public function getAmount(): int
    {
        return (int) $this->getData('amount');
    }

    public function setAmount(int $value): void
    {
        $this->setData('amount', $value);
    }

    public function getCurrency(): string
    {
        return $this->getData('currency');
    }

    public function setCurrency(string $value): void
    {
        $this->setData('currency', $value);
    }
}
