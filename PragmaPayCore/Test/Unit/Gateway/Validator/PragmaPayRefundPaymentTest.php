<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Validator;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Gateway\Validator\PragmaPayRefundPayment;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class PragmaPayRefundPaymentTest extends TestCase
{
    private PragmaPayRefundPayment $validator;

    protected function setUp(): void
    {
        // Mock the ResultInterfaceFactory dependency
        $resultFactory = $this->createMock(ResultInterfaceFactory::class);
        // Arrange: Instantiate the PragmaPayRefundPayment class
        $this->validator = new PragmaPayRefundPayment($resultFactory);
    }

    public function testIsSuccessfulTransactionReturnsTrueForValidResponse(): void
    {
        // Arrange
        $response = [
            'bankAccount' => '123456789',
            'id' => 'txn123',
            'link' => 'https://example.com',
            'validTill' => '2023-12-31',
        ];

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsSuccessfulTransactionReturnsFalseForInvalidResponse(): void
    {
        // Arrange
        $response = [
            'bankAccount' => '123456789',
            'id' => 'txn123',
        ]; // Missing 'link' and 'validTill'

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertFalse($result);
    }
}
