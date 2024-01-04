<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Webmozart\Assert\Assert;

final class RemainingTotalProvider implements RemainingTotalProviderInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    /** @var RepositoryInterface */
    private $orderItemRepository;

    /** @var RepositoryInterface */
    private $orderRepository;

    /** @var RepositoryInterface */
    private $adjustmentRepository;

    /** @var RepositoryInterface */
    private $refundRepository;

    public function __construct(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $orderItemRepository,
        RepositoryInterface $orderRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository
    ) {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->adjustmentRepository = $adjustmentRepository;
        $this->refundRepository = $refundRepository;
    }

    public function getTotalLeftToRefund(int $id, RefundType $type): int
    {
        if ($type->equals(RefundType::orderItem())) {
            /** @var OrderItemInterface $orderItem */
            $orderItem = $this->orderItemRepository->find($id);
            Assert::notNull($orderItem);

            return $orderItem->getTotal();
        }

        if ($type->equals(RefundType::order())) {
            /** @var OrderInterface $order */
            $order = $this->orderRepository->find($id);
            Assert::notNull($order);

            return $order->getTotal();
        }
        
        $unitTotal = $this->getRefundUnitTotal($id, $type);
        $refunds = $this->refundRepository->findBy(['refundedUnitId' => $id, 'type' => $type->__toString()]);

        if (count($refunds) === 0) {
            return $unitTotal;
        }

        $refundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $refundedTotal += $refund->getAmount();
        }

        return $unitTotal - $refundedTotal;
    }

    private function getRefundUnitTotal(int $id, RefundType $refundType): int
    {
        if ($refundType->equals(RefundType::orderItemUnit())) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitRepository->find($id);
            Assert::notNull($orderItemUnit);

            return $orderItemUnit->getTotal();
        }

        if ($refundType->equals(RefundType::shipment())) {
            /** @var AdjustmentInterface $shipment */
            $shipment = $this->adjustmentRepository->findOneBy([
                'id' => $id,
                'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT,
            ]);
            Assert::notNull($shipment);

            return $shipment->getAmount();
        }

        if ($refundType->equals(RefundType::orderItem())) {
            /** @var OrderItemInterface $orderItem */
            $orderItem = $this->orderItemRepository->find($id);
            Assert::notNull($orderItem);

            return $orderItem->getTotal();
        }

        if ($refundType->equals(RefundType::order())) {
            /** @var OrderInterface $order */
            $order = $this->orderRepository->find($id);
            Assert::notNull($order);

            return $order->getTotal();
        }
    }
}
