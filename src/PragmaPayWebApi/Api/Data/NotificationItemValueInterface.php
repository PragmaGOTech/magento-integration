<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Api\Data;

interface NotificationItemValueInterface
{
    /**
     * @return string
     */
    public function getFormat(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setFormat(string $value): void;

    /**
     * @return int
     */
    public function getAmount(): int;

    /**
     * @param int $value
     * @return void
     */
    public function setAmount(int $value): void;

    /**
     * @return string
     */
    public function getCurrency(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setCurrency(string $value): void;
}
