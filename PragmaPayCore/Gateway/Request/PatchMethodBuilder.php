<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class PatchMethodBuilder implements BuilderInterface
{
    public function build(array $buildSubject): array
    {
        return [
            'method' => 'PATCH',
        ];
    }
}
