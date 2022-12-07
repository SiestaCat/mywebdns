<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
	headerfile("");
        showerror(_DNSError);
}

#
# nslookup DNS
#
if (isset($nslookupdns)) {
	$tmpfname = tempnam("/tmp", "nslookup");
	headerfile(_nslookupDNSHeader);
	$sql = "SELECT dnsfqdn FROM dns WHERE id=$iddns;";
        $result = mysqli_query($conn,$sql) or die(_SQLQueryError);
        $dati = mysqli_fetch_array($result);
	exec ("nslookup -querytype=any $dati[dnsfqdn] > $tmpfname");
	$result = file($tmpfname);

echo <<< EOB
        <DIV ALIGN=CENTER>
        <TABLE WIDTH=100% BORDER=0 CELLSPACING=2 CELLPADDING=2>
	<TR>
		<TD COLSPAN=1><HR></TD>
	</TR>
EOB;
	echo "\n";
	for ($i=0; $i<count($result)-1; $i++) {
		echo "\t<TR>\n";
		if ($result[$i] != "\n")
                        echo "\t\t<TD><WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Courier>$result[$i]</FONT></TD>\n";
                else
                        echo "\t\t<TD><BR></TD>\n";
		echo "\t</TR>\n";
	}
echo <<< EOB
	<TR>
		<TD COLSPAN=1><HR></TD>
	</TR>
	</TABLE>
	</DIV>
EOB;
	unlink($tmpfname);
	exit;
}

#
# Informazioni DNS
#
if (isset($infodns)) {
	headerfile(_InfoDNSHeader);
        $sql = "SELECT * FROM dns WHERE id=$iddns;";
        $result = mysqli_query($conn,$sql) or die(_SQLQueryError);
        $dati = mysqli_fetch_array($result);
	extract($dati);
        $ip = iplong2ipdot($DNSIP);

echo <<< EOB
        <DIV ALIGN=CENTER>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=4>
        <TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_DNS_FQDN</B></FONT></TD>
        	<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$DNSFQDN</TD>
		<TD>&nbsp;</TD>
        </TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Descr</B></FONT></TD>
        	<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$DNSDESCR</TD>
		<TD>&nbsp;</TD>
        </TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_IP</B></FONT></TD>
	        <TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$ip</TD>
		<TD>&nbsp;</TD>
        </TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Create_Date</B></FONT></TD>
	        <TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$DATA</TD>
		<TD>&nbsp;</TD>
        </TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Dir_Named</B></FONT></TD>
	        <TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$DIRNAMED</TD>
		<TD><A HREF="viewfile.php?filename=$DIRNAMED"><IMG SRC=/icons/search.gif BORDER=0 ALT='$Alt_View'></TD>
		
        </TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Inc_Named</B></FONT></TD>
	        <TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$INCLUDEZONENAMED</TD>
		<TD><A HREF="viewfile.php?filename=$INCLUDEZONENAMED"><IMG SRC=/icons/search.gif BORDER=0 ALT='$Alt_View'></TD>
        </TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Dir_Zones</B></FONT></TD>
	        <TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$DIRZONES</TD>
		<TD>&nbsp;</TD>
        </TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Cmd_Reload</B></FONT></TD>
	        <TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$RNDCRELOAD</TD>
		<TD>&nbsp;</TD>
        </TR>
	<TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
        </TABLE>
        </DIV>
EOB;

	echo "\n";
        $sql = "SELECT * FROM domain WHERE iddns=$iddns;";
        $result = mysqli_query($conn,$sql) or die(_SQLQueryError);
        $numdom = mysql_num_rows($result);
        echo "\t<BR>\n";
        echo "\t<CENTER><CLASS=LINKNERO>$Mod_DomainHandled<B>$numdom</B></CENTER>\n";
	echo "\t</BODY>\n";
	echo "\t</HTML>\n";
        exit;
}

#
# Modifica DNS
#
if (isset($moddns)) {
	if ($_SESSION['session_groups'] != "administration") {
        	headerfile("");
        	showerror(_AccessDenied);
	}
	headerfile(_ModifyDNSHeader);
        $sql = "SELECT * FROM dns WHERE id=$iddns;";
        $result = mysqli_query($conn,$sql) or die(_SQLQueryError);
        $dati = mysqli_fetch_array($result);
	extract($dati);
        $ip = iplong2ipdot($DNSIP);

echo <<< EOB

        <DIV ALIGN=CENTER>
        <FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=2 CELLPADDING=2>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_DNS_FQDN</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dnsfqdn VALUE='$DNSFQDN'></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Descr</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dnsdescr VALUE='$DNSDESCR'></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_IP</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=15 SIZE=20 NAME=ip VALUE='$ip'></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Dir_Named</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dirnamed VALUE='$DIRNAMED'></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Inc_Named</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=includezonenamed VALUE='$INCLUDEZONENAMED'></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Dir_Zones</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dirzones VALUE='$DIRZONES'></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Cmd_Reload</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dnsreload VALUE='$RNDCRELOAD'></TD>
        </TR>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        </TABLE>
        <BR>
        <INPUT TYPE=HIDDEN VALUE='$iddns' NAME=iddns>
        <INPUT TYPE=BUTTON VALUE='$Button_OK' NAME=continuemoddns onClick='javascript:ChangeConfigDNS()'>
        </FORM>
        </DIV>
        </BODY>
        </HTML>
EOB;
        exit;
	mysqli_close($conn);
}

if (isset($continuemoddns)) {
	if ($_SESSION['session_groups'] != "administration") {
        	headerfile("");
        	showerror(_AccessDenied);
	}

	headerfile(_ModifyDNSHeader);

        // Modifica del DNS selezionato
        $ip = trim($ip);
        $ipdns = ipdot2iplong($ip);
        $sql = "UPDATE dns SET dnsfqdn='$dnsfqdn', dnsdescr='$dnsdescr', dnsip=$ipdns, dirnamed='$dirnamed', includezonenamed='$includezonenamed', dirzones='$dirzones', rndcreload='$dnsreload' WHERE id=$iddns;";
        mysqli_query($conn,$sql) or die(_SQLQueryError);
        showresult(_ModifiedOkMsg);
}

#
# Cancella DNS
#
if (isset($deletedns)) {
	if ($_SESSION['session_groups'] != "administration") {
        	headerfile("");
        	showerror(_AccessDenied);
	}

	headerfile(_DeleteDNSHeader);

        // Cancellazione del record di dns
        $sql = "DELETE FROM dns WHERE id=$iddns;";
        mysqli_query($conn,$sql) or die(_SQLQueryError);

	// Cancellazione dei domini del dns cancellato
	$sql = "UPDATE domain SET state='D' WHERE iddns=$iddns;";
        mysqli_query($conn,$sql) or die(_SQLQueryError);
       	showresult(_DeleteMsg);
}

#
# Main
#
headerfile(_ConfigDNSHeader);
$sql = "SELECT * FROM dns;";
$result = mysqli_query($conn,$sql) or die(_SQLQueryError);
$dati = mysqli_fetch_array($result);

echo <<< EOB
	<DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
	<TABLE WIDTH=20% BORDER=0 CELLSPACING=3 CELLPADDING=3>
	<TR>
		<TD COLSPAN=6><HR></TD>
	</TR>
EOB;
$counter = 1;
do {
	echo "\n";
	extract($dati);
	echo "\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
echo <<< EOB
		<TD><A HREF=$PHP_SELF?nslookupdns=OK&iddns=$ID><IMG SRC=/icons/view.gif	BORDER=0 ALT='$Alt_Nslookup'></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO><B>$DNSFQDN</B></TD>
		<TD><A HREF=$PHP_SELF?infodns=OK&iddns=$ID><IMG SRC=/icons/info.gif BORDER=0 ALT='$Alt_Info'></TD>
EOB;
	if ($_SESSION['session_groups'] == "administration") {
		echo "\t\t<TD><A HREF=$PHP_SELF?moddns=OK&iddns=$ID><IMG SRC=/icons/edit.gif BORDER=0 ALT='$Alt_Change'></TD>\n";
		echo "\t\t<TD><A HREF='javascript:DelDNS(\"$DNSFQDN\",$ID)'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>\n";
	}
echo <<< EOB
	</TR>
EOB;
} while ($dati = mysqli_fetch_array($result));
echo "\n";
echo <<< EOB
	<TR>
		<TD COLSPAN=6><HR></TD>
	</TR>
	</TABLE>
	</FORM>
	</DIV>
	</BODY>
	</HTML>
EOB;
	
mysqli_close($conn);
?>
