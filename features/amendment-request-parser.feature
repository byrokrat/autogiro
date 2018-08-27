Feature: Amendment request parser
  In order to work with the autogiro
  As a user
  I need to be able to parse amendment requests

  Scenario: I parse the BGC testfile autogiro_exempelfilermakulerings-andringsunderlag_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120080611AUTOGIRO                                            4711170009912346
    2300099123460000000000000101
    2300099123460000000000000102
    2300099123460000000000000103
    240009912346000000000222210120080612
    240009912346000000000555524220080613
    25000991234600000000000001052008061600000002750082        UTBETALN1
    25000991234600000000055510042008061800000005250082        UTBETALN2
    25000991234600000000000001062008061900000008250032
    25000991234600000000077710142008061900000008250032
    260009912346                                      20080630
    270009912346                20080613              20080630
    280009912346000000000000010720080612              20080623
    280009912346000000000000010820080613              20080624
    2900099123460000000004440104200806120000000175008220080623
    2900099123460000000003331022200806130000000475008220080624
    2900099123460000000000000109200806240000000555003220080619
    2900099123460000000000000110200806130000000400003220080624UTBETALN3
    """
    Then I find a "AutogiroRequestFile" layout
    And I find 17 "AmendmentRequest" nodes

  Scenario: I parse the BGC testfile autogiro-gl_makulerings-andringsunderlag_till-bankgirot_exempelfil_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041018AUTOGIRO                                            4711170009912346
    2300099123460000000005550731
    25000991234600000000044450622004101900000002350082
    2900099123460000000003334451200411030000000150008220041102
    """
    Then I find a "AutogiroRequestFile" layout
    And I find 3 "AmendmentRequest" nodes

  Scenario: I parse the BGC testfile autogiro_gl_makulering-datumandring_till-bankgirot-medgivanden-kontonummer_exempelfil_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041018AUTOGIRO                                            4711170009912346
    2300099123460000000000000102
    25000991234600000000000001032004101900000002350082
    2900099123460000000000000104200411030000000150008220041102
    """
    Then I find a "AutogiroRequestFile" layout
    And I find 3 "AmendmentRequest" nodes
