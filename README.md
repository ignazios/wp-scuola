# WPScuola
è un tema per [**WordPress 5**](https://it.wordpress.org/) (CMS open-source) specifico per le scuole italiane. 

Il tema è basata sul tema originale [**ItaliaWP2 Versione: 1.1.6**](https://github.com/italia/design-wordpress-theme-italiaWP2) autore [**Boris Amico**](http://italiawp.borisamico.it/). 
Del tema originale è stato mantenuto il framework e l'header compreso il menu principale. I template di pagine, articoli, archivi e l'impostazione della home page sono stati personalizzati in funzione delle esigenze del mondo della scuola.

Un grazie particolare a [**Andrea Smith**](https://www.facebook.com/profile.php?id=100001279909352) per l'aiuto prezioso e paziente nel testing continuo dle tema.

WPScuola utilizza:
- la libreria [**Bootstrap Italia**](https://italia.github.io/bootstrap-italia/) basata su [**Bootstrap 4.3.1**](https://getbootstrap.com/),di cui eredita tutte le funzionalità, componenti, griglie e classi di utilità, personalizzandole secondo le Linee Guida di Design per i siti web della Pubblica Amministrazione. 
- la libreria [**jQuery (licenza)**](https://jquery.org/license/)
- la libreria di icone [**Font Awesome (licenza)**](https://github.com/FortAwesome/Font-Awesome#license)

***Si ricorda che il tema è ancora in fase di sviluppo e potrebbero essere apportate in futuro modifiche significative.***
![Logo](https://github.com/ignazios/wp-scuola/blob/master/screenshot.png)
#Licenza
WPScuola è rilasciato con licenza [**AGPL-3.0**](https://opensource.org/licenses/AGPL-3.0) (https://www.gnu.org/graphics/agplv3-155x51.png) che permette il libero utilizzo anche per uso commerciale.
## Manuale
## Tema WPScuola
https://github.com/ignazios/wp-scuola
## Video
[**Presentazione del tema**](https://www.youtube.com/watch?v=3DRjP9RIVzo&t=20s)
[**Tutorial, funzionalità ed impostazioni**](https://www.youtube.com/playlist?list=PLqSQgRX-fP44XOIxEU6q7PD74QzYK0KEs)
## Sito demo
[**ISItalia**](http://isitalia.eduva.org/)
## Scarica il tema 
[Qui](https://raw.githubusercontent.com/ignazios/wp-scuola/master/wp-scuola.zip) puoi scaricare il **Pacchetto** utilizzabile per la prima installazione o per l'aggiornamento manuale
## Supporto
facebook: https://www.facebook.com/wpscuola/
## Tema verificato per le seguenti versioni
 - **Wordpress** 	*5.0* - *6.3*
 - **PHP** 		*8*
## Shortcode
	[articoli id_categoria=ID della categoria da cui estrarre gli articoli (=0 oppure omesso se si vogliono elencare gli articoli per tag)           
			  id_tag=ID del tag da cui estrarre gli articoli (=0 oppure omesso se si vogliono elencare gli articoli per categoria) 		  
			  numero=Numero degli articoli da visualizzare (Default 5)		  
			  imgevidenza=si/no Indica se visualizzare l'immagine in evidenza (Default no)]		  
	Visualizza ***numero*** di articoli di una specifica categoria/tag
		
	[gfolderdrive idfolder=Id della cartella che è l'ultimo elemento del link              
				tipovis=grid/list modaità di visualizzazione             
				border=dimensione del bordo (0)              
				width=larghezza del frame in %              
				height=altezza del frame in px              
				scrolling=yes/no/auto]
	Visualizza una cartella di Googlo Drive Condivisa
	
	[faq order=Ordine degli elementi. Se omesso 'ASC'
         orderby=Campo su cui eseguire l'ordinamento. Se omesso 'title'
		 posts_per_page=Numero di elemnti da visuazlizzare. Se omesso -1(Tutti)
         gruppi=Elenco separato da , dei gruppi da visualizzare. Se omesso vengono estratti tutti i gruppi]
	Visualizza le FAQ
				
	Visualizza una finestra con i files inclusi in una cartella condivisa di Google Drive 
## Change log
- **1.3.1**
  - ***Aggiornata*** l'interfaccia del plugin Albo Pretorio personalizzato per il tema.
- **1.3**
  - ***Aggiornamento*** della libreria Bootstrap Italia alla versione 1.6.4 
  - ***Revisionato*** il codice del modulo Gestione Circolari
  - ***Revisionato*** il codice del modulo Prenotazioni
  - ***Revisionato*** il codice del modulo Orario Argo
  - ***Revisionato*** il codice generale del tema
  - ***Eliminato*** il widget Calendario Event-manager
- **1.2.8**
  - ***Aggiunta*** sezione Script in Personalizzazione. Adesso è possibile inserire degli script nell'Header come avveniva nel Footer.
- **1.2.7**
  - ***Corretti*** i widget Scuola  Servizi e Scuola Link che con la versione di Wordpress 5.9 non si ridimensionano le immagini
  - ***Corretto*** il widget Scuola Articoli Griglia
- **1.2.6**
  - ***Corretti*** alcuni elementi per compatibilità con la versione 5.9 di Wordpress
  - ***Corretto*** link leggi tutto nel widget Scuola Articoli
- **1.2.5**
  - ***Corretti*** alcuni Bug di cui uno di sicurezza
  - ***Sistemato*** la renderizzazione del blocco file PDF in anteprima. Nel caso in cui si attivava con i valori di defalt veniva visualizzato a dimensione ridotta
  - ***Corretti*** alcuni errori di accessibilità
- **1.2.4**
  - ***Corretto*** un errore nella gestione del blocco File che visualizzava il blocco anteprima di dimensione ridotta se si lasciava il vaolre di default
- **1.2.3**
  - ***Corretti*** alcuni bug minori
  - ***Verificata*** compatibilità con la versione 5.8 di Wordpress
  - ***Modificato*** il blocco file di Gutenberg per adeguarlo alla visualizzazione dei file Pdf
  - ***Corretti*** alcuni errori del modulo Circolari
- **1.2.2**
  - ***Corretti*** alcuni bug minori
  - ***Aggiunta*** la gestione degli **Eventi Scolastici**. Per attivare il Modulo andare in Aspetto > Personalizza > Impostazioni Scuola > Moduli funzionalità > Attiva il modulo Eventi
				   Si attiverà la voce di menu Eventi Scolastici che permette la gestione degli eventi che sono un CustomPostType con i seguenti campi specifici:
				   URL dell'Evento, Titolo del pulsante dell'Evento, Destinazione link Evento, Data di scadenza dell'Evento e Banner Evento (750x1200)
				   Gli eventi vengono visualizzati in Home Page ad inizio pagina, dopo l'eventuale Hero, vengono implementati tramite schede Flip sul fronte c'è il Banner e sul retro il Riassunto dell'evento con il link alla pagina dell'evento e l'eventuale pulsante per andare al link dell'evento
- **1.2.1**
  - ***Corretti*** alcuni bug minori:
  Footer; gestione indirizzi email
  Colore Testo Bottoni
  Plugin Circolari; corretto errore gestione testi 
  Widget Articoli Griglia; nelle impostazioni non veniva visualizzato correttamente il flag Leggi Tutto
  Widget Link; corretto il testo alternativo dell'immagine
  - ***Aggiunto*** in Aspetto > Personalizza > Impostazioni Scuola > Impostazioni del Footer inserita la sezione **Codice da inserire nel footer** che permette di inserire script nel footer delle pagine es. scpript per l'inserimento di UserWay
  - ***Corretto*** il comportamento dell'archivio degli articoli che mostrava in testa all'elenco gli articoli in evidenza, adesso vengono riportati tutti in ordine cronologico
  - ***Modificata*** la gestione grafica dell'archivio degli articoli risultato della ricerca
  - ***Aggiunta*** nuova modalità di visualizzazione dei blocchi **Piatti**(quello attuale),**Card**. Aggiunto anche il campo ***Vai a*** per la visualizzazione Card 
- **1.2**
  - ***Corretto*** un bug delo shortcode [at-search] di Amministrazione Trasparente
  - ***Aggiunti*** gli stili circle - disc - square da utilizzare con l'editor Gutenberg Avanzate > Classe/i CSS aggiuntiva/e per modificare l'elemento grafico dell'enco puntato
  - ***Aggiunto*** un parametro al widget Servizi che permette di escludere deelle categorie di servizi nella visualizzazione del servizio quando si sceglie la Tipologia di Visualizzazione: Per tipologia di servizio
#content ul.tratto li:before {content: "-";padding-right: 1em;font-weight: bold;}
#content ul.tratto l
- **1.1.9**
  - ***Corretto*** un bug del modulo circolari
- **1.1.8**
  - ***Aggiunta*** area in Tema > Impostazioni Scuola > Metadati comunicazioni in cui si possono impostare:
                           Attiva Visualizzazione Data
						   Attiva link archivio Data
						   Attiva Visualizzazione Autore
						   Attiva link archivio Autore
						   Attiva il conteggio delle visite di pagine/articoli/CPT
- **1.1.7**
 - ***Aggiunto*** shortcode [feedrss] che permette di visualizzare i link ai Feed RSS per Categorie e/o Tags all'interno di una pagina
  - ***Corretti*** alcuni bug del modulo circolari
  - ***Corretti*** alcuni bug minori
- **1.1.6**
  - ***Modificato*** lo shortcode [faq] adesso se si omette il parametro gruppi, vengono visualizzati tutti i gruppi di FAQ
  - ***Aggiunto*** Widget Scuola Eventi che visualizza un elenco di eventi del plugin Events Manager
  - ***Aggiunto*** l'IBAN dei dati della scuola, si imposta in Aspetto > Personalizza > Impostazioni Scuola > Dati Amministrazione e viene visualizzato nel footer
  - ***Corretti*** alcuni bug minori
- **1.1.5**
  - ***Corretti*** alcuni errori sul plugin dell'Albo
- **1.1.4**
  - ***Impostato*** lo stile di default delle tabelle. Le tabelle verranno Bordate, avranno l'header ed il footer Dark e l'effetto hover
  - ***Corretti*** alcuni errori di validazione
  - ***Aggiunti*** filtri per ripulire il nome del file ed il titolo che viene caricato nei media.
  - ***Aggiunto*** per le immagini caricate nei media la Didascalia e la Descrizione uguali al Titolo
  - ***Aggiunto*** shortcode [canccookies] che permette di visualizzare un pulsante/link per la cancellazione dei Cookies. Parametri vis[bottone/link] e testo; il testo che verrà visualizzato sul bottone/link
  - ***Aggiunto*** shortcode [viscookies] che permette di visualizzare i cookies che il sito ha memorizzato in locle.
- **1.1.3**
  - ***Rivisto*** il modulo delle prenotazioni
  - ***Aggiunta***  opzione **Nascondi Login** in Aspetto > Personalizza > Impostazione Scuola > Impostazione dell'Header, che permette di togliere il link ***Accesso*** nel pre Header
  - ***Corretti*** alcuni bugs minori
- **1.1.2**
  - ***Corretti*** alcuni bugs minori
  - ***Modificata*** la gestione dei servizi, aggiunta la possibilit di personalizzare il testo del terzo pulsante di default Descrizione
- **1.1.1**
  - ***Corretto*** un bug nella gestione dei Servizi
- **1.1**
  - ***Modificato*** lo shortcode [articoli ]. Aggiunto il parametro id_tag, se specificato al posto di id_categoria visualizza l'elendo di articoli per tag
  - ***Modificato*** il widget Scuola Link, aggiunto il parametro **Numero schede su schermi Large:** si possono psecificare 3/4 schede per la viusalizzazione su schermi Large
  - ***Aggiunto*** il tempate di pagina Nuvola di Tag, che permette di visualizzare una pagina che riporta la nuvola di tag con grandezza proporzionale del Tag
  - ***Aggiunta*** in Tema > Personalizza > Impostazione Scuola > Moduli funzionalità, la voce **Attiva il conteggio delle pagine/articoli/CPT** che permette di attivare/disattivare il conteggio delle visualizzazioni di Articoli/pagine/CPT
  - ***Aggiunto*** in Tema > Personalizza > Impostazione Scuola > Moduli funzionalità, il pulsante che permette di azzerare tutti i contatori di visualizzazione del conteggio delle visualizzazioni di Articoli/pagine/CPT
  - ***Corretti*** alcuni bug minori
- **1.0.9**
  - ***Creato*** lo shortcode [gfolderdrive idfolder=Id della cartella che è l'ultimo elemento del link 
                tipovis=grid/list modaità di visualizzazione
                border=dimensione del bordo (0)
                width=larghezza del frame in %
                height=altezza del frame in px
                scrolling=yes/no/auto] che permette di visualizzare una cartella condivisa di Google Drive
  - ***Aggiunto*** widget che simulauna bacheca, al momento, con la possibilitàdi visualizzare 4 bottoni sensibili all'Hover con icona di fontawesome, testo e link
  - ***Sistemata*** il template di pagina della pagina degli articoli (Blog) in cui vengono visualizzati tutti gli articoli
  - ***Corretti*** alcuni bug minori
  - ***Aggiunto*** nel Blocco Scuola la possibilità di inserire nella prima cartella un link; Immagine + Testo e la didascalia del video
  - ***Modificato*** il Widget Trasparenza, adesso si possono personalizzare i testi dei 4 pulsanti  
- **1.0.8**
  - ***Modificato*** shortcode del modulo per la gestione dell'orario da Argo Darwin. Lo shortcode è:
		
		[orarioDarwin orari="docenti;classi;classiorario;classiricevimento;aule"]
		l'opzione orari può assumere una qualsiasi combinazione degli elementi tra:		
		docenti           => Visualizza una comboBox con l'elenco degli insegnati. Dopo aver selezionato il docente bisogna premere sul pulsante a forma di calendario per visualizzare la tabella con l'orario settimanale del docente		
		classi            => Visualizza una comboBox con l'elenco delle classi. Dopo aver selezionato la classe bisogna premere sul pulsante a forma di calendario per visualizzare la tabella con l'orario settimanale delle lezioni della classe o il pulsante a forma di dialogo per visualizzare la tabella con l'orario settimanale di ricevimento dei docenti della classe	
		classiorario      => Visualizza una comboBox con l'elenco delle classi. Dopo aver selezionato la classe bisogna premere sul pulsante a forma di calendario per visualizzare la tabella con l'orario settimanale delle lezioni della classe	
		classiricevimento => Visualizza una comboBox con l'elenco delle classi. Dopo aver selezionato la classe bisogna premere sul pulsante a forma di dialogo per visualizzare la tabella con l'orario settimanale di ricevimento dei docenti della classe	
		aule              => Visualizza una comboBox con l'elenco delle aule. Dopo aver selezionato l'aula bisogna premere sul pulsante a forma di calendario per visualizzare la tabella con l'orario settimanale dell'aula	
		questi parametri possono devono essere inseriti nella posizione in cui si vuole che vengano visualizzati ed è possibile specifica solo quelli che interessano
- **1.0.7**
  - ***Aggiunta*** la gestione dell'orario da Argo Darwin, la gestione viene fatta attraverso il file XML esportato dall'applicazione. Modulo attivabile
  - ***Aggiornata*** la personalizzazione dell'albo in base all'ultimo aggiornamento del plugin 
  - ***Aggiunta*** la possibilità di impostare il colre dei bottoni
  - ***Aggiunto*** ai Servizi il terzo link
  - ***Aggiunta*** nei Servizi la possibilità di impostare il testo dei bottoni
  - ***Modificato*** il Breadcrumb che adesso viene allineato a sinistra
  - ***Modificato*** l'offset delle pagine
- **1.0.6**
  - ***Corretti*** inserita descrizione del sito nell'Header solo per schermi > 1200px
- **1.0.5**
  - ***Corretti*** alcuni errori nel modulo Gestione Circolari
- **1.0.4**
  - ***Corretti*** alcuni errori nel modulo Gestione Circolari
- **1.0.3**
  - ***Sospesa*** la visualizzazione personalizzata dell'Albo OnLine in attesa della pubblicazione dell'aggiornamento del plugin
- **1.0.2**
  - ***Aggiunto*** il supporto per gli stili del plugin ***Gutenberg Avanzato***(https://it.wordpress.org/plugins/advanced-gutenberg/)
  - ***Modificata*** la gestione del Menu di navigazione sottopagine sulla destra. Adesso con meno di 5 elementi ilmenu si attacca in alto e non scrolla insieme alla pagina
  - ***Corretto*** il template di pagina Mappa del sito
  - ***Coretti*** alcuni Bug minori
- **1.0.1**
  - ***Modificata*** la visualizzazione dell'archivio temporale
  - ***Creato*** lo shortcode [articoli id_categoria="0" numero="5" imgevidenza="si"] che permette di visualizzare gli ultimi **numero** articoli della categoria **id_categoria** e si può visualizzare l'immagine in evidenza impostando ilparametro **imgevidenza** uguale a si
  - ***Modificata*** la visualizzazione dell'archivio temporale
  - ***Aggiunta*** la gestione integrata dei breadcrumb. Vengono visualizzati in tutte le pagine del sito tranne che in Home Page
- **1.0.0**
  - ***Revizione*** totale del codice. Il tema adesso è basato su Italiawp2 per quanto riguarda la configurazione del framework Bootstrap Italia. E' stato rivisto completamente l'header ed il menu principale.
  - ***Sono stati tolti*** i menu: mega menu, menu social
  - ***E' stato revisionato*** tutto il codice.
  - ***Sono state effettuate*** verifiche sull'accessibilità delle principali pagine del tema che hanno ottenuto esito positivo. Stiamo lavorando per verificare l'intero tema.
  - ***E' stato aggiunto*** un sistema che tiene traccia e visualizza il numero di volte che un articolo/pagina viene visualizzato.
- **0.2.1**
  - ***Modificato*** la visualizzazione del blocco file di Gutenberg, adesso visualizza l'icona dle tipo file, la dimensione e la descrizione gon grafica compatibile con il tema
  - ***Aggiunti*** i template del singolo link e della pagina dei dettagli per il plugin ***Download Manager*** con le impostazioni del tema
  - ***Modificato*** l'Header delle pagine in formato mobile, con menu Hamburger fino alla risoluzione 1140px
  - ***Corretti*** alcuni bag minori nel widget Link
- **0.2.0**
  - ***Corretti*** alcuni bag minori nel widget Link
- **0.1.9**
  - ***Aggiunta*** opzione al **Blocco Scuola**  ***Codice del video*** che permette di visualizzare nella prima cartella un video al posto del testo della pagina selezionata. Da usare per inserire, ad es. il video promozionale della scuola
  - ***Aggiunta*** opzione al **Denominazione del sito**  ***Titolo corto*** che viene riportato nell'header in modalità sticky 
  - ***Aggiunta*** voce al **Widget Scuola Articoli Griglia**  ***Seleziona la categoria: *** Tutti gli articoli in modo da visualizzare il blog 
  - ***Aggiunta*** opzione al **Widget Scuola Link**  *** Visualizza a pieno schermo: *** che permette di visualizzare il blocco in full-width. Utili soprattutto per la presentazione degli indirizzi di studio 
  - ***Aggiunto*** il conteggio della visualizzazione degli Articoli
  - ***Aggiunto*** il supporto dei Tag alle circolari.Adesso possono essere taggate e vengono restituite insieme gli articoli nell'archivio per Tag
  - ***Modificato*** l'**header in modalità sticky**, adesso viene riportato il nome della scuola/istituto in formato ridotto accanto al logo ed il pulsante di ricerca rimane sulla destra
  - ***Corretti*** alcuni bag minori
- **0.1.8**
  - ***Aggiunta*** opzione al **Blocco Comunicazioni in evidenza**  ***Visualizza Immagine in Evidenza*** che permette di visualizzare/non visualizzare l'immagine in evidenza del post
  - ***Aggiunta*** opzione al **Widget Scuola Articoli**  ***Visualizza Immagine in Evidenza*** che permette di visualizzare/non visualizzare l'immagine in evidenza del post
  - ***Corretti*** alcuni bag minori
- **0.1.7**
  - ***Modificati*** alcuni menu della personalizzazione. Aggiunta la sezione Personalizzazione Header e dentro è stato spostato il menu di personalizzazione dell'Amministrazione afferente  
  - ***Corretti*** alcuni bag minori
- **0.1.6**
  - ***Ridisegnata*** l'interfaccia pubblica dell'albo in funzione dell'aggiornamento 4.5 del plugin Albo OnLine 
  - ***Corretti*** alcuni bag minori
- **0.1.5**
  - ***Ridisegnata*** l'interfaccia dello shortcode [at-sezioni] 
  - ***Corretti*** alcuni bag minori
- **0.1.4**
  - ***Corretti*** alcuni bag minori
- **0.1.3**
  - ***Implementato*** sistema di importazione gruppi e circoalri da Circolari Groups
  - ***Corretti*** alcuni bag minori
- **0.1.2**
  - ***Corretti*** alcuni bag minori
- **0.1.1**
  - ***Corretti*** alcuni bag minori
- **0.1.0**
  - ***Revisionati*** e ***Tradotti*** tutti i widget implementati dal tema
  - ***Corretti*** alcuni bag minori
  - ***Aggiornati*** il file delle traduzioni wpscuola/languages/wpscuola-it_IT.po   
- **0.0.9**
  - ***Implementato*** il modulo Prenotazioni basato sul plugin omonimo con interfaccia pubblica ridisegnata
  - ***Risolti*** alcuni bug minori
  - ***Personalizzata*** l'interfaccia pubblica della visualizzazione degli allegati con Icona Titolo del file e Dimensione
- **0.0.8**
  - ***Inserito*** l'ordinamento nella visualizzazione delle Titologie di servizi
  - ***Inserito*** l'ordinamento nella visualizzazione delle Categorie di Link e dei Link 
- **0.0.7**
  - ***Inserito*** l'ordinamento nella visualizzazione dei servizi
- **0.0.6**
  - ***Implementato*** la gestione dei link
  - ***Aggiunto*** widget per la galleria dei link
  - ***Modificato*** la visualizzazione degli allegati agli articoli/pagine
- **0.0.5**
  - ***Modificato*** in rendering del blocco file dell'Editor Gutenberg
  - ***Predisposto*** tutto il testo del tema per la traduzione
  - ***Corretto*** un errore nella gestione della visualizzazione della domanda della FAQ nel FrontEnd
  - ***Corretti*** alcuni bugs minori
- **0.0.4**
  - ***Implementato*** la gestione delle lingue
  - ***Corretti*** alcuni bug del modulo Circolari
- **0.0.3**
  - ***Sistemata*** la gestione dei colori nella personalizzazione del tema Aspetto > Personalizza > Colori
  - ***Sistemato*** modulo Circolari: testi e risposte delle circolari
- **0.0.2**
  - ***Messa*** a punto dell'installazione e delle componenti della Home Page
- **0.0.1**
  - ***Pubblicazione del tema***
