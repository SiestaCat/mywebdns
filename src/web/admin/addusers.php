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
if (isset($adduser)) {
	echo "\n";
	headerfile(_AddUser);
echo <<< EOB
	<TITLE>myWebDNS</TITLE>
        <DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=3>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Name</B></FONT></TD>
                <TD><INPUT TYPE=TEXT NAME=fullname MAXLENGHT=50 SIZE=25></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Login</B></FONT></TD>
                <TD><INPUT TYPE=TEXT NAME=login MAXLENGHT=25 SIZE=25></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Pwd</B></FONT></TD>
                <TD><INPUT TYPE=PASSWORD NAME=pwd MAXLENGHT=25 SIZE=25></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Pwd_Re</B></FONT></TD>
                <TD><INPUT TYPE=PASSWORD NAME=pwdreply MAXLENGHT=25 SIZE=25></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Group</B></FONT></TD>
                <TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>
EOB;
        echo "\n";
        $i = 0;
        $sql = "SELECT * FROM mysql_auth_group WHERE groups<>'administration' ORDER BY groups;";
        $result =  mysql_query($sql,$conn) or die(_SQLQueryError);
        if (($out = mysql_fetch_array($result)) != NULL) {
                echo "\t\t\t<SELECT NAME=group_name>\n";
                do {
                        extract($out);
                        if ($i==0) {
                                echo "\t\t\t\t<OPTION SELECTED VALUE='$GROUPS'>$GROUPS</OPTION>\n";
                                $i++;
                        } else
                                echo "\t\t\t\t<OPTION VALUE='$GROUPS'>$GROUPS</OPTION>\n";
                } while ($out = mysql_fetch_array($result));
                echo "\t\t\t</SELECT></TD>\n";
                echo "\t\t\t\t<INPUT TYPE=HIDDEN NAME=groupexist VALUE='SI'>\n";
        } else {
                echo "\t\t\t\t<WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>$Mod_NoGroupMsg</FONT>\n";
                echo "\t\t\t\t<INPUT TYPE=HIDDEN NAME=groupexist VALUE='NO'>\n";
        }
echo <<< EOB
        </TR>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        </TABLE>
        <INPUT TYPE=BUTTON VALUE='$Button_Add'   NAME=adduser OnClick='javascript: addusername()'>
        <INPUT TYPE=BUTTON VALUE='$Button_Close' NAME=close OnClick='javascript: self.close()'>
        </FORM>

        </BODY>
        </HTML>

EOB;
}
?>
