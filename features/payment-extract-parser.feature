Feature: Payment extract parser
  In order to work with the autogiro
  As a user
  I need to be able to parse payment extracts

  Scenario: I parse new testfile autogiro_exempelfilutdrag-ur-bevakningsregistret_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    0120080611AUTOGIRO9900BEVAKNINGSREG                           4711170009912346
    82200806300    0000000000000101000000012000          FAKTURANR122
    82200806301006 0000000000000102000000550555          FAKTURANR120
    82200806302002 0000000000000103000000077500          FAKTURANR110
    82200807010    0000000000000104000000005000          RIDLEKTION
    82200807010006 0000000000000105000000010000          FAKTURANR111
    32200806300    0000000007771014000000125500          ÅTERBET
    32200806300    0000000005551004000000060000          ÅTERBET
    32200806300    0000000000000106000000037550          ÅTERBET
    32200807010    0000000003331022000000003500
    32200807010    0000000000000107000000005075
    09200806119900              0000002316250000050000050000000000655055000000000000
    """
    Then I find a "AutogiroPaymentExtractFile" layout
    And I find 5 "IncomingPayment" nodes
    And I find 5 "OutgoingPayment" nodes
