Feature: Payment rejection response parser
  In order to work with the autogiro
  As a user
  I need to be able to parse payment rejection responses

  Scenario: I parse 'new' testfile 'autogiro_exempelfilavvisade_betalningsuppdrag_sv.txt'
    Given a parser that ignores account and id structures
    When I parse:
    """
    01AUTOGIRO              20080611            AVVISADE BET UPPDR  4711170009912346
    822008061150060000000000003333000000007500RIDLEKTION      02
    822008061180110000000000004444000000025000FAKTNR158       06
    822008061120020000000000005555000000055051FAKTNR160       08
    32200806110   0000000000001212000000007500RIDLEKTION ATERB01
    32200806300   0000000000002323000000008000RIDLEKTION ATERB12
    32200806110   0000000000001414000000025000FAKTNR161       10
    32200806030   0000000000005556000000007500FAKTNR162       13
    32200806110   0000000000007575000000080200RIDLEKTION ATERB01
    09200806119900000005000000128200000003000000087551
    """
    Then I find a "AutogiroPaymentRejectionFile" layout

  Scenario: I parse 'old' testfile 'autogiro_gl_avvisadebetaln_fran-bankgirot-medgivanden-bankgironummer_exempelfil_sv.txt'
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041022AUTOGIRO9900FELLISTA REG.KONTRL                     4711170009912346
    82200410230   0000000002222101000000050000                01
    82200410230   0000000003333102000000020000                01
    82200410230   0000000004444103000000010000                03
    82200410230   0000000005555104000000015000                07
    09200410229900000000000000000000000004000000095000
    """
    Then I find a "AutogiroPaymentRejectionFile" layout

  Scenario: I parse 'old' testfile 'autogiro_gl_exempelfil_avvisadebetalningar_fran-bankgirot-medgivanden-kontonummer_exemeplfil_sv.txt'
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041022AUTOGIRO9900FELLISTA REG.KONTRL                     4711170009912346
    82200410230   0000000000000101000000050000                01
    82200410230   0000000000000102000000020000                03
    82200410230   0000000000000103000000010000                02
    82200410230   0000000000000104000000015000                07
    09200410229900000000000000000000000004000000095000
    """
    Then I find a "AutogiroPaymentRejectionFile" layout
