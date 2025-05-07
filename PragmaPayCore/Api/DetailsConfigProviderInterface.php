<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

interface DetailsConfigProviderInterface
{
    public function getTitle(int $storeId): string;
}
