<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Api\Data;

interface NotificationItemInterface
{
    /**
     * @return string|null
     */
    public function getPartnerItemId(): ?string;

    /**
     * @param string|null $value
     * @return void
     */
    public function setPartnerItemId(?string $value): void;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setStatus(string $value): void;

    /**
     * @return \Pragma\PragmaPayWebApi\Api\Data\NotificationItemValueInterface
     */
    public function getValue(): NotificationItemValueInterface;

    /**
     * @param \Pragma\PragmaPayWebApi\Api\Data\NotificationItemValueInterface $value
     * @return void
     */
    public function setValue(NotificationItemValueInterface $value): void;
}
