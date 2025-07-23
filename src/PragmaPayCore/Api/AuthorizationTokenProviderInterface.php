<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

interface AuthorizationTokenProviderInterface
{
    public function getAccessToken(?int $storeId): ?string;
}
