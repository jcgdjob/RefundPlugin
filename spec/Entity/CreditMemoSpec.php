<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;

final class CreditMemoSpec extends ObjectBehavior
{
    function let(): void
    {
        $creditMemoUnit = new CreditMemoUnit('Portal gun', 1000, 50);

        $this->beConstructedWith(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2018/07/00003333',
            '0000222',
            1000,
            'USD',
            'en_US',
            new CreditMemoChannel('WEB-US', 'United States', 'Linen'),
            [$creditMemoUnit->serialize()],
            'Comment',
            new \DateTime('01-01-2020 10:10:10')
        );
    }

    function it_implements_credit_memo_interface(): void
    {
        $this->shouldImplement(CreditMemoInterface::class);
    }

    function it_has_id(): void
    {
        $this->getId()->shouldReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
    }

    function it_has_number(): void
    {
        $this->getNumber()->shouldReturn('2018/07/00003333');
    }

    function it_has_order_number(): void
    {
        $this->getOrderNumber()->shouldReturn('0000222');
    }

    function it_has_total(): void
    {
        $this->getTotal()->shouldReturn(1000);
    }

    function it_has_currency_code(): void
    {
        $this->getCurrencyCode()->shouldReturn('USD');
    }

    function it_has_locale_code(): void
    {
        $this->getLocaleCode()->shouldReturn('en_US');
    }

    function it_has_channel(): void
    {
        $this->getChannel()->shouldBeLike(new CreditMemoChannel('WEB-US', 'United States', 'Linen'));
    }

    function it_has_units(): void
    {
        $this->getUnits()->shouldBeLike([new CreditMemoUnit('Portal gun', 1000, 50)]);
    }

    function it_has_date_of_creation(): void
    {
        $this->getIssuedAt()->shouldBeLike(new \DateTime('01-01-2020 10:10:10'));
    }

    function it_has_comment(): void
    {
        $this->getComment()->shouldReturn('Comment');
    }
}
