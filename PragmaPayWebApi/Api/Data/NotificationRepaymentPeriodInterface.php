<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Api\Data;

interface NotificationRepaymentPeriodInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setType(string $value): void;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setValue(string $value): void;
}
