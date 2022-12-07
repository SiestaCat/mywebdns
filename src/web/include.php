<?

$DBdatabase = "<DATABASE>";
$DBusername = "<ADMINISTRATOR>";
$DBpassword = "<PASSWD>";
$DBhost     = "<MYSQLSERVER>";

// Funzione per la connessione al DB
function connect_db() {
        global $DBdatabase, $DBusername, $DBpassword, $DBhost;
        $dblink = mysql_connect($DBhost, $DBusername, $DBpassword) or die("\nERROR: cannot to connect to MySQL server\n\n");
        @mysql_select_db($DBdatabase) or die("\nERROR: cannot select database <$database>\n\n");

	session_start();

        return($dblink);
}

// Registrazione delle variabili di sessione
if (!session_is_registered("session_language")) {
	$conn1 = connect_db();
	$sql = "SELECT * FROM configuration WHERE id=1;";
	$result = mysql_query($sql,$conn1) or die(_SQLQueryError);
	$line = mysql_fetch_array($result);
	mysql_close($conn1);
	extract($line);

        session_register("session_version");
        session_register("session_language");
        session_register("session_record_ns");
        session_register("session_record_mx");
        session_register("session_record_a");
        session_register("session_record_ptr");
        session_register("session_record_cname");

	$session_version = "$VERSION";
	$session_language = "$LANGUAGE";
	$session_record_ns = "$RECORD_NS";
	$session_record_mx = "$RECORD_MX";
	$session_record_a = "$RECORD_A";
	$session_record_ptr = "$RECORD_PTR";
	$session_record_cname = "$RECORD_CNAME";
}

require("<PREFIX>configure/languages/$session_language/language.php");

// Funzione di stampa dell'intestazione del file
function headerfile($title) {
	$conn1 = connect_db();
	$sql = "SELECT * FROM configuration WHERE id=1;";
	$result = mysql_query($sql,$conn1) or die(_SQLQueryError);
	$line = mysql_fetch_array($result);
	extract($line);
	$session_language = "$LANGUAGE";

echo <<< EOB
	<HTML>
	<HEAD>
		<META CONTENT=NO-CACHE HTTP-EQUIV=PRAGMA>
		<META CONTENT=0 HTTP-EQUIV=EXPIRES>
		<LINK REL="stylesheet" HREF="/style.css" TYPE="TEXT/CSS">
	</HEAD>

	<BODY BGCOLOR="#FFFFFF">
	<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="/configure/languages/$session_language/functions.js"></SCRIPT>
EOB;
	if ($title != "") {
		echo "\n";
echo <<< EOB

	<DIV ALIGN=CENTER><BR>
		<H2>$title</H2>
	</DIV>

EOB;
	}
}

// Funzione per transformazione IP: dot form --> long form
function ipdot2iplong($ipdot) {
	$conn1 = connect_db();
        $sql = "SELECT ip2long('$ipdot');";
        $result = mysql_query($sql,$conn1) or die(_SQLQueryError);
        $out = mysql_fetch_row($result);
        return ($out[0]);
	mysql_close($conn1);
}

// Funzione per transformazione IP: long form --> dot form
function iplong2ipdot($iplong) {
	$conn1 = connect_db();
        $sql = "SELECT long2ip($iplong);";
        $result = mysql_query($sql,$conn1) or die(_SQLQueryError);
        $out = mysql_fetch_row($result);
        return ($out[0]);
	mysql_close($conn1);
}

// Funzione per transformazione NETMASK: dot form --> long form
function maskdot2masklong($maskdot) {
	$conn1 = connect_db();
	$sql = "SELECT maskdot2masklong('$maskdot');";
	$result = mysql_query($sql,$conn1) or die(_SQLQueryError);
	if (($out = mysql_fetch_row($result)) != NULL)
		return ($out[0]);
	else
		return -1;
	mysql_close($conn1);
}

// Funzione per transformazione NETMASK: long form --> dot form
function masklong2maskdot($masklong) {
        if (($masklong < 0) || ($masklong > 32))
                return -1;
	$conn1 = connect_db();
        $sql = "SELECT masklong2maskdot($masklong);";
        $result = mysql_query($sql,$conn1) or die(_SQLQueryError);
        $out = mysql_fetch_row($result);
        return ($out[0]);
	mysql_close($conn1);
}

// Mostra un messaggio di errore a video
function showerror($str) {
	$title = _ErrorTitle;
echo <<< EOB
	<BR>
	<P>
		<FONT SIZE=3><B><CENTER><FONT FACE=Lucida COLOR=red>$title</FONT></CENTER><B></FONT>
	</P>
	<DIV ALIGN=CENTER>
	<P>
		<FONT SIZE=3><B><FONT FACE=Lucida>$str</FONT><B></FONT>
	</P>
	</DIV>
EOB;
	exit;
}

// Mostra un messaggio di risultato a video
function showresult($str) {
	$title = _ResultTitle;
echo <<< EOB
	<BR>
	<P>
		<FONT SIZE=3><B><CENTER><FONT FACE=Lucida COLOR=blue>$title</FONT></CENTER><B></FONT>
	</P>
	<DIV ALIGN=CENTER>
	<P>
		<FONT SIZE=3><B><FONT FACE=Lucida>$str</FONT><B></FONT>
	</P>
	</DIV>
EOB;
	exit;
}

// Mostra un messaggio a video
function showmessage($str) {
echo <<< EOB
	<DIV ALIGN=CENTER>
	<P>
		<FONT SIZE=3><B><FONT FACE=Lucida>$str</FONT><B></FONT>
	</P>
	</DIV>
EOB;
	exit;
}

// Trova l'elemento KEY in ARRAY e ne restituisce la posizione
function findarray($key,$array) {
	$found = -1;
	for($ptr=0; $ptr<count($array); $ptr++) {
		if ($array[$ptr] == $key) return $ptr;
	}
	return $found;
}

// Verifica la presenza di DNS del db
function checkdns() {
	$conn1 = connect_db();
	$sql= "SELECT id FROM dns;";
	$result = mysql_query($sql,$conn1) or die(_SQLQueryError);
	if (mysql_fetch_row($result)==NULL)
		return (0);
	else
		return (1);
	mysql_close($conn1);
}

// Verifica la correttezza di un indirizzo IP in dot form
function checkip($ip) {
	$check = 1;
	if ((ipdot2iplong($ip) == 4294967295) || (ipdot2iplong($ip) == -1))
		$check = 0;
	return($check);
}

// Switch del colore a seconda se il valore e' pari o dispari
function switchcolor($counter) {
	$evenodd = $counter%2;
	if($evenodd)
       		return "#DDDDDD";
	else
       		return "#FFFFFF";
}

// Funzione per la determinazione delle caratteristiche di una rete
function find_net($host,$mask) {
        ### $host = IP address or hostname of target host (string) 
        ### $mask = Subnet mask of host in dotted decimal (string) 
        ### returns array with 
        ### "cidr" => host and mask in CIDR notation 
        ### "network" => network address 
        ### "broadcast" => broadcast address 
        $bits=strpos(decbin(ip2long($mask)),"0");
        $net["cidr"]=gethostbyname($host)."/".$bits;
        $ipnet = explode(".",gethostbyname($host));
        $ipmask = explode(".",$mask);
        $net["network"]=(intval($ipnet[0]) & intval($ipmask[0])).".".(intval($ipnet[1]) & intval($ipmask[1])).".".(intval($ipnet[2]) & intval($ipmask[2])).".".(intval($ipnet[3]) & intval($ipmask[3]));

        $binhost=str_pad(decbin(ip2long(gethostbyname($host))),32,"0",STR_PAD_LEFT);
        $binmask=str_pad(decbin(ip2long($mask)),32,"0",STR_PAD_LEFT);
        for ($i=0; $i<32; $i++) {
                if (substr($binhost,$i,1)=="1" ||
                        substr($binmask,$i,1)=="0") {
                        $broadcast.="1";
                } else {
                        $broadcast.="0";
                }
        }
        $net["broadcast"]=long2ip(bindec($broadcast));
        return $net;
}

?>
