1. ifall kontonummer ska vara payerNr och payerNr är ett personnunmmer (personkonto)
   så måste clearning 3300 läggas till...

   * lägg till denna funktion till banking!

1. lägg till även utdaterade meddelanden till Messages
   det finns meddelanden rörande Medgivandeavisering jag inte lagt till...

1. få alla testfiler rörande medgivndeavisering att passera.

1. Implement Writer as a facade to a Visitor that visits request records...

    Detta är vad jag tänker att jag behöver för request making...

    Denna layout innehåller dessutom flera olika sectioner.
        Det ska jag såklart hålla fasta på när jag parsar
        Hur göra reda för det i writer? (Strukturen ska vara sådan att writer bara kan skriva...)
        Vad ska parser egentligen returnera? LayoutInterface??

        // eller så kan jag på detta sätt skapa flera olika layout containers
        // en generisk RequestLayout
        // samt layouter för undersektionerna
            // RequestMandateLayout, RequestTransactionLayout, RequestTransactionChangeLayout
            // behöver jag i så fall unika objekt för dessa? Eller kan jag har något generiskt??

        RequestLayout::getMandateRequests()->getLayoutId() == Layouts::LAYOUT_MANDATE_REQUEST ?

    // OBS jag är locked på dessa namn, se visitor...

    * RequestMandateNode
    * RequestMandateRemovalNode
    * RequestMandateChangeNode
    * RequestIncomingTransactionNode
    * RequestOutgoingTransactionNode
    * RequestTransactionRemovalNode
    * RequestTransactionChangeNode

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

1. Sid 18ff (avsnitt 6.1.5): Datasetnamn. BGC vill att filer till dem ges specifika
   namn. Nordea löser nog det åt mig. Men något stöd måste jag skriva. Även att
   se vilket format vi kan förvänta oss av intputfilen borde kunna ses på detta
   sätt..

1. OBS! I filer till bankgirot: Posten Byte av betalarnummer (TK05) är endast
   tillåtet för Medgivanden med bankkontonummer.

1. Sid. 31f innehåller lista över vad som skiljer gammal och ny layout. Bra!
