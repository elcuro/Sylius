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

namespace Sylius\Bundle\ApiBundle\Validator\CatalogPromotion;

use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0, use the usual symfony logic for validation.',
    PercentageDiscountActionValidator::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0, use the usual symfony logic for validation. */
final class PercentageDiscountActionValidator implements ActionValidatorInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
    {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        if (!$this->sectionProvider->getSection() instanceof AdminApiSection) {
            return;
        }

        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        if (!isset($configuration['amount'])) {
            $context->buildViolation($constraint->notNumberOrEmpty)->atPath('configuration.amount')->addViolation();

            return;
        }

        if (!is_float($configuration['amount']) && !is_int($configuration['amount'])) {
            $context->buildViolation($constraint->notNumberOrEmpty)->atPath('configuration.amount')->addViolation();

            return;
        }

        if ($configuration['amount'] < 0 || $configuration['amount'] > 1) {
            $context->buildViolation($constraint->notInRangeDiscount)->atPath('configuration.amount')->addViolation();
        }
    }
}
