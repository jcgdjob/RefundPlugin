<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class ItemRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $itemId;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, int $itemId, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->itemId = $itemId;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function itemId(): int
    {
        return $this->itemId;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
