<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefunding;
use Sylius\RefundPlugin\Exception\RefundUnitsNotBelongToOrder;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsBelongToOrderValidatorInterface;

final class RefundUnitsCommandValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator,
        RefundUnitsBelongToOrderValidatorInterface $refundUnitsBelongToOrderValidator,
    ): void {
        $this->beConstructedWith(
            $orderRefundingAvailabilityChecker,
            $refundAmountValidator,
            $refundUnitsBelongToOrderValidator,
        );
    }

    function it_throws_exception_when_order_is_not_available_for_refund(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000001')->willReturn(false);

        $refundUnits = new RefundUnits('000001', [], 1, '');

        $this
            ->shouldThrow(OrderNotAvailableForRefunding::class)
            ->during('validate', [$refundUnits])
        ;
    }

    function it_throws_exception_when_order_item_units_amount_is_not_valid(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator,
        RefundUnitsBelongToOrderValidatorInterface $refundUnitsBelongToOrderValidator,
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000001')->willReturn(true);

        $orderItemUnitRefund = new OrderItemUnitRefund(1, 10);

        $refundUnits = new RefundUnits('000001', [$orderItemUnitRefund], 1, '');

        $refundAmountValidator
            ->validateUnits([$orderItemUnitRefund])
            ->willThrow(InvalidRefundAmount::class)
        ;

        $refundUnitsBelongToOrderValidator
            ->validateUnits([$orderItemUnitRefund], '000001')
            ->shouldBeCalled()
        ;

        $this->shouldThrow(InvalidRefundAmount::class)->during('validate', [$refundUnits]);
    }

    function it_throws_exception_when_order_item_units_do_not_belong_to_an_order(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator,
        RefundUnitsBelongToOrderValidatorInterface $refundUnitsBelongToOrderValidator,
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000001')->willReturn(true);

        $orderItemUnitRefund = new OrderItemUnitRefund(1, 10);

        $refundUnits = new RefundUnits('000001', [$orderItemUnitRefund], 1, '');

        $refundAmountValidator
            ->validateUnits([$orderItemUnitRefund])
            ->shouldNotBeCalled()
        ;

        $refundUnitsBelongToOrderValidator
            ->validateUnits([$orderItemUnitRefund], '000001')
            ->willThrow(RefundUnitsNotBelongToOrder::class)
        ;

        $this->shouldThrow(RefundUnitsNotBelongToOrder::class)->during('validate', [$refundUnits]);
    }

    function it_throws_exception_when_shipment_amount_is_not_valid(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator,
        RefundUnitsBelongToOrderValidatorInterface $refundUnitsBelongToOrderValidator,
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000001')->willReturn(true);

        $shipmentRefund = new ShipmentRefund(1, 10);

        $refundUnits = new RefundUnits('000001', [$shipmentRefund], 1, '');

        $refundAmountValidator
            ->validateUnits([$shipmentRefund])
            ->willThrow(InvalidRefundAmount::class)
        ;

        $refundUnitsBelongToOrderValidator
            ->validateUnits([$shipmentRefund], '000001')
            ->shouldBeCalled()
        ;

        $this->shouldThrow(InvalidRefundAmount::class)->during('validate', [$refundUnits]);
    }

    function it_throws_exception_when_shipment_does_not_belong_to_an_order(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator,
        RefundUnitsBelongToOrderValidatorInterface $refundUnitsBelongToOrderValidator,
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('000001')->willReturn(true);

        $shipmentRefund = new ShipmentRefund(1, 10);

        $refundUnits = new RefundUnits('000001', [$shipmentRefund], 1, '');

        $refundAmountValidator
            ->validateUnits([$shipmentRefund])
            ->shouldNotBeCalled()
        ;

        $refundUnitsBelongToOrderValidator
            ->validateUnits([$shipmentRefund], '000001')
            ->willThrow(RefundUnitsNotBelongToOrder::class)
        ;

        $this->shouldThrow(RefundUnitsNotBelongToOrder::class)->during('validate', [$refundUnits]);
    }
}
