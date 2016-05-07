1. Needs to support both old and new syntax. Does `Layouts` need a rewrite?
   Important to have a good testning strategy in place!

1. What should `Parser::parse()` return? Tried to write a generic `Section` class.
   Wrapp in `Dataset` that implements `getSection(Layout::ID)` and `getAllSections()`?
   Depends on the BGC specs. Are return values regular in this respect?

1. **FileObject** is not used in `Parser` at all! Keept in source for reference.
   If `Line` should handle all encoding conversions/line endings; move functionality from
   `FileObject`. It is also an open question how `Line/FileObject` should be used
   in `Writer`...

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

1. When tesing: The number of generated `Record` objects should match the number
   of lines in the raw file. Make the testing strategy validate this..

1. Olika värdebärare for record-sub-parts
    ```php
    $interval = new Record\Intervall\MonthlyOnDate;
    echo $interval->getCode();
    echo $interval->getDescription();
    ```

1. `Dataset`, `Section` and `Record` could all implement `JsonSerializable`...

1. Implement interfaces for `OpeningRecord` and `ClosingRecord` for type checking
   in `Section` and so on..?? Depends on how I want to implement records in the end..

1. `Writer` could be an object that transforms a `Dataset` inte raw `Line` objects..
   And a `Builder` could wrap the writer and define convenience methods for adding
   invoices, donors and so on..

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

1. Sid 18ff (avsnitt 6.1.5): Datasetnamn. BGC vill att filer till dem ges specifika
   namn. Nordea löser nog det åt mig. Men något stöd måste jag skriva. Även att
   se vilket format vi kan förvänta oss av intputfilen borde kunna ses på detta
   sätt..

1. OBS! I filer till bankgirot: Posten Byte av betalarnummer (TK05) är endast
   tillåtet för Medgivanden med bankkontonummer.

1. Sid. 31f innehåller lista över vad som skiljer gammal och ny layout. Bra!
