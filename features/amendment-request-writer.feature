Feature: Amendment request writer
  In order to work with the autogiro
  As a user
  I need to be able to generate amendment requests

  Scenario: I request payments be deleted
    Given a writer with BGC number "666666", bankgiro "1111-1119" and date "20180910"
    When I call writer method "deletePayments" with argument "3333333333"
    And I generate the request file
    Then I get a file like:
    """
    0120180910AUTOGIRO                                            6666660011111119
    2300111111190000003333333333
    """

  Scenario: I parse a generated payments deletion
    Given a writer with BGC number "222222", bankgiro "1111-1119" and date "20180910"
    And a parser
    When I call writer method "deletePayments" with argument "1111111111"
    And I generate the request file
    And I parse the generated file
    Then I find a "AutogiroRequestFile" layout
    And I find 1 "AmendmentRequest" nodes
