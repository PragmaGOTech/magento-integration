<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Service;

use DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Service\CustomerTransactionHistoryProvider;

class CustomerTransactionHistoryProviderTest extends TestCase
{
    private CustomerTransactionHistoryProvider $transactionHistoryProvider;
    private MockObject $orderCollectionFactory;
    private MockObject $timezone;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->orderCollectionFactory = $this->createMock(CollectionFactory::class);
        $this->timezone = $this->createMock(TimezoneInterface::class);

        // Arrange: Instantiate the CustomerTransactionHistoryProvider
        $this->transactionHistoryProvider = new CustomerTransactionHistoryProvider(
            $this->orderCollectionFactory,
            $this->timezone
        );
    }

    public function testExecuteReturnsOrdersGroupedByMonth(): void
    {
        // Arrange
        $customerEmail = 'test@example.com';
        $storeId = 1;

        $order1 = $this->createMock(\Magento\Sales\Model\Order::class);
        $order1->method('getCreatedAt')->willReturn('2023-10-01 12:00:00');
        $order1->method('getGrandTotal')->willReturn(100.0);
        $order1->method('getTotalRefunded')->willReturn(0);

        $order2 = $this->createMock(\Magento\Sales\Model\Order::class);
        $order2->method('getCreatedAt')->willReturn('2023-10-15 12:00:00');
        $order2->method('getGrandTotal')->willReturn(200.0);
        $order2->method('getTotalRefunded')->willReturn(50.0);

        $orderCollection = $this->createMock(Collection::class);
        $orderCollection->method('addFieldToSelect')->willReturnSelf();
        $orderCollection->method('addFieldToFilter')->willReturnSelf();
        $orderCollection->method('setOrder')->willReturnSelf();
        $orderCollection->method('getIterator')->willReturn(new \ArrayIterator([$order1, $order2]));

        $this->orderCollectionFactory->method('create')->willReturn($orderCollection);

        $this->timezone->method('date')->willReturnCallback(function ($date) {
            return new DateTime($date->format('Y-m-d H:i:s'));
        });

        // Act
        $result = $this->transactionHistoryProvider->execute($customerEmail, $storeId);

        // Assert
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('2023-10-01', $result);
        $this->assertSame(300.0, $result['2023-10-01']['total_amount']);
        $this->assertSame(2, $result['2023-10-01']['order_count']);
        $this->assertSame(1, $result['2023-10-01']['refund_count']);
        $this->assertSame(50.0, $result['2023-10-01']['total_refunded']);
    }

    public function testExecuteHandlesEmptyOrderCollection(): void
    {
        // Arrange
        $customerEmail = 'test@example.com';
        $storeId = 1;

        $orderCollection = $this->createMock(Collection::class);
        $orderCollection->method('addFieldToSelect')->willReturnSelf();
        $orderCollection->method('addFieldToFilter')->willReturnSelf();
        $orderCollection->method('setOrder')->willReturnSelf();
        $orderCollection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $this->orderCollectionFactory->method('create')->willReturn($orderCollection);

        // Act
        $result = $this->transactionHistoryProvider->execute($customerEmail, $storeId);

        // Assert
        $this->assertEmpty($result);
    }
}
