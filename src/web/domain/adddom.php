<?
require("../include.php");
$conn = connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
	showerror(_DNSError);
}

#
# Main Function
#
if (isset($adddomain)) {
        headerfile("");
	echo "\n";

        // Tab Menu
	echo "\t<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BORDER=0>\n";
	echo "\t<TR>\n";
	if (isset($adddomain))
		echo "\t\t<TD CLASS=TABCHECK WIDTH=60><A CLASS=TABCHECK HREF='$PHP_SELF?adddomain=OK'>$Tab_Dom</A></TD>\n";
	else
		echo "\t\t<TD CLASS=TABUNCHECK WIDTH=60><A CLASS=TABUNCHECK HREF='$PHP_SELF?adddomain=OK'>$Tab_Dom</A></TD>\n";
	echo "\t\t<TD WIDTH=1 BGCOLOR=#c0c0c0><IMG SRC=/images/cleardot.gif WIDTH=1 HEIGHT=1></TD>\n";

	echo "\t\t<TD WIDTH=1 BGCOLOR=#c0c0c0><IMG SRC=/images/cleardot.gif WIDTH=1 HEIGHT=1></TD>\n";
	echo "\t</TR>\n";
	echo "\t</TABLE>\n";

	// Tab Title
	echo "\n";
	echo "\t<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0 BORDER=0>\n";
	echo "\t<TR>\n";
	if (isset($adddomain))
		echo "\t\t<TD CLASS=TABCHECK NOWRAP><A CLASS=TABTITLE>"._AddDomainHeader."</TD>\n";
	echo "\t</TR>\n";
	echo "\t</TABLE>\n";
}

#
# Aggiungi dominio
#
if (isset($adddomain)) {
	$i=0;
	$sql = "SELECT * FROM dns ORDER BY dnsfqdn;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	$out = mysql_fetch_array($result);

echo <<< EOB
	<BR>
	<BR>
	<DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
	<TABLE WIDTH=20% BORDER=0 CELLSPACING=3 CELLPADDING=3>
	<TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_DNS</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>
			<SELECT NAME=iddns>
EOB;
	echo "\n";
	do {
	        extract($out);
        	if ($i==0) {
                	echo "\t\t\t\t<OPTION SELECTED VALUE='$ID'>$DNSFQDN</OPTION>\n";
	                $i++;
        	} else
                	echo "\t\t\t\t<OPTION VALUE='$ID'>$DNSFQDN</OPTION>\n";
	} while ($out = mysql_fetch_array($result));
echo <<< EOB
			</SELECT></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Name</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=30 NAME=domainname>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Type</B></FONT></TD>
	        <TD><SELECT NAME=zonetype><OPTION SELECTED VALUE='M'>$Mod_Dom_Mas
						<OPTION VALUE='S'>$Mod_Dom_Slv
						<OPTION VALUE='F'>$Mod_Dom_For
					  </SELECT></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
	</TABLE>
	<BR>
	<INPUT TYPE=HIDDEN VALUE='ok' NAME=continueadddomain>
	<INPUT TYPE=SUBMIT VALUE='$Button_Continue' NAME=continueadddomain>
	</FORM>
	</DIV>
	</BODY>
	</HTML>
EOB;
	mysql_close($conn);
	exit;
}

if (isset($continueadddomain)) {
        // Check sui dati inseriti
        if ($domainname == "") {
		headerfile("");
		showerror(_CheckInputError);
	}

	$sql = "SELECT * FROM dns WHERE id=$iddns;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	$out = mysql_fetch_array($result);
	extract($out);
	$hostdns = $DNSFQDN.".";
	$rootdns = "root.".$DNSFQDN.".";

	$sql = "SELECT * FROM domain WHERE name='$domainname' AND state<>'D' AND iddns=$iddns;";
       	$result = mysql_query($sql,$conn) or die(_SQLQueryError);

	// Check sul dominio
	if (mysql_fetch_array($result) != NULL) {
		headerfile("");
		showerror(_DomainRegError);
	}
		
	$data = date("Y-m-d");			// Data di creazione del dominio
	if (strstr($domainname, ".in-addr.arpa")) {
		$level = 0;			// Livello
		$zonemastertype = "R";
	} else {
		$buffer = explode (".", $domainname);
		$level = count($buffer);	// Livello
		$zonemastertype = "M";
	}

	switch($zonetype) {
		// Master
		case "M":
			$serial = date("Ymd")."00";
			$sql = "INSERT INTO domain VALUES (NULL,'$domainname',$level,'$data',86400,'$hostdns','$rootdns',$serial,86400,7200,2592000,86400,'$zonetype','$zonemastertype','A','0',$iddns,0);";
                        mysql_query($sql,$conn) or die(_SQLQueryError);
                        $iddom = mysql_insert_id();
                        header("Location: editdom.php?iddom=$iddom");
                        break;
		// Slave & Forward
		case "S":
		case "F":
                	$sql = "INSERT INTO domain VALUES (NULL,'$domainname',$level,'$data',0,'','',0,0,0,0,0,'$zonetype','$zonemastertype','A','0',$iddns,0,0);";
                        mysql_query($sql,$conn) or die(_SQLQueryError);
			$iddom = mysql_insert_id();
			header("Location: infodom.php?iddom=$iddom");
			break;
	}
	mysql_close($conn);
	exit;
}

?>
