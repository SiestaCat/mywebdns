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
if (isset($adminusers)) {
EOB;
	echo "\n";
	headerfile(_AdminUsers);
	$sql = "SELECT * FROM mysql_auth;";
	$result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
	$out = mysqli_fetch_array($result);

echo <<< EOB
        <DIV ALIGN=CENTER>
        <FORM METHOD=GET ACTION=$PHP_SELF>
        <TABLE WIDTH=20% BORDER=0 CELLSPACING=0 CELLPADDING=7>
        <TR>
                <TD COLSPAN=5><HR></TD>
        </TR>
        <TR>
                <TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_Login</B></STRONG></TD>
                <TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_Name</B></STRONG></TD>
                <TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>$Mod_Group</B></STRONG></TD>
                <TD>&nbsp;</TD>
                <TD>&nbsp;</TD>
                <TD>&nbsp;</TD>
        </TR>
EOB;

        echo "\n";
        $counter = 1;
        do {
        	extract($out);
                echo "\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
                echo "\t\t<TD WIDTH=35% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO><B>$USERNAME</B></TD>\n";
                echo "\t\t<TD WIDTH=35% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$FULLNAME</TD>\n";
                echo "\t\t<TD WIDTH=35% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$GROUPS</TD>\n";
		echo "\t\t<TD WIDTH=5%><A HREF='$PHP_SELF?edituser=ok&login=$USERNAME'><IMG SRC=/icons/edit.gif BORDER=0 ALT='$Alt_Change'></TD>\n";
		if ($GROUPS != "administration") {
			echo "\t\t<TD WIDTH=5%><A HREF='$PHP_SELF?deluser=ok&login=$USERNAME'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>\n";
		} else {
			echo "\t\t<TD>&nbsp;</TD>\n";
			echo "\t\t<TD>&nbsp;</TD>\n";
		}
	} while ($out = mysqli_fetch_array($result));

echo <<< EOB
        <TR>
                <TD COLSPAN=5><HR></TD>
        </TR>
        </TABLE>
	<INPUT TYPE=BUTTON VALUE='$Button_Add' NAME=adduser OnClick='javascript: open_window("addusers.php?adduser=ok","AggiungiUtente",380,350)'>
        </DIV>
EOB;
}

#
# Aggiunge un utente
#
if (isset($adduser)) {
	// Verifico che l'utente non sia gi� inserito
	$login = strtolower($login);
        $sql = "SELECT * FROM mysql_auth WHERE username='$login';";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
	if (mysqli_fetch_array($result) != NULL)
		header("Location: $PHP_SELF?adminusers=ok");
	else {
		$sql = "INSERT INTO mysql_auth VALUES('$login',password('$pwd'),'$group_name','$fullname');";
		$result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
		header("Location: $PHP_SELF?adminusers=ok");
	}
}

#
# Cancella un utente
#
if (isset($deluser)) {
	// Cancellazione utente
        $sql = "DELETE FROM mysql_auth WHERE username='$login';";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
	header("Location: $PHP_SELF?adminusers=ok");
}

#
# Modifica i dati di un utente
#
if (isset($edituser)) {
	headerfile(_AdminUsers);
	$sql = "SELECT * FROM mysql_auth WHERE username='$login';";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
	$out = mysqli_fetch_array($result);
	extract($out);

echo <<< EOB
        <DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=3>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Name</B></FONT></TD>
                <TD><INPUT TYPE=TEXT NAME=new_fullname VALUE='$FULLNAME' MAXLENGHT=50 SIZE=25></TD>
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
EOB;
	if ($GROUPS != "administration") {
		echo "\t<TR>\n";
                echo "\t\t<TD BGCOLOR=#DDDDDD WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Group</B></FONT></TD>\n";
                echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>\n";
        	echo "\n";
		$current_groupname = $GROUPS;
	        $sql = "SELECT * FROM mysql_auth_group WHERE groups<>'administration' ORDER BY groups;";
       		$result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
	        if (($out = mysqli_fetch_array($result)) != NULL) {
       	        	echo "\t\t\t<SELECT NAME=group_name>\n";
                	do {
                        	extract($out); 
                        	if ($current_groupname == $GROUPS) {
                                	echo "\t\t\t\t<OPTION SELECTED VALUE='$GROUPS'>$GROUPS</OPTION>\n";
                        	} else
                                	echo "\t\t\t\t<OPTION VALUE='$GROUPS'>$GROUPS</OPTION>\n";
                	} while ($out = mysqli_fetch_array($result));
                	echo "\t\t\t</SELECT></TD>\n";
                	echo "\t\t\t\t<INPUT TYPE=HIDDEN NAME=groupexist VALUE='SI'>\n";
        	} else {
                	echo "\t\t\t\t<WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>$Mod_NoGroupMsg</FONT>\n";
                	echo "\t\t\t\t<INPUT TYPE=HIDDEN NAME=groupexist VALUE='NO'>\n";
        	}
		echo "\t</TR>\n";
	}
echo <<< EOB
	<TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        </TABLE>
        <INPUT TYPE=BUTTON VALUE='$Button_Change' NAME=changeuser OnClick='javascript: changeuserdata()'>
        </FORM>

        </BODY>
        </HTML>
EOB;
}

if (isset($changeuser)) {
	// Aggiorno la tabella degli utenti
        $sql = "UPDATE mysql_auth SET fullname='$new_fullname', password=PASSWORD('$new_pwd'), groups='$group_name' WHERE username='$login';";
	$result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
        header("Location: $PHP_SELF?adminusers=ok");
}

?>
