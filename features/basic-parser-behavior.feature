Feature: Basic behavior
  In order to work with the autogiro
  As a user
  I need to be able to do basic parsing

  Scenario: I parse a file with invalid content
    Given a parser
    When I parse:
    """
    This is not valid content
    """
    Then I get an error

  Scenario: I parse a file with inconsistent payee bankgiro information
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120080611AUTOGIRO                                            1111110000000001
    04000000000200000000000001013300001212121212191212121212
    """
    Then I get a "Non-matching payee bankgiro numbers (expecting: 0000000001, found: 0000000002) on line 2" error

  Scenario: I parse a file with inconsistent payee BGC customer information
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120080611AUTOGIRO                                            1111110123456789
    04012345678900000000000001013300001212121212191212121212
    0120080611AUTOGIRO                                            2222220123456789
    04012345678900000000000001013300001212121212191212121212
    """
    Then I get a "Non-matching payee BGC customer numbers (expecting: 111111, found: 222222) on line 3" error

  Scenario: I parse a file with trailing empty lines
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120080611AUTOGIRO                                            1111110000000001
    04000000000100000000000001013300001212121212191212121212


    """
    Then I find a "AutogiroRequestFile" layout
