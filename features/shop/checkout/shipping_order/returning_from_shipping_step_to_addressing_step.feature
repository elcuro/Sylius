@checkout
Feature: Returning from shipping step to addressing step
    In order to readdress already addressed order
    As a Customer
    I want to be able to go back to addressing step from shipping step

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Apollo 11 T-Shirt" priced at "$49.99"
        And the store ships everywhere for Free
        And I am a logged in customer

    @no-api @ui @javascript
    Scenario: Going back to addressing step with button
        When I add product "Apollo 11 T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I decide to change my address
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again

    @no-api @ui @javascript
    Scenario: Going back to the addressing step with steps panel
        When I add product "Apollo 11 T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I go to the addressing step
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again
