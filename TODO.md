TODO
====

1. Är PayerNumberNode en TextNode som validerar att det är bara siffror??
   Kanske finns det fler nodes som skulle kunna valideras på detta sätt..

1. Writer as a Facade:
    Fortsätt med TreeBuilder
        - Gå igenom Grammar och bestäm hur noder slutgiltigt ska definieras innan jag fortsätter...
        - Lägg till transaktionskoder och ny-rader i PrintingVisitor i takt med att jag lägger till TreeBuilder

1. Generated files must NOT include (end with) and empty line
   In the deprecated georg system this was solved using
   ```php
   return rtrim($this->buildNative(), "\r\n");
   ```
   in `DonorWorker->billAll()`.

BGC specs
---------
1. Är det någon skillnad på `Betalningsspecifikation` och
   `Betalningsspecifikation och stoppade betalningar i täckningskontroll`?

1. Dessa raporter är tydligen standard (sida 12)
   1. Betalningsspecifikation (/+ stoppade betalningar...   )
   1. Makulering/ändring av betalningar
   1. Medgivandeavisering
   1. Avvisade betalningar
   1. Medgivanden via Internetbanken

1. För de betalare som har Medgivande med bankgironummer är betalarnumret alltid
   bankgironumret (s. 14, avsnitt 5.3)

1. S. 18 (avsnitt 6.1.4): Filer till Bankgirot ska vara i ASCII- eller EBCDIC-format,
   beroende på val av Kommunikationssätt. För ASCII-filer rekommenderas ISO8859-1
   (Latin-1) för teckenrepresentation, samt <CRLF> för att indikera radavslut.
   Postlängden är fast, 80 positioner.

1. OBS! I filer till bankgirot: Posten Byte av betalarnummer (TK05) är endast
   tillåtet för Medgivanden med bankkontonummer.

1. Sid. 31f innehåller lista över vad som skiljer gammal och ny layout. Bra!
