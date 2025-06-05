<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Validator;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Gateway\Validator\PragmaPayCreatePayment;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;


class PragmaPayCreatePaymentTest extends TestCase
{
    private PragmaPayCreatePayment $validator;

    protected function setUp(): void
    {
        // Mock the ResultInterfaceFactory dependency
        $resultFactory = $this->createMock(ResultInterfaceFactory::class);

        // Pass the mock to the constructor
        $this->validator = new PragmaPayCreatePayment($resultFactory);

    }

    public function testIsSuccessfulTransactionReturnsTrueForValidResponse(): void
    {
        // Arrange
        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_REDIRECT_URL_FIELD => 'https://example.com',
            PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD => 'payment123',
        ];

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsSuccessfulTransactionReturnsFalseForMissingRedirectUrlField(): void
    {
        // Arrange
        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD => 'payment123',
        ]; // Missing API_RESPONSE_REDIRECT_URL_FIELD

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_REDIRECT_URL_FIELD => 'https://example.com',
        ]; // Missing API_RESPONSE_PAYMENT_ID_FIELD

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsSuccessfulTransactionReturnsFalseForEmptyResponse(): void
    {
        // Arrange
        $response = []; // Empty response

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertFalse($result);
    }
}
