<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

if (!file_exists($filename)) {
	headerfile("");
	showerror(_FindFileError);
}

#
# Main Function
#
if (isset($filename)) {
	headerfile(_ViewFileHeader);
echo <<< EOB
	<TITLE>myWebDNS</TITLE>
	<BR>
        <DIV ALIGN=CENTER>
        <FORM METHOD=GET ACTION=$PHP_SELF>
        <TABLE WIDTH=100% BORDER=0 CELLSPACING=2 CELLPADDING=2>
        <TR>
                <TD COLSPAN=1><HR></TD>
        </TR>
		<TD BGCOLOR="#FFFFFF" TEXT="#000000" ALIGN=LEFT>
		<PRE>
EOB;
	echo "\n";
	$arrayfile = file($filename);
	for ($i=0; $i<count($arrayfile); $i++) {
		$arrayfile[$i] = rtrim($arrayfile[$i]);
		echo "$arrayfile[$i]<BR>";
	}

echo <<< EOB
		</PRE>
		</TD>
	</TR>
        <TR>
                <TD COLSPAN=3><HR></TD>
        </TR>
        </TABLE>
        </DIV>

        </BODY>
        </HTML>
EOB;
}
?>
