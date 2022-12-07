<?php
require("../include.php");
$conn=connect_db();

if ($_SESSION['session_groups'] != "administration") {
        headerfile("");
        showerror(_AccessDenied);
}

#
# Main Function
#
if (isset($addgroup)) {
	echo "\n";
	headerfile(_AddGroup);

echo <<< EOB
	<TITLE>myWebDNS</TITLE>
        <DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH="30%" BORDER=0 CELLSPACING="3" CELLPADDING="3">
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH="30%" NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Group</B></FONT></TD>
                <TD><INPUT TYPE=TEXT NAME=group_name MAXLENGHT=25 SIZE=25></TD>
        </TR>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        </TABLE>
        <INPUT TYPE=BUTTON VALUE='$Button_Add' NAME=addgroup OnClick='javascript: addgroupname(group_name);'>
	<INPUT TYPE=BUTTON VALUE='$Button_Close' NAME=close OnClick='javascript: self.close()'>
        </FORM>

        </BODY>
        </HTML>
EOB;
}
?>
