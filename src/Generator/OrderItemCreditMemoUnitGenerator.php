<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Webmozart\Assert\Assert;

final class OrderItemCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderItemRepository;

    public function __construct(RepositoryInterface $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    public function generate(int $unitId, int $amount = null): CreditMemoUnitInterface
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->orderItemRepository->find($unitId);
        Assert::notNull($orderItem);
        Assert::lessThanEq($amount, $orderItem->getTotal());

        /** @var OrderItemInterface $orderItem */
        $total = $orderItem->getTotal();

        if ($amount === $total) {
            return new CreditMemoUnit(
                $orderItem->getProductName(),
                $total,
                $orderItem->getTaxTotal()
            );
        }

        $taxTotal = (int) ($orderItem->getTaxTotal() * ($amount / $total));

        return new CreditMemoUnit(
            $orderItem->getProductName(),
            $amount,
            $taxTotal
        );
    }
}
