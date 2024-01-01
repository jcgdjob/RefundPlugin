<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

use Sylius\RefundPlugin\Exception\RefundTypeNotResolved;

class RefundType
{
    public const ORDER_ITEM_UNIT = 'order_item_unit';
    public const SHIPMENT = 'shipment';

    public const ORDER_ITEM = 'order_item'; // item refund including overall changes / discount changes
    public const ORDER = 'order'; // order refund for overall changes / discount changes
    

    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::ORDER_ITEM_UNIT, self::SHIPMENT, self::ORDER_ITEM, self::ORDER])) {
            throw RefundTypeNotResolved::withType($value);
        }

        $this->value = $value;
    }

    public static function orderItemUnit(): self
    {
        return new self(self::ORDER_ITEM_UNIT);
    }

    public static function shipment(): self
    {
        return new self(self::SHIPMENT);
    }

    public static function orderItem(): self
    {
        return new self(self::ORDER_ITEM);
    }

    public static function order(): self
    {
        return new self(self::ORDER);
    }

    public function equals(self $refundType): bool
    {
        return $this->__toString() === $refundType->__toString();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
