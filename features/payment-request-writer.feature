Feature: Payment request writer
  In order to work with the autogiro
  As a user
  I need to be able to generate payment requests

  Scenario: I request payment
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I call writer method "addPayment" with arguments:
        | type   | value      |
        | string | 1111111111 |
        | SEK    | 100        |
        | Date   | 20180905   |
    And I call writer method "addOutgoingPayment" with arguments:
        | type   | value      |
        | string | 1111111111 |
        | SEK    | 100        |
        | Date   | 20180905   |
    And I call writer method "addMonthlyPayment" with arguments:
        | type   | value      |
        | string | 1111111111 |
        | SEK    | 100        |
        | Date   | 20180905   |
    And I call writer method "addImmediatePayment" with arguments:
        | type   | value      |
        | string | 1111111111 |
        | SEK    | 100        |
    And I generate the request file
    And I parse the generated file
    Then I find a "AutogiroRequestFile" layout
    And I find 3 "IncomingPaymentRequest" nodes
    And I find 1 "OutgoingPaymentRequest" nodes
