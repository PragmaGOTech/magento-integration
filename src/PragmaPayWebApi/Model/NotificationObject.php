<?php
    declare(strict_types=1);

    namespace Pragma\PragmaPayWebApi\Model;

    use Magento\Framework\DataObject;
    use Pragma\PragmaPayWebApi\Api\Data\NotificationObjectInterface;
    use Pragma\PragmaPayWebApi\Api\Data\NotificationRepaymentPeriodInterface;

    class NotificationObject extends DataObject implements NotificationObjectInterface
    {
        public function getPaymentId(): string
        {
            return $this->getData('payment_id');
        }

        public function setPaymentId(string $value): void
        {
            $this->setData('payment_id', $value);
        }

        public function getRepaymentPeriod(): NotificationRepaymentPeriodInterface
        {
            return $this->getData('repayment_period');
        }

        public function setRepaymentPeriod(NotificationRepaymentPeriodInterface $value): void
        {
            $this->setData('repayment_period', $value);
        }

        public function getItems(): array
        {
            return $this->getData('items');
        }

        public function setItems(array $value): void
        {
            $this->setData('items', $value);
        }

        public function getTimestamp(): int
        {
            return (int)$this->getData('timestamp');
        }

        public function setTimestamp(int $timestamp): void
        {
            $this->setData('timestamp', $timestamp);
        }
    }
