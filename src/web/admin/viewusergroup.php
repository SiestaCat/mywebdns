<?
require("../include.php");
$conn=connect_db();

if ($session_groups != "administration") {
        headerfile("");
        showerror(_AccessDenied);
}

#
# Main Function
#
if (isset($groupname)) {
	headerfile(_UserGroup);
	$sql = "SELECT * FROM mysql_auth WHERE groups='$groupname';";
	$result =  mysql_query($sql,$conn) or die(_SQLQueryError);
	if (($out = mysql_fetch_array($result)) != NULL) {

echo <<< EOB
	<TITLE>myWebDNS</TITLE>
	<CENTER><FONT COLOR=darkblue><STRONG><B>$groupname</B></STRONG></FONT></CENTER>
	<BR>
        <DIV ALIGN=CENTER>
        <FORM METHOD=GET ACTION=$PHP_SELF>
        <TABLE WIDTH=20% BORDER=0 CELLSPACING=0 CELLPADDING=7>
        <TR>
                <TD COLSPAN=3><HR></TD>
        </TR>
        <TR>
		<TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>#</B></STRONG></FONT></TD>
		<TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_Login</B></STRONG></FONT></TD>
		<TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_Name</B></STRONG></FONT></TD>
        </TR>

EOB;

        echo "\n";
        $counter = 1;
	$index = 1;
        do {
        	extract($out);
                echo "\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
                echo "\t\t<TD WIDTH=5%  NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO><I>$index</I></TD>\n";
                echo "\t\t<TD WIDTH=45% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$USERNAME</TD>\n";
                echo "\t\t<TD WIDTH=55% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$FULLNAME</TD>\n";
		$index++;
	} while ($out = mysql_fetch_array($result));

echo <<< EOB
        <TR>
                <TD COLSPAN=3><HR></TD>
        </TR>
        </TABLE>
        <INPUT TYPE=BUTTON VALUE='$Button_Close' NAME=close OnClick='javascript: self.close()'>
        </DIV>

        </BODY>
        </HTML>
EOB;
	} else
		showmessage(_NoUserMsg);

}
?>
