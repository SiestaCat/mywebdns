// Function per la sostituzione di un carattere con un altro in una stringa
function replaceString (string, separator, newseparetor) {
	var newstring = "";

	arrayOfStrings = string.split(separator)
	for (var i=0; i < arrayOfStrings.length-1; i++) {
	      newstring = newstring+arrayOfStrings[i]+newseparetor;
   	}
	newstring = newstring+arrayOfStrings[i];

	return newstring
}

//Check empty object
function CheckEmpty(obj) {
	if (obj.charAt(0) == " ") {
		alert("ERRORE: inserire un valore")
		return 1
	} 
	if (! obj.length) {
		alert("ERRORE: inserire un valore")
		return 1
	} else {
		return 0
	}
}

// Check object value
function CheckObj(obj,objType) {
	if (! CheckEmpty(obj)) {
		switch (objType) {
			case "number":
				var NoChar = /[^0123456789]/
				break
			case "ip":
				var NoChar = /[^0123456789.]/
				break
			case "string":
				var NoChar = /[^a-zA-Z0-9\-]/
				break
			default: number
		}
		for (i = 0; i <= (obj.length - 1); i++) {
			Char = obj.charAt(i)
			if (Char.search(NoChar) != -1) {
				alert("ERRORE: caratteri immessi non accettati")
				return 1
			}
		}
		return 0
	}
}

// Check range number
function CheckNum(num,min,max) {
	var NoChar = /[^0123456789]/
	for (i = 0; i <= (num.length - 1); i++) {
		Char = num.charAt(i)
		if (Char.search(NoChar) != -1) {
			alert("ERRORE: caratteri immessi non accettati")
			return 1
		}	
	}
	if (num >= min && num <= max) {
		return 0
	} else {
		alert("ERRORE: il valore deve essere compreso tra "+min+" e "+max)
		return 1
	}
}

// IP check
function CheckIP(ipValue) {
	if (! CheckObj(ipValue,"ip")) {
		var Error = 0;
		var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
		var ipArray = ipValue.match(ipPattern);
		if (ipValue == "0.0.0.0" || ipValue == "255.255.255.255") {
			Error = 1;
		}
		if (ipArray == null) {
			Error = 1;
		} else {
			for (i = 1; i < 5; i++) {
				thisSegment = ipArray[i];
				if (i == 1 && thisSegment == 0) {
					Error = 1; i = 5;
				}
				if (thisSegment >= 255) {
					Error = 1; i = 5;
				}
			}
		}
		if (Error == 1) {
			alert ("ERRORE: indirizzo IP non valido");
			return 1;
		} else {
			return 0;
		}
	} else {
		return 1;
	}
}

// Delete Groups
function DelGroup(groupname) {
	if (confirm("La cancellazione del gruppo "+groupname+" comporta la cancellazione di tutti gli utenti dello stesso.\nNon sarà più possibile alcun recupero")) {
		window.location.href="groups.php?delgroup=ok&group_name="+groupname
	}
}

// Delete DNS
function DelDNS(namedns,iddns) {
	if (confirm("Sei sicuro di voler cancellare il dns "+namedns+" ?\nNon sarà più possibile alcun recupero")) {
		window.location.href="configdns.php?deletedns=ok&iddns="+iddns
	}
}

// Delete domain
function DelDomain(namedom,iddom,searchdomain) {
	if (confirm("Sei sicuro di voler cancellare il dominio "+namedom+" ?")) {
		window.location.href="domain.php?deldomain=ok&iddom="+iddom+"&domain="+searchdomain
	}
}

// Domain recover
function RecoverDomain(namedom,iddom) {
	if (confirm("Sei sicuro di voler recuperare il dominio "+namedom+" ?")) {
		window.location.href="recoverdom.php?continuerecoverdomain=ok&iddom="+iddom
	}
}

//Check isIP
function isIP(ipValue) {
	var NoChar = /[^0123456789.]/
        var Error = 0;
        var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
        var ipArray = ipValue.match(ipPattern);

	for (i = 0; i <= (ipValue.length - 1); i++) {
        	Char = ipValue.charAt(i)
                if (Char.search(NoChar) != -1) {
                        return 0
                }
        }
        if (ipValue == "0.0.0.0" || ipValue == "255.255.255.255") {
        	Error = 1;
        }
        if (ipArray == null) {
        	Error = 1;
        } else {
        	for (i = 1; i < 5; i++) {
                	thisSegment = ipArray[i];
                        if (i == 1 && thisSegment == 0) {
                        	Error = 1; i = 5;
                        }
                        if (thisSegment >= 255) {
                        	Error = 1; i = 5;
                        }
		}
	}
	if (Error == 1) {
		return 0
	}
	return 1
}

// Save Domain Soa Function
function CheckFormSOA(oldttl,newttl,oldrefresh,newrefresh,oldretry,newretry,oldexpire,newexpire,oldminimum,newminimum) {
	// Controllo che i dati nseriti siano dei numeri
	if (isNaN(newttl)) {
        	alert("ERRORE: TTL deve essere un numero")
	        return 1
        }
	if (isNaN(newrefresh)) {
        	alert("ERRORE: REFRESH deve essere un numero")
	        return 1
        }
	if (isNaN(newretry)) {
        	alert("ERRORE: RETRY deve essere un numero")
	        return 1
        }
	if (isNaN(newexpire)) {
        	alert("ERRORE: EXPIRE deve essere un numero")
	        return 1
        }
	if (isNaN(newminimum)) {
        	alert("ERRORE: MINIMUM deve essere un numero")
	        return 1
        }

	// Check sui dati inseriti
	if (CheckNum(newttl,1,86400)) {
		return 1
	}
	if (CheckNum(newrefresh,1,86400)) {
		return 1
	}
	if (CheckNum(newretry,1,7200)) {
		return 1
	}
	if (CheckNum(newexpire,1,2592000)) {
		return 1
	}
	if (CheckNum(newminimum,1,86400)) {
		return 1
	}

	// Check tra nuovi e vecchi dati
	if ((oldttl == newttl) && (oldrefresh == newrefresh) && (oldretry == newretry) && (oldexpire == newexpire) && (oldminimum == newminimum)) {
		return 1
	}

	return 0
}

//function to trim leading and trailing white spaces
function trim(str){
	return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}

// Funzione per il check del gruppo inserito
function checkgroupname(group_name) {
        var NoChar = /[^a-zA-Z0-9\-]/

        for (i = 0; i <= (group_name.length - 1); i++) {
        	Char = group_name.charAt(i)
                	if (Char.search(NoChar) != -1) {
				return 0
                }
        }

        if(group_name == "")
		return 1;
	else 
		return 2;
}

// Funzione per l'apertura di una finestra
function open_window(page,title,h,w) {
	window.open(page , title, "width="+w+",height="+h+",screenX=20,screenY=40,left=20,top=40,toolbar=no, directories=no, status=no, menubar=no, resizable=no, scrollbars=yes");
}

// Funzione per aggiungere un gruppo
function addgroupname(group_name) {
	group_name.value = trim(group_name.value);
	switch (checkgroupname(group_name.value)) {
		case 0 : alert("ERRORE: caratteri immessi non accettati")
       			 break;
                case 1 : alert("ERRORE: inserire un nome")
                	 break;
                case 2 : window.location.href="groups.php?addgroup=ok&group_name="+group_name.value
			 window.opener.location.reload();
			 self.close();
                	 break;
        }
}

function changegroupname(old_group_name, new_group_name) {
	new_group_name.value = trim(new_group_name.value);
        switch (checkgroupname(new_group_name.value)) {
        	case 0 : alert("ERRORE: caratteri immessi non accettati")
                         break;
                case 1 : alert("ERRORE: inserire un nome")
                         break;
                case 2 : if (confirm("La seguente modifica verrà estesa a tutti gli utenti del gruppo.")) {
                		window.location.href="groups.php?changegroup=ok&old_group_name="+old_group_name+"&new_group_name="+new_group_name.value
                         }
	}
}

// Funzione per il controllo dei dati inseriti nella forma del DNS
function CheckConfigDNS() {
	if (isIP(document.forms[0].ip.value)) {
		var parameters = "adddns.php?adddns=ok";
		if ((document.forms[0].dnsfqdn.value.charAt(0) == " ") || (! document.forms[0].dnsfqdn.value.length)) {
                	alert ("ERRORE: il DNS FQDN deve essere specificato")
                        return
                }
		parameters = parameters+"&dnsfqdn="+document.forms[0].dnsfqdn.value

                // Sotituisco gli spazi vuoti con la stringa "%20"
                document.forms[0].dnsdescr.value = replaceString(document.forms[0].dnsdescr.value," ","%20");
		parameters = parameters+"&dnsdescr="+document.forms[0].dnsdescr.value

		parameters = parameters+"&ip="+document.forms[0].ip.value
        	if ((document.forms[0].dirnamed.value.charAt(0) == " ") || (! document.forms[0].dirnamed.value.length)) {
        		alert ("ERRORE: la directory del named.conf deve essere specificata")
	                return
        	}
		parameters = parameters+"&dirnamed="+document.forms[0].dirnamed.value
        	if ((document.forms[0].includezonenamed.value.charAt(0) == " ") || (! document.forms[0].includezonenamed.value.length)) {
                	alert ("ERRORE: la Include Zone named.conf deve essere specificata")
                	return
        	}
		parameters = parameters+"&includezonenamed="+document.forms[0].includezonenamed.value
        	if ((document.forms[0].dirzones.value.charAt(0) == " ") || (! document.forms[0].dirzones.value.length)) {
                	alert ("ERRORE: la directory delle zone deve essere specificata")
                	return
        	}
		parameters = parameters+"&dirzones="+document.forms[0].dirzones.value
        	if ((document.forms[0].dnsreload.value.charAt(0) == " ") || (! document.forms[0].dnsreload.value.length)) {
                	alert ("ERRORE: il comando del reload DNS deve essere specificato")
                	return
        	}
                // Sotituisco gli spazi vuoti con la stringa "%20"
                document.forms[0].dnsreload.value = replaceString(document.forms[0].dnsreload.value," ","%20");
                parameters = parameters+"&dnsreload="+document.forms[0].dnsreload.value

                window.location.href=parameters
	} else {
        	alert ("IP inserito non corretto")
        }
}

// Funzione per la modifica dei dati inseriti nella forma del DNS
function ChangeConfigDNS() {
	if (isIP(document.forms[0].ip.value)) {
		var parameters = "configdns.php?continuemoddns=ok";
		parameters = parameters+"&iddns="+document.forms[0].iddns.value
		if ((document.forms[0].dnsfqdn.value.charAt(0) == " ") || (! document.forms[0].dnsfqdn.value.length)) {
                	alert ("ERRORE: il DNS FQDN deve essere specificato")
                        return
                }
		parameters = parameters+"&dnsfqdn="+document.forms[0].dnsfqdn.value

                // Sotituisco gli spazi vuoti con la stringa "%20"
                document.forms[0].dnsdescr.value = replaceString(document.forms[0].dnsdescr.value," ","%20");
		parameters = parameters+"&dnsdescr="+document.forms[0].dnsdescr.value

		parameters = parameters+"&ip="+document.forms[0].ip.value
        	if ((document.forms[0].dirnamed.value.charAt(0) == " ") || (! document.forms[0].dirnamed.value.length)) {
        		alert ("ERRORE: la directory del named.conf deve essere specificata")
	                return
        	}
		parameters = parameters+"&dirnamed="+document.forms[0].dirnamed.value
        	if ((document.forms[0].includezonenamed.value.charAt(0) == " ") || (! document.forms[0].includezonenamed.value.length)) {
                	alert ("ERRORE: la Include Zone named.conf deve essere specificata")
                	return
        	}
		parameters = parameters+"&includezonenamed="+document.forms[0].includezonenamed.value
        	if ((document.forms[0].dirzones.value.charAt(0) == " ") || (! document.forms[0].dirzones.value.length)) {
                	alert ("ERRORE: la directory delle zone deve essere specificata")
                	return
        	}
		parameters = parameters+"&dirzones="+document.forms[0].dirzones.value
        	if ((document.forms[0].dnsreload.value.charAt(0) == " ") || (! document.forms[0].dnsreload.value.length)) {
                	alert ("ERRORE: il comando del reload DNS deve essere specificato")
                	return
        	}
                // Sotituisco gli spazi vuoti con la stringa "%20"
                document.forms[0].dnsreload.value = replaceString(document.forms[0].dnsreload.value," ","%20");
                parameters = parameters+"&dnsreload="+document.forms[0].dnsreload.value

                window.location.href=parameters
	} else {
        	alert ("IP inserito non corretto")
        }
}

// Funzione per aggiungere un'ACL ad un dominio
function addACL() {
	if (isIP(document.forms[0].ip.value)) {
		if ((isNaN(document.forms[0].netmask.value)) || (document.forms[0].netmask.value<1) || (document.forms[0].netmask.value>32)) {
			alert ("Netmask errata")
		} else {
			window.location.href="acldom.php?continueaddacl=ok&iddomain="+document.forms[0].iddomain.value+"&ip="+document.forms[0].ip.value+"&netmask="+document.forms[0].netmask.value+"&acltype="+document.forms[0].acltype.options[document.forms[0].acltype.options.selectedIndex].value
		}
	} else {
		alert ("IP inserito non corretto")
	}
}

// Funzione per aggiungere un utente
function addusername() {
	if (document.forms[0].groupexist.value == "NO")
		alert("ERRORE: nessun gruppo esistente");
        else {
        	var parameters = "users.php?adduser=ok";
                if (document.forms[0].fullname.value == "") {
                	alert("ERRORE: inserire un nome");
                	return
		}
        	// Sotituisco gli spazi vuoti con la stringa "%20"
        	document.forms[0].fullname.value = replaceString(document.forms[0].fullname.value," ","%20");
        	parameters = parameters+"&fullname="+document.forms[0].fullname.value

        	if (document.forms[0].login.value == "") {
        		alert("ERRORE: inserire una login");
	                return
        	}
        	parameters = parameters+"&login="+document.forms[0].login.value

	        if (document.forms[0].pwd.value == "") {
       			alert("ERRORE: inserire una password");
	        	return
        	}

        	if (document.forms[0].pwdreply.value == "") {
        		alert("ERRORE: confermare la password");
        		return
	        }

        	if (document.forms[0].pwd.value != document.forms[0].pwdreply.value) {
        		alert("ERRORE: la password non è stata confermata correttamente");
                	return
        	}
        	parameters = parameters+"&pwd="+document.forms[0].pwd.value
        	parameters = parameters+"&group_name="+document.forms[0].group_name.options[document.forms[0].group_name.options.selectedIndex].value

        	window.location.href=parameters

		window.opener.location.reload();
                self.close();
	}
}

// Funzione per modificare i dati di un utente
function changeuserdata() {
	var parameters = "users.php?changeuser=ok&login="+document.forms[0].login.value;

        if (document.forms[0].new_pwd.value == "") {
                alert("ERRORE: inserire una password");
                return
        }

        if (document.forms[0].new_pwdreply.value == "") {
        	alert("ERRORE: confermare la password");
        	return
        }

        if (document.forms[0].new_pwd.value != document.forms[0].new_pwdreply.value) {
        	alert("ERRORE: la password non è stata confermata correttamente");
        	return
        }
        parameters = parameters+"&pwd="+document.forms[0].new_pwd.value
        if (document.forms[0].new_fullname.value == "") {
        	alert("ERRORE: inserire un nome");
                return
        }
        // Sotituisco gli spazi vuoti con la stringa "%20"
        document.forms[0].new_fullname.value = replaceString(document.forms[0].new_fullname.value," ","%20");
        parameters = parameters+"&new_fullname="+document.forms[0].new_fullname.value

        parameters = parameters+"&group_name="+document.forms[0].group_name.options[document.forms[0].group_name.options.selectedIndex].value

        window.location.href=parameters
}

// Funzione per modificare la password di un utente
function changeuserpwd() {
        var parameters = "changepw.php?changeuser=ok&login="+document.forms[0].login.value;

        if (document.forms[0].new_pwd.value == "") {
                alert("ERRORE: inserire la nuova password");
                return
        }

        if (document.forms[0].new_pwdreply.value == "") {
                alert("ERRORE: confermare la password");
                return
        }

        if (document.forms[0].new_pwd.value != document.forms[0].new_pwdreply.value) {
                alert("ERRORE: la password non è stata confermata correttamente.");
                return
        }
        parameters = parameters+"&pwd="+document.forms[0].new_pwd.value

        window.location.href=parameters
}

/* Funzione per il check del SOA del dominio */
function CheckDomainSOA() {
        if (isNaN(document.forms[0].soattl.value)) {
                alert("ERRORE: TTL deve essere un numero")
                return false
        }
        if (CheckNum(document.forms[0].soattl.value,1,86400)) {
                return false
        }
        if (isNaN(document.forms[0].soarefr.value)) {
                alert("ERRORE: REFRESH deve essere un numero")
                return false
        }
        if (CheckNum(document.forms[0].soarefr.value,1,86400)) {
                return false
        }
        if (isNaN(document.forms[0].soaret.value)) {
                alert("ERRORE: RETRY deve essere un numero")
                return false
        }
        if (CheckNum(document.forms[0].soaret.value,1,7200)) {
                return false
        }
        if (isNaN(document.forms[0].soaexp.value)) {
                alert("ERRORE: EXPIRE deve essere un numero")
                return false
        }
        if (CheckNum(document.forms[0].soaexp.value,1,2592000)) {
                return false
        }
        if (isNaN(document.forms[0].soamin.value)) {
                alert("ERRORE: MINIMUM deve essere un numero")
                return false
        }
        if (CheckNum(document.forms[0].soamin.value,1,86400)) {
                return false
        }
        return true
}

/* Funzione di inizializzazione della form del dominio */
function initdefault_domain() {
        /* Variabili di gestione dei record */
        document.forms[0].id_record_deleted.value = '';
        /*  SOA */
        document.forms[0].soattl.value=document.forms[0].soattl.defaultValue;
        document.forms[0].soarefr.value=document.forms[0].soarefr.defaultValue;
        document.forms[0].soaret.value=document.forms[0].soaret.defaultValue;
        document.forms[0].soaexp.value=document.forms[0].soaexp.defaultValue;
        document.forms[0].soamin.value=document.forms[0].soamin.defaultValue;
        /* Record */
        for (i=0; i<document.forms[0].check_rec.length; i++) {
                document.forms[0].record_state[i].value = 'N';
                document.forms[0].oldrechost[i].value=document.forms[0].rechost[i].defaultValue;
                document.forms[0].oldrecttl[i].value=document.forms[0].recttl[i].defaultValue;
                document.forms[0].oldrecmxprior[i].value=document.forms[0].oldrecmxprior[i].defaultValue;
                document.forms[0].oldrectarget[i].value=document.forms[0].rectarget[i].defaultValue;
        }
}

/*  Check per la cancellazione di un record */
function check_delete_domain_record(idrec) {
        /* Ricerca del record da modificare */
        found = false;
        id = document.forms[0].id_record.value.split(";");
        i=0;
        while (!found) {
                if (id[i] == idrec) {
                        idchecked = i;
                        found = true;
                }
                i++;
        }

        /* Modifica dei dati del record */
        if (document.forms[0].record_state[idchecked].value == 'D') {
                /* Record cancellato */
                alert("Modifica non effettuabile. Record cancellato")

                /* Host Source */
                document.forms[0].rechost[idchecked].value=document.forms[0].oldrechost[idchecked].value;

                /* TTL */
                document.forms[0].recttl[idchecked].value=document.forms[0].oldrecttl[idchecked].value;

                /* MX */
                if (document.forms[0].index_mxprior[idchecked].value != '*') {
                        mxidchecked = document.forms[0].index_mxprior[idchecked].value;
                        mxpriority = (document.forms[0].oldrecmxprior[idchecked].value/5)-1;
                        document.forms[0].recmxprior[mxidchecked].options.selectedIndex = mxpriority;
                }

                /* Target */
                document.forms[0].rectarget[idchecked].value=document.forms[0].oldrectarget[idchecked].value;
        } else {
                /*  Record da recuperare */
                document.forms[0].record_state[idchecked].value='M';

                /* Host Source */
                document.forms[0].oldrechost[idchecked].value=document.forms[0].rechost[idchecked].value;

                /* TTL */
                if (isNaN(document.forms[0].recttl[idchecked].value)) {
                        document.forms[0].recttl[idchecked].value=document.forms[0].oldrecttl[idchecked].value;
                        alert("ERRORE: TTL deve essere un numero")
                        return false
                } else {
                        if (CheckNum(document.forms[0].recttl[idchecked].value,1,86400)) {
                                document.forms[0].recttl[idchecked].value=document.forms[0].oldrecttl[idchecked].value;
                                return false
                        } else
                                document.forms[0].oldrecttl[idchecked].value=document.forms[0].recttl[idchecked].value;
                }

                /* MX */
                if (document.forms[0].index_mxprior[idchecked].value != '*') {
                        mxidchecked = document.forms[0].index_mxprior[idchecked].value;
                        mxpriority = (document.forms[0].recmxprior[mxidchecked].options.selectedIndex+1)*5;
                        document.forms[0].oldrecmxprior[idchecked].value = mxpriority;
                } else
                        mxpriority = 0;
                document.forms[0].oldrecmxprior[idchecked].value=mxpriority;

                /* Target */
                document.forms[0].oldrectarget[idchecked].value=document.forms[0].rectarget[idchecked].value;
        }
}

/* Cancellazione di un record del dominio */
function delete_domain_record(idrec) {
        /* Ricerca del record da modificare */
        found = false;
        id = document.forms[0].id_record.value.split(";");
        i=0;
        while (!found) {
                if (id[i] == idrec) {
                        idchecked = i;
                        found = true;
                }
                i++;
        }

        /* Modifica dei dati del record */
        if (document.forms[0].check_rec[idchecked].checked) {
                /*  Cancellazione di un record */
                document.forms[0].record_state[idchecked].value='D';
                document.forms[0].id_record_deleted.value += idrec+";";
        } else {
                /* Recupero di un record */
                document.forms[0].record_state[idchecked].value='M';
                document.forms[0].id_record_deleted.value = '';
                for (var i=0; i<document.forms[0].check_rec.length; i++) {
                        if (document.forms[0].check_rec[i].checked) {
                                document.forms[0].id_record_deleted.value += document.forms[0].check_rec[i].value+";";
                        }
                }
        }
}

/* Funzione per il check del TTL del SOA */
function check_SOA_ttl(soattl,oldsoattl,min,max) {
	if (isNaN(document.forms[0].soattl.value)) {
		document.forms[0].soattl.value = document.forms[0].oldsoattl.value;
		alert("ERRORE: TTL deve essere un numero")
                return false
	}
	if ((document.forms[0].soattl.value < min) || (document.forms[0].soattl.value > max)) {
		document.forms[0].soattl.value = document.forms[0].oldsoattl.value;
		alert("ERRORE: TTL deve essere compreso tra "+min+" e "+max)
                return false
	}
	document.forms[0].oldsoattl.value = document.forms[0].soattl.value;
	return true
}

/* Funzione per il check del REFRESH del SOA */
function check_SOA_refr(soarefr,oldsoarefr,min,max) {
        if (isNaN(document.forms[0].soarefr.value)) {
                document.forms[0].soarefr.value = document.forms[0].oldsoarefr.value;
                alert("ERRORE: REFRESH deve essere un numero")
                return false
        }
        if ((document.forms[0].soarefr.value < min) || (document.forms[0].soarefr.value > max)) {
                document.forms[0].soarefr.value = document.forms[0].oldsoarefr.value;
                alert("ERRORE: REFRESH deve essere compreso tra "+min+" e "+max)
                return false
        }
        document.forms[0].oldsoarefr.value = document.forms[0].soasoarefr.value;
        return true
}

/* Funzione per il check del RETRY del SOA */
function check_SOA_ret(soaret,oldsoaret,min,max) {
        if (isNaN(document.forms[0].soaret.value)) {
                document.forms[0].soaret.value = document.forms[0].oldsoaret.value;
                alert("ERRORE: RETRY deve essere un numero")
                return false
        }
        if ((document.forms[0].soaret.value < min) || (document.forms[0].soaret.value > max)) {
                document.forms[0].soaret.value = document.forms[0].oldsoaret.value;
                alert("ERRORE: RETRY deve essere compreso tra "+min+" e "+max)
                return false
        }
        document.forms[0].oldsoaret.value = document.forms[0].soaret.value;
        return true
}

/* Funzione per il check dell'EXPIRE del SOA */
function check_SOA_exp(soaexp,oldsoaexp,min,max) {
        if (isNaN(document.forms[0].soaexp.value)) {
                document.forms[0].soaexp.value = document.forms[0].oldsoaexp.value;
                alert("ERRORE: EXPIRE deve essere un numero")
                return false
        }
        if ((document.forms[0].soaexp.value < min) || (document.forms[0].soaexp.value > max)) {
                document.forms[0].soaexp.value = document.forms[0].oldsoaexp.value;
                alert("ERRORE: EXPIRE deve essere compreso tra "+min+" e "+max)
                return false
        }
        document.forms[0].oldsoaexp.value = document.forms[0].soaexp.value;
        return true
}

/* Funzione per il check del MINIMUM del SOA */
function check_SOA_min(soamin,oldsoamin,min,max) {
        if (isNaN(document.forms[0].soamin.value)) {
                document.forms[0].soamin.value = document.forms[0].oldsoamin.value;
                alert("ERRORE: MINIMUM deve essere un numero")
                return false
        }
        if ((document.forms[0].soamin.value < min) || (document.forms[0].soamin.value > max)) {
                document.forms[0].soamin.value = document.forms[0].oldsoamin.value;
                alert("ERRORE: MINIMUM deve essere compreso tra "+min+" e "+max)
                return false
        }
        document.forms[0].oldsoamin.value = document.forms[0].soamin.value;
        return true
}

/* Funzione per il check del dominio */
function CheckDomain() {
	var consider;
	var parameters	  = "";
	var array_changed = "";		/* Indici dei record modificati */
	var array_deleted = "";		/* Indici dei record cancellati */
	var array_added	  = "";		/* Indici dei record aggiunti */

	var num_record = document.forms[0].index_record.length;

	// ID dominio e tipo di zona
	parameters = "&id_domain="+document.forms[0].id_domain.value+"&zmt="+document.forms[0].zone_master_type.value;

	// Dati struttura SOA
	parameters = parameters+"&soaserial="+document.forms[0].soaserial.value+"&soattl="+document.forms[0].soattl.value+"&soarefr="+document.forms[0].soarefr.value+"&soaret="+document.forms[0].soaret.value+"&soaexp="+document.forms[0].soaexp.value+"&soamin="+document.forms[0].soamin.value

	for (var i=0; i < num_record; i++) {
		/* Controllo quali dei nuovi record va considerato */
		if (document.forms[0].record_type[i].value == 'NEW') {
			if ((document.forms[0].record_state[i].value == 'N') || (document.forms[0].record_state[i].value == 'D')) {
				consider = false;
			} else {
				consider = true;
			}
		} 

		// Analizzo STATO e TIPO
		if (document.forms[0].record_type[i].value == 'OLD') {
			if (document.forms[0].record_state[i].value == 'D') {
				array_deleted = array_deleted+document.forms[0].record_id[i].value+";"
			}
			if (document.forms[0].record_state[i].value == 'M') {
			        if (!CheckFormRec(i)) {
                			return 0
        			} else {
                                        if (document.forms[0].rectype[i].value == 'MX') {
						var imx = document.forms[0].index_mxprior[i].value
						mxprior = document.forms[0].recmxprior[imx].options.selectedIndex * 5;
					} else {
						mxprior = 0;
					}
					array_changed = array_changed+document.forms[0].record_id[i].value+","+document.forms[0].rechost[i].value+","+document.forms[0].recttl[i].value+","+document.forms[0].rectype[i].value+","+mxprior+","+document.forms[0].rectarget[i].value+";"
				}
			}
		} else {
			/* Se è verificata la condizione è stato inserito un nuovo record */
			if (consider == true) {
			        if (!CheckFormRec(i)) {
                			return 0
        			} else {
                                        if (document.forms[0].rectype[i].value == 'MX') {
                                                var imx = document.forms[0].index_mxprior[i].value
                                                mxprior = document.forms[0].recmxprior[imx].options.selectedIndex * 5;
                                        } else {
                                                mxprior = '0';
                                        }
					array_added = array_added+"NULL,"+document.forms[0].rechost[i].value+","+document.forms[0].recttl[i].value+","+document.forms[0].rectype[i].value+","+mxprior+","+document.forms[0].rectarget[i].value+";"
				}
			}
		}
   	}
	window.location.href="editdom.php?savedomain=ok&array_deleted="+array_deleted+"&array_changed="+array_changed+"&array_added="+array_added+parameters
}

// Esegue il check del record della tabella
function CheckFormRec(i) {
	switch (document.forms[0].rectype[i].value) {
		case "A" :
				// Verifico che la sorgente non sia un IP
				if (isIP(document.forms[0].rechost[i].value)) {
					alert("["+document.forms[0].index_record[i].value+"] ERRORE: la sorgente deve essere un host")
					return 0
				}
                                // Verifico che la sorgente non sia una stringa vuota
				document.forms[0].rechost[i].value = trim(document.forms[0].rechost[i].value)
				if (document.forms[0].rechost[i].value == '') {
					alert("["+document.forms[0].index_record[i].value+"] ERRORE: la sorgente deve essere specificata")
					return 0
                                }
				// Verifico che la destinazione sia un IP
                                if (! isIP(document.forms[0].rectarget[i].value)) {
                                	alert("["+document.forms[0].index_record[i].value+"] ERRORE: la destinazione deve essere un IP")
                                        return 0
                                }
				return 1
				break;
		case "NS" :	
		case "MX" :
		case "CNAME" :
                                // Verifico che la sorgente non sia un IP
                                if (isIP(document.forms[0].rechost[i].value)) {
                                        alert("["+document.forms[0].index_record[i].value+"] ERRORE: la sorgente deve essere un host")
                                        return 0
                                }
                                // Verifico che la dwstinazione non sia un IP
                                if (isIP(document.forms[0].rectarget[i].value)) {
                                        alert("["+document.forms[0].index_record[i].value+"] ERRORE: la destinazione deve essere un host")
                                        return 0
                                }
				 // Verifico che per il CNAME sia definito la sorgente
				if (document.forms[0].rectype[i].value == 'CNAME') {
					document.forms[0].rechost[i].value = trim(document.forms[0].rechost[i].value)
                                	if (document.forms[0].rechost[i].value == '') {
                                        	alert("["+document.forms[0].index_record[i].value+"] ERRORE: la sorgente deve essere specificata")
                                        	return 0
					}
				}
				// Verifico che la destinazione sia definita
				document.forms[0].rectarget[i].value = trim(document.forms[0].rectarget[i].value)
                                if (document.forms[0].rectarget[i].value == '') {
                                       	alert("["+document.forms[0].index_record[i].value+"] ERRORE: la destinazione deve essere specificata")
                                       	return 0
				}
				// Verifico la priorita' per il record MX
				if (document.forms[0].rectype[i].value == 'MX') {
					imx = document.forms[0].index_mxprior[i].value
					if (document.forms[0].recmxprior[imx].options.selectedIndex == 0) {
						alert("["+document.forms[0].index_record[i].value+"] ERRORE: e' necessario definire una priorita'")
						return 0
					}
				}
				return 1
				break;
		case "PTR" :	
				// Verifico che la sorgente non sia una stringa vuota
                                document.forms[0].rechost[i].value = trim(document.forms[0].rechost[i].value)
                                if (document.forms[0].rechost[i].value == '') {
                                        alert("["+document.forms[0].index_record[i].value+"] ERRORE: la sorgente deve essere specificata")
                                        return 0
                                }
				// Verifico che la sorgente sia un numero
				if (isNaN(document.forms[0].rechost[i].value)) {
					alert("["+document.forms[0].index_record[i].value+"] ERRORE: la sorgente deve essere un numero")
					return 0
				}
				// Check della sorgente
				if ((document.forms[0].rechost[i].value < 1) || (document.forms[0].rechost[i].value > 254)) {
					alert("["+document.forms[0].index_record[i].value+"] ERRORE: la sorgente deve essere compresa tra 1 e 254")
					return 0
				}
				// Verifico che la destinazione non sia una stringa vuota
                                document.forms[0].rectarget[i].value = trim(document.forms[0].rectarget[i].value)
                                if (document.forms[0].rectarget[i].value == '') {
                                        alert("["+document.forms[0].index_record[i].value+"] ERRORE: la destinazione deve essere specificata")
                                        return 0
                                }
				return 1
				break;
	}
}	

// Esegue il check della configurazione
function CheckConfiguration() {
	var parameters = "config.php?configuration=ok";
        parameters = parameters+"&language="+document.forms[0].language.options[document.forms[0].language.options.selectedIndex].value
	if (isNaN(document.forms[0].myrecord_ns.value)) {
        	alert("ERRORE: NS deve essere un numero")
	        return 1
        }
        if (CheckNum(document.forms[0].myrecord_ns.value,0,99)) {
                return false
        }
        parameters = parameters+"&myrecord_ns="+document.forms[0].myrecord_ns.value

	if (isNaN(document.forms[0].myrecord_mx.value)) {
        	alert("ERRORE: MX deve essere un numero")
	        return 1
        }
        if (CheckNum(document.forms[0].myrecord_mx.value,0,99)) {
                return false
        }
        parameters = parameters+"&myrecord_mx="+document.forms[0].myrecord_mx.value

	if (isNaN(document.forms[0].myrecord_a.value)) {
        	alert("ERRORE: A deve essere un numero")
	        return 1
        }
        if (CheckNum(document.forms[0].myrecord_a.value,0,99)) {
                return false
        }
        parameters = parameters+"&myrecord_a="+document.forms[0].myrecord_a.value

	if (isNaN(document.forms[0].myrecord_ptr.value)) {
        	alert("ERRORE: PTR deve essere un numero")
	        return 1
        }
        if (CheckNum(document.forms[0].myrecord_ptr.value,0,99)) {
                return false
        }
        parameters = parameters+"&myrecord_ptr="+document.forms[0].myrecord_ptr.value

	if (isNaN(document.forms[0].myrecord_cname.value)) {
        	alert("ERRORE: CNAME deve essere un numero")
	        return 1
        }
        if (CheckNum(document.forms[0].myrecord_cname.value,0,99)) {
                return false
        }
        parameters = parameters+"&myrecord_cname="+document.forms[0].myrecord_cname.value

        window.location.href=parameters
}

