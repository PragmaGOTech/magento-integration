<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Http;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Gateway\Http\TransferFactory;

class TransferFactoryTest extends TestCase
{
    private TransferFactory $transferFactory;
    private MockObject $transferBuilder;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->transferBuilder = $this->createMock(TransferBuilder::class);

        // Arrange: Instantiate the TransferFactory
        $this->transferFactory = new TransferFactory($this->transferBuilder);
    }

    public function testCreateReturnsTransferInterface(): void
    {
        // Arrange
        $request = [
            'uri' => 'https://api.example.com',
            'body' => ['key' => 'value'],
            'headers' => ['Authorization' => 'Bearer token'],
            'method' => Request::HTTP_METHOD_POST,
        ];

        $transferMock = $this->createMock(TransferInterface::class);

        $this->transferBuilder->expects($this->once())
            ->method('setUri')
            ->with($request['uri'])
            ->willReturnSelf();

        $this->transferBuilder->expects($this->once())
            ->method('setBody')
            ->with($request['body'])
            ->willReturnSelf();

        $this->transferBuilder->expects($this->once())
            ->method('setHeaders')
            ->with($request['headers'])
            ->willReturnSelf();

        $this->transferBuilder->expects($this->once())
            ->method('setMethod')
            ->with($request['method'])
            ->willReturnSelf();

        $this->transferBuilder->expects($this->once())
            ->method('build')
            ->willReturn($transferMock);

        // Act
        $result = $this->transferFactory->create($request);

        // Assert
        $this->assertInstanceOf(TransferInterface::class, $result);
        $this->assertSame($transferMock, $result);
    }
}
