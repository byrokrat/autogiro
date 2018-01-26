Feature: Mandate request parser
  In order to work with the autogiro
  As a user
  I need to be able to parse mandate requests

  Scenario: I parse the BGC testfile autogiro_exempelfil_medgivandeunderlag_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120080611AUTOGIRO                                            4711170009912346
    04000991234600000000000001013300001212121212191212121212
    04000991234600000000000001028901003232323232005556000521
    04000991234600000000000001035001000001000020196803050000
    0400099123460000000002222101
    0400099123460000000003331022
    0400099123460000000007771014
    04000991234600000000000001063300001212121212191212121212                    AV
    04000991234600000000000001075001000001000020196803050000                    AV
    0300099123460000000005551004
    0300099123460000000004440104
    050009912346000000000000011100099123460000000000000121
    050009912346000000000000011200099123460000000000000122
    050009912346000000000555524200099123460000000003330202
    """
    Then I find a "LAYOUT_REQUEST" layout
    And I find 3 "CreateMandateRequest" nodes
    And I find 3 "AcceptDigitalMandateRequest" nodes
    And I find 2 "RejectDigitalMandateRequest" nodes
    And I find 2 "DeleteMandateRequest" nodes
    And I find 3 "UpdateMandateRequest" nodes

  Scenario: I parse the BGC testfile autogiro_gl_medgivandeunderlag_till-bankgirot-medgivanden-bankgironummer_exempelfil_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041015AUTOGIRO                                            4711170009912346
    0400099123460000000002222101
    0400099123460000000003330202
    0400099123460000000004440104
    0300099123460000000005555242
    """
    Then I find a "LAYOUT_REQUEST" layout
    And I find 3 "AcceptDigitalMandateRequest" nodes
    And I find 1 "DeleteMandateRequest" nodes

  Scenario: I parse the BGC testfile autogiro_gl_medgivandeunderlag_till-bankgirot-medgivanden-kontonummer_exempelfiler_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041015AUTOGIRO                                            4711170009912346
    04000991234600000000000001013300001212121212191212121212
    04000991234600000000000001028901003232323232005556000521
    04000991234600000000000001035001000001000020196803050000
    04000991234600000000000001029918000002010150194608170000
    04000991234600000000000001029918000002010114193701270000                    AV
    0300099123460000000000000242
    """
    Then I find a "LAYOUT_REQUEST" layout
    And I find 4 "CreateMandateRequest" nodes
    And I find 1 "RejectDigitalMandateRequest" nodes
    And I find 1 "DeleteMandateRequest" nodes
