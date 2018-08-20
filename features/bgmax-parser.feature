Feature: BGMAX parser
  In order to work with the autogiro
  As a user
  I need to be able to process bgmax files

  Scenario: I parse the BGC testfile autogiro_exempelfil_bg-max-forma_sv.txt
    Given a parser
    When I parse:
    """
    01BGMAX               0120120914173035010331P
    050009912346          SEK
    20000378351165598                    00000000000001000024
    26Kalles Plåt AB
    27Storgatan 2                        12345
    28Storåker
    29005500001234
    20000000000084629                    00000000000002000024
    20000000000039857                    00000000000003000024
    200037835121644591                   00000000000001000024
    26Larssons Delikatesser
    27Vingbyvägen 59                     12345
    28Storåker
    29005500001233
    15000000000000000000058410000010098232009060300036000000000000070000SEK00000004
    7000000004000000000000000000000001
    """
    Then I get a "BGMAX format currently not supported" error
