TODO
====
1. Support billing at next possible date (the low level syntax for
   this is `GENAST`, instead of a numeric date). OBS! S. 27 i manual: Periodkod
   1-8 kan inte användas om GENAST har angetts som betalningsdag i TK32 eller TK82.

1. Generated files must NOT include (end with) and empty line
   In the deprecated georg system this was solved using
   ```php
   return rtrim($this->buildNative(), "\r\n");
   ```
   in `DonorWorker->billAll()`.

1. Implement the `.json` file solution for automating tests on test files. file
   type guessing and parsing could then be tested in an automated manner.

1. Write something like `byrokrat\autogiro\testfiles\FileProvidingTrait` to
   allow for easy access to test files when writing tests.

1. Olika värdebärare for record-sub-parts
    ```php
    $interval = new Record\Intervall\MonthlyOnDate;
    echo $interval->getCode();
    echo $interval->getDescription();
    ```

1. Släpp under GPL för att fucka för de som använder koden =)

1. Det måste vara möjligt att skicka medgivanden och så vidare även om konto
   är felformaterat (alltså även om byrokrat/banking säger att det är fel)
   i de fall byrokrat/banking har fel måste det gå att override..


Billing
-------
Strunta i billing när jag utvecklar här. Skapa isället ett eget paket som
knyter ihop med billing

byrokrat/autogiro-billing

varför inte byta namn på paperinvoice till

byrokrat/pdf-billing


BGC:s specifikation
-------------------
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

1. Sid 18ff (avsnitt 6.1.5): Datasetnamn. BGC vill att filer till dem ges specifika
   namn. Nordea löser nog det åt mig. Men något stöd måste jag skriva. Även att
   se vilket format vi kan förvänta oss av intputfilen borde kunna ses på detta
   sätt..

1. OBS! I filer till bankgirot: Posten Byte av betalarnummer (TK05) är endast
   tillåtet för Medgivanden med bankkontonummer.

1. Sid. 31f innehåller lista över vad som skiljer gammal och ny layout. Bra!
