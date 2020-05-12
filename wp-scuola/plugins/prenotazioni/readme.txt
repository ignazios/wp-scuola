=== Prenotazioni ===
Contributors: Scimone Ignazio
Tags: Prenotazione spazi, Prenotazioni Aule,booking meeting rooms
Requires at least: 3.8
Tested up to: 5.2.4
Stable tag: 1.6.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sistema di gestione delle prenotazioni delle aule, laboratori, sale conferenza etc..

== Description ==
Questo plugin permette la gestione delle prenotazioni di spazi come aule, laboratori, sale conferenza etc.. 
Le prenotazioni possono essere realizzate solo dagli utenti registrati al sito mentre i visitatori del sito possono solo vedere il catalogo degli spazi messi a disposizione dalla struttura
Il plugin utilizza Jquery massivamente sia nel lato amministrativo che nella parte pubblica del sito.
== Installation ==

Di seguito sono riportati i passi necessari per l'installazione del plugin.


1. Scaricare il plugin dal repository di wordpress
2. Attivare il plugin dal menu Plugins
3. Impostazioni dei parametri
4. Creazione degli spazi 
5. Inizio memorizzazione prenotazioni
== Screenshots ==

1. Pannello Impostazioni
2. Custom Post Type per la codifica degli spazi
3. Finestra prenotazione spazi
4. Dettaglio informazioni spazio attivato al passaggio del mouse sul nome dello spazio
5. Finestra dati registrazione nuova prenotazione
6. Finestra cancellazione prenotazione
7. Finestra dati prenotazione
8. FrontEnd finestra <strong>Nuova</strong> prenotazione (solo per utenti registrati)
9. FrontEnd finestra <strong>Statistiche</strong> (solo per utenti registrati)
10. FrontEnd finestra <strong>Catalogo Spazi</strong>
== Changelog ==
= 1.6.6 =
- <strong>Aggiunta</strong> la possibilità di personalizzare l'interfaccia di frontend inserendo nel proprio tema i files:
	- wp-content/themes/NOME DEL TEMA/plugins/prenotazioni/js/Prenotazioni_FrontEnd.js
	- wp-content/themes/NOME DEL TEMA/plugins/prenotazioni/lib/frontend.php
partendo dall'originale si possono apportare le modifiche necessarie per adeguarla alle impostazioni del proprio tema
= 1.6.5 =
- <strong>Adeguamento</strong> alla versione 5 di Wordpress ed al nuovo editor Gutenberg
= 1.6.4 =
- <strong>Corretto</strong> bug che permetteva ad un utente di cancellare le prenotazioni di altri utenti
= 1.6.3 =
- <strong>Aggiunta</strong> opzione che permette di visualizzare le informazioni di chi ha prenotato e le note della prenotazione a tutti gli utenti nel backend
= 1.6.2 =
- <strong>Corretti</strong> piccoli bugs minori
= 1.6.1 =
- <strong>Corretto</strong> bug che rendeva incompatibile il plugin con il plugin Gestione Circolari
= 1.6 =
- <strong>Modificata</strong> l'iconografia del Backend
- <strong>Inserita</strong> la possibilit&aacute; di inviare email di notifica quando un utente esegue la registrazione di uno spazio. Le mail possono essere inviate all'amministratore e/o all'utente che ha effettuato la prenotazione.
- <strong>Corretto</strong> errore nel FrontEnd che non permetteva la prenotazione multisettimanale.
- <strong>Incrementato</strong> il sistema di sicurezza del codice in fase di prenotazione.
- <strong>Modificato</strong> il ruolo minimo per la gestione del report settimanale di occupazione degli spazi. Adesso è possibile visualizzare e stampare il piano settimanale già con il ruolo di contributore.
= 1.5.6 =
- <strong>Corretta</strong> la gestione dei report settimali sia del FrontEnd che del Backend
= 1.5.5 =
- <strong>Corretto</strong> la generazione dell'elenco delle settimane di cui stampare il report
- <strong>Corretto</strong> funzionamento frontend colorazione dei pulsanti in fase di prenotazione
= 1.5.4 =
- <strong>Corretto</strong> bug nel frontend
= 1.5.3 =
- <strong>Modificato</strong> il sistema di prenotazione del FrontEnd per renderlo utilizzabile sui dispositivi mobili
= 1.5.2 =
- <strong>Migliorata</strong> la visualizzazione sui dispositivi mobili del FrontEnd
- <strong>Aggiunti</strong> allo ShortCode Prenotazioni due parametri: schede e titoli
	<strong>schede</strong> valore di default nuovo,statistiche,spazi [Testi separati da virgola] definisce le scede che devono essere visualizzate, indicarne 1,2 oppure tutte e 3 nel caso si indica una sola scheda, viene visualizzata senza la linguette ed il titolo viene visualizzata come titolo della finestra
	<strong>titoli</strong> valore di default Dati Nuova Prenotazione,Statische mie Prenotazioni,Catalogo Spazi [Testi separati da virgola] obbligatoriamente tre elementi in corrispondenza della scheda se non presente la scheda indicare solo la virgola
= 1.5.1 =
- <strong>Sistemato</strong> errore che non permetteva l'apertura della pagina dei parametri
= 1.5 =
- <strong>Sistemati</strong> alcuni errori. Il più importante è quello che nel BackEnd bloccava la pagina dopo la cancellazione di una prenotazione
- <strong>Migliorata</strong> la sicurezza.
- <strong>Implementato</strong> la nuova pagina Bacheca in vengono spiegati tra l'altro l'uso ed i parametri degli shortcode
- <strong>Implementato</strong> un nuovo shortcode <em>OccupazioneSpazio</em> con i segienti parametri:
	<strong>titolo</strong> valore di default Piano occupazione Spazio [Testo Libero] si può definire un testo personalizzato per ogni spazio
	<strong>didascalia</strong> valore di default si [si/no] indica se visualizzare o meno la didascalia con il colore che indica l'occupazione dello spazio nello specifico giorno/ora
	<strong>etichetta_spazio</strong> valore di default Spazio [Testo libero] testo che viane visualizzato prima del nome dello spazio
	<strong>coloreoccupato</strong> valore di default defo [def/colore in base alle specifiche CSS] def indica il colore definito nelle impostazioni, altrimenti può essere inserito un colore con il nome mnemonico es. red o il valore esadecimale es. #1f4f7a
	<strong>visibilità</strong> valore di default Tutti [Tutti/Visitatori/Utenti] se viene specificato 
		<em><strong>Tutti</strong></em>, la finestra con le informazioni verrà visualizzata a tutti a prescindere se sono loggati o meno
		<em><strong>Visitatori</strong></em>, la finestra viene visualizzata solo ai visitatori e non agli utenti loggato
		<em><strong>Utenti</strong></em>, la finestra viene visualizzata solo agli utenti loggati.
= 1.4 =
- <strong>Implementato</strong> la stampa delle prenotazioni settimanali degli spazi
- <strong>Implementata</strong> nuova opzione per le premotazini settimanalmente. In Parametri è stata inserita l'opzione <strong><em>La Prenotazione può essere effettuata dal Lunedi al Venerdi della settimana precedente</em></strong>
= 1.3 =
- <strong>Corretti</strong> bug nella procedura di prenotazione nel frontend, mancava il parametro numero settimane (prenotazioni ripetute)
= 1.2 =
- <strong>Corretti</strong> piccoli bugs
- <strong>Aggiunto</strong> parametro numero settimane nelle prenotazioni del backend che permette di realizzare prenotazioni multiple. 
- <strong>Aggiunta</strong> voce di menu nel backend <em><strong>Mie prenotazioni</strong></em> che permette di di visualizzare tutte le prenotazioni registrate e di cancellare quelle future 
= 1.1.2 =
- <strong>Corretto</strong> errore di visualizzazione nel frontend della lisa degli spazi per i visitatori del sito
= 1.1.1 =
- <strong>Corretti</strong> limite di 5 spazi che potevano essere gestiti nel frontend
= 1.1 =
- <strong>Corretti</strong> limite di 5 spazi che potevano essere gestiti nel backend
= 1.0 =
- <strong>Corretti</strong> vari bugs
= 0.2 =
- <strong>Corretti</strong> vari bugs: Motivazione prenotazione e numero ore prenotazione, alla seconda registrazione consecutiva non venivano riportate correttamente
- <strong>Inserito</strong> nuovo parametro <em>Numero ore entro cui bisogna fare le prenotazioni</em> disabilita le prenotazioni fino a N. ore dal momento della registrazione
- <strong>Inserito</strong> nuovo parametro <em>Colore Ore non Prenotabili</em>
- <strong>Beta version</strong>
= 0.1 =
- <strong>Alfa version</strong>
 == Upgrade Notice ==
Aggiornare sempre il plugin all'ultima versione fini a che non si arriva ad una versione stabile ed operativa
== Note ==
Per segnalare errori, problemi di utilizzo o per promuovere modifiche usare l'indirizzo email ignazio.scimone@gmail.com segnalando il sito in cui e' installato il plugin, una breve descrizione del problema riscontrato, la persona di riferimento con indirizzo email.
Non prendo in considerazione richieste non corredate da tutti i dati sopraelencati. 
