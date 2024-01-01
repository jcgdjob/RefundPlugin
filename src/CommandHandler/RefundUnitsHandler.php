<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundUnitsHandler
{
    /** @var RefunderInterface */
    private $orderUnitsRefunder;

    /** @var RefunderInterface */
    private $orderShipmentsRefunder;

    private $orderItemsRefunder;
    private $orderOrdersRefunder;

    /** @var MessageBusInterface */
    private $eventBus;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RefundUnitsCommandValidatorInterface */
    private $refundUnitsCommandValidator;

    public function __construct(
        RefunderInterface $orderUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        RefunderInterface $orderItemsRefunder,
        RefunderInterface $orderOrdersRefunder,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator
    ) {
        $this->orderUnitsRefunder = $orderUnitsRefunder;
        $this->orderShipmentsRefunder = $orderShipmentsRefunder;
        $this->orderItemsRefunder = $orderItemsRefunder;
        $this->orderOrdersRefunder = $orderOrdersRefunder;
        $this->eventBus = $eventBus;
        $this->orderRepository = $orderRepository;
        $this->refundUnitsCommandValidator = $refundUnitsCommandValidator;
    }

    public function __invoke(RefundUnits $command): void
    {
        $this->refundUnitsCommandValidator->validate($command);

        $orderNumber = $command->orderNumber();

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $refundedTotal = 0;
        $refundedTotal += $this->orderUnitsRefunder->refundFromOrder($command->units(), $orderNumber);
        $refundedTotal += $this->orderShipmentsRefunder->refundFromOrder($command->shipments(), $orderNumber);
        $refundedTotal += $this->orderItemsRefunder->refundFromOrder($command->items(), $orderNumber);
        $refundedTotal += $this->orderOrdersRefunder->refundFromOrder($command->orders(), $orderNumber);

        $this->eventBus->dispatch(new UnitsRefunded(
            $orderNumber,
            $command->units(),
            $command->shipments(),
            $command->items(),
            $command->orders(),
            $command->paymentMethodId(),
            $refundedTotal,
            $order->getCurrencyCode(),
            $command->comment()
        ));
    }
}
