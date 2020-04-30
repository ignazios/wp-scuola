=== Gestione Circolari ===
Contributors: Scimone Ignazio
Tags: Gestione Circolari, Scuola, Gestione Scuola
Requires at least: 3.7
Tested up to: 4.5
Stable tag: 3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Gestione Circolari Scolastiche. 

== Description ==

Gestione delle circolari scolastiche con la possibilit&agrave; di richiedere la firma e l'adesione alle circolari sindacali.
Questo plugin utilizza un sistema interno di profilatura degli utenti in gruppi (es.Docenti, Personale ATA, etc..) che saranno utilizzati per indirizzare le circolari a specifici utenti. 

== Installation ==

Di seguito sono riportati i passi necessari per l'installazione del plugin.


1. Scaricare il plugin dal repository di wordpress o dal sito di riferimento
2. Attivare il plugin dal menu Plugins
3. Creare i gruppi di utenti es. Docenti, ATA, Tutti, etc....
4. Impostare i Parametri:
<br />	4.1 <strong>Gruppo Pubblico Circolari</strong>; in genere Tutti, indica la visibilit&agrave; delle corcolari nella gestione della presa visione 
<br />	4.2 <strong>Categoria Circolari</strong>; indica la categoria delle circolari utilizzate nei post, questo parametro permette di fondere le circolari codificate negli articoli a quelli codificati con la gestione delle circoalri 
5. Inserire le circolari, selezionando i destinatari nella finestra dei destinatari, il numero progressivo se diverso da quello proposto, selezionare l'eventuale richiesta di firma, selezionare l'eventuale indicazione di circolare per sciopero 
6. Creare una pagina con lo shortcode [VisCircolari] e collegarla ad una voce di menu
7. inserire il widget che indica, se l'utente &egrave; loggato il numero di circolari da firmare o da prendere in visione <strong>Circolari</strong>.
8. inserire il widget che riproduce la struttura temporale della pubblicazione delle circolari <strong>Navigazione Circolari</strong>.

== Screenshots ==

1. Elenco Circolari
2. Ambiente di creazione/modifica delle circolari con tutte le finestre per la codifcia dei parametri specifici della gestione
3. Finestra di gestione delle circolari da firmare
4. Finestra associata ad ogni circolare per la verifica delle firme/adesioni
5. Finestra lato pubblico con i due widget; Circolari, che riporta il numero di circolari da visionare/firmare. Navigazione Circolari, che implementa il sistema di navigazione per Anno/mese delle circolari
6. Creazione della pagina che conterr� le circolari
7. Pagina con l'elenco delle circolari
8. Visualizzazione di una circolare

== Changelog ==
= 2.7.3 =
- <strong>Adeguamento</strong> alla versione 4.5.
= 2.7.2 =
- <strong>Sistemato</strong> generazione link nella gestione del widget di navigazione per mese/anno, modificato il carattere di inizio stringa parametr in funzione del Permalink.
= 2.7.1 =
- <strong>Sistemata</strong> la gestione del widget di navigazione per mese/anno, modificato il carattere di inizio stringa parametr in funzione del Permalink.
= 2.7 =
- <strong>Riorganizzata</strong> la gestione del widget di navigazione per mese/anno, adesso i singoli mesi/anni si possono selezionare attraverso combobox una per anno in ui sono presenti circolari
= 2.6 =
- <strong>Riorganizzato</strong> l'archivio delle circolari, sono state eliminate le due voci <strong><em>Firmate</em></strong> - <strong><em>Sadute e non firmate</em></strong> e sostituite con la voce <strong>Archivio Circolari</em></strong>. 
In questo elenco vengono riportate le date di scadenza colorate in base all'approssimarsi della data di scadenza o al fatto che a circolare � scaduta e non � stata firmata. Tutte le circolari con data di scadenza di color nero sono firmate/da non firmare/da firmare ma emesse prima della creazione dell'utente
= 2.5.2 =
- <strong>Corretto</strong> problema generazione numero progressivo circolari
- <strong>Corretto</strong> problema memorizzazione anno circolare nel formato aaaa/aaaa
- <strong>Eliminata</strong> la modifica veloce della circolare nell'elenco che generava problemi con i campi aggiunti
= 2.5.1 =
- <strong>Aggiornata</strong> librera  DataTables; nuova versione 1.10.9
- <strong>Aggiunta</strong> funzione in utility che permette di verificare il formato della data e di correggere gli errori nel numero di cifre di Anno Mese e Giorno
= 2.5 =
- <strong>Risolti</strong> alcuni problemi di sicurezza
= 2.4.5 =
- <strong>Corretto</strong> messaggio di conferma della firma in caso di sciopero/circolare sindacale adesso non riporta la dicitura <strong><em>allo sciopero</em></strong>
= 2.4.4 =
- <strong>Corretto</strong> errore di visualizzazione delle circolari private conoscendo il link diretto
= 2.4.3 =
- <strong>Sistemata</strong> problema sulla visualizzazione del gruppo di appaerteneza nella lista degli utenti
= 2.4.2 =
- <strong>Sistemata</strong> la visualizzazione dei parametri delle circolari nell'elenco del FrontEnd
= 2.4.1 =
- <strong>Corretta</strong> la cancellazione del gruppo che si verificava quando un utente aggiornava il proprio profilo
= 2.4 =
- <strong>Corretto</strong> il warning generato in visualizzazione firme nel caso in cui non sono state codificate circolari
- <strong>Sostituito</strong> il set di icone, sono state adottate sia nel ForntEnd che nel BackEnd icone falt in bianco e nero
- <strong>Risolto</strong> problema di visualizzazione dell'elenco circolari
= 2.3.2 =
- <strong>Risolto</strong> conflitto con Wordfence Security delle TableTools
= 2.3.1 =
- <strong>Sistemati</strong> bug: link dai widget per la firma
= 2.3 =
- <strong>Sistemati</strong> diversi bug
- <strong>Ottimizzato</strong> il codice per aumentare la velocit� del sito con il plugin attivato si a nel front-end sia nel back-end
- <strong>Aggiunto</strong>aggiunto shortcode VisualizzaCircolariHome da utilizzare nel template Pasw2013 per elencare le circolari provenienti dal plugin. Codice gentilmente fornito da Christian Ghellere
= 2.2.2 =
- <strong>Sistemato</strong> errore che posizionava le circolari firmate dopo la scadenza nella cartella delle circolari scadute e non firmate 
- <strong>Modificato</strong> il comportamente della creazione della lista delle circolari lato pubblico. Quando si naviga senza essere loggati le circolari private non vengono pi� visualizzate nella lista
- <strong>Disattivata Momentaneamente</strong> la notifica del numero di circolari da firmare nella Barra degli strumenti e nel menu delle Circolari. Questa notifica rallenta il BackEnd del sito e verr� rivista al pi� presto con relativa ottimizzazione complessiva del codice.
= 2.2.1 =
- <strong>Sistemato</strong> errore che permetteva agli utenti di modificarsi il gruppo di appartenenza 
= 2.2 =
- <strong>Sistemati</strong> diversi bug
= 2.1 =
- <strong>Sistemati</strong> diversi bug di visualizzazione delle circolari
- <strong>Migliorata</strong> l'interfaccia sia pubblica che amministrativa
- <strong>Implementata</strong> la possibilita' di inserire la data entro cui firmare le circolari
- <strong>Implementata</strong> la visualizzazione delle circolari relative all'utente per tipologia;Firmate, Non Firmate e Scadute
- <strong>Implementata</strong> la gestione dinamica delle tabelle tramite plugin JQuery con la possibilit� di stampare o esportare le tabelle in CSV, Excel e Pdf
- <strong>Modificata</strong> la gestione della numerazione delle circolari per anno scolastico nel formato aaaa/aa
= 2.0.1 =
- <strong>Sistemato</strong> bug calcolo numero circolari da firmare
= 2.0 =
- <strong>Sistemati</strong> piccoli bug
= 1.9 =
- <strong>Implementata</strong> gestione delle circolari protette da password
- <strong>Sistemato</strong> bug che poteva essere generato in fase di verifica dei destinatari della circolare
= 1.8 =
- <strong>Sistemato</strong> bug della visualizzazione Circolari Firmate nel FrontEnd
= 1.7 =
- <strong>Sistemato</strong> bug di gestione delle firme dal FrontEnd
- <strong>Creata</strong> una pagina nel BackEnd per visualizzare le circolari firmate
- <strong>Sistemato</strong> bug nella paginazione delle circolari da firmare e formate
- <strong>Modificata</strong> la procedura della firma nella visualizzazione della circolare del BackEnd, adesso, dopo la firma si rimane dentro la circolare
= 1.6 =
- <strong>Sistemati</strong> vari bug di secondaria importanza
- <strong>Inserito</strong> nel Frontend un'icona con testo in rosso che evidenzia le circolari da firmare o da prendere visione
- <strong>Implementata</strong> la navigazione per pagine nella visualizzazione delle firme/presa visione sciopero
= 1.5 =
- <strong>Sistemati</strong> due bugs di validazione del FrontEnd
= 1.4 =
- <strong>Sistemati</strong> bugs vari visualizzazione FrontEnd
= 1.3 =
- <strong>Sistemato</strong> bugs widget navigazione circolari
= 1.2 =
- <strong>Sistemati</strong> bugs vari
= 1.1 =
- <strong>Sistemati</strong> bugs vari
- <strong>Implementato</strong> il sitema che limita la visibilit� delle circolari ai soli destinatari
= 1.0 =
- <strong>Sistemati</strong> bugs vari
- <strong>Inserita</strong> la possibilit� di inviare le circolari per email attraverso il plugin <strong>ALO EasyMail Newsletter</strong>
- <strong>Modificata</strong> la firma delle circolari, adesso per le circolari sindacali viene chiesta la conferma.
- <strong>Sistemati</strong> alcuni problemi in fase di firma delle circolari, che adesso &egrave; possibile firmare anche nel Front End del sito.
= 0.2 =
- <strong>Inserito</strong> lo shortcode da inserire nella pagina che elenca le circolari
- <strong>Sistemati</strong> bugs vari
= 0.1 =
- <strong>Seconda versione</strong>
= 0.01 =
- <strong>Prima versione</strong>
 == Upgrade Notice ==
Aggiornare sempre il plugin all'ultima versione fino a che non si arriva ad una versione stabile ed operativa

== Note ==
Versione in fase di test 
Da utilizzare tenendo in considerazione che potrebbero essere presenti errori e malfunzionamenti. Per segnalare errori o problemi di utilizzo usare l'indirizzo email ignazio.scimone@gmail.com segnalando il sito in cui &egrave; installato il plugin, una breve descrizione del problema riscontrato, la persona di riferimento con indirizzo email.
Non prendo in considerazione richieste non corredate da tutti i dati sopraelencati. 

