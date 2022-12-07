<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

headerfile(_ViewDomainHeader);
echo "\n\t<HR>\n";

if (isset($iddom)) {
	$sql = "SELECT * FROM domain WHERE id=$iddom;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	$line = mysql_fetch_array($result);
	extract($line);

echo <<< EOB
	<TABLE WIDTH=100% BORDER=0 COLSPAN=3>
	<TR>
		<TD CLASS=dominfo>$Mod_Dom</TD>
		<TD>$NAME</TD>
		<TD CLASS=dominfo>$Mod_Host_DNS</TD>
		<TD>$HOSTDNS</TD>
		<TD CLASS=dominfo>$Mod_TTL</TD>
		<TD>$TTL</TD>
	</TR>
	<TR>
		<TD CLASS=dominfo>$Mod_Serial</TD>
		<TD>$SERIAL</TD>
		<TD CLASS=dominfo>$Mod_Root_DNS</TD>
		<TD>$ROOTDNS</TD>
		<TD CLASS=dominfo>$Mod_Refresh</TD>
		<TD>$REFRESH</TD>
	</TR>
	<TR>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD CLASS=dominfo>$Mod_Retry</TD>
		<TD>$RETRY</TD>
	</TR>
	<TR>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD CLASS=dominfo>$Mod_Expire</TD>
		<TD>$EXPIRE</TD>
	</TR>
	<TR>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD CLASS=dominfo>$Mod_Minimum</TD>
		<TD>$MINIMUM</TD>
	</TR>
	</TABLE>
EOB;

	if ($ZONEMASTERTYPE == "M")
		$sql = "SELECT * FROM recordmaster WHERE iddom=$ID ORDER BY type DESC ,name ASC, priority ASC, hosttarget ASC;";
	else
		$sql = "SELECT *, LPAD(ip,7,' ') AS newip FROM recordreverse WHERE iddom=$ID ORDER BY newip;";
		
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);

	echo "\n\t<BR>\n";
	echo "\t<TABLE WIDTH=100% ALIGN=LEFT BORDER=0 CELLSPACING=0 CELLPADDING=2>\n";
	$counter = 1;
	while ($out = mysql_fetch_array($result)) {
		echo "\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";

		if ($ZONEMASTERTYPE == "M")
			echo "\t\t<TD ALIGN=LEFT WIDTH=20%>$out[NAME]&nbsp;</TD>\n";
		else
			echo "\t\t<TD ALIGN=LEFT WIDTH=20%>$out[IP]&nbsp;</TD>\n";

		echo "\t\t<TD ALIGN=CENTER WIDTH=5%>$out[TTL]</TD>\n";
		echo "\t\t<TD ALIGN=CENTER WIDTH=5%>IN</TD>\n";
		echo "\t\t<TD ALIGN=CENTER WIDTH=10%>$out[TYPE]</TD>\n";
		switch ($out[TYPE]) {
			case "MX":
				echo "\t\t<TD ALIGN=LEFT WIDTH=25%>$out[PRIORITY] $out[HOSTTARGET]</TD>\n";
				break;
			case "A":
				$ip=iplong2ipdot($out[IP]);
				echo "\t\t<TD ALIGN=LEFT WIDTH=25%>$ip</TD>\n";
				break;
			case "NS":
			case "CNAME":
			case "PTR":
				echo "\t\t<TD ALIGN=LEFT WIDTH=25%>$out[HOSTTARGET]</TD>\n";
		}
		echo "\t</TR>\n";
	}
echo <<< EOB
	<TR>
		<TD COLSPAN=5><HR></TD>
	</TR>
	</TABLE>
	</BODY>
	</HTML>
EOB;
	mysql_close($conn);
	exit;
}

showerror(_SelectDomainError);
?>
</BODY>
</HTML>
