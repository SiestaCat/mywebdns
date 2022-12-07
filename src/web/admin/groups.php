<?php
require("../include.php");
$conn=connect_db();

if ($_SESSION['session_groups'] != "administration") {
        headerfile("");
        showerror(_AccessDenied);
}

#
# Main function
#
if (isset($admingroups)) {
	echo "\n";
	headerfile(_AdminGroups);
	$sql = "SELECT * FROM mysql_auth_group;";
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
                <TD ALIGN=CENTER><FONT COLOR=darkblue><STRONG><B>#</B></STRONG></TD>
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
                echo "\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";

		$sql = "SELECT * FROM mysql_auth WHERE groups='$GROUPS';";
       		$result_group =  mysqli_query($conn,$sql) or die(_SQLQueryError);
        	echo "\t\t<TD WIDTH=35% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO><I>".mysql_num_rows($result_group)."</I></TD>\n";

                echo "\t\t<TD WIDTH=35% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$GROUPS</TD>\n";
                echo "\t\t<TD WIDTH=5%><A HREF='javascript:open_window(\"viewusergroup.php?groupname=$GROUPS\",\"VisualizzaUtenti\",400,300)'><IMG SRC=/icons/search.gif BORDER=0 ALT='$Alt_View_Users'></TD>\n";
		if ($GROUPS != "administration") {
                	echo "\t\t<TD WIDTH=5%><A HREF='$PHP_SELF?editgroup=ok&old_group_name=$GROUPS'><IMG SRC=/icons/edit.gif BORDER=0 ALT='Modifica'></TD>\n";
			echo "\t\t<TD WIDTH=5%><A HREF='javascript:DelGroup(\"$GROUPS\")'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>\n";
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
	<INPUT TYPE=BUTTON VALUE='$Button_Add' NAME=addgroup OnClick='javascript: open_window("addgroups.php?addgroup=ok","AggiungiGruppo",200,270)'>
        </DIV>
EOB;
}

#
# Aggiunge un gruppo
#
if (isset($addgroup)) {
	// Verifico che il gruppo inserito non esista
	$group_name = strtolower($group_name);
        $sql = "SELECT * FROM mysql_auth_group WHERE groups='$group_name';";
        $result =  mysqli_query($conn, $sql) or die(_SQLQueryError);
	if (mysqli_fetch_array($result) != NULL)
		header("Location: $PHP_SELF?admingroups=ok");
	else {
		$sql = "INSERT INTO mysql_auth_group VALUES(NULL,'$group_name');";
		$result =  mysqli_query($conn, $sql) or die(_SQLQueryError);
		header("Location: $PHP_SELF?admingroups=ok");
	}
}

#
# Cancella un gruppo
#
if (isset($delgroup)) {
	// Cancellazione gruppo
        $sql = "DELETE FROM mysql_auth_group WHERE groups='$group_name';";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);

	// Cancellazione degli utenti del gruppo appena rimosso
	$sql = "DELETE FROM mysql_auth WHERE groups='$group_name';";
	$result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
	header("Location: $PHP_SELF?admingroups=ok");
}

#
# Modifica i dati di un gruppo
#
if (isset($editgroup)) {
	echo "\n";
	headerfile(_AdminGroups);

echo <<< EOB
        <DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=3>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Old_Group</B></FONT></TD>
                <TD WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>$old_group_name</FONT></TD>
        </TR>
        <TR>
                <TD BGCOLOR="#DDDDDD" WIDTH=30% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_New_Group</B></FONT></TD>
                <TD><INPUT TYPE=TEXT NAME=new_group_name MAXLENGHT=25 SIZE=25></TD>
        </TR>
        <TR>
                <TD COLSPAN=2><HR></TD>
        </TR>
        </TABLE>
        <INPUT TYPE=BUTTON VALUE='Modifica' NAME=changenamegroup OnClick='javascript: changegroupname("$old_group_name", new_group_name)'>
        </FORM>

        </BODY>
        </HTML>
EOB;
}

if (isset($changegroup)) {
        // Verifico che il gruppo inserito esista
        $new_group_name = strtolower($new_group_name);
        $sql = "SELECT * FROM mysql_auth_group WHERE groups='$new_group_name';";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
        if (mysqli_fetch_array($result) != NULL)
                header("Location: $PHP_SELF?admingroups=ok");
        else {
		// Aggiorno la tabella dei gruppi
                $sql = "UPDATE mysql_auth_group SET groups='$new_group_name' WHERE groups='$old_group_name'";
                $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);

		// Aggiorno la tabella degli utenti
                $sql = "UPDATE mysql_auth SET groups='$new_group_name' WHERE groups='$old_group_name'";
                $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
                header("Location: $PHP_SELF?admingroups=ok");
        }
}

?>
