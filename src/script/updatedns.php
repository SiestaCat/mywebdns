<PHPBINPATH>
<?php
// +--------------------------------------------------------------------+
// | Progetto WebDNS - updatedns.php					|
// +--------------------------------------------------------------------+
// | Effettua l'allineamento dei file di dominio rispetto ai dati del   |
// | DB.								|
// +--------------------------------------------------------------------+
// | Autore : Pasquale Affinito                                         |
// +--------------------------------------------------------------------+

$DBdatabase = "<DATABASE>";
$DBusername = "<ADMINISTRATOR>";
$DBpassword = "<PASSWD>";
$DBhost     = "<MYSQLSERVER>";

// Setto il tempo di esecuzione in maniera illimitata
set_time_limit(0);

// Funzione di connessiona al DB
function connect_db() {
	global $DBdatabase, $DBusername, $DBpassword, $DBhost;
	$dblink = @mysql_connect($DBhost, $DBusername, $DBpassword) or die("\nERROR: cannot to connect to MySQL server\n\n"); 
	@mysql_select_db($DBdatabase) or die("\nERROR: cannot select database <$database>\n\n");
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

// Funzione di scrittura di un array in un file
function writefile ($file, $array) {
        $fd = fopen ($file,"w");
        for ($i=0; $i<count($array); $i++) 
                fwrite($fd,$array[$i]);
        fclose($fd);
}

// Funzione per l'inserimento delle ACL di zona nel named.conf
function insertACL($iddom, $acltype, $allow) {
	global $conn, $arraynamed;

	$sql = "SELECT * FROM acldomain WHERE iddom=$iddom AND type='$acltype' ORDER BY ip;";
        $resdom = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

	if (($outdom = mysqli_fetch_array($resdom)) != NULL ) {
        	$arraynamed[] = "\t$allow {\n";
               	do {
	               	extract ($outdom);
                        $ip = iplong2ipdot($IP);
               	        $arraynamed[] = "\t\t$ip/$NETMASK;\n";
	        } while ($outdom = mysqli_fetch_array($resdom));
        	$arraynamed[] = "\t};\n";
	}
}

#
# Main
#
if ($argc != 2)
	die ("\nUsage: updatedns.php <DNSFQDN>\n\n");
else
	$dnstoupdate = strtolower($argv[1]);

// Effettuo la connessione al DB
$conn = connect_db();

// Check del DNS inserito
$sql = "SELECT * FROM dns WHERE dnsfqdn='$dnstoupdate';";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_array($result)) == NULL )
	die("\nERROR: DNS <$dnstoupdate> is not configured into DB\n\n");

$iddns = $out[ID];
$filenamed = $out[INCLUDEZONENAMED];
$dirzones = $out[DIRZONES];
$len = strlen($dirzones);
if (substr($dirzones, $len-1, $len-1) != "/")
	$dirzones = $dirzones."/";

// Backup del named.conf
$data = date("Ymd");
if (file_exists($filenamed)) {
	copy($filenamed,$filenamed.".".$data);
	unlink($filenamed);
}
touch($filenamed);
$arraynamed = array();
		
// Cancellazione dei domini con STATE='D' nel dns selezionato
$sql = "SELECT * FROM domain WHERE iddns=$iddns AND state='D';";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_array($result)) != NULL ) {
	do {
		extract ($out);

		// Definizione del nome del file di dominio
		if ($ZONETYPE != "F")
			$filezone = $NAME.".zone";

		// Cancellazione delle ACL sul dominio
		$sql = "DELETE FROM acldomain WHERE iddom=$ID;";
		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

		switch ($ZONETYPE) {
			// Master
			case "M":
				  // Cancellazione dei record del dominio
				  switch ($ZONEMASTERTYPE) {
					case "M": 
				  		$sql = "DELETE FROM recordmaster WHERE iddom=$ID;";
				  		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
				  		break;
					case "R": 
				  		$sql = "DELETE FROM recordreverse WHERE iddom=$ID;";
				  		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
				  		break;
				  }
				  break;
			// Slave
			case "S":
				  // Cancellazione degli ip dei DNS MASTER
				  $sql = "DELETE FROM ipdnsmaster WHERE iddom=$ID;";
				  mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
				  break;
			// Forward
			case "F":
				  // Cancellazione degli ip dei DNS FORWARDERS
				  $sql = "DELETE FROM ipdnsforwarders WHERE iddom=$ID;";
				  mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
				  break;
		}

		// Cancellazione del dominio
		$sql = "DELETE FROM domain WHERE id=$ID;";
		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

		// Cancellazione fisica del file di dominio
		if (file_exists($dirzones.$filezone))
			unlink($dirzones.$filezone);

	} while ($out = mysqli_fetch_array($result));
}

$sql = "SELECT * FROM domain WHERE iddns=$iddns ORDER BY name;";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_array($result)) != NULL ) {
	do {
		extract ($out);
		
		$arraynamed[] = "#\n";
		$arraynamed[] = "# Domain created on $DATA\n";
		$arraynamed[] = "#\n";
		$arraynamed[] = "zone \"$NAME\" {\n";
		switch($ZONETYPE) {
			case "M":  $arraynamed[] = "\tzone-statistics yes;\n";
				   $arraynamed[] = "\ttype master;\n";
				   $arraynamed[] = "\tfile \"$NAME.zone\";\n";
				   break;
			case "S":  $arraynamed[] = "\tzone-statistics yes;\n";
				   $arraynamed[] = "\ttype slave;\n";
				   $arraynamed[] = "\tfile \"$NAME.zone\";\n";
				   $arraynamed[] = "\tmasters {\n";
				   $sql = "SELECT * FROM ipdnsmaster WHERE iddom=$ID ORDER BY ip;";
				   $resdom = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
				   if (($outdom = mysqli_fetch_array($resdom)) != NULL ) {
					do {
						extract ($outdom);
						$ip = iplong2ipdot($IP);
						$arraynamed[] = "\t\t$ip;\n";
					} while ($outdom = mysqli_fetch_array($resdom));
				   }
				   $arraynamed[] = "\t};\n";
				   break;
			case "F" : $arraynamed[] = "\ttype forward;\n";
				   $arraynamed[] = "\tforwarders {\n";
				   $sql = "SELECT * FROM ipdnsforwarders WHERE iddom=$ID ORDER BY ip;";
				   $resdom = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
				   if (($outdom = mysqli_fetch_array($resdom)) != NULL ) {
					do {
						extract ($outdom);
						$ip = iplong2ipdot($IP);
						$arraynamed[] = "\t\t$ip;\n";
					} while ($outdom = mysqli_fetch_array($resdom));
				   }
				   $arraynamed[] = "\t};\n";
				   break;
		}

		// Inserimento delle ACL
		insertACL($ID,"NOT","allow-notify");
		insertACL($ID,"QRY","allow-query");
		insertACL($ID,"TRX","allow-transfer");
		insertACL($ID,"UPD","allow-update");

		$arraynamed[] = "};\n";
		$arraynamed[] = "\n";
	} while ($out = mysqli_fetch_array($result));

	writefile($filenamed, $arraynamed);
}

// Cancellazione dei domini con STATE='D' nei dns cancellati
$sql = "SELECT DISTINCT domain.ID, domain.NAME, domain.ZONETYPE, domain.ZONEMASTERTYPE FROM dns,domain WHERE domain.state='D' AND domain.iddns<>dns.id;";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_array($result)) != NULL ) {
        do {
                extract ($out);

                // Definizione del nome del file di dominio
                if ($ZONETYPE != "F")
                        $filezone = $NAME.".zone";

                // Cancellazione delle ACL sul dominio
                $sql = "DELETE FROM acldomain WHERE iddom=$ID;";
                mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

                switch ($ZONETYPE) {
                        // Master
                        case "M":
                                  // Cancellazione dei record del dominio
                                  switch ($ZONEMASTERTYPE) {
                                        case "M":
                                                $sql = "DELETE FROM recordmaster WHERE iddom=$ID;";
                                                mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
                                                break;
                                        case "R":
                                                $sql = "DELETE FROM recordreverse WHERE iddom=$ID;";
                                                mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
                                                break;
                                  }
                                  break;
                        // Slave
                        case "S":
                                  // Cancellazione degli ip dei DNS MASTER
                                  $sql = "DELETE FROM ipdnsmaster WHERE iddom=$ID;";
                                  mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
                                  break;
                        // Forward
                        case "F":
                                  // Cancellazione degli ip dei DNS FORWARDERS
                                  $sql = "DELETE FROM ipdnsforwarders WHERE iddom=$ID;";
                                  mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
                                  break;
                }

                // Cancellazione del dominio
                $sql = "DELETE FROM domain WHERE id=$ID;";
                mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

                // Cancellazione fisica del file di dominio
                if (file_exists($dirzones.$filezone))
                        unlink($dirzones.$filezone);

        } while ($out = mysqli_fetch_array($result));
}

// Selezione dei domini appartenenti al dns specificato
$sql = "SELECT *, id AS IDDOMAIN, name AS NAMEDOM FROM domain WHERE iddns=$iddns AND state<>'N' ORDER BY namedom;";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_array($result)) != NULL ) {
	do {
		$arrayzone = array();
		extract($out);

		// Definizione del nome del file di dominio
		if ($ZONETYPE != "F")
			$filezone = $NAMEDOM.".zone";

		switch($STATE) {
			// Aggiungi
			case "A":
			// Modifica
			case "M":
				  switch ($ZONETYPE) {
					// Master
					case "M":
						  // Zona da creare fisicamente
						  $arrayzone[] = "; Do not modify this file manually.\n";
						  $arrayzone[] = "; Every modification will be ignored.\n";
						  $arrayzone[] = "\n";

						  // TTL
						  $arrayzone[] = "\$TTL $TTL\n";

						  // Record SOA
						  $arrayzone[] = "@\tIN\tSOA\t$HOSTDNS\t$ROOTDNS (\n";

						  $arrayzone[] = "\t\t\t$SERIAL\t; serial\n";
						  $arrayzone[] = "\t\t\t$REFRESH\t\t; refresh\n";
						  $arrayzone[] = "\t\t\t$RETRY\t\t; retry\n";
						  $arrayzone[] = "\t\t\t$EXPIRE\t\t; expire\n";
						  $arrayzone[] = "\t\t\t$MINIMUM )\t\t; minimum\n";
						  $arrayzone[] = "\n";

						  // Record zona
						  switch ($ZONEMASTERTYPE) {
							case "M":
								  $sql = "SELECT * FROM recordmaster WHERE iddom=$IDDOMAIN ORDER BY type DESC ,name ASC, priority ASC, hosttarget ASC;";
								  $resdom = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
								  break;
							case "R":
								  $sql = "SELECT *, LPAD(ip,7,' ') AS newip FROM recordreverse WHERE iddom=$IDDOMAIN ORDER BY newip;";
								  $resdom = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
								  break;
						  }
						  if (($outdom = mysqli_fetch_array($resdom)) != NULL) {
							do {
								switch ($ZONEMASTERTYPE) {
									case "M" : $record = str_pad($outdom[NAME],40,' ',STR_PAD_RIGHT);
										   break;
									case "R" : $record = str_pad($outdom[IP],40,' ',STR_PAD_RIGHT);
										   break;
								}
								$record .= "$outdom[TTL]\tIN\t$outdom[TYPE]";
								
								switch ($outdom[TYPE]) {
									case "MX":
										  $record .= "\t$outdom[PRIORITY]  $outdom[HOSTTARGET]\n";
										  break;
									case "A":
										  $ip = iplong2ipdot($outdom[IP]);
										  $record .= "\t$ip\n";
										  break;
									case "NS":
									case "CNAME":
									case "PTR":
										  $record .= "\t$outdom[HOSTTARGET]\n";
										  break;
								}
								$arrayzone[] = $record;
							} while ($outdom = mysqli_fetch_array($resdom));
						  }

					  	  // Scrittura fisica del file di dominio
						  writefile($dirzones.$filezone, $arrayzone);
						  unset($arrayzone);

						  // MD5 del file
						  $fp = fopen($dirzones.$filezone, "rb");
						  $contents = fread($fp, filesize($dirzones.$filezone));
						  $domainmd5 = md5($contents);
						  fclose($fp);

						  break;
					// Slave
					case "S": $domainmd5 = "";
						  break;
					// Forward
					case "F": $domainmd5 = "";
						  break;
				  }
				  break;
		}

		// Modifica dello stato del dominio a N
		$sql = "UPDATE domain SET state='N', md5='$domainmd5' WHERE id=$IDDOMAIN;";
		mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");

	} while($out = mysqli_fetch_array($result));
}

?>
