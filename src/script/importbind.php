<PHPBINPATH>
<?php
// +--------------------------------------------------------------------+
// | Progetto WebDNS - imporbind.php					|
// +--------------------------------------------------------------------+
// | Effettua l'importind dei domini di un DNS parsando il relativo     |
// | named.conf. 							|
// +--------------------------------------------------------------------+
// | Autore : Pasquale Affinito                                         |
// +--------------------------------------------------------------------+

$DBdatabase = "<DATABASE>";
$DBusername = "<ADMINISTRATOR>";
$DBpassword = "<PASSWD>";
$DBhost     = "<MYSQLSERVER>";

$ReportLog = "/tmp/importbind_report.log";
$HeaderReport = "*****************************************\n".
		"importbind.php REPORT - ".date("Y-m-d, H:i")."\n".
		"*****************************************\n\n";
$EndHeaderReport = "*********************************************\n".
   		   "importbind.php END REPORT - ".date("Y-m-d, H:i")."\n".
		   "*********************************************";
$cmdnamedxfer = "/usr/libexec/named-xfer";

// Setto il tempo di esecuzione in maniera illimitata
set_time_limit(0);

// Funzione di connessiona al DB
function connect_db() {
	global $DBdatabase, $DBusername, $DBpassword, $DBhost;
	$dblink = @mysqli_connect($DBhost, $DBusername, $DBpassword,$DBdatabase) or die("\nERROR: cannot to connect to MySQL server\n\n");
	return($dblink);
}

// Funzione per transformazione IP: dot form --> long form
function ipdot2iplong($ipdot) {
        $conn=connect_db();
        $sql = "SELECT ip2long('$ipdot');";
        $result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
        $out = mysqli_fetch_row($result);
        return ($out[0]);
        mysqli_close($conn);
}

// Funzione per transformazione IP: long form --> dot form
function iplong2ipdot($iplong) {
        $conn=connect_db();
        $sql = "SELECT long2ip($iplong);";
        $result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
        $out = mysqli_fetch_row($result);
        return ($out[0]);
        mysqli_close($conn);
}

// Verifica la correttezza di un indirizzo IP in dot form
function checkip($ip) {
        $check = 1;
        if ((ipdot2iplong($ip) == 4294967295) || (ipdot2iplong($ip) == -1))
                $check = 0;
        return($check);
}

// Funzione di parsing sul named.conf che restituisce le zone con il tipo associato nella forma zona#tipo#IP#ALLOW
function parsernamed($filenamed) {
	$named = file($filenamed);
	for ($i=0; $i<count($named); $i++) {
		$named[$i] = strtolower($named[$i]);

		// Leggo la zona in cerca della parola "zone"
		if (eregi("^([\t]*)([[:space:]]*)zone",$named[$i])) {
			$buf = split("\"",$named[$i]);
			$zone = $buf[1];

			// Identifico il type [Master Slave Forward]
			while (!eregi("[[:space:]]+type[[:space:]]+",$named[$i])) {
				$i++;
				$named[$i] = strtolower($named[$i]);
			}
			$buf = split('[[:space:]]+',$named[$i]);
			$type = substr($buf[2],0,-1);
			$ipmasters="";

			// Se Slave identifico gli IP dei DNS Master
			if ($type == "slave") {
				do {
					$address = explode (" ",trim($named[$i]));
					$j = 0;
					do {
						$address[$j] = trim($address[$j]);
						if (eregi("[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}",$address[$j],$ip)) {
							// Verifico se ho trovato un IP
							if ((ipdot2iplong($ip[0]) != 4294967295) && (ipdot2iplong($ip[0]) != -1))
								$ipmasters=$ipmasters.$ip[0]."#";
						}
                        	       		$j++;
					} while ($j != count($address));
					$i++;
                               		$named[$i] = strtolower($named[$i]);
				} while (!eregi("};", $named[$i]));

				// Verifico se "};" sta da solo sulla linea
				if (!eregi("^([\t]*)([[:space:]]*)};",$named[$i])) {
					$address = explode (" ",trim($named[$i]));
					$j = 0;
					do {
						$address[$j] = trim($address[$j]);
						if (eregi("[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}",$address[$j],$ip)) {
							// Verifico se ho trovato un IP
							if ((ipdot2iplong($ip[0]) != 4294967295) && (ipdot2iplong($ip[0]) != -1))
								$ipmasters=$ipmasters.$ip[0]."#";
						}
                               		$j++;
					} while ($j != count($address));
				}
				$i++;
			}

			// Se Forward identifico gli IP della sezione FORWARDERS
			if ($type == "forward") {
				do {
					$address = explode (" ",trim($named[$i]));
					$j = 0;
					do {
						$address[$j] = trim($address[$j]);
						if (eregi("[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}",$address[$j],$ip)) {
							// Verifico se ho trovato un IP
							if ((ipdot2iplong($ip[0]) != 4294967295) && (ipdot2iplong($ip[0]) != -1))
								$ipmasters=$ipmasters.$ip[0]."#";
						}
                        	       	$j++;
					} while ($j != count($address));
					$i++;
                               		$named[$i] = strtolower($named[$i]);
				} while (!eregi("};", $named[$i]));

				// Verifico se "};" sta da solo sulla linea
				if (!eregi("^([\t]*)([[:space:]]*)};",$named[$i])) {
					$address = explode (" ",trim($named[$i]));
					$j = 0;
					do {
						$address[$j] = trim($address[$j]);
						if (eregi("[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}",$address[$j],$ip)) {
							// Verifico se ho trovato un IP
							if ((ipdot2iplong($ip[0]) != 4294967295) && (ipdot2iplong($ip[0]) != -1))
								$ipmasters=$ipmasters.$ip[0]."#";
						}
        		                       	$j++;
					} while ($j != count($address));
				}
				$i++;
			}

			// Identifico eventuali sezione allow
			while (!eregi("^([\t]*)([[:space:]]*)};",$named[$i])) {
				if (eregi("^([\t]*)([[:space:]]*)allow-([a-z]*)",$named[$i],$allowtype)) {
					do {
						$address = explode (" ",trim($named[$i]));
						$j = 0;
						do {
							$address[$j] = trim($address[$j]);
							if (eregi("[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\/?[0-9]{0,2}",$address[$j],$ip)) {
								$ip = explode ("/", $ip[0]);
								if (ipdot2iplong($ip[0]) == -1)
									$ip[0] = $ip[0].".0";	
								if ($ip[1] == "")
									$ip[1] = 32;
								switch (trim($allowtype[0]))  {
									case "allow-query":	$ipmasters=$ipmasters."QRY#";
												break;
									case "allow-transfer":	$ipmasters=$ipmasters."TRX#";
												break;
									case "allow-update" :	$ipmasters=$ipmasters."UPD#";
												break;
									case "allow-notify" :	$ipmasters=$ipmasters."NOT#";
												break;
								}
								$ipmasters = $ipmasters.$ip[0]."/".$ip[1]."#";
							}
                	        	       		$j++;
						} while ($j != count($address));
						if (!eregi("};", $named[$i])) $i++;
                               			$named[$i] = strtolower($named[$i]);
					} while (!eregi("};", $named[$i])); 

					// Verifico se "};" sta da solo sulla linea e se la linea successiva � "};"
					if ((!eregi("^([\t]*)([[:space:]]*)allow-([a-z]*)",$named[$i])) && (eregi("^([\t]*)([[:space:]]*)};",$named[$i+1]))) {
						$address = explode (" ",trim($named[$i]));
						$j = 0;
						do {
							$address[$j] = trim($address[$j]);
							if (eregi("[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\/?[0-9]{0,2}",$address[$j],$ip)) {
								$ip = explode ("/", $ip[0]);
								if (ipdot2iplong($ip[0]) == -1)
									$ip[0] = $ip[0].".0";	
								if ($ip[1] == "")
									$ip[1] = 32;
								switch (trim($allowtype[0]))  {
									case "allow-query":	$ipmasters=$ipmasters."QRY#";
												break;
									case "allow-transfer":	$ipmasters=$ipmasters."TRX#";
												break;
									case "allow-update" :	$ipmasters=$ipmasters."UPD#";
												break;
									case "allow-notify" :	$ipmasters=$ipmasters."NOT#";
												break;
								}
								$ipmasters = $ipmasters.$ip[0]."#".$ip[1]."#";
							}
        		                       		$j++;
						} while ($j != count($address));
					}
				}
				$i++;
                              	$named[$i] = strtolower($named[$i]);
			}

			// Costruisco l'output
			$zones[]=$zone."#".$type."#".$ipmasters;
		}
	}	
	return ($zones);
}

#
# Main
#

if (!file_exists($cmdnamedxfer))
	die("\nERROR: command named-xfer not found! Change the \$cmdnamedxfer variable to set the complete full right path of the command.\n\n");

$named = readline("named.conf full path [named.conf]: ");
if ($named == "")
	$named = "./named.conf";
echo "\n";

if (!file_exists($named))
	die("\nERROR: file \"$named\" not exist!\n\n");

// Effettuo la connessione al DB
$conn = connect_db();

// Parsing sul named.conf per ottenere le informazioni di zona nella forma ZONA#TIPO#IPDNSMASTER#ALLOW
$zones = parsernamed($named);

// Leggo il DNS da cui prelevare le zone
$fromdnsserver = readline("DNS which take the zones from: ");
echo "\n";

// Check del DNS inserito
$addr = gethostbyname(trim($fromdnsserver));
if (!checkip($addr))
	die("\nERROR: the input DNS not result a NS\n\n");

// Leggo il DNS  su cui trasferire le zone
$todnsserver = readline("DNS which move the zones on: ");
echo "\n";

// Selezione dell'IDDNS dalla tabella DNS
$sql = "SELECT id FROM dns WHERE dnsfqdn='$todnsserver';";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_row($result)) == NULL ) die("\nERROR: not exist a record for the dns \"$todnsserver\" into table DNS.\n\n");
$iddns = $out[0];

echo "$HeaderReport\n";
exec ("echo \"$HeaderReport\" > $ReportLog");

$tmpfname = tempnam("/tmp", "zone");
for ($i=0; $i<count($zones); $i++) {
	$buf = explode("#", $zones[$i]);
	
	$domain = strtolower(trim($buf[0]));		// Nome dominio
	$domaintype = strtolower(trim($buf[1]));	// Tipo di dominio

	// IP DNS Master
	unset($ipdnsmaster); $ipdnsmaster = array();
	if ($domaintype == "slave") {
		$j = 2;
		while ( (ipdot2iplong($buf[$j]) != -1) && ($j<=count($buf))) {
			$ipdnsmaster[] = $buf[$j];
			$j++;
		}
	}

	// IP DNS Forwarders
	unset($ipdnsforward); $ipdnsforward = array();
	if ($domaintype == "forward") {
		$j = 2;
		while ( (ipdot2iplong($buf[$j]) != -1) && ($j<=count($buf))) {
			$ipdnsforward[] = $buf[$j];
			$j++;
		}
	}

	// IP ALLOW
	unset($allowqry); $allowqry = array();
	unset($allowtrx); $allowtrx = array();
	unset($allowupd); $allowupd = array();
	unset($allownot); $allownot = array();
        while ($j<=count($buf)) {
		switch ($buf[$j]) {	
			case "QRY":	$j++;
					$allowqry[] = $buf[$j];
					break;
			case "TRX":	$j++;
					$allowtrx[] = $buf[$j];
					break;
			case "UPD":	$j++;
					$allowupd[] = $buf[$j];
					break;
			case "NOT":	$j++;
					$allownot[] = $buf[$j];
					break;
		}
		$j++;
        }

	switch ($domaintype) {
		case "master":
			$zonetype="M";
			if (strstr($domain, ".in-addr.arpa")) {
				echo "Domain importing $domain: REVERSE\n";
				exec (" echo \"Importing domain $domain: REVERSE\" >> $ReportLog");

				// Escludo la zona di loopback
				if ($domain != "0.0.127.in-addr.arpa") {
					$zonemastertype = "R";
				} else {
					$zonemastertype = "*";
					echo "WARNING: Proceed to the manual modification of the named.conf\n\n";
					exec (" echo \"WARNING: Proceed to the manual modification of the named.conf\n\" >> $ReportLog");
				}
			} else {
				$zonemastertype = "M";
				echo "Domain importing $domain: MASTER\n";
				exec (" echo \"Domain importing $domain: MASTER\" >> $ReportLog");
			}
			break;
		case "slave":
			$zonetype="S";
			if (strstr($domain, ".in-addr.arpa"))
				$zonemastertype = "R";
			else
				$zonemastertype = "M";
                        echo "Domain importing $domain: SLAVE\n";
			exec (" echo \"Domain importing $domain: SLAVE\" >> $ReportLog");
                        break;
		case "forward":
			$zonetype="F";
			$zonemastertype = "*";
			echo "Domain importing $domain: FORWARD\n";
			exec (" echo \"Domain importing $domain: FORWARD\" >> $ReportLog");
			break;
		case "hint":
			$zonetype = "H";
			$zonemastertype = "*";
			echo "Domain importing $domain: HINT\n";
			exec (" echo \"Domain importing $domain: HINT\" >> $ReportLog");
			echo "WARNING: Proceed to the manual modification of the named.conf\n\n";
			exec (" echo \"WARNING: Proceed to the manual modification of the named.conf\n\" >> $ReportLog");
			break;
		default:
			die("\nERROR: The domain type \"$domaintype\" has not be recognized!\n\n");
	}

	// Zona Forward
	if ($zonetype == "F") {
		// Calcolo il livello del dominio
		if (strstr($domain, ".in-addr.arpa"))
			$level = 0;
		else {
			$buffer = explode (".", $domain);
			$level = count($buffer);
		}

		// Selezione dell'ID della zona dalla tabella DOMAIN
		$sql = "SELECT id FROM domain WHERE name='$domain' AND iddns=$iddns;";
		$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

		// Verifico che la zona non sia gia' inserita
        	if (($out = mysqli_fetch_row($result)) == NULL ) {
			$data = date("Y-m-d");
			// Registrazione del nuovo dominio
			if (strstr($domain, ".in-addr.arpa"))
				$sql = "INSERT INTO domain VALUES (NULL,'$domain',$level,'$data',0,'','',0,0,0,0,0,'$zonetype','R','A','0',$iddns,0);";
			else
				$sql = "INSERT INTO domain VALUES (NULL,'$domain',$level,'$data',0,'','',0,0,0,0,0,'$zonetype','M','A','0',$iddns,0);";
			mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

			// Selezione del'ID del dominio appena inserito
			$iddom = mysqli_insert_id($conn);

			// Memorizzo gli IP dei DNS forward
			for ($y=0; $y<count($ipdnsforward); $y++) {
                		$ip = ipdot2iplong($ipdnsforward[$y]);
                		$sql = "INSERT INTO ipdnsforwarders VALUES (NULL,$iddom,$ip);";
                		$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
			}
		} else {
			echo "ERROR: The domain $domain already exist with ID $out[0]\n";
			exec (" echo \"ERROR: The domain $domain already exist with ID $out[0]\" >> $ReportLog");
			// Salto il dominio
			$zonetype = "*";
			$zonemastertype = "*";
			unset($allowqry);
        		unset($allowtrx);
        		unset($allowupd);
        		unset($allownot);
                }
	}

	// Zona Master Reverse
	if (($zonemastertype == "R") || ($zonemastertype == "M")) {

		// Fare riferimento alla variabile $cmdnamedxfer per settare il path del comando
		exec("$cmdnamedxfer -z $domain -f $tmpfname $fromdnsserver  >/dev/null 2>&1");
		$zona = file($tmpfname);

		// Calcolo il livello del dominio
		if (strstr($domain, ".in-addr.arpa"))
			$level = 0;
		else {
			$buffer = explode (".", $domain);
			$level = count($buffer);
		}

		// Leggo i dati del record SOA
		$buffer = explode("\t", trim($zona[5]));
		$buffer[0] = "";
		$last = $buffer[0];
		$filename = $domain.".zone";
		$ttl = $buffer[1];
		if ($ttl == "")
			$ttl = "86400";
		if ($zonetype == "M") {
			$hostdns = $todnsserver.".";
			$rootdns = "root.".$todnsserver.".";
		} else {
			$hostdns = $buffer[4];
			$rootdns = $buffer[5];
		}
		$buffer = explode(" ", trim($zona[6]));
		$serial = $buffer[0];

		// Verifico il seriale della zone e ne ricavo la data di creazione
		if (strlen($serial)==10) {
			if (checkdate(substr($serial,4,2),substr($serial,6,2),substr($serial,0,4)))
				$data=substr($serial,0,4)."-".substr($serial,4,2)."-".substr($serial,6,2);
			else {
				$serial=substr($serial,0,4).substr($serial,6,2).substr($serial,4,2).substr($serial,8,2);
				$data=substr($serial,0,4)."-".substr($serial,6,2)."-".substr($serial,4,2);
			}
		} else {
			$serial = date("Ymd01");
			$data = date("Y-m-d");
			echo "WARNING: Serial of zone not recognized: set up to $serial\n";
			exec (" echo \"WARNING: Serial of zone not recognized: set up to $serial\" >> $ReportLog");
		}
		
		$refresh = $buffer[1];
		$retry = $buffer[2];
		$expire = $buffer[3];
		$minimum = $buffer[4];
		$origin = "";

		// Selezione dell'ID della zona dalla tabella DOMAIN
		$sql = "SELECT id FROM domain WHERE name='$domain' AND iddns=$iddns;";
		$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

		// Verifico che la zona non sia gia' inserita
        	if (($out = mysqli_fetch_row($result)) == NULL ) {
			// Registrazione del nuovo dominio
			if ($zonetype == "M")
				$sql = "INSERT INTO domain VALUES (NULL,'$domain',$level,'$data',$ttl,'$hostdns','$rootdns',$serial,$refresh,$retry,$expire,$minimum,'$zonetype','$zonemastertype','A','0',$iddns,0);";
			else
				$sql = "INSERT INTO domain VALUES (NULL,'$domain',$level,'$data',0,'','',0,0,0,0,0,'$zonetype','$zonemastertype','A','0',$iddns,0);";
				
			mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

			// Selezione del'ID del dominio appena inserito
			$iddom = mysqli_insert_id($conn);
		} else {
			echo "ERROR: The domain $domain already exist with ID $out[0]\n";
			exec (" echo \"ERROR: The domain $domain already exist with ID $out[0]\" >> $ReportLog");
			// Salto il dominio
			$zonetype = "*";
			$zonemastertype = "*";
			unset($allowqry);
        		unset($allowtrx);
        		unset($allowupd);
        		unset($allownot);
		}

		// Verifico se e' una zona SLAVE
               	if ($zonetype == "S") {
               		// Memorizzo gli IP dei DNS master per la zona slave
               		for ($y=0; $y<count($ipdnsmaster); $y++) {
                        	$ip = ipdot2iplong($ipdnsmaster[$y]);
                                $sql = "INSERT INTO ipdnsmaster VALUES (NULL,$iddom,$ip);";
                                $result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
                        }
		}

		if ($zonetype == "M") {
			// Inizio da 7 saltando i commenti ed il record SOA
			for ($j=7; $j<count($zona); $j++) {
				$buffer = rtrim($zona[$j]);

				// Verifico la presenza di host gestiti con la direttiva ORIGIN
				if (trim(strstr($buffer, "\$ORIGIN")) != "") {
					$buf = explode(" ", $buffer);
					$origin = $buf[1];
					if ($origin != ($domain.".")) {
						$len = strpos($origin, ($domain."."));
						$origin = (".".substr($origin, 0, $len-1));
					} else
						$origin = "";
					$j++;
					$buffer = rtrim($zona[$j]);
				}

				$field = explode("\t",$buffer);
				if ($field[0] == "")
					$field[0] = $last;
				else
					$field[0] = $field[0].$origin;

				// Controllo sul record
				if (eregi("^(#)",$field[0])) {
					echo "WARNING: $field[0]: record mistake. Proceed to the manual cancellation\n";
					exec (" echo \"WARNING: $field[0]: record mistake. Proceed to the manual cancellation\" >> $ReportLog");
				}

				// Controllo sul TTL
				if ($field[1] == "")
					$field[1] = "86400";
				if ($field[1] != "86400") {
					echo "WARNING: $field[0]: TTL ($field[1]) not standard (86400)\n";
					exec (" echo \"WARNING: $field[0]: TTL ($field[1]) not standard (86400)\" >> $ReportLog");
				}

				if ($field[3] == "MX" ) {
					$buffer = explode(" ", $field[4]);
					$field[4] = $buffer[0];			// MX Priority
					$field[] = $buffer[1];			// Server MX
				}
				$last=$field[0];
		
				// Selezione dell'ID del tipo di record
				$rectype = $field[3];

				// Zona Master
				if ($zonemastertype == "M") {

					// Record MX
					if ($field[3] == "MX") {

						// Verifico se il nome di host e' a lettere minuscole
						if ($field[5] != strtolower($field[5])) {
							echo "WARNING: $field[5]: hostname not standard (converted in tiny)\n";
							exec (" echo \"WARNING: $field[5]: hostname not standard (converted in tiny)\" >> $ReportLog");
							$field[5] = strtolower($field[5]);
						}
			
						$sql = "INSERT INTO recordmaster VALUES (NULL,$iddom,'$field[0]',$field[1],'$rectype',$field[4],'$field[5]',0);";
					} else {
						// Record A
						if ($field[3] == "A") {

							// Verifico se il nome di host e' a lettere minuscole
							if ($field[0] != strtolower($field[0])) {
								echo "WARNING: $field[0]: hostname not standard (converted in tiny)\n";
								exec (" echo \"WARNING: $field[0]: hostname not standard (converted in tiny)\" >> $ReportLog");
								$field[0] = strtolower($field[0]);
							}
			
							$ip = ipdot2iplong($field[4]);
							$sql = "INSERT INTO recordmaster VALUES (NULL,$iddom,'$field[0]',$field[1],'$rectype',0,'',$ip);";
						} else {
						// Record NS, CNAME

							// Verifico se il nome di host e'  a lettere minuscole
							if ($field[4] != strtolower($field[4])) {
								echo "WARNING: $field[4]: hostname not standard (converted in tiny)\n";
								exec (" echo \"WARNING: $field[4]: hostname not standard (converted in tiny)\" >> $ReportLog");
								$field[4] = strtolower($field[4]);
							}
			
							$sql = "INSERT INTO recordmaster VALUES (NULL,$iddom,'$field[0]',$field[1],'$rectype',0,'$field[4]',0);";
						}
					}
				// Zona Reverse
				} else {
                	        	// Record MX
	                        	if ($field[3] == "MX" ) {

						// Verifico se il nome di host e' a lettere minuscole
						if ($field[5] != strtolower($field[5])) {
							echo "WARNING: $field[5]: hostname not standard (converted in tiny)\n";
							exec (" echo \"WARNING: $field[5]: hostname not standard (converted in tiny)\" >> $ReportLog");
							$field[5] = strtolower($field[5]);
						}
			
		                                $sql = "INSERT INTO recordreverse VALUES (NULL,$iddom,'$field[0]',$field[1],'$rectype',$field[4],'$field[5]');";
					} else {
						// Verifico se il nome di host e' a lettere minuscole
						if ($field[4] != strtolower($field[4])) {
							echo "WARNING: $field[4]: hostname not standard (converted in tiny)\n";
							exec (" echo \"WARNING: $field[4]: hostname not standard (converted in tiny)\" >> $ReportLog");
							$field[4] = strtolower($field[4]);
						}
			
                		                // Record PTR, NS, CNAME
                        		        $sql = "INSERT INTO recordreverse VALUES (NULL,$iddom,'$field[0]',$field[1],'$rectype',0,'$field[4]');";
					}
				}
		
				// Registrazione del record nel DB
				mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
			}		
		} 

	}

	// Direttive ALLOW-QUERY
	for ($j=0; $j<count($allowqry); $j++) {
		$buf = explode("/", $allowqry[$j]);
		$address = ipdot2iplong($buf[0]);
		$netmask = $buf[1];
		$sql = "INSERT INTO acldomain VALUES (NULL,$iddom,$address,$netmask,'QRY');";
		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
	}
	// Direttive ALLOW-TRANSFER
	for ($j=0; $j<count($allowtrx); $j++) {
		$buf = explode("/", $allowtrx[$j]);
		$address = ipdot2iplong($buf[0]);
		$netmask = $buf[1];
		$sql = "INSERT INTO acldomain VALUES (NULL,$iddom,$address,$netmask,'TRX');";
		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
	}
	// Direttive ALLOW-UPDATE
	for ($j=0; $j<count($allowupd); $j++) {
		$buf = explode("/", $allowupd[$j]);
		$address = ipdot2iplong($buf[0]);
		$netmask = $buf[1];
		$sql = "INSERT INTO acldomain VALUES (NULL,$iddom,$address,$netmask,'UPD');";
		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
	}
	// Direttive ALLOW-NOTIFY
	for ($j=0; $j<count($allownot); $j++) {
		$buf = explode("/", $allownot[$j]);
		$address = ipdot2iplong($buf[0]);
		$netmask = $buf[1];
		$sql = "INSERT INTO acldomain VALUES (NULL,$iddom,$address,$netmask,'NOT');";
		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
	}
	if ($zonetype != "*") {
		echo "OK: Domain importing completed\n\n";
		exec (" echo \"OK: Domain importing completed\n\" >> $ReportLog");
	} else {
		echo "SKIP: Domain importing skipped\n\n";
		exec (" echo \"SKIP: Domain importing skipped\n\" >> $ReportLog");
	}
} 

echo "$EndHeaderReport\n";
exec ("echo \"\n$EndHeaderReport\" >> $ReportLog");
echo "\nFor possible problems consult the report file: $ReportLog\n";
unlink($tmpfname);
mysqli_close($conn);
