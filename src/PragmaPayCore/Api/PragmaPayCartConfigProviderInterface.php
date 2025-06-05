<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

interface PragmaPayCartConfigProviderInterface
{
    public const MINIMUM_AMOUNT = 100;
    public const MAXIMUM_AMOUNT = 50000;

    public function getMinOrderTotal(int $storeId): float;
    public function getMaxOrderTotal(int $storeId): float;
}
