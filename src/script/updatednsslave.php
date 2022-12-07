<PHPBINPATH>
<?php
// +--------------------------------------------------------------------+
// | Progetto WebDNS - updatednsslave.php				|
// +--------------------------------------------------------------------+
// | Effettua l'allineamento del named.conf rispetto ai dati del dns	|
// | master inserito nel DB.						|
// +--------------------------------------------------------------------+
// | Autore : Pasquale Affinito                                         |
// +--------------------------------------------------------------------+

$DBdatabase = "<DATABASE>";
$DBusername = "<ADMINISTRATOR>";
$DBpassword = "<PASSWD>";
$DBhost     = "<MYSQLSERVER>";

// Setto il tempo di esecuzione in maniera illimitata per la gestione dei
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
$ipmaster = iplong2ipdot($out[DNSIP]);
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

$sql = "SELECT * FROM domain WHERE iddns=$iddns AND state<>'D' ORDER BY name;";
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
				   $arraynamed[] = "\ttype slave;\n";
				   $arraynamed[] = "\tfile \"$NAME.zone\";\n";
				   $arraynamed[] = "\tmasters {\n";
				   $arraynamed[] = "\t\t$ipmaster;\n";
				   $arraynamed[] = "\t};\n";
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

?>
