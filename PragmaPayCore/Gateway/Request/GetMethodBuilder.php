<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class GetMethodBuilder implements BuilderInterface
{
    public function build(array $buildSubject): array
    {
        return [
            'method' => 'GET',
        ];
    }
}
