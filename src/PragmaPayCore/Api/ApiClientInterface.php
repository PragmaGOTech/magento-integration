<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

use Pragma\PragmaPayCore\Exception\ApiException;

interface ApiClientInterface
{
    /**
     * @param string $actionUri
     * @param array $params
     * @param array $headers
     * @param string $method
     * @return string
     * @throws ApiException
     */
    public function submit(
        string $actionUri,
        array $params,
        array $headers = [],
        string $method = 'POST'
    ): string;
}
