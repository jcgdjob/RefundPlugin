<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class OrderRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $orderId;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, int $orderId, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function orderId(): int
    {
        return $this->orderId;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
