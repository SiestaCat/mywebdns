<?
// Variabili globali
$array_languages = array("Italiano","Inglese");
$array_languages_symbols = array("it","uk");

// Menu'
$M_user  = "UTENTE";
$M_group = "GRUPPO";

// DNS Menu'
$M1_title = "DNS";
$M1_item1 = "Aggiungi";
$M1_item2 = "Configura";
$M1_item3 = "Reload";

// Domain Menu'
$M2_title = "Dominio";
$M2_item1 = "Aggiungi";
$M2_item2 = "Cerca";
$M2_item3 = "Recupera";

// Adminitration Menu'
$M3_title = "Amministrazione";
$M3_item1 = "Configurazione";
$M3_item2 = "Utenti";
$M3_item3 = "Gruppi";
$M3_item4 = "Cambia password";

// Main Page
$MainTitle = "Che cos'è myWebDNS?";
$MainTitle = "What is myWebDNS?";
$Description = "myWebDNS è un software basato su MySQL/PHP per amministrare un server DNS configurato con Bind 9. Permette sia di operare come amministratore per creare  e gestire il DNS che come utente finale per poter gestire i domini. Il programma \xe8 sviluppato complemtamente in PHP e usa Javascript per effettuare diversi check di controllo.";
$License = "Questo software è distribuito sotto licenza GPL.";
$Info = "Per maggiori informazioni fare riferimento alla pagina del progetto: ";
$Comment = "Per segnalare errori o commenti mandami un email a: ";

// Form
$Mod_ACLNot	= "Notify";
$Mod_ACLQry	= "Query";
$Mod_ACLTrx	= "Transfer";
$Mod_ACLUpd	= "Update";
$Mod_Chg_Date	= "Data ultima modifica";
$Mod_Cmd_Reload	= "Comando reload DNS";
$Mod_Create_Date= "Data di creazione";
$Mod_Descr	= "Descrizione";
$Mod_Dir_Named	= "Path completo named.conf";
$Mod_Dir_Zones	= "Directory zones";
$Mod_DNS	= "DNS";
$Mod_DNS_FQDN	= "DNS FQDN";
$Mod_DNS_For	= "DNS Forwarders";
$Mod_DNS_Prim	= "DNS Primario";
$Mod_DNS_Sec	= "DNS Secondario";
$Mod_Dom	= "Dominio";
$Mod_Dom_Mas	= "Master";
$Mod_Dom_Slv	= "Slave";
$Mod_Dom_For	= "Forward";
$Mod_Dom_Lev	= "Livello";
$Mod_DomainHandled	= "Numero di domini attualmente gestiti : ";
$Mod_Expire	= "Expire";
$Mod_General	= "Generale";
$Mod_Group 	= "Gruppo";
$Mod_Host_DNS	= "Host DNS";
$Mod_Inc_Named	= "Path completo include zone named.conf";
$Mod_IP		= "IP";
$Mod_IP_Forw	= "IP DNS Forwarders";
$Mod_IP_Mast	= "IP DNS Master";
$Mod_IP_Net	= "IP/Netmask";
$Mod_Language	= "Linguaggio";
$Mod_Login 	= "Login";
$Mod_Minimum	= "Minimum";
$Mod_Name  	= "Nome";
$Mod_New_Group	= "Nuovo Gruppo";
$Mod_New_Record = "Nuovi Records";
$Mod_NoACL	= "Nessuna ACL definita";
$Mod_NoGroupMsg	= "Nessun gruppo";
$Mod_Old_Group	= "Vecchio Gruppo";
$Mod_Pwd	= "Password";
$Mod_Pwd_Old	= "Vecchia Password";
$Mod_Pwd_New	= "Nuova Password";
$Mod_Pwd_New_Re	= "Ripeti Nuova Password";
$Mod_Pwd_Re	= "Ripeti Password";
$Mod_Refresh	= "Refresh";
$Mod_Retry	= "Retry";
$Mod_RisSearch	= "Record trovati : ";
$Mod_Root_DNS	= "Root DNS";
$Mod_Serial	= "Seriale";
$Mod_State	= "Stato";
$Mod_State_AM	= "Zona da creare";
$Mod_State_AS	= "Zona da trasferire";
$Mod_State_D	= "Zona da cancellare";
$Mod_State_M	= "Zona da modificare";
$Mod_State_N	= "Nessuno";
$Mod_State_U	= "Sconosciuto";
$Mod_TTL	= "TTL";
$Mod_Type	= "Tipo";
$Mod_Type_Zone	= "Tipo di Zona";
$Mod_Zone_File	= "File di zona";

// Tab
$Tab_Dom	= "dominio";
$Tab_Dig	= "dig";
$Tab_Nslookup	= "nslookup";
$Tab_Whois	= "whois";

// ALT Image
$Alt_ACL	= "Access List";
$Alt_Change	= "Modifica";
$Alt_Check	= "Controlla";
$Alt_Delete	= "Cancella";
$Alt_Info	= "Informazioni";
$Alt_Lock	= "Proteggi da cancellazione";
$Alt_Nslookup	= "nslookup";
$Alt_Recover	= "Recupera";
$Alt_View	= "Visualizza";
$Alt_View_Users = "Visualizza utenti";
$Alt_Unlock	= "Sproteggi da cancellazione";

// Errori
define("_ACLDomainError","Nessuna ACL definita");
define("_CheckInputError","Non sono stati inseriti tutti i parametri correttamente");
define("_CheckIPError","IP inserito non corretto");
define("_CheckRecoverDomain","Nessun dominio da recuperare");
define("_CopyLanguagesError","Errore durante la copia del file di linguaggio");
define("_DomainRecoverError","Recupero impossibile. Questo dominio già esiste");
define("_DomainRegError","Dominio già esistente");
define("_DNSError","Nessun DNS configurato");
define("_DNSRegError","DNS già registrato");
define("_ErronNoLanguage","Nessun linguaggio");
define("_FindDomainError","Dominio non trovato");
define("_FindFileError","File non trovato");
define("_NotFileLanguagesError","File di linguaggio non trovato");
define("_SelectDomainError","Nessun dominio selezionato");
define("_TLDError","Il dominio inserito non è riconosciuto");
define("_SQLQueryError","Impossibile eseguire il comando SQL");

// Operazioni o bottoni
$Button_Add 	= "Aggiungi";
$Button_Apply 	= "Applica";
$Button_Cancel	= "Annulla";
$Button_Change 	= "Modifica";
$Button_Close	= "Chiudi";
$Button_Continue= "Continua";
$Button_Delete 	= "Cancella";
$Button_OK	= "OK";
$Button_Reset 	= "Reset";
$Button_Save    = "Salva";
$Button_Select 	= "Seleziona";

// Messaggi di output
define("_AccessDenied","Accesso negato!");
define("_AddMsg","Inserimento effettuato");
define("_DeleteMsg","Cancellazione effettuata con successo");
define("_ErrorTitle","ERRORE");
define("_ModifiedOkMsg","Modifica effettuata");
define("_NoUserMsg","Nessun utente");
define("_PwdChanged","Password modificata");
define("_ReloadConfig","Caricamento configurazione in corso...");
define("_ReloadMsg","Reload effettuato con successo");
define("_ResultTitle","RISULTATO");
define("_UpdateDomain","Aggiornamento del dominio in corso...");

// Header di pagina
// DNS
define("_AddDNSHeader","Aggiungi DNS");
define("_ConfigDNSHeader","Configura DNS");
define("_DeleteDNSHeader","Cancella DNS");
define("_InfoDNSHeader","Informazioni DNS");
define("_nslookupDNSHeader","nslookup DNS");
define("_ModifyDNSHeader","Modifica DNS");
define("_ReloadDNSHeader","Reload DNS");
define("_ViewFileHeader","Visualizza file");

// Dominio
define("_ACLDomainHeader","Access List dominio");
define("_AddDomainHeader","Aggiungi dominio");
define("_EditDomainHeader","Modifica dominio");
define("_InfoDomainHeader","Informazioni dominio");
define("_RecoverDomainHeader","Recupera dominio");
define("_SearchDomainHeader","Domini trovati");
define("_SelectDomainHeader","Cerca dominio");
define("_VerifyDomainHeader","Controlla dominio");
define("_ViewDomainHeader","Visualizza dominio");

// Amministrazione
define("_AddGroup","Aggiungi Gruppo");
define("_AddUser","Aggiungi Utente");
define("_AdminConfig","Configurazione");
define("_AdminGroups","Amministrazione Gruppi");
define("_AdminPwd","Cambia Password");
define("_AdminUsers","Amministrazione Utenti");
define("_ChangePwd","Cambia Password");
define("_UserGroup","Lista Utenti");
?>
