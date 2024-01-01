<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Webmozart\Assert\Assert;

final class OrderCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderRepository;

    public function __construct(RepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function generate(int $unitId, int $amount = null): CreditMemoUnitInterface
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($unitId);
        Assert::notNull($order);
        Assert::lessThanEq($amount, $order->getTotal());

        /** @var OrderInterface $order */
        $total = $order->getTotal();

        if ($amount === $total) {
            return new CreditMemoUnit(
                'Order changes',//$order->getProductName(),
                $total,
                $order->getTaxTotal()
            );
        }

        $taxTotal = (int) ($order->getTaxTotal() * ($amount / $total));

        return new CreditMemoUnit(
            $order->getProductName(),
            $amount,
            $taxTotal
        );
    }
}
