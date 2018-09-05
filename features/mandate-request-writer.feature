Feature: Mandate request writer
  In order to work with the autogiro
  As a user
  I need to be able to generate mandate requests

  Scenario: I request a mandate deletion
    Given a writer with BGC number "666666", bankgiro "1111-1119" and date "20170111"
    When I call writer method "deleteMandate" with argument "3333333333"
    And I generate the request file
    Then I get a file like:
    """
    0120170111AUTOGIRO                                            6666660011111119
    0300111111190000003333333333
    """

  Scenario: I parse a generated mandate deletion
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I call writer method "deleteMandate" with argument "1111111111"
    And I generate the request file
    And I parse the generated file
    Then I find a "AutogiroRequestFile" layout
    And I find 1 "DeleteMandateRequest" nodes

  Scenario: I parse a generated added mandate file
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I call writer method "addNewMandate" with arguments:
        | type    | value       |
        | string  | 1111111111  |
        | account | 50001111116 |
        | id      | 820323-2775 |
    And I generate the request file
    And I parse the generated file
    Then I find a "AutogiroRequestFile" layout
    And I find 1 "CreateMandateRequest" nodes

  Scenario: I respond to received digital mandates
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I call writer method "acceptDigitalMandate" with argument "1111111111"
    And I call writer method "rejectDigitalMandate" with argument "2222222222"
    And I generate the request file
    And I parse the generated file
    Then I find a "AutogiroRequestFile" layout
    And I find 1 "AcceptDigitalMandateRequest" nodes
    And I find 1 "RejectDigitalMandateRequest" nodes

  Scenario: I update a mandate
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I call writer method "updateMandate" with arguments:
        | type   | value      |
        | string | 1111111111 |
        | string | 2222222222 |
    And I generate the request file
    And I parse the generated file
    Then I find a "AutogiroRequestFile" layout
    And I find 1 "UpdateMandateRequest" nodes

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
