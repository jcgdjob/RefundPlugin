<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class OrderItemRefund implements UnitRefundInterface
{
    /** @var int */
    private $itemId;

    /** @var int */
    private $total;

    public function __construct(int $itemId, int $total)
    {
        $this->itemId = $itemId;
        $this->total = $total;
    }

    public function id(): int
    {
        return $this->itemId;
    }

    public function total(): int
    {
        return $this->total;
    }
}
