# WPScuola
è un tema per [**WordPress 5**](https://it.wordpress.org/) (CMS open-source) specifico per le scuole italiane. 

Il tema è basata sul tema originale [**ItaliaWP2 Versione: 1.1.6**](https://github.com/italia/design-wordpress-theme-italiaWP2) autore [**Boris Amico**](http://italiawp.borisamico.it/). 
Del tema originale è stato mantenuto il frmework e l'header compreso il menu principale. I template di pagine, articoli, archivi e l'impostazione della home page sono stati personalizzati in funzione delle esigenze del mondo della scuola.

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
 - **Wordpress** 	*5.0* - *5.5*
 - **PHP** 		*7.0*
## Change log
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
