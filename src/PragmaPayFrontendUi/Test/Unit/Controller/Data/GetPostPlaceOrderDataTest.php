<?php

declare(strict_types=1);

namespace Pragma\PragmaPayFrontendUi\Test\Unit\Controller\Data;

use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface as PragmaConfig;
use Pragma\PragmaPayFrontendUi\Controller\Data\GetPostPlaceOrderData;

class GetPostPlaceOrderDataTest extends TestCase
{
    private ResultFactory $resultFactory;
    private Session $checkoutSession;
    private UrlInterface $url;
    private Json $jsonResult;
    private GetPostPlaceOrderData $controller;

    protected function setUp(): void
    {
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->checkoutSession = $this->createMock(Session::class);
        $this->url = $this->createMock(UrlInterface::class);
        $this->jsonResult = $this->createMock(Json::class);

        $this->resultFactory->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($this->jsonResult);

        $this->controller = new GetPostPlaceOrderData(
            $this->resultFactory,
            $this->checkoutSession,
            $this->url
        );
    }

    public function testExecuteWithRedirectUrl(): void
    {
        // Arrange
        $redirectUrl = 'https://example.com/redirect';

        $payment = $this->createMock(Payment::class);
        $payment->method('getAdditionalInformation')->willReturn([
            PragmaConfig::ADDITION_KEY_REDIRECT_URL => $redirectUrl
        ]);

        $order = $this->createMock(Order::class);
        $order->method('getPayment')->willReturn($payment);

        $this->checkoutSession->method('getLastRealOrder')->willReturn($order);

        $expectedData = [
            'success' => true,
            PragmaConfig::REDIRECT_URI_FIELD => $redirectUrl
        ];

        $this->jsonResult->expects($this->once())->method('setData')->with($expectedData)->willReturnSelf();

        // Act
        $result = $this->controller->execute();

        // Assert
        $this->assertSame($this->jsonResult, $result);
    }

    public function testExecuteWithoutRedirectUrl(): void
    {
        // Arrange
        $successUrl = 'https://example.com/success';

        $payment = $this->createMock(Payment::class);
        $payment->method('getAdditionalInformation')->willReturn([]);

        $order = $this->createMock(Order::class);
        $order->method('getPayment')->willReturn($payment);

        $this->checkoutSession->method('getLastRealOrder')->willReturn($order);
        $this->url->method('getUrl')->with('checkout/onepage/success')->willReturn($successUrl);

        $expectedData = [
            'success' => true,
            PragmaConfig::REDIRECT_URI_FIELD => $successUrl
        ];

        $this->jsonResult->expects($this->once())->method('setData')->with($expectedData)->willReturnSelf();

        // Act
        $result = $this->controller->execute();

        // Assert
        $this->assertSame($this->jsonResult, $result);
    }

    public function testExecuteHandlesException(): void
    {
        // Arrange
        $this->checkoutSession->method('getLastRealOrder')->willThrowException(new \Exception('Error occurred'));

        $expectedData = [
            'success' => false,
            'message' => 'Error occurred'
        ];

        $this->jsonResult->expects($this->once())->method('setData')->with($expectedData)->willReturnSelf();

        // Act
        $result = $this->controller->execute();

        // Assert
        $this->assertSame($this->jsonResult, $result);
    }
}
