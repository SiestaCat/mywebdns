<?
// Variabili globali
$array_languages = array("Italian","English");
$array_languages_symbols = array("it","uk");

// Menu'
$M_user  = "USER";
$M_group = "GROUP";

// DNS Menu'
$M1_title = "DNS";
$M1_item1 = "Add";
$M1_item2 = "Configure";
$M1_item3 = "Reload";

// Domain Menu'
$M2_title = "Domain";
$M2_item1 = "Add";
$M2_item2 = "Search";
$M2_item3 = "Recover";

// Adminitration Menu'
$M3_title = "Administration";
$M3_item1 = "Configuration";
$M3_item2 = "Users";
$M3_item3 = "Groups";
$M3_item4 = "Change password";

// Main Page
$MainTitle = "What is myWebDNS?";
$Description = "myWebDNS is a MySQL/PHP-Web based package to manage a DNS server configured with Bind 9. It permit to operate as administrator to create and manage the DNS, and as a user to create and manage the domains. The package as written completly in PHP and use Javascript for various checks.";
$License = "This software is distribuited under a GPL license.";
$Info = "For many info refer to sourceforge page project: ";
$Comment = "For bugs & comments send me an email to: ";

// Form
$Mod_ACLNot	= "Notify";
$Mod_ACLQry	= "Query";
$Mod_ACLTrx	= "Transfer";
$Mod_ACLUpd	= "Update";
$Mod_Chg_Date	= "Last change date";
$Mod_Cmd_Reload	= "Reload DNS command";
$Mod_Create_Date= "Creation date";
$Mod_Descr	= "Description";
$Mod_Dir_Named	= "named.conf full path";
$Mod_Dir_Zones	= "Zones Directory";
$Mod_DNS	= "DNS";
$Mod_DNS_FQDN	= "DNS FQDN";
$Mod_DNS_For	= "DNS Forwarders";
$Mod_DNS_Prim	= "DNS Primary";
$Mod_DNS_Sec	= "DNS Secondary";
$Mod_Dom	= "Domain";
$Mod_Dom_Mas	= "Master";
$Mod_Dom_Slv	= "Slave";
$Mod_Dom_For	= "Forward";
$Mod_Dom_Lev	= "Level";
$Mod_DomainHandled	= "Number of domains currently handled : ";
$Mod_Expire	= "Expire";
$Mod_General    = "General";
$Mod_Group 	= "Group";
$Mod_Host_DNS	= "Host DNS";
$Mod_Inc_Named	= "Include Zone named.conf full path";
$Mod_IP		= "IP";
$Mod_IP_Forw	= "IP DNS Forwarders";
$Mod_IP_Mast	= "IP DNS Master";
$Mod_IP_Net	= "IP/Netmask";
$Mod_Language	= "Language";
$Mod_Login 	= "Login";
$Mod_Minimum	= "Minimum";
$Mod_Name  	= "Name";
$Mod_New_Group	= "New Group";
$Mod_New_Record = "New Records";
$Mod_NoACL	= "No ACL defined";
$Mod_NoGroupMsg	= "No Group";
$Mod_Old_Group	= "Old Group";
$Mod_Pwd	= "Password";
$Mod_Pwd_Old	= "Old Password";
$Mod_Pwd_New	= "New Password";
$Mod_Pwd_New_Re	= "Repeat New Password";
$Mod_Pwd_Re	= "Repeat Password";
$Mod_Refresh	= "Refresh";
$Mod_Retry	= "Retry";
$Mod_RisSearch	= "Record found : ";
$Mod_Root_DNS	= "Root DNS";
$Mod_Serial	= "Serial";
$Mod_State	= "State";
$Mod_State_AM	= "Zone to create";
$Mod_State_AS	= "Zone to transfer";
$Mod_State_D	= "Zone to delete";
$Mod_State_M	= "Zone to change";
$Mod_State_N	= "Nothing";
$Mod_State_U	= "Unknown";
$Mod_TTL	= "TTL";
$Mod_Type	= "Type";
$Mod_Type_Zone	= "Zone type";
$Mod_Zone_File	= "Zone file";

// Tab
$Tab_Dom	= "domain";
$Tab_Dig	= "dig";
$Tab_Nslookup	= "nslookup";
$Tab_Whois	= "whois";

// ALT Image
$Alt_ACL	= "Access List";
$Alt_Change	= "Change";
$Alt_Check	= "Check";
$Alt_Delete	= "Cancel";
$Alt_Info	= "Info";
$Alt_Lock	= "Protect from deletetion";
$Alt_Nslookup	= "nslookup";
$Alt_Recover	= "Recover";
$Alt_View	= "View";
$Alt_View_Users = "View users";
$Alt_Unlock	= "Unprotect from deletetion";

// Errori
define("_ACLDomainError","No ACL defined");
define("_CheckInputError","No all parameters are inserted correctly");
define("_CheckIPError","IP inserted not correct");
define("_CheckRecoverDomain","No domain to recover");
define("_CopyLanguagesError","Copy error of language's file");
define("_DomainRecoverError","Recover impossible. This domain already exist");
define("_DomainRegError","Domain already exist");
define("_DNSError","No DNS configured");
define("_DNSRegError","DNS already registered");
define("_ErronNoLanguage","No language");
define("_FindDomainError","Domain not found");
define("_FindFileError","File not found");
define("_NotFileLanguagesError","Language file not found");
define("_SelectDomainError","No domain is selected");
define("_TLDError","Domain inserted is unknown");
define("_SQLQueryError","Impossible to execute SQL command");

// Operazioni o bottoni
$Button_Add 	= "Add";
$Button_Apply 	= "Apply";
$Button_Cancel	= "Cancel";
$Button_Change 	= "Change";
$Button_Close	= "Close";
$Button_Continue= "Continue";
$Button_Delete 	= "Delete";
$Button_OK	= "OK";
$Button_Reset 	= "Reset";
$Button_Save 	= "Save";
$Button_Select 	= "Select";

// Messaggi di output
define("_AccessDenied","Access denied!");
define("_AddMsg","Made insertion");
define("_DeleteMsg","Cancellation made successfully");
define("_ErrorTitle","ERROR");
define("_ModifiedOkMsg","Made modification");
define("_NoUserMsg","No user");
define("_PwdChanged","Password changed");
define("_ReloadConfig","Configuration loading in course...");
define("_ReloadMsg","Reload made successfully");
define("_ResultTitle","RESULT");
define("_UpdateDomain","Updating of the domain in course...");

// Header di pagina
// DNS
define("_AddDNSHeader","Add DNS");
define("_ConfigDNSHeader","Configure DNS");
define("_DeleteDNSHeader","Cancel DNS");
define("_InfoDNSHeader","Info DNS");
define("_nslookupDNSHeader","nslookup DNS");
define("_ModifyDNSHeader","Change DNS");
define("_ReloadDNSHeader","Reload DNS");
define("_ViewFileHeader","View file");

// Dominio
define("_ACLDomainHeader","Access List domain");
define("_AddDomainHeader","Add domain");
define("_EditDomainHeader","Change domain");
define("_InfoDomainHeader","Info domain");
define("_RecoverDomainHeader","Recover domain");
define("_SearchDomainHeader","Found domains");
define("_SelectDomainHeader","Search domain");
define("_VerifyDomainHeader","Check domain");
define("_ViewDomainHeader","View domain");

// Amministrazione
define("_AddGroup","Add Group");
define("_AddUser","Add User");
define("_AdminConfig","Configuration");
define("_AdminGroups","Groups administration");
define("_AdminPwd","Changes password");
define("_AdminUsers","Users administration");
define("_ChangePwd","Changes password");
define("_UserGroup","Users List");
?>
