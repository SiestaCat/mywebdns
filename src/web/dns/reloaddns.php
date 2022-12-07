<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
	headerfile("");
        showerror(_DNSError);
}


if (isset($reloaddns)) {
        $stroutput = _ReloadDNSHeader;
echo <<< EOB
	<BODY BGCOLOR="#FFFFFF" onLoad=setTimeout('history.back();',3000)>
        <BR>
        <DIV ALIGN=CENTER>
        <H3>$stroutput</H3>
        </DIV>
EOB;

	// Inserire le istruzioni per il reload 
	$sql = "SELECT * FROM dns WHERE id=$iddns;";
	$result = mysqli_query($conn,$sql) or die(_SQLQueryError);
	$out = mysqli_fetch_array($result);
	extract($out);
	exec("$RNDCRELOAD&",$array);
	showresult(_ReloadMsg);
}
	
headerfile(_ReloadDNSHeader);

$i=0;
$sql = "SELECT * FROM dns ORDER BY dnsfqdn;";
$result = mysqli_query($conn,$sql) or die(_SQLQueryError);
$out = mysqli_fetch_array($result);

echo <<< EOB
	<DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
	<TABLE WIDTH=20% BORDER=0 CELLSPACING=3 CELLPADDING=3>
	<TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
	<TR>
		<TD WIDTH=60% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_DNS</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT   VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>
			<SELECT name=iddns>
EOB;

echo "\n";
do {
	extract($out);
	if ($i==0) {
		echo "\t\t\t\t<OPTION SELECTED VALUE='$ID'>$DNSFQDN</OPTION>\n";
		$i++;
	} else
		echo "\t\t\t\t<OPTION VALUE='$ID'>$DNSFQDN</OPTION>\n";
} while ($out = mysqli_fetch_array($result));
echo <<< EOB
			</SELECT>
		</TD>
		<TD> <INPUT TYPE=SUBMIT VALUE='Reload' NAME=reloaddns></TD>
	</TR>
	<TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
	</TABLE>
	<BR>
	</FORM>
	</DIV>
	</BODY>
	</HTML>
EOB;
mysqli_close($conn);
?>
