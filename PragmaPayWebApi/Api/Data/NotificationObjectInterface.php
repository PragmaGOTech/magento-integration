<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Api\Data;

interface NotificationObjectInterface
{
    /**
     * @return string
     */
    public function getPaymentId(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setPaymentId(string $value): void;

    /**
     * @return \Pragma\PragmaPayWebApi\Api\Data\NotificationRepaymentPeriodInterface
     */
    public function getRepaymentPeriod(): NotificationRepaymentPeriodInterface;

    /**
     * @param \Pragma\PragmaPayWebApi\Api\Data\NotificationRepaymentPeriodInterface $value
     * @return void
     */
    public function setRepaymentPeriod(NotificationRepaymentPeriodInterface $value): void;

    /**
     * @return \Pragma\PragmaPayWebApi\Api\Data\NotificationItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param \Pragma\PragmaPayWebApi\Api\Data\NotificationItemInterface[] $value
     * @return void
     */
    public function setItems(array $value): void;

    /**
     * @return int
     */
    public function getTimestamp(): int;

    /**
     * @param int $timestamp
     * @return void
     */
    public function setTimestamp(int $timestamp): void;
}
