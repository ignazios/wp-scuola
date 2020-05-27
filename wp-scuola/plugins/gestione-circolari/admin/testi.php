<?php 
/**
 * Gestione Circolari Groups- Funzioni Gestione Testi
 * 
 * @package Gestione Circolari Groups
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @ver 2.3.2
 */
if ( !class_exists( 'Circolari_Risposta' ) )  {
	class Circolari_Risposta{
		private $IdRisposta;
		private $Risposta;
		private $RispostaMail;
		
		public function get_IDRisposta(){
			return $this->IdRisposta;
		}
		public function set_IDRisposta($value){
			$this->IdRisposta=$value;
		}
		public function get_Risposta(){
			return $this->Risposta;
		}
		public function set_Risposta($value){
			$this->Risposta=$value;
		}
		public function get_RispostaMail(){
			return $this->RispostaMail;
		}
		public function set_RispostaMail($value){
			$this->RispostaMail=$value;
		}
		function __construct($IdRisposta,$Risposta,$RispostaMail){
			$this->IdRisposta=$IdRisposta;
			$this->Risposta=$Risposta;
			$this->RispostaMail=$RispostaMail;
		}
		public function ToString(){
			return "IdRisposta=>".$this->IdRisposta." Risposta=>".$this->Risposta." RispostaMail=>".$this->RispostaMail;
		}
	}
}
if ( !class_exists( 'Circolari_Tipo' ) ) {
	class Circolari_Tipo{
		private $Tipo;
		private $DescrizioneTipo;
		private $Prefisso;
		private $Popup;
		private $Risposte=array();
	// Testo descrittivo nel box firme nella pagina di Creazione/modifica della circolare
		private $Descrizione;
	// Testo che viene visualizzato nell'elenco delle circolari'
		private $TestoElenco;
		
		public function get_Popup(){
			return stripslashes($this->Popup);
		}
		public function set_Popup($value){
			$this->Popup=$value;
		}
		public function get_Risposte(){
			return $this->Risposte;
		}
		public function set_Risposte($value){
			$this->Risposte=$value;
		}
		public function get_Tipo(){
			return stripslashes($this->Tipo);
		}
		public function is_set_Risposta($value){
			foreach ($this->Risposte as $Risposta){
				if ($Risposta==$value)
					return TRUE;
			}
			return FALSE;
		}
		public function set_Tipo($value){
			$this->Tipo=$value;
		}
		public function get_DescrizioneTipo(){
			return stripslashes($this->DescrizioneTipo);
		}
		public function set_DescrizioneTipo($value){
			$this->DescrizioneTipo=$value;
		}
		public function get_Prefisso(){
			return stripslashes($this->Prefisso);
		}
		public function set_Prefisso($value){
			$this->Prefisso=$value;
		}
		public function get_Descrizione(){
			return stripslashes($this->Descrizione);
		}
		public function set_Descrizione($value){
			$this->Descrizione=$value;
		}
		public function get_TestoElenco(){
			if (is_string($this->TestoElenco))
				return stripslashes($this->TestoElenco);
			else
				return $this->TestoElenco[0];
		}
		public function set_TestoElenco($value){
			$this->TestoElenco=$value;
		}
		
		
		function __construct($Tipo,$Popup,$DescrizioneTipo,$Prefisso,$Descrizione,$TestoElenco,$Risposte){
			$this->Tipo=$Tipo;
			$this->Popup=$Popup;
			$this->DescrizioneTipo=$DescrizioneTipo;
			$this->Prefisso=$Prefisso;
			$this->Descrizione=$Descrizione;
			$this->TestoElenco=$TestoElenco;
			$this->Risposte=$Risposte;
		}
		public function ToString(){
			return "Tipo=>".$this->Tipo." Prefisso=>".$this->Prefisso;
		}
		
		//Funzioni sull'insieme dei tipi
		public static function get_Tipi($Tipo=""){
			global $wps_Testi;
			$Elemento="";
			foreach($wps_Testi as $Testo){
				$Elemento.="
		<p class='description'>
	 		<input type='radio' id='Imposta".$Testo->get_Tipo()."' name='Sign' value='".$Testo->get_Tipo()."'";
	 		if ($Tipo==$Testo->get_Tipo())
	 			$Elemento.="checked='checked'";
	 		$Elemento.="/>
	 		<label for='".$Testo->get_Tipo()."'>".$Testo->get_Descrizione()."</label>
		</p>";
			}	
			return $Elemento;
		} 
		
		public static function get_TipoCircolare($TipoCircolare=""){
			global $wps_Testi;
			foreach($wps_Testi as $TipoC){
				if ($TipoCircolare==$TipoC->get_Tipo())
					return $TipoC;
			}	
			return NULL;
		} 
	}
}
?>