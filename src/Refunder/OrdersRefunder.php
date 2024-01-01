<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\OrderRefunded;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\OrderRefund;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrdersRefunder implements RefunderInterface
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(RefundCreatorInterface $refundCreator, MessageBusInterface $eventBus)
    {
        $this->refundCreator = $refundCreator;
        $this->eventBus = $eventBus;
    }

    public function refundFromOrder(array $units, string $orderNumber): int
    {
        $refundedTotal = 0;

        foreach ($units as $unit) {
            $this->refundCreator->__invoke($orderNumber, $unit->id(), $unit->total(), RefundType::order());

            $refundedTotal += $unit->total();

            $this->eventBus->dispatch(new OrderRefunded($orderNumber, $unit->id(), $unit->total()));
        }

        return $refundedTotal;
    }
}
