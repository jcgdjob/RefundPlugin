<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CreditMemoUnitGeneratorInterface */
    private $orderItemUnitCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $shipmentCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $itemCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $orderCreditMemoUnitGenerator;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    /** @var CurrentDateTimeProviderInterface */
    private $currentDateTimeProvider;

    /** @var CreditMemoIdentifierGeneratorInterface */
    private $uuidCreditMemoIdentifierGenerator;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $itemCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $orderCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        CreditMemoIdentifierGeneratorInterface $uuidCreditMemoIdentifierGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemUnitCreditMemoUnitGenerator = $orderItemUnitCreditMemoUnitGenerator;
        $this->shipmentCreditMemoUnitGenerator = $shipmentCreditMemoUnitGenerator;
        $this->itemCreditMemoUnitGenerator = $itemCreditMemoUnitGenerator;
        $this->orderCreditMemoUnitGenerator = $orderCreditMemoUnitGenerator;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
        $this->currentDateTimeProvider = $currentDateTimeProvider;
        $this->uuidCreditMemoIdentifierGenerator = $uuidCreditMemoIdentifierGenerator;
    }

    public function generate(
        string $orderNumber,
        int $total,
        array $units,
        array $shipments,
        array $items,
        array $orders,
        string $comment
    ): CreditMemoInterface {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        if ($order === null) {
            throw OrderNotFound::withNumber($orderNumber);
        }

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $creditMemoUnits = [];

        /** @var UnitRefundInterface $unit */
        foreach ($units as $unit) {
            $creditMemoUnits[] = $this->orderItemUnitCreditMemoUnitGenerator
                ->generate($unit->id(), $unit->total())
                ->serialize()
            ;
        }

        /** @var UnitRefundInterface $shipment */
        foreach ($shipments as $shipment) {
            $creditMemoUnits[] = $this->shipmentCreditMemoUnitGenerator
                ->generate($shipment->id(), $shipment->total())
                ->serialize()
            ;
        }

        /** @var UnitRefundInterface $item */
        foreach ($items as $item) {
            $creditMemoUnits[] = $this->itemCreditMemoUnitGenerator
                ->generate($item->id(), $item->total())
                ->serialize()
            ;
        }

        /** @var UnitRefundInterface $order */
        foreach ($orders as $o) {
            $creditMemoUnits[] = $this->orderCreditMemoUnitGenerator
                ->generate($o->id(), $o->total())
                ->serialize()
            ;
        }

        return new CreditMemo(
            $this->uuidCreditMemoIdentifierGenerator->generate(),
            $this->creditMemoNumberGenerator->generate(),
            $orderNumber,
            $total,
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            new CreditMemoChannel($channel->getCode(), $channel->getName(), $channel->getColor()),
            $creditMemoUnits,
            $comment,
            $this->currentDateTimeProvider->now()
        );
    }
}
