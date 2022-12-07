<PHPBINPATH>
<?php
// +--------------------------------------------------------------------+
// | Progetto WebDNS - checkdns.php					|
// +--------------------------------------------------------------------+
// | Effettua i seguenti controlli sui domini di un particolare dns:	|
// | 1. File di zona per ogno dominio che non sia di tipo Forward	|
// | 2. TTL di dominio non standard (<>86400)				|
// | 3. IP DNS Master per i domini Slave				|
// | 4. IP DNS Forwarders per i domini Forward				|
// | 5. Eventuali modifiche manuali dei file di zona			|
// | 									|
// | ATTENZIONE!!!							| 
// | Per un uso corretto e veritiero e' consigliato eseguire lo script	|
// | dopo updatedns.php.						|
// +--------------------------------------------------------------------+
// | Autore : Pasquale Affinito                                         |
// +--------------------------------------------------------------------+

$DBdatabase = "<DATABASE>";
$DBusername = "<ADMINISTRATOR>";
$DBpassword = "<PASSWD>";
$DBhost     = "<MYSQLSERVER>";

$ReportLog = "/tmp/checkdns_report.log";
$HeaderReport = "***************************************\n".
		"checkdns.php REPORT - ".date("Y-m-d, H:i")."\n".
		"***************************************\n";
$EndHeaderReport = "*******************************************\n".
   		   "checkdns.php END REPORT - ".date("Y-m-d, H:i")."\n".
		   "*******************************************";

// Setto il tempo di esecuzione in maniera illimitata
//set_time_limit(0);

// Funzione di connessiona al DB
function connect_db() {
	global $DBdatabase, $DBusername, $DBpassword, $DBhost;
	$dblink = @mysql_connect($DBhost, $DBusername, $DBpassword) or die("\nERROR: cannot to connect to MySQL server\n\n"); 
	@mysql_select_db($DBdatabase) or die("\nERROR: cannot select database  <$database>\n\n");
	return($dblink);
}

// Funzione per transformazione IP: dot form --> long form
function ipdot2iplong($ipdot) {
        $conn=connect_db();
        $sql = "SELECT ip2long('$ipdot');";
        $result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
        $out = mysql_fetch_row($result);
        return ($out[0]);
        mysqli_close($conn);
}

// Funzione per transformazione IP: long form --> dot form
function iplong2ipdot($iplong) {
        $conn=connect_db();
        $sql = "SELECT long2ip($iplong);";
        $result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
        $out = mysql_fetch_row($result);
        return ($out[0]);
        mysqli_close($conn);
}

// Funzione di scrittura di un array in un file
function writefile ($file, $array) {
        $fd = fopen ($file,"a");
        for ($i=0; $i<count($array); $i++) 
                fwrite($fd,$array[$i]);
        fclose($fd);
}

// Funzione per il check del TTL
function checkttl($ttl) {
	if ($ttl != 86400)
		return(0);
	else
		return(1);
}

// Funzione per il check del file di dominio
function checkfiledomain($name, $zonetype) {
	global $dirzones;
	global $zonefile;

	$zonefile = $name.".zone";
	if ($zonetype != "F") {
		if (!file_exists($dirzones.$zonefile))
			return (0);
		else
			return (1);
	}
		return (1);
}

// Funzione di check degli IP DNS MASTER per i domini slave
function checkipmaster($iddom) {
	global $conn;

	$sql = "SELECT * FROM ipdnsmaster WHERE iddom=$iddom ORDER BY ip;";
	$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
	if (mysqli_fetch_array($result) != NULL )
		return (1);
	else
		return (0);
}

// Funzione di check degli IP DNS FORWARDERS per i domini forward
function checkipforwarders($iddom) {
	global $conn;

	$sql = "SELECT * FROM ipdnsforwarders WHERE iddom=$iddom ORDER BY ip;";
	$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
	if (mysqli_fetch_array($result) != NULL )
		return (1);
	else
		return (0);
}

// Funzione per il check sull'md5 dei file per una verifica di modifiche manuali
function checkmd5($name, $zonetype, $md5) {
	global $dirzones;
	
	$zonefile = $name.".zone";

	// MD5 del file
        $fp = fopen($dirzones.$zonefile, "rb");
        $contents = fread($fp, filesize($dirzones.$zonefile));
        $filemd5 = md5($contents);
        fclose($fp);

	if ($zonetype == "M") {
		if ($filemd5 == $md5)
			return (1);
		else
			return (0);
	} else
		return (1);
}

#
# Main
#

if ($argc != 2)
	die ("\nUsage: checkdns.php <DNSFQDN>\n\n");
else
	$dnstocheck = strtolower($argv[1]);

// Effettuo la connessione al DB
$conn = connect_db();

// Check del DNS inserito
$sql = "SELECT * FROM dns WHERE dnsfqdn='$dnstocheck';";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_array($result)) == NULL )
	die("\nERROR: DNS <$dnstocheck> is not configured into DB\n\n");

$iddns = $out[ID];
$dirzones = $out[DIRZONES];
$len = strlen($dirzones);
if (substr($dirzones, $len-1, $len-1) != "/")
	$dirzones = $dirzones."/";

// Selezione dei domini appartenenti al dns specificato
$sql = "SELECT * FROM domain WHERE iddns=$iddns ORDER BY name;";
$result = mysqli_query($conn,$sql) or die("\nERROR: impossible to execute SQL command ($sql)\n\n");
if (($out = mysqli_fetch_array($result)) != NULL ) {

	exec ("echo \"$HeaderReport\" > $ReportLog");

	// Inizializzazione degli array di output
	$filedom = array();
	$ipmast = array();
	$ipforw = array();
	$ttldom = array();
	$manual = array();

	do {
		extract($out);

		$zonefile = "";

		switch($STATE) {
			case "A": 
				switch ($ZONETYPE) {
					case "M":
						if (!checkfiledomain($NAME,$ZONETYPE))
							$filedom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." FILE = ".$zonefile."\n";
						else {
							if (!checkmd5($NAME,$ZONETYPE,$MD5))
								$manual[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						}
				  		if (!checkttl($TTL))
							$ttldom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." TTL = ".$TTL."\n";
						break;
					case "S":
				  		if (!checkipmaster($ID))
							$ipmast[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						break;
					case "F":
				  		if (!checkipforwarders($ID))
							$ipforw[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						break;
				}
				break;
			case "M":
				switch ($ZONETYPE) {
					case "M":
						if (!checkfiledomain($NAME,$ZONETYPE))
							$filedom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." FILE = ".$zonefile."\n";
						else {
							if (!checkmd5($NAME,$ZONETYPE,$MD5))
								$manual[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						}
				  		if (!checkttl($TTL))
							$ttldom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." TTL=".$TTL."\n";
						break;
					case "S":
				  		if ((!checkttl($TTL)) && ($TTL!=0))
							$ttldom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." TTL=".$TTL."\n";
				  		if (!checkipmaster($ID))
							$ipmast[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						break;
					case "F":
				  		if (!checkipforwarders($ID))
							$ipforw[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						break;
				}
				break;
			case "D": break;
			case "N": 
				switch ($ZONETYPE) {
					case "M":
						if (!checkfiledomain($NAME,$ZONETYPE))
							$filedom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." FILE = ".$zonefile."\n";
						else {
							if (!checkmd5($NAME,$ZONETYPE,$MD5))
								$manual[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						}
				  		if (!checkttl($TTL))
							$ttldom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." TTL=".$TTL."\n";
						break;
					case "S":
				  		if (!checkttl($TTL))
							$ttldom[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)." TTL=".$TTL."\n";
				  		if (!checkipmaster($ID))
							$ipmast[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						break;
					case "F":
				  		if (!checkipforwarders($ID))
							$ipforw[] = str_pad($NAME,40,' ',STR_PAD_RIGHT)."\n";
						break;
				}
				break;
		}
	} while($out = mysqli_fetch_array($result));

	if (count($filedom)>0) {
		exec ("echo \"\nDomains without zonefile\" >> $ReportLog");
		exec ("echo \"************************\" >> $ReportLog");
		writefile($ReportLog,$filedom);
	}
	if (count($ipmast)>0) {
		exec ("echo \"\nSlave domains without IP Master\" >> $ReportLog");
		exec ("echo \"*******************************\" >> $ReportLog");
		writefile($ReportLog,$ipmast);
	}
	if (count($ipforw)>0) {
		exec ("echo \"\nForward domains without IP Forwarders\" >> $ReportLog");
		exec ("echo \"*************************************\" >> $ReportLog");
		writefile($ReportLog,$ipforw);
	}
	if (count($ttldom)>0) {
		exec ("echo \"\nDomains with not standard TTL (<>86400)\" >> $ReportLog");
		exec ("echo \"***************************************\" >> $ReportLog");
		writefile($ReportLog,$ttldom);
	}
	if (count($manual)>0) {
		exec ("echo \"\nDomains changed manually\" >> $ReportLog");
		exec ("echo \"************************\" >> $ReportLog");
		writefile($ReportLog,$manual);
	}

	exec ("echo \"\n$EndHeaderReport\" >> $ReportLog");

	$fp = fopen($ReportLog, "rb");
	$contents = fread($fp, filesize($ReportLog));
	fclose($fp);

	mail("<TOMAILCHECK>","CheckDNS \"$dnstocheck\"", $contents,
		 "MIME-Version: 1.0\r\n"
		."From: myWebDNS <root@$HTTP_SERVER_VARS[HOST]>\r\n");
}

?>

