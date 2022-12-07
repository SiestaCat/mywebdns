<?php
require("../include.php");
$conn=connect_db();

// Check sui DNS configurati
if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

#
# Informazioni sul dominio selezionato
#
if (isset($iddom)) {
	headerfile(_InfoDomainHeader);
        $sql = "SELECT * FROM domain WHERE id=$iddom;";
        $result = mysqli_query($conn, $sql) or die(_SQLQueryError);
        $line = mysqli_fetch_array($result);
	extract($line);

	echo "\n";
echo <<< EOB
        <DIV ALIGN=CENTER>
        <TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=4>
        <TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
        <TR>
        	<TD BGCOLOR=#DDDDDD WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Name</B></FONT></TD>
	        <TD WIDTH=30% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$NAME</TD>
		<FORM METHOD=POST ACTION=$PHP_SELF>
EOB;
	echo "\n";
	if ($LOCKDEL == 0)
		echo "\t\t\t<TD WIDTH=10%><A HREF='infodom.php?lockdel=ok&iddomain=$ID'><IMG SRC=/icons/unlock.gif BORDER=0 ALT='$Alt_Lock'></TD>\n";
	
	else
		echo "\t\t\t<TD WIDTH=10%><A HREF='infodom.php?unlockdel=ok&iddomain=$ID'><IMG SRC=/icons/lock.gif BORDER=0 ALT='$Alt_Unlock'></TD>\n";

echo <<< EOB
		       	<INPUT TYPE=HIDDEN VALUE='$iddom' NAME=iddomain>
		</FORM>
        </TR>
EOB;
        echo "\n";

	if ($ZONEMASTERTYPE != "R") {
echo <<< EOB
       	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Dom_Lev</B></FONT></TD>
        	<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$LEVEL �</TD>
	</TR>
EOB;
	}

        echo "\n";
	if ($ZONETYPE != "F") {
		$filename = $NAME.".zone";
echo <<< EOB
       	<TR>
	       	<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Zone_File</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$NAME.zone</TD>
        </TR>
EOB;
        echo "\n";

	}

echo <<< EOB
        <TR>
	      	<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Create_Date</B></FONT></TD>
	        <TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$DATA</TD>
        </TR>
EOB;
        echo "\n";

	if ($ZONETYPE== "M") {
		$datamod = substr($SERIAL,0,4)."-".substr($SERIAL,4,2)."-".substr($SERIAL,6,2);
echo <<< EOB
      	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Chg_Date</B></FONT></TD>
		<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$datamod</TD>
        </TR>
EOB;
	}

        echo "\n";
       	echo "\t<TR>\n";
       	echo "\t\t<TD BGCOLOR=#DDDDDD WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Type_Zone</B></FONT></TD>\n";
       	switch ($ZONETYPE) {
                case "M": 	echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_Dom_Mas</TD>\n";
                          	break;
                case "S": 	echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_Dom_Slv</TD>\n";
                		$sql = "SELECT * FROM ipdnsmaster WHERE iddom=$ID ORDER BY ip;";
                                $res =  mysqli_query($conn,$sql) or die(_SQLQueryError);
                                if (($out = mysqli_fetch_array($res)) != NULL) {
                                        $counter = 1;
                                        do {
						$ipdot = iplong2ipdot($out[IP]);
echo <<< EOB
	<TR>
		<TD BGCOLOR=#DDDDDD WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_DNS_Prim [$counter]</B></FONT></TD>
		<TD WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$ipdot</TD>
		<TD WIDTH=10%><A HREF='$PHP_SELF?delip=ok&iddomain=$ID&idip=$out[ID]&zonetype=$ZONETYPE'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Button_Delete'></TD>
	</TR>
EOB;
						echo "\n";
                                                $counter++;
                                        } while ($out = mysqli_fetch_array($res));
                                }
                                break;
                case "F":       echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_Dom_For</TD>\n";
                                $sql = "SELECT * FROM ipdnsforwarders WHERE iddom=$ID ORDER BY ip;";
                                $res =  mysqli_query($conn,$sql) or die(_SQLQueryError);
                                if (($out = mysqli_fetch_array($res)) != NULL) {
                                        $counter = 1;
                                        do {
						$ipdot = iplong2ipdot($out[IP]);
echo <<< EOB
	<TR>
		<TD BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_DNS_For [$counter]</B></FONT></TD>
		<TD WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><CLASS=LINKNERO>$ipdot</TD>
		<TD WIDTH=10%><A HREF='$PHP_SELF?delip=ok&iddomain=$ID&idip=$out[ID]&zonetype=$ZONETYPE'><IMG SRC=/icons/trash.gif BORDER=0 ALT='$Button_Delete'></TD>
	</TR>
EOB;
						echo "\n";
                                                $counter++;
                                        } while ($out = mysqli_fetch_array($res));
                                }
                                break;

	}

echo <<< EOB
	</TR>
        <TR>
	        <TD BGCOLOR=#DDDDDD WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_State</B></FONT></TD>
EOB;
	echo "\n";

        switch ($STATE) {
                case "A": switch ($ZONETYPE) {
				case "M":	echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_State_AM</TD>\n";
						break;
				case "S":	echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_State_AS</TD>\n";
						break;
				case "F":	echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_State_N</TD>\n";
						break;
				default :	echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_State_U</TD>\n";
						break;
			  }
                          break;
                case "M": echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_State_M</TD>\n";
                          break;
                case "D": echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_State_D</TD>\n";
                          break;
                case "N": echo "\t\t<TD WIDTH=40% NOWRAP ALIGN=LEFT  VALIGN=MIDDLE><CLASS=LINKNERO>$Mod_State_N</TD>\n";
                          break;
        }

echo <<< EOB
        </TR>
        <TR>
		<TD COLSPAN=3><HR></TD>
	</TR>
        </TABLE>
        </DIV>
EOB;
	echo "\n";
	if ($ZONETYPE != "M") {

echo <<< EOB
        <DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
       	<TABLE WIDTH=30% BORDER=0 CELLSPACING=3 CELLPADDING=3>
	<TR>
		<TD COLSPAN=2><HR></TD>
	</TR>
        <TR>
EOB;
        echo "\n";
		switch ($ZONETYPE) {
			case "S" :
	        		echo "\t\t<TD BGCOLOR=#DDDDDD WIDTH=40% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_IP_Mast</B></FONT></TD>\n";
				break;
			case "F" :
	        		echo "\t\t<TD BGCOLOR=#DDDDDD WIDTH=40% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_IP_Forw</B></FONT></TD>\n";
				break;
		}
echo <<< EOB
	        <TD><WIDTH=60% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>
		        <INPUT TYPE=TEXT SIZE=3 NAME=ip1>.<INPUT TYPE=TEXT SIZE=3 NAME=ip2>.<INPUT TYPE=TEXT SIZE=3 NAME=ip3>.<INPUT TYPE=TEXT SIZE=3 NAME=ip4>
		</TD>
        </TR>
        <TR>
		<TD COLSPAN=2><HR></TD>
	</TR>
        </TABLE>
	<BR>
        <INPUT TYPE=HIDDEN VALUE='$iddom'    NAME=iddomain>
        <INPUT TYPE=HIDDEN VALUE='$ZONETYPE' NAME=zonetype>
	<INPUT TYPE=SUBMIT VALUE='$Button_Add'  NAME=continueaddip>
        </FORM>
	</DIV>
EOB;
	}
        exit;
}

#
# Aggiungi IP
#
if (isset($continueaddip)) {
        $ip1=trim($ip1);
        $ip2=trim($ip2);
        $ip3=trim($ip3);
        $ip4=trim($ip4);

        // Check sui dati inseriti
        if (($ip1=="") || ($ip2=="") || ($ip3=="") || ($ip4=="")) {
                header("Location: $PHP_SELF?iddom=$iddomain");
                exit;
        }

        // Check sull'IP
        $ip = $ip1.".".$ip2.".".$ip3.".".$ip4;
        $ip = trim($ip);
        if (!checkip($ip)) {
                header("Location: $PHP_SELF?iddom=$iddomain");
                exit;
        }

        // Inserimento dell'IP (se non esiste)
        $address = ipdot2iplong($ip);
	switch ($zonetype) {
		case "S":
			$sql = "SELECT * FROM ipdnsmaster WHERE iddom=$iddomain AND ip=$address;"; 
			break;
		case "F":
			$sql = "SELECT * FROM ipdnsforwarders WHERE iddom=$iddomain AND ip=$address;"; 
			break;
	}
        $result = mysqli_query($conn,$sql) or die(_SQLQueryError);
        if (mysqli_fetch_array($result) == NULL) {
                $sql = "UPDATE domain SET state='M' WHERE id=$iddomain AND state<>'A';";
                mysqli_query($conn,$sql) or die(_SQLQueryError);
		switch ($zonetype) {
	                case "S":
				$sql = "INSERT INTO ipdnsmaster VALUES (NULL,$iddomain,$address);";
				break;
			case "F":
				$sql = "INSERT INTO ipdnsforwarders VALUES (NULL,$iddomain,$address);";
				break;
		}
                mysqli_query($conn,$sql) or die(_SQLQueryError);
        }
        header("Location: $PHP_SELF?iddom=$iddomain");
        exit;
}

#
# Cancellazione IP
#
if (isset($delip)) {
        $sql = "UPDATE domain SET state='M' WHERE id=$iddomain AND state<>'A';";
        mysqli_query($conn,$sql) or die(_SQLQueryError);
	switch ($zonetype) {
		case "S":
        		$sql = "DELETE FROM ipdnsmaster WHERE id=$idip;";
			break;
		case "F":
        		$sql = "DELETE FROM ipdnsforwarders WHERE id=$idip;";
			break;
	}
        mysqli_query($conn,$sql) or die(_SQLQueryError);
        header("Location: $PHP_SELF?iddom=$iddomain");
        exit;
}

#
# Lock del dominio
#
if (isset($lockdel)) {
        $sql = "UPDATE domain SET lockdel=1 WHERE id=$iddomain;";
        mysqli_query($conn,$sql) or die(_SQLQueryError);
        mysqli_query($conn,$sql) or die(_SQLQueryError);
        header("Location: $PHP_SELF?iddom=$iddomain");
        exit;
}

#
# Unlock del dominio
#
if (isset($unlockdel)) {
        $sql = "UPDATE domain SET lockdel=0 WHERE id=$iddomain;";
        mysqli_query($conn,$sql) or die(_SQLQueryError);
        header("Location: $PHP_SELF?iddom=$iddomain");
        exit;
}

headerfile(_InfoDomainHeader);
showerror(_SelectDomainError);
?>
</BODY>
</HTML>
