<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

#
# Main Function
#
if (isset($setpwd)) {
	headerfile(_ChangePwd);
	$sql = "SELECT * FROM mysql_auth WHERE username='$session_username';";
        $result =  mysql_query($sql,$conn) or die(_SQLQueryError);
	$out = mysql_fetch_array($result);
	extract($out);

echo <<< EOB
        <DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=3>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Name</B></FONT></TD>
                <TD BGCOLOR="#FFFFFF" WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>$FULLNAME</FONT></TD>
        </TR>
        <TR>
		<INPUT TYPE=HIDDEN NAME=login VALUE='$USERNAME'>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Login</B></FONT></TD>
                <TD BGCOLOR="#FFFFFF" WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>$USERNAME</FONT></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Pwd_New</B></FONT></TD>
                <TD><INPUT TYPE=PASSWORD NAME=new_pwd MAXLENGHT=25 SIZE=25></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Pwd_New_Re</B></FONT></TD>
                <TD><INPUT TYPE=PASSWORD NAME=new_pwdreply MAXLENGHT=25 SIZE=25></TD>
        </TR>
	<TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        </TABLE>
        <INPUT TYPE=BUTTON VALUE='$Button_Change' NAME=changeuser OnClick='javascript: changeuserpwd()'>
        </FORM>

        </BODY>
        </HTML>
EOB;
}

#
# Cambia la password
#
if (isset($changeuser)) {
echo <<< EOB
        <BODY BGCOLOR="#FFFFFF" onLoad=setTimeout('parent.top.document.location.href="/index.php"',2000)>
        <BR>
EOB;

	// Aggiorno la tabella degli utenti
        $sql = "UPDATE mysql_auth SET password=PASSWORD('$new_pwd') WHERE username='$login';";
	$result =  mysql_query($sql,$conn) or die(_SQLQueryError);
	showresult(_PwdChanged);
}

?>
