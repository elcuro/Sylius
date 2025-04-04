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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Admin\CustomerGroup\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingCustomerGroupsContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private FormElementInterface $formElement,
    ) {
    }

    /**
     * @When I want to create a new customer group
     */
    public function iWantToCreateANewCustomerGroup(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->formElement->fillElement($code ?? '', 'code');
    }

    /**
     * @When I specify a too long code
     */
    public function iSpecifyATooLongCode(): void
    {
        $this->formElement->fillElement(str_repeat('a', 256), 'code');
    }

    /**
     * @When I specify its name as :name
     * @When I remove its name
     */
    public function iSpecifyItsNameAs(?string $name = null): void
    {
        $this->formElement->fillElement($name ?? '', 'name');
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When /^I want to edit (this customer group)$/
     */
    public function iWantToEditThisCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->updatePage->open(['id' => $customerGroup->getId()]);
    }

    /**
     * @When I check (also) the :customerGroupName customer group
     */
    public function iCheckTheCustomerGroup(string $customerGroupName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $customerGroupName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @When I browse customer groups
     * @When I want to browse customer groups
     */
    public function iWantToBrowseCustomerGroups(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I sort them by the (code|name) in (asc|desc)ending order$/
     */
    public function iSortThemByTheField(string $field, string $order): void
    {
        $this->indexPage->sortBy($field, $order);
    }

    /**
     * @Then the customer group :customerGroup should appear in the store
     */
    public function theCustomerGroupShouldAppearInTheStore(CustomerGroupInterface $customerGroup): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $customerGroup->getName()]));
    }

    /**
     * @Then this customer group with name :name should appear in the store
     * @Then I should see the customer group :name in the list
     */
    public function thisCustomerGroupWithNameShouldAppearInTheStore(string $name): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then there should be :amountOfCustomerGroups customer groups in the list
     */
    public function thereShouldBeCustomerGroupsInTheList(int $amountOfCustomerGroups = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amountOfCustomerGroups);
    }

    /**
     * @Then I should see a single customer group in the list
     * @Then I should see :amountOfCustomerGroups customer groups in the list
     *
     * This step is a duplicate of the above because some scenarios require to open the index page before checking anything
     */
    public function iShouldSeeCustomerGroupsInTheList(int $amountOfCustomerGroups = 1): void
    {
        $this->indexPage->open();

        Assert::same($this->indexPage->countItems(), $amountOfCustomerGroups);
    }

    /**
     * @Then /^the (\d+)(?:|st|nd|rd|th) customer group on the list should have (name|code) "([^"]+)" and (name|code) "([^"]+)"$/
     */
    public function theFirstCustomerGroupOnTheListShouldHave(
        int $position,
        string $firstField,
        string $firstValue,
        string $secondField,
        string $secondValue,
    ): void {
        $fields = $this->indexPage->getColumnFields($firstField);

        Assert::same($fields[$position - 1], $firstValue);

        $fields = $this->indexPage->getColumnFields($secondField);

        Assert::same($fields[$position - 1], $secondValue);
    }

    /**
     * @Then /^(this customer group) should still be named "([^"]+)"$/
     */
    public function thisCustomerGroupShouldStillBeNamed(CustomerGroupInterface $customerGroup, string $customerGroupName): void
    {
        $this->iWantToBrowseCustomerGroups();

        Assert::same($customerGroup->getName(), $customerGroupName);
        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $customerGroupName]));
    }

    /**
     * @Then I should be notified that name is required
     */
    public function iShouldBeNotifiedThatNameIsRequired(): void
    {
        Assert::same(
            $this->formElement->getValidationMessage('name'),
            'Please enter a customer group name.',
        );
    }

    /**
     * @Then I should be notified that customer group with this code already exists
     */
    public function iShouldBeNotifiedThatCustomerGroupWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->formElement->getValidationMessage('code'), 'Customer group code has to be unique.');
    }

    /**
     * @Then I should be notified that code is too long
     */
    public function iShouldBeNotifiedThatCodeIsTooLong(): void
    {
        Assert::contains(
            $this->formElement->getValidationMessage('code'),
            'must not be longer than 255 characters.',
        );
    }

    /**
     * @Then I should be informed that this form contains errors
     */
    public function iShouldBeInformedThatThisFormContainsErrors(): void
    {
        Assert::true($this->formElement->hasFormErrorAlert());
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->formElement->isCodeDisabled());
    }

    /**
     * @When I delete the :customerGroup customer group
     */
    public function iDeleteTheCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->iWantToBrowseCustomerGroups();

        $this->indexPage->deleteResourceOnPage(['name' => $customerGroup->getName()]);
    }

    /**
     * @Then /^(this customer group) should no longer exist in the registry$/
     */
    public function thisCustomerGroupShouldNoLongerExistInTheRegistry(CustomerGroupInterface $customerGroup): void
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $customerGroup->getName()]),
            sprintf(
                'Customer group %s should no longer exist in the registry',
                $customerGroup->getName(),
            ),
        );
    }
}
