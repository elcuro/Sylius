<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PhpSpec\ObjectBehavior;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class RemoveItemFromCartHandlerSpec extends ObjectBehavior
{
    use MessageHandlerAttributeTrait;

    function let(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderModifierInterface $orderModifier,
        ProductVariantResolverInterface $variantResolver,
    ): void {
        $this->beConstructedWith(
            $orderItemRepository,
            $orderModifier,
            $variantResolver,
        );
    }

    function it_removes_order_item_from_cart(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderModifierInterface $orderModifier,
        OrderInterface $cart,
        OrderItemInterface $cartItem,
    ): void {
        $orderItemRepository->findOneByIdAndCartTokenValue(
            'ORDER_ITEM_ID',
            'TOKEN_VALUE',
        )->willReturn($cartItem);

        $cartItem->getOrder()->willReturn($cart);

        $cart->getTokenValue()->willReturn('TOKEN_VALUE');

        $orderModifier->removeFromOrder($cart, $cartItem)->shouldBeCalled();

        $this(new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID'))->shouldReturn($cart);
    }

    function it_throws_an_exception_if_order_item_was_not_found(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemInterface $cartItem,
    ): void {
        $orderItemRepository->findOneByIdAndCartTokenValue(
            'ORDER_ITEM_ID',
            'TOKEN_VALUE',
        )->willReturn(null);

        $cartItem->getOrder()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during(
                '__invoke',
                [new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID')],
            )
        ;
    }

    function it_throws_an_exception_if_cart_token_value_was_not_properly(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderModifierInterface $orderModifier,
        OrderItemInterface $cartItem,
        OrderInterface $cart,
    ): void {
        $orderItemRepository->findOneByIdAndCartTokenValue(
            'ORDER_ITEM_ID',
            'TOKEN_VALUE',
        )->willReturn($cartItem);

        $cartItem->getOrder()->willReturn($cart);

        $cart->getTokenValue()->willReturn('WRONG_TOKEN_VALUE_');

        $orderModifier->removeFromOrder(null, $cartItem)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during(
                '__invoke',
                [new RemoveItemFromCart(orderTokenValue: 'TOKEN_VALUE', itemId: 'ORDER_ITEM_ID')],
            )
        ;
    }
}
