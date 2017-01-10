Feature: Transaction requests
  In order to work with the autogiro
  As a user
  I need to be able to parse transaction requests

  Scenario: I parse the BGC testfile autogiro_exempelfil-betalningsunderlag_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120080611AUTOGIRO                                            4711170009902346
    82200806120    00000000000001010000000750000009902346ÅRSKORT-2008
    82200806120    00000000000001020000000250500009902346KVARTAL-2008
    82200806305006 00000000000001030000001100000009902346MÅNAD-2008
    82200806306003 00000000022221010000005000250009902346KVARTAL-2008
    82GENAST  0    00000000033310220000000350000009902346KUNDNR5
    32200806120    00000000077710140000000125000009902346UTBETALN
    32200806120    00000000000001040000000325000009902346UTBETALN
    32200806120    00000000000001050000000030000009902346UTBETALN1
    32200806120    00000000055510040000000030000009902346UTBETALN2
    """
    Then I find a "LAYOUT_PAYMENT_REQUEST" layout
    And I find 5 "IncomingTransactionRequestNode" nodes
    And I find 4 "OutgoingTransactionRequestNode" nodes

  Scenario: I parse the BGC testfile autogiro_gl_betalningsunderlag_till-bankgirot-medgivanden-kontonummer_exempelfil_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041026AUTOGIRO                                            4711170009912346
    82200410270    00000010203040510000000750000009912346ÅRSKORT-2005
    82200410270    00000020304050620000000250000009912346KVARTAL-2005
    32200410280    00000030405060730000000125000009912346ÅTERBET
    """
    Then I find a "LAYOUT_PAYMENT_REQUEST" layout
    And I find 2 "IncomingTransactionRequestNode" nodes
    And I find 1 "OutgoingTransactionRequestNode" nodes

  Scenario: I parse the BGC testfile autogiro_gl_betalningsunderlag_till-bankgirot-medgivanden-bankgironummer_exempelfil_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120041026AUTOGIRO                                            4711170009912346
    82200410270    00000000033344510000000750000009912346FAKT 12345678
    82200410270    00000000044450620000000250000009912346FAKT 34567899
    32200410280    00000000055507310000001250000009912346FAKT 78787878
    """
    Then I find a "LAYOUT_PAYMENT_REQUEST" layout
    And I find 2 "IncomingTransactionRequestNode" nodes
    And I find 1 "OutgoingTransactionRequestNode" nodes
