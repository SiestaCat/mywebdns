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
if (isset($iddom)) {
	headerfile(_ACLDomainHeader);
	echo "\n";
echo <<< EOB
        <DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
        <TABLE WIDTH=50% BORDER=0 CELLSPACING=3 CELLPADDING=3>
        <TR>
		<TD COLSPAN=2><HR></TD>
	</TR>
        <TR>
	        <TD BGCOLOR="#DDDDDD" WIDTH=40% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_IP_Net</B></FONT></TD>
	        <TD><WIDTH=60% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>
			<INPUT TYPE=TEXT NAME=ip MAXLENGHT=16 SIZE=20>/<INPUT TYPE=TEXT NAME=netmask MAXLENGHT=2 SIZE=3>
        	</TD>
        </TR>
        <TR>
        	<TD BGCOLOR="#DDDDDD" WIDTH=40% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Type</B></FONT></TD>
	        <TD WIDTH=60%>
			<SELECT NAME=acltype>
				<OPTION SELECTED VALUE='NOT'>$Mod_ACLNot
				<OPTION VALUE='QRY'>$Mod_ACLQry
				<OPTION VALUE='TRX'>$Mod_ACLTrx
				<OPTION VALUE='UPD'>$Mod_ACLUpd
			</SELECT>
		</TD>
        </TR>
        <TR>
		<TD COLSPAN=2><HR></TD>
	</TR>
        </TABLE>
        <BR>
	<INPUT TYPE=HIDDEN VALUE='$iddom'   NAME=iddomain>
        <INPUT TYPE=BUTTON VALUE='$Button_Add' NAME=continueaddacl onClick='javascript:addACL()'>
        </FORM>
        </DIV>

	<DIV ALIGN=CENTER>
        <TABLE WIDTH=100% BORDER=0 CELLSPACING=3 CELLPADDING=4>
        <TR>
		<TD COLSPAN=4></TD>
	</TR>
	<TR>
		<TD WIDTH=25% ALIGN=CENTER> <FONT COLOR=darkblue><STRONG><B>$Mod_ACLNot</B></STRONG></TD>
		<TD WIDTH=25% ALIGN=CENTER> <FONT COLOR=darkblue><STRONG><B>$Mod_ACLQry</B></STRONG></TD>
		<TD WIDTH=25% ALIGN=CENTER> <FONT COLOR=darkblue><STRONG><B>$Mod_ACLTrx</B></STRONG></TD>
		<TD WIDTH=25% ALIGN=CENTER> <FONT COLOR=darkblue><STRONG><B>$Mod_ACLUpd</B></STRONG></TD>
	</TR>
	<TR>
	        <TD WIDTH=25% VALIGN=TOP>
			<DIV ALIGN=CENTER>
		        <TABLE WIDTH=100% VALIGN=UP BORDER=0 CELLSPACING=3 CELLPADDING=4>
        		<TR>
				<TD COLSPAN=2><HR></TD>
			</TR>
EOB;
	//Tabella Allow-Notify
	echo "\n";
	$sql = "SELECT * FROM acldomain WHERE iddom=$iddom AND type='NOT' ORDER BY ip;";
	$result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
	if (($out = mysqli_fetch_array($result)) != NULL) {
		$counter = 1;
                do {
                        extract($out);
                        echo "\t\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
                        $ip = iplong2ipdot($IP);
echo <<< EOB
				<TD WIDTH=90% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$ip/$NETMASK</TD>
        	                <TD WIDTH=10%><A HREF='$PHP_SELF?delacl=ok&idacl=$ID&iddomain=$iddom'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>
			</TR>
EOB;
			echo "\n";
                } while ($out = mysqli_fetch_array($result));
	} else {

echo <<< EOB
			<TR>
				<TD WIDTH=40% WRAP ALIGN=CENTER VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_NoACL</TD>
			</TR>
EOB;
	}
	echo "\n";

echo <<< EOB
                        <TR>
                                <TD COLSPAN=2><HR></TD>
                        </TR>	
			</TABLE>
			</DIV>
		</TD>
                <TD WIDTH=25% VALIGN=TOP>
                        <DIV ALIGN=CENTER>
                        <TABLE WIDTH=100% VALIGN=UP BORDER=0 CELLSPACING=3 CELLPADDING=4>
                        <TR>
                                <TD COLSPAN=2><HR></TD>
EOB;
	echo "\n";
        //Tabella Allow-Query
        $sql = "SELECT * FROM acldomain WHERE iddom=$iddom AND type='QRY' ORDER BY ip;";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
        if (($out = mysqli_fetch_array($result)) != NULL) {
		$counter = 1;
		do {
			extract($out);
                        echo "\t\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
                        $ip = iplong2ipdot($IP);
echo <<< EOB
                                <TD WIDTH=90% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$ip/$NETMASK</TD>
                                <TD WIDTH=10%><A HREF='$PHP_SELF?delacl=ok&idacl=$ID&iddomain=$iddom'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>
                        </TR>
EOB;
                        echo "\n";
		} while ($out = mysqli_fetch_array($result));
        } else {
echo <<< EOB
                        <TR>
                                <TD WIDTH=40% WRAP ALIGN=CENTER VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_NoACL</TD>
                        </TR>
EOB;
        }
        echo "\n";

echo <<< EOB
                        <TR>
                                <TD COLSPAN=2><HR></TD>
                        </TR>
                        </TABLE>
                        </DIV>
                </TD>
                <TD WIDTH=25% VALIGN=TOP>
                        <DIV ALIGN=CENTER>
                        <TABLE WIDTH=100% VALIGN=UP BORDER=0 CELLSPACING=3 CELLPADDING=4>
                        <TR>
                                <TD COLSPAN=2><HR></TD>
EOB;
        echo "\n";
        //Tabella Allow-Transfer
        $sql = "SELECT * FROM acldomain WHERE iddom=$iddom AND type='TRX' ORDER BY ip;";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
        if (($out = mysqli_fetch_array($result)) != NULL) {
		$counter = 1;
                do {
                        extract($out);
                        echo "\t\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
                        $ip = iplong2ipdot($IP);
echo <<< EOB
                                <TD WIDTH=90% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$ip/$NETMASK</TD>
                                <TD WIDTH=10%><A HREF='$PHP_SELF?delacl=ok&idacl=$ID&iddomain=$iddom'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>
                        </TR>
EOB;
                        echo "\n";
                } while ($out = mysqli_fetch_array($result));
        } else {
echo <<< EOB
                        <TR>
                                <TD WIDTH=40% WRAP ALIGN=CENTER VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_NoACL</TD>
                        </TR>
EOB;
        }

echo <<< EOB
                        <TR>
                                <TD COLSPAN=2><HR></TD>
                        </TR>
                        </TABLE>
                        </DIV>
                </TD>
                <TD WIDTH=25% VALIGN=TOP>
                        <DIV ALIGN=CENTER>
                        <TABLE WIDTH=100% VALIGN=UP BORDER=0 CELLSPACING=3 CELLPADDING=4>
                        <TR>
                                <TD COLSPAN=2><HR></TD>
EOB;
        echo "\n";
        //Tabella Allow-Update
        $sql = "SELECT * FROM acldomain WHERE iddom=$iddom AND type='UPD' ORDER BY ip;";
        $result =  mysqli_query($conn,$sql) or die(_SQLQueryError);
        if (($out = mysqli_fetch_array($result)) != NULL) {
		$counter = 1;
                do {
                        extract($out);
                       echo "\t\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";
                        $ip = iplong2ipdot($IP);
echo <<< EOB
                                <TD WIDTH=90% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$ip/$NETMASK</TD>
                                <TD WIDTH=10%><A HREF='$PHP_SELF?delacl=ok&idacl=$ID&iddomain=$iddom'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Alt_Delete'></TD>
                        </TR>
EOB;
                        echo "\n";
                } while ($out = mysqli_fetch_array($result));
        } else {
echo <<< EOB
                        <TR>
                                <TD WIDTH=40% WRAP ALIGN=CENTER VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_NoACL</TD>
                        </TR>
EOB;
        }
echo <<< EOB
                        <TR>
                                <TD COLSPAN=2><HR></TD>
                        </TR>
                        </TABLE>
                        </DIV>
                </TD>
	</TR>
        <TR>
		<TD COLSPAN=4></TD>
	</TR>
        </TABLE>
        </DIV>
EOB;
	mysqli_close($conn);
        exit;
}

if (isset($continueaddacl)) {
        $ip = trim($ip);

        // Check sui dati inseriti
        if (($netmask == "") || ($ip == "")) {
        	header("Location: $PHP_SELF?iddom=$iddomain");
		exit;
	}

        // Check sull'IP
        if (!checkip($ip)) {
        	header("Location: $PHP_SELF?iddom=$iddomain");
		exit;
	}

        // Check sulla netmask
        if (($netmask<1) || ($netmask>32)) {
                header("Location: $PHP_SELF?iddom=$iddomain");
                exit;
        }

	// Inserimento della ACL (se non esiste)
	$address = ipdot2iplong($ip);
	$sql = "SELECT * FROM acldomain WHERE iddom=$iddomain AND ip=$address AND netmask=$netmask AND type='$acltype';";
	$result = mysqli_query($conn,$sql) or die(_SQLQueryError);
        if (mysqli_fetch_array($result) == NULL) {
		$sql = "UPDATE domain SET state='M' WHERE id=$iddomain AND state<>'A';";
		mysqli_query($conn,$sql) or die(_SQLQueryError);
		$sql = "INSERT INTO acldomain VALUES (NULL,$iddomain,$address,$netmask,'$acltype');";
		mysqli_query($conn,$sql) or die(_SQLQueryError);
	}
        header("Location: $PHP_SELF?iddom=$iddomain");
	exit;
}

if (isset($delacl)) {
	$sql = "UPDATE domain SET state='M' WHERE id=$iddomain AND state<>'A';";
	mysqli_query($conn,$sql) or die(_SQLQueryError);
	$sql = "DELETE FROM acldomain WHERE id=$idacl;";
	mysqli_query($conn,$sql) or die(_SQLQueryError);
        header("Location: $PHP_SELF?iddom=$iddomain");
	exit;

}

headerfile(_ACLDomainHeader);
showerror(_SelectDomainError);
?>
</BODY>
</HTML>

