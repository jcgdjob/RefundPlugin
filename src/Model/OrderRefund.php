<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class OrderRefund implements UnitRefundInterface
{
    /** @var int */
    private $orderId;

    /** @var int */
    private $total;

    public function __construct(int $orderId, int $total)
    {
        $this->orderId = $orderId;
        $this->total = $total;
    }

    public function id(): int
    {
        return $this->orderId;
    }

    public function total(): int
    {
        return $this->total;
    }
}
