<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Validator;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Api\PragmaPaymentStatus;
use Pragma\PragmaPayCore\Gateway\Validator\PragmaPayGetPaymentStatus;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class PragmaPayGetPaymentStatusTest extends TestCase
{
    private PragmaPayGetPaymentStatus $validator;

    protected function setUp(): void
    {
        // Mock the ResultInterfaceFactory dependency
        $resultFactory = $this->createMock(ResultInterfaceFactory::class);
        // Arrange: Instantiate the PragmaPayGetPaymentStatus class
        $this->validator = new PragmaPayGetPaymentStatus($resultFactory);
    }

    public function testIsSuccessfulTransactionReturnsTrueForFinancedStatus(): void
    {
        // Arrange
        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD => [
                [
                    PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEM_STATUS_FIELD => PragmaPaymentStatus::STATUS_FINANCED,
                ],
            ],
        ];

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsSuccessfulTransactionReturnsFalseForMissingItemsField(): void
    {
        // Arrange
        $response = []; // Missing API_RESPONSE_ITEMS_FIELD

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsSuccessfulTransactionReturnsFalseForNonFinancedStatus(): void
    {
        // Arrange
        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD => [
                [
                    PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEM_STATUS_FIELD => 'PENDING', // Not STATUS_FINANCED
                ],
            ],
        ];

        // Act
        $result = $this->validator->isSuccessfulTransaction($response);

        // Assert
        $this->assertFalse($result);
    }
}
