Feature: Digital mandate parser
  In order to work with the autogiro
  As a user
  I need to be able to parse digital mandates

  Scenario: I parse the BGC testfile autogiro_exempelfil_nya_medgivanden_via_internetbank_sv.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    512008061199000009912346AG-EMEDGIV
    52000991234600000000000001118901003232323232005556000521     0
    53JAG ÖNSKAR BETALA MÅNADSVIS
    54ANDERS JOHANSSON                    C/O ANNA NILSSON
    55LUGNA GATAN 5
    5612838SKARPNÄCK
    52000991234600000000000001123300001212121212191212121212     0
    53JAG VILL SKÄNKA 100 KR I KVARTALET
    54MALIN WIKTORSSON
    55ARBETSVÄGEN 10
    5612838SKARPNÄCK
    52000991234600000000000001135001000001000020196803050000     1
    53I LIKE TO PAY MONTHLY
    54JOHN ANDERSSON
    558601 EAST ORCHARD ROAD              ACAMPO CA 95220
    5600000USA
    52000991234600000000000001148901003232323232005556000521     1
    53BRA ATT NI BÖRJAT MED AUTOGIRO
    54MARIA CARLSSON
    55TREVNA GRÄND 3
    5612838SKARPNÄCK
    592008061199000000020
    """
    Then I find a "DigitalMandateFile" layout
    And I find 4 "DigitalMandate" nodes

  Scenario: I parse the BGC testfile autogiro_gl_medgivanden_via_internetbanken_exempelfil_fran-bankgirot.txt
    Given a parser that ignores account and id structures
    When I parse:
    """
    512004101599000009912346AG-EMEDGIV
    52000991234600000000000101339918000000041014194512121212     0
    53JAG VILL BETALA MÅNADSVIS
    54DORIS DEMOSSON                      C/o DAVID DEMOSSON
    55DEMOVÄGEN 1
    5610000DEMOSTAD
    592004101599000000005
    """
    Then I find a "DigitalMandateFile" layout
    And I find 1 "DigitalMandate" nodes
