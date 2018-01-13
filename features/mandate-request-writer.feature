Feature: Mandate request writer
  In order to work with the autogiro
  As a user
  I need to be able to generate mandate requests

  Scenario: I request a mandate deletion
    Given a writer with BGC number "666666", bankgiro "1111-1119" and date "20170111"
    When I request mandate "3333333333" be deleted
    And I generate the request file
    Then I get a file like:
    """
    0120170111AUTOGIRO                                            6666660011111119
    0300111111190000003333333333
    """

  Scenario: I parse a generated mandate deletion
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I request mandate "1111111111" be deleted
    And I generate the request file
    And I parse the generated file
    Then I find a "LAYOUT_MANDATE_REQUEST" layout
    And I find 1 "DeleteMandateRequest" nodes

  Scenario: I parse a generated added mandate file
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I request mandate "1111111111" be added
    And I generate the request file
    And I parse the generated file
    Then I find a "LAYOUT_MANDATE_REQUEST" layout
    And I find 1 "CreateMandateRequest" nodes

  Scenario: I respond to received digital mandates
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I request mandate "1111111111" be accepted
    And I request mandate "2222222222" be rejected
    And I generate the request file
    And I parse the generated file
    Then I find a "LAYOUT_MANDATE_REQUEST" layout
    And I find 1 "AcceptDigitalMandateRequest" nodes
    And I find 1 "RejectDigitalMandateRequest" nodes

  Scenario: I update a mandate
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I request mandate "1111111111" be updated to "2222222222"
    And I generate the request file
    And I parse the generated file
    Then I find a "LAYOUT_MANDATE_REQUEST" layout
    And I find 1 "UpdateMandateRequest" nodes

  Scenario: I request payment
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20170111"
    And a parser
    When I request a payment of "100" SEK from "1111111111"
    And I request a payment of "100" SEK to "1111111111"
    And I request a monthly payment of "100" SEK from "1111111111"
    And I request an immediate payment of "100" SEK from "1111111111"
    And I generate the request file
    And I parse the generated file
    Then I find a "LAYOUT_PAYMENT_REQUEST" layout
    And I find 3 "IncomingPaymentRequest" nodes
    And I find 1 "OutgoingPaymentRequest" nodes
