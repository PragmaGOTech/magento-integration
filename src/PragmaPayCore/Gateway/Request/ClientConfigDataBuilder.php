<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class ClientConfigDataBuilder implements BuilderInterface
{
    public const CLIENT_HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    public function build(array $buildSubject): array
    {
        return [
            'headers' => self::CLIENT_HEADERS,
        ];
    }
}
