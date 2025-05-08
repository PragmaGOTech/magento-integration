<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Payment\Model\Method\Adapter;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\AvailabilityCheckerInterface;
use Pragma\PragmaPayCore\Observer\IsPragmaPayActiveObserver;

class IsPragmaPayActiveObserverTest extends TestCase
{
    private Observer|MockObject $observer;
    private AvailabilityCheckerInterface|MockObject $availabilityChecker;
    private DataObject|MockObject $dataObject;
    private Adapter|MockObject $adapter;
    private IsPragmaPayActiveObserver $isPragmaPayActiveObserver;

    protected function setUp(): void
    {
        $this->observer = $this->createMock(Observer::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->dataObject = $this->createMock(DataObject::class);
        $this->adapter = $this->createMock(Adapter::class);
        $quote = $this->createMock(Quote::class);

        $callMap = [
            ['result', null, $this->dataObject],
            ['method_instance', null, $this->adapter],
            ['quote', null, $quote],
        ];

        $this->observer->method('getData')->willReturnCallback(function ($key, $default = null) use ($callMap) {
            foreach ($callMap as [$expectedKey, $expectedDefault, $returnValue]) {
                if ($key === $expectedKey && $default === $expectedDefault) {
                    return $returnValue;
                }
            }
            return null;
        });

        $this->isPragmaPayActiveObserver = new IsPragmaPayActiveObserver($this->availabilityChecker);
    }

    public function testExecute(): void
    {
        $this->adapter->expects($this->once())->method('getCode')->willReturn('pragma_payment');
        $this->dataObject->expects($this->once())->method('getData')->with('is_available')->willReturn(true);
        $this->dataObject->expects($this->once())->method('setData')->with('is_available');
        $this->availabilityChecker->expects($this->once())->method('execute')->willReturn(true);

        $this->isPragmaPayActiveObserver->execute($this->observer);
    }

    public function testExecuteNotAvailable(): void
    {
        $this->adapter->expects($this->once())->method('getCode')->willReturn('pragma_payment');
        $this->dataObject->expects($this->once())->method('getData')->with('is_available')->willReturn(false);

        $this->assertNever();
    }

    public function testExecuteDifferentPaymentMethod(): void
    {
        $this->adapter->expects($this->once())->method('getCode')->willReturn('test_test');
        $this->dataObject->expects($this->never())->method('getData')->with('is_available')->willReturn(false);

        $this->assertNever();
    }

    private function assertNever(): void
    {
        $this->dataObject->expects($this->never())->method('setData')->with('is_available');
        $this->availabilityChecker->expects($this->never())->method('execute');

        $this->isPragmaPayActiveObserver->execute($this->observer);
    }
}
