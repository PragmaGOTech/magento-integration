<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Http;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class TransferFactory implements TransferFactoryInterface
{
    private TransferBuilder $transferBuilder;

    public function __construct(TransferBuilder $transferBuilder)
    {
        $this->transferBuilder = $transferBuilder;
    }

    public function create(array $request): TransferInterface
    {
        return $this->transferBuilder
            ->setUri($request['uri'] ?? '')
            ->setBody($request['body'] ?? [])
            ->setHeaders($request['headers'] ?? [])
            ->setMethod($request['method'] ?? Request::HTTP_METHOD_POST)
            ->build();
    }
}
