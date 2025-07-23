<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

use Pragma\PragmaPayCore\Exception\ApiException;

interface ApiClientInterface
{
    public function submit(
        string $actionUri,
        array $params,
        array $headers = [],
        string $method = 'POST'
    ): string;
}
