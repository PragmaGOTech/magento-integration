<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use InvalidArgumentException;
use LogicException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Client\RequestManager;
use Pragma\PragmaPayCore\Gateway\Request\CustomerBuilder;

class CustomerBuilderTest extends TestCase
{
    private CustomerBuilder $customerBuilder;
    private RequestManager $requestManager;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->requestManager = $this->createMock(RequestManager::class);
        // Instantiate the CustomerBuilder with mocked dependencies
        $this->customerBuilder = new CustomerBuilder($this->requestManager);
    }

    public function testBuildWithValidData(): void
    {
        // Arrange
        $storeId = 1;
        $email = 'test@example.com';
        $firstName = 'John';
        $lastName = 'Doe';
        $telephone = '123456789';
        $countryId = 'US';
        $vatId = '12345';
        $formattedPhone = ['prefix' => '+11', 'phone'=>'23456789'];
        $formattedRegistrationNumber = ['country' => 'US', 'registrationNumber' => 'US12345'];

        $billingAddress = $this->createMock(OrderAddressInterface::class);
        $billingAddress->method('getEmail')->willReturn($email);
        $billingAddress->method('getFirstname')->willReturn($firstName);
        $billingAddress->method('getLastname')->willReturn($lastName);
        $billingAddress->method('getTelephone')->willReturn($telephone);
        $billingAddress->method('getCountryId')->willReturn($countryId);
        $billingAddress->method('getVatId')->willReturn($vatId);

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getBillingAddress')->willReturn($billingAddress);

        $orderPayment = $this->createMock(OrderPaymentInterface::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);
        $paymentDataObject->method('getPayment')->willReturn($orderPayment);

        $this->requestManager->method('formatPhone')->with($telephone)->willReturn($formattedPhone);
        $this->requestManager->method('formatRegistrationNumber')->with($countryId, $vatId)->willReturn($formattedRegistrationNumber);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->customerBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('customer', $result['body']);
        $this->assertSame([
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'registrationNumber' => $formattedRegistrationNumber,
            'phone' => $formattedPhone,
        ], $result['body']['customer']);
    }

    public function testBuildWithInvalidPaymentThrowsException(): void
    {
        // Arrange
        $buildSubject = ['payment' => null];

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');

        // Act
        $this->customerBuilder->build($buildSubject);
    }

    public function testBuildWithInvalidOrderPaymentThrowsException(): void
    {
        // Arrange
        $orderAdapter = $this->createMock(OrderAdapterInterface::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);
        $paymentDataObject->method('getPayment')->willReturn(null);

        $buildSubject = ['payment' => $paymentDataObject];

        // Assert
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Order payment should be provided.');

        // Act
        $this->customerBuilder->build($buildSubject);
    }
}
