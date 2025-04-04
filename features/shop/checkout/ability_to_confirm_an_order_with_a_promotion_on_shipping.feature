@checkout
Feature: Ability to confirm an order with a promotion on shipping
    In order to be sure that the shipping discount was applied to my order
    As a Customer
    I want to be able to see all remaining steps

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has "DHL" shipping method with "$50.00" fee
        And there is a promotion "Holiday promotion"
        And the promotion gives "10%" discount on shipping to every order
        And the store allows paying Offline
        And I am a logged in customer

    @api @ui @javascript
    Scenario: Successfully placing an order
        When I add product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "DHL" shipping method and "Offline" payment
        And I confirm my order
        Then I should see the thank you page
