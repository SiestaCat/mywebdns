<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

// E' necessario per l'uso della funzione HEADER
if (isset($selectdomain) || isset($searchdomain)) {
	// Tab Menu
	headerfile("");
	echo "\n";
	echo "\t<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BORDER=0>\n";
	echo "\t<TR>\n";
	if (isset($selectdomain) || isset($searchdomain))
        	echo "\t\t<TD CLASS=TABCHECK WIDTH=60><A CLASS=TABCHECK HREF='$PHP_SELF?selectdomain=OK'>$Tab_Dom</A></TD>\n";
	else
        	echo "\t\t<TD CLASS=TABUNCHECK WIDTH=60><A CLASS=TABUNCHECK HREF='$PHP_SELF?selectdomain=OK'>$Tab_Dom</A></TD>\n";
	echo "\t\t<TD WIDTH=1 BGCOLOR=#c0c0c0><IMG SRC=/images/cleardot.gif WIDTH=1 HEIGHT=1></TD>\n";
        echo "\t</TR>\n";
        echo "\t</TABLE>\n";
        // Tab Title
        echo "\n";
        echo "\t<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0 BORDER=0>\n";
        echo "\t<TR>\n";
	if (isset($selectdomain))
		echo "\t\t<TD CLASS=TABCHECK NOWRAP><A CLASS=TABTITLE>"._SelectDomainHeader."</TD>\n";
	if (isset($searchdomain))
		echo "\t\t<TD CLASS=TABCHECK NOWRAP><A CLASS=TABTITLE>"._SearchDomainHeader."</TD>\n";
	echo "\t</TR>\n";
	echo "\t</TABLE>\n";
}

#
# Selezione del dominio
#
if (isset($selectdomain)) {
echo <<< EOB
        <DIV ALIGN=CENTER>
	<FORM METHOD=GET ACTION=$PHP_SELF>
	<TABLE WIDTH=20% BORDER=0 CELLSPACING=3 CELLPADDING=3>
 	<TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
       	<TR>
		<INPUT TYPE=HIDDEN NAME=searchdomain VALUE=ok>
       		<TD><WIDTH=60% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Dom</B></FONT></TD>
	        <TD><WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=30 NAME=domain></TD>
	        <TD><INPUT TYPE=SUBMIT VALUE='$Button_Select' NAME=searchdomain></TD>
        </TR>
        <TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
        </TABLE>
        <BR>
        </FORM>
        </DIV>
EOB;
	echo "\n";
        mysql_close($conn);
        exit;
}

#
# Ricerca del dominio
#
if (isset($searchdomain)) {
	// Selezione del dominio
	$domain = trim($domain);

	// Si selezionano tutti i domini tranne che quelli cancellati e apparteneneti ad un DNS configurato
	$sql = "SELECT domain.ID, domain.NAME, domain.ZONETYPE, domain.ZONEMASTERTYPE, domain.LOCKDEL, dns.DNSFQDN FROM domain, dns WHERE domain.name LIKE '%$domain%' AND domain.state<>'D' AND domain.iddns=dns.id ORDER BY name;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);

	if (($data = mysql_fetch_array($result)) != NULL) {
		echo "\n\t<BR><CENTER><FONT COLOR=darkblue><B>$Mod_RisSearch".mysql_num_rows($result)."</B></CENTER>\n";
echo <<< EOB
	
	<DIV ALIGN=CENTER>
	<FORM METHOD=GET ACTION=$PHP_SELF>
	<TABLE WIDTH=20% BORDER=0 CELLSPACING=0 CELLPADDING=7>
	<TR>
                <TD COLSPAN=9><HR></TD>
        </TR>
	<TR>
		<TD>&nbsp;</TD>
		<TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_Type</B></STRONG></TD>
		<TD ALIGN=LEFT>  <FONT COLOR=darkblue><STRONG><B>$Mod_Dom</B></STRONG></TD>
		<TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_DNS</B></STRONG></TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
	</TR>
EOB;
		echo "\n";
		$counter = 1;
		do {
			extract($data);
			echo "\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
echo <<< EOB
		<TD WIDTH=5%><A HREF='checkdom.php?nslookup=OK&iddom=$ID'><IMG SRC='/icons/help.gif 'BORDER=0 ALT='$Alt_Check'></TD>
		<TD WIDTH=5% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><CLASS=LINKNERO>$ZONETYPE</TD>
		<TD WIDTH=35% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO><B>$NAME</B></TD>
		<TD WIDTH=30% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><CLASS=LINKNERO>$DNSFQDN</TD>
		<TD WIDTH=5%><A HREF='infodom.php?iddom=$ID'><IMG SRC=/icons/info.gif BORDER=0 ALT='$Alt_Info'></TD>
		<TD WIDTH=5%><A HREF='acldom.php?iddom=$ID'><IMG SRC=/icons/acl.gif BORDER=0 ALT='$Alt_ACL'></TD>
EOB;
			echo "\n";
			if ($ZONETYPE == "M") {
				echo "\t\t<TD WIDTH=5%><A HREF='viewdom.php?iddom=$ID'><IMG SRC=/icons/search.gif BORDER=0 ALT='$Alt_View'></TD>\n";
				echo "\t\t<TD WIDTH=5%><A HREF='editdom.php?iddom=$ID'><IMG SRC=/icons/edit.gif BORDER=0 ALT='$Alt_Change'></TD>\n";
			} else {
				echo "\t\t<TD>&nbsp;</TD>\n";
				echo "\t\t<TD>&nbsp;</TD>\n";
			}
			//if (!in_array ($NAME,$safezone))
			if ($LOCKDEL == 0)
				echo "\t\t<TD WIDTH=5%><A HREF='javascript:DelDomain(\"$NAME\",$ID,\"$domain\")'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>\n";
			else 
				echo "\t\t<TD>&nbsp;</TD>\n";
			echo "\t</TR>\n";
		} while ($data = mysql_fetch_array($result));
echo <<< EOB
	<TR>
		<TD COLSPAN=9><HR></TD>
	</TR>
	</TABLE>
	</DIV>
	</BODY>
	</HTML>
EOB;
		exit;
	}
	showmessage(_FindDomainError);
}

#
# Cancellazione del dominio
#
if (isset($deldomain)) {
	$sql = "UPDATE domain SET state='D' WHERE id=$iddom;";
	mysql_query($sql,$conn) or die(_SQLQueryError);
	header("Location: $PHP_SELF?searchdomain=ok&domain=$domain");
	exit;
}

?>
