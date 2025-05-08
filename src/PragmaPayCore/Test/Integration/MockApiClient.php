<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Integration;

use Pragma\PragmaPayCore\Api\ApiClientInterface;
use Magento\Framework\Serialize\Serializer\Json;

class MockApiClient implements ApiClientInterface
{
    public function __construct(
    ) {
    }

    public function submit(
        string $actionUri,
        array $params,
        array $headers = [],
        string $method = 'POST'
    ): string
    {
        $json = new Json();
        if ($actionUri === 'api/v2/partner/payment') {
            return $json->serialize([
                'url' => 'business.pragmago.pl/pragma-pay/test34432342',
                'items' => [
                    [
                        'itemId' => 'test_item_id',
                        'partnerItemId' => 'test_partner_item_id'
                    ]
                ],
                'paymentId' => 'test_payemnt_id'
            ]);
        }

        if ($actionUri === 'api/partner/authorize') {
            return $json->serialize(['token' => 'tokenuuid']);
        }

        throw new \InvalidArgumentException('Unsupported action URI: ' . $actionUri);
    }
}
