<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

interface AuthorizationTokenProviderInterface
{
    /**
     * @param int|null $storeId
     * @return string|null
     */
    public function getAccessToken(?int $storeId): ?string;
}
