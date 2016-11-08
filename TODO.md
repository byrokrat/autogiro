TODO
====

Supported formats at this point
-------------------------------
* Medgivandeunderlag i ny och gammal layout
* Medgivandeavisering i ny och gammal layout

General
-------
1. Skriv om GrammarSpec med Behat
    - kanske kan alla ParserIntegrationTest skrivas med behat?
    - om nödvändigt en lista över godkända fel lika i accounting..
    - varför är det egentligen viktigt här att skilja på GrammarSpec och IntegrationTest??
      det är en teoretiskt snygg uppdelning. Men i praktiken kanske de kan göra samma sak...

1. det ska inte vara ett krav att sista raden avslutas med ett enterslag
   men hur jag ska implementera det i Grammar återstår att se
   något i stil med
   (EOL / EOF)
   men hur ska EOF definieras???

1. Nodes to make:
    - RequestIncomingTransactionNode
    - RequestOutgoingTransactionNode
    - RequestTransactionDeletionNode
    - RequestTransactionUpdateNode

1. ifall kontonummer ska vara payerNr och payerNr är ett personnunmmer (personkonto)
   så måste clearning 3300 läggas till...
   har fixat detta i banking 1.2.0, börja använda här...

1. Implement Writer as a facade to a Visitor that visits request records...

   // Nu har jag kommit så pass långt i Granmmar att jag skulle unna skriva Writer
   // för mandate requests

   $writer = new Writer($bg, 'cust1232345', $date = null);
   $writer->deleteMandate('123456789');
   $writer->createMandate(...);

   $writer->getContent(); // string

1. Support billing at next possible date (the low level syntax for
   this is `GENAST`, instead of a numeric date). OBS! S. 27 i manual: Periodkod
   1-8 kan inte användas om GENAST har angetts som betalningsdag i TK32 eller TK82.

1. Generated files must NOT include (end with) and empty line
   In the deprecated georg system this was solved using
   ```php
   return rtrim($this->buildNative(), "\r\n");
   ```
   in `DonorWorker->billAll()`.

1. Olika värdebärare for record-sub-parts
    ```php
    $interval = new Record\Intervall\MonthlyOnDate;
    echo $interval->getCode();
    echo $interval->getDescription();
    ```

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
