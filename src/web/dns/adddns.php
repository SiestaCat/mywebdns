<?php
require("../include.php");
$conn = connect_db();

if ($session_groups != "administration") {
	headerfile("");
	showerror(_AccessDenied);
}

headerfile(_AddDNSHeader);
if (isset($adddns)) {
	$dnsfqdn = trim($dnsfqdn);
	$ip = trim($ip);

	// Check sui dati inseriti
	if ( ($dnsfqdn == "") || ($ip == "") || ($dirnamed == "") || ($dirzones == "") || ($dnsreload == ""))
		showerror(_CheckInputError);
	
	// Check sull'IP
	if (!checkip($ip))
                showerror(_CheckIPError);

	// Verifica della presenza del DNS inserito nella tabella dns
	$sql = "SELECT * FROM dns WHERE dnsfqdn='$dnsfqdn';";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
        if (mysql_fetch_row($result) == NULL) {
		$dnsip=ipdot2iplong($ip);
		$data=date("Y-m-j");
		$sql = "INSERT INTO dns VALUES (NULL,'$dnsfqdn','$dnsdescr',$dnsip,'$data','$dirnamed','$includezonenamed','$dirzones','$dnsreload');";
		mysql_query($sql,$conn) or die(_SQLQueryError);
		showresult(_AddMsg);
	} else showerror(_DNSRegError);
}

echo <<< EOB

	<DIV ALIGN=CENTER>
	<FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=4>

	<TR>
		<TD COLSPAN=2><HR></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_DNS_FQDN</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dnsfqdn></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Descr</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dnsdescr></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_IP</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=15 SIZE=20 NAME=ip></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Dir_Named</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dirnamed></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Inc_Named</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=includezonenamed></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Dir_Zones</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dirzones></TD>
	</TR>
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT COLOR=red FACE=Lucida SIZE=3><B>$Mod_Cmd_Reload</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=60 SIZE=60 NAME=dnsreload></TD>
	</TR>
	<TR>
		<TD COLSPAN=2><HR></TD>
	</TR>
	</TABLE>
	<BR>
	<INPUT TYPE=BUTTON VALUE='$Button_Add' NAME=adddns onClick='javascript:CheckConfigDNS()'>
	</FORM>
	</DIV>
	</BODY>
	</HTML>
EOB;

mysql_close($conn);
?>
