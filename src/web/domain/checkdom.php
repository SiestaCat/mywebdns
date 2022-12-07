<?php

require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

if (!isset($nslookup) && !isset($dig) && !isset($whois))
	showerror(_DomainError);

$query = "SELECT name FROM domain WHERE id=$iddom;";
$result = mysqli_query($conn,$query) or die(_SQLQueryError);
$data = mysqli_fetch_array($result);

headerfile("");
echo "\n";
// Tab Menu
echo "\t<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BORDER=0>\n";
echo "\t<TR>\n";
if (isset($nslookup))
	echo "\t\t<TD CLASS=TABCHECK WIDTH=60><A CLASS=TABCHECK HREF='$PHP_SELF?nslookup=OK&iddom=$iddom'>$Tab_Nslookup</A></TD>\n";
else
	echo "\t\t<TD CLASS=TABUNCHECK WIDTH=60><A CLASS=TABUNCHECK HREF='$PHP_SELF?nslookup=OK&iddom=$iddom'>$Tab_Nslookup</A></TD>\n";
echo "\t\t<TD WIDTH=1 BGCOLOR=#c0c0c0><IMG SRC=/images/cleardot.gif WIDTH=1 HEIGHT=1></TD>\n";

if (isset($dig))
	echo "\t\t<TD CLASS=TABCHECK WIDTH=60><A CLASS=TABCHECK HREF='$PHP_SELF?dig=OK&iddom=$iddom'>$Tab_Dig</A></TD>\n";
else
	echo "\t\t<TD CLASS=TABUNCHECK WIDTH=60><A CLASS=TABUNCHECK HREF='$PHP_SELF?dig=OK&iddom=$iddom'>$Tab_Dig</A></TD>\n";
echo "\t\t<TD WIDTH=1 BGCOLOR=#c0c0c0><IMG SRC=/images/cleardot.gif WIDTH=1 HEIGHT=1></TD>\n";

if (isset($whois))
	echo "\t\t<TD CLASS=TABCHECK WIDTH=60><A CLASS=TABCHECK HREF='$PHP_SELF?whois=OK&iddom=$iddom'>$Tab_Whois</A></TD>\n";
else
	echo "\t\t<TD CLASS=TABUNCHECK WIDTH=60><A CLASS=TABUNCHECK HREF='$PHP_SELF?whois=OK&iddom=$iddom'>$Tab_Whois</A></TD>\n";
echo "\t\t<TD WIDTH=1 BGCOLOR=#c0c0c0><IMG SRC=/images/cleardot.gif WIDTH=1 HEIGHT=1></TD>\n";
echo "\t</TR>\n";
echo "\t</TABLE>\n";

echo <<< EOB

	<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0 BORDER=0>
	<TR>
		<TD CLASS=TABCHECK NOWRAP><A CLASS=TABTITLE>$data[name]</TD>
	</TR>
	<TR>
		<TD COLSPAN=1><BR>
		<PRE>
EOB;

#
# Main Function
#
if (isset($nslookup))
	system("nslookup -querytype=any $data[name]");

if (isset($dig))
	system("dig any $data[name]");

if (isset($whois)) {
	$err_ext = 1;
	$domain = $data[name];
	$domarray = explode(".", $domain);
	$count = count($domarray) - 1;
	$ext = strtolower($domarray[$count]);
	$maindomain = strtolower($domarray[$count-1].".".$domarray[$count]);
	$niclist = "ar im as am au bt bg it cl cc cg bi rw zr cr cz dk ec fo gl fr de ir jo my mx ni nl nu pk pl ru sg sk es tw th to tm uk ua co ch li";
	$uslist = "com net org";
	$ripelist = "il gr gg je";
	$apniclist = "";
	$jpniclist = "jp";

	if($ext != "") {
        	if(ereg($ext, $niclist)) {
                	$whois_serv = "whois." . "nic." . $ext;
                    	$errorlevel = 0;
        	}
        	elseif(ereg($ext, $uslist)) {
                	$whois_serv = "whois.networksolutions.com";
                	$errorlevel = 0;
        	}
        	elseif(ereg($ext, $ripelist)) {
                	$whois_serv = "whois.ripe.net";
               		$errorlevel = 0;
        	}
        	elseif(ereg($ext, $apniclist)) {
                	$whois_serv = "whois.apnic.net";
               	 	$errorlevel = 0;
        	}
        	elseif(ereg($ext, $jpniclist)) {
                	$whois_serv = "whois.nic.ad.jp";
                	$domain = $domain . "/e";
                	$errorlevel = 0;
        	}
        	elseif($ext == "se") {
                	$whois_serv = "whois.nic-se.se";
                	$errorlevel = 0;
        	}
        	elseif($ext == "lu") {
                	$whois_serv = "whois.restena.lu";
                	$errorlevel = 0;
        	}
        	else {
                	$errorlevel = 2;
			showerror(_TLDError);
        	}
	}
        system("whois -h $whois_serv $maindomain");
}

echo <<< EOB
		</PRE>
		</TD>
	</TR>
	</TABLE>
	</BODY>
	</HTML>
EOB;
?>
