<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\OrderItemRefund;
use Sylius\RefundPlugin\Model\OrderRefund;

final class RefundUnits
{
    /** @var string */
    private $orderNumber;

    /** @var array|OrderItemUnitRefund[] */
    private $units;

    /** @var array|ShipmentRefund[] */
    private $shipments;

    private $items;
    private $orders;

    /** @var int */
    private $paymentMethodId;

    /** @var string */
    private $comment;

    public function __construct(string $orderNumber, array $units, array $shipments, array $items, array $orders, int $paymentMethodId, string $comment)
    {
        $this->orderNumber = $orderNumber;
        $this->units = $units;
        $this->shipments = $shipments;
        $this->items = $items;
        $this->orders = $orders;
        $this->paymentMethodId = $paymentMethodId;
        $this->comment = $comment;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    /** @return array|OrderItemUnitRefund[] */
    public function units(): array
    {
        return $this->units;
    }

    /** @return array|ShipmentRefund[] */
    public function shipments(): array
    {
        return $this->shipments;
    }

    public function items(): array
    {
        return $this->items;
    }
    public function orders(): array
    {
        return $this->orders;
    }

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
    }

    public function comment(): string
    {
        return $this->comment;
    }
}
