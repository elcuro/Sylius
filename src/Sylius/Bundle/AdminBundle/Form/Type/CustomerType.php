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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType as BaseCustomerType;
use Symfony\Component\Form\AbstractType;

final class CustomerType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'sylius_admin_customer';
    }

    public function getParent(): string
    {
        return BaseCustomerType::class;
    }
}
