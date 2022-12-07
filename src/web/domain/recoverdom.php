<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

// E' necessario per l'uso della funzione HEADER
if (isset($recoverdomain)) {
	headerfile("");
	echo "\n";

	// Tab Menu
        echo "\t<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BORDER=0>\n";
        echo "\t<TR>\n";
	if (isset($recoverdomain))
        	echo "\t\t<TD CLASS=TABCHECK WIDTH=60><A CLASS=TABCHECK HREF='$PHP_SELF?recoverdomain=OK'>$Tab_Dom</A></TD>\n";
	else
        	echo "\t\t<TD CLASS=TABUNCHECK WIDTH=60><A CLASS=TABUNCHECK HREF='$PHP_SELF?recoverdomain=OK'>$Tab_Dom</A></TD>\n";
	echo "\t\t<TD WIDTH=1 BGCOLOR=#c0c0c0><IMG SRC=/images/cleardot.gif WIDTH=1 HEIGHT=1></TD>\n";
        echo "\t</TR>\n";
        echo "\t</TABLE>\n";

	// Tab Title
        echo "\n";
        echo "\t<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0 BORDER=0>\n";
        echo "\t<TR>\n";
	if (isset($recoverdomain))
		echo "\t\t<TD CLASS=TABCHECK NOWRAP><A CLASS=TABTITLE>"._RecoverDomainHeader." </TD>\n";
        echo "\t</TR>\n";
        echo "\t</TABLE>\n";
}

#
# Recupero dominio
#
if (isset($recoverdomain)) {
	$i=0;
	// Selezione dei domini cancellati e appartenenti ad un DNS configurato
	$sql = "SELECT domain.ID, domain.NAME, domain.IDDNS, domain.ZONETYPE, dns.DNSFQDN FROM domain, dns WHERE domain.STATE='D' AND domain.IDDNS=dns.ID ORDER BY domain.NAME;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	if (($out = mysql_fetch_array($result)) != NULL) {

echo <<< EOB
	<CENTER>
	<BR>
	<DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
	<TABLE WIDTH=20% BORDER=0 CELLSPACING=0 CELLPADDING=7>
	<TR>
		<TD COLSPAN=7><HR></TD>
	</TR>
	<TR>
		<TD ALIGN=LEFT>  <FONT COLOR=darkblue><STRONG><B>$Mod_Dom</B></STRONG></TD>
		<TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_DNS</B></STRONG></TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
	</TR>
EOB;
		echo "\n";
		$counter = 1;
        	do {
	        	extract($out);
        	        echo "\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
                	echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO><B>$NAME</B></TD>\n";
	                echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><CLASS=LINKNERO>$DNSFQDN</TD>\n";
			if ($ZONETYPE == "M")
                                echo "\t\t<TD><A HREF='viewdom.php?iddom=$ID'><IMG SRC=/icons/search.gif BORDER=0 ALT='$Alt_View'></TD>\n";
                         else 
                                echo "\t\t<TD>&nbsp;</TD>\n";
			echo "\t\t<TD><A HREF='javascript:RecoverDomain(\"$NAME\",$ID)'><IMG SRC=/icons/replace.gif BORDER=0 ALT='$Alt_Recover'></TD>\n";
                	echo "\t</TR>\n";
	        } while ($out = mysql_fetch_array($result));
echo <<< EOB
	<TR>
		<TD COLSPAN=7><HR></TD>
	</TR>
	</TABLE>
	</DIV>
	</CENTER>
	</BODY>
	</HTML>
EOB;
	        exit;
	}
	showmessage(_CheckRecoverDomain);
	mysql_close($conn);
}

if (isset($continuerecoverdomain)) {
	$sql = "SELECT * FROM domain WHERE id=$iddom;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	$out = mysql_fetch_array($result);
	extract($out);
	$sql = "SELECT * FROM domain WHERE name='$NAME' AND iddns=$IDDNS AND state<>'D';";
        $result = mysql_query($sql,$conn) or die(_SQLQueryError);
        if (mysql_fetch_array($result) == NULL) {
		$sql = "UPDATE domain SET state='A' WHERE id=$iddom;";
		$result = mysql_query($sql,$conn) or die(_SQLQueryError);
		header("Location: $PHP_SELF?recoverdomain=ok");
	} else {
		headerfile("");
		showerror(_DomainRecoverError);
	}
}

?>
