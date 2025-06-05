<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use Ramsey\Uuid\Uuid;

class GenerateUuid5
{
    public function execute(string $value): string
    {
        return Uuid::uuid5(
            Uuid::NAMESPACE_DNS,
            $value
        )->toString();
    }
}
