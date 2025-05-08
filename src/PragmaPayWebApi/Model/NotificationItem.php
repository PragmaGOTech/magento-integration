<?php

declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Model;

use Magento\Framework\DataObject;
use Pragma\PragmaPayWebApi\Api\Data\NotificationItemInterface;
use Pragma\PragmaPayWebApi\Api\Data\NotificationItemValueInterface;

class NotificationItem extends DataObject implements NotificationItemInterface
{
    public function getPartnerItemId(): ?string
    {
        return $this->getData('partner_item_id');
    }

    public function setPartnerItemId(?string $value): void
    {
        $this->setData('partner_item_id', $value);
    }

    public function getStatus(): string
    {
        return $this->getData('status');
    }

    public function setStatus(string $value): void
    {
        $this->setData('status', $value);
    }

    public function getValue(): NotificationItemValueInterface
    {
        return $this->getData('value');
    }

    public function setValue(NotificationItemValueInterface $value): void
    {
        $this->setData('value', $value);
    }
}
