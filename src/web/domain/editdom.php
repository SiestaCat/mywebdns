<?php
require("../include.php");
$conn=connect_db();

if (!checkdns()) {
        headerfile("");
        showerror(_DNSError);
}

if (isset($iddom)) {
	headerfile(_EditDomainHeader);

	// Tabella Principale
	echo "\n";

echo <<< EOB
        <DIV ALIGN=CENTER>
        <TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
        <TR>
		<TD>
EOB;

	$sql = "SELECT * FROM domain WHERE id=$iddom;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	$out = mysql_fetch_array($result);
	extract($out);

	// Calcolo il nuovo seriale della zona
	$newserial = date("Ymd01");
	if ($SERIAL < $newserial)
		$serial = $newserial;
	else
		$serial = $SERIAL+1;

	echo "\n";
echo <<< EOB
		<DIV ALIGN=CENTER><FORM METHOD=POST ACTION=$PHP_SELF>
		<TABLE WIDTH=100% BORDER=0 CELLSPACING=3 CELLPADDING=1>
		<TR>
			<TD COLSPAN=7><HR></TD>
		</TR>
		<TR>
			<TD WIDTH=15% CLASS=dominfo>Dominio</TD>
			<TD WIDTH=20%> <B>$NAME</B></TD>
			<TD WIDTH=15% CLASS=dominfo>Host DNS</TD>
			<TD WIDTH=20%>$HOSTDNS</TD>
			<TD WIDTH=15% CLASS=dominfo>TTL</TD>
			<INPUT TYPE=HIDDEN NAME=oldsoattl VALUE=$TTL>
			<TD WIDTH=10%><INPUT TYPE=TEXT NAME=soattl VALUE=$TTL MAXLENGTH=5 SIZE=10 onChange='javascript:check_SOA_ttl(soattl,oldsoattl,1,86400)'></TD>
		</TR>
		<TR>
			<TD WIDTH=15% CLASS=dominfo>Seriale</TD>
			<INPUT TYPE=HIDDEN NAME=soaserial VALUE=$serial>
			<TD WIDTH=20%>$serial</TD>
			<TD WIDTH=15% CLASS=dominfo>Root DNS</TD>
			<TD WIDTH=20%>$ROOTDNS</TD>
			<TD WIDTH=15% CLASS=dominfo>Refresh</TD>
			<INPUT TYPE=HIDDEN NAME=oldsoarefr VALUE=$REFRESH>
			<TD WIDTH=10%><INPUT TYPE=TEXT NAME=soarefr VALUE=$REFRESH MAXLENGTH=5 SIZE=10 onChange='javascript:check_SOA_refr(soarefr,oldsoarefr,1,86400)'></TD>
		</TR>
		<TR>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD WIDTH=15% CLASS=dominfo>Retry</TD>
			<INPUT TYPE=HIDDEN NAME=oldsoaret VALUE=$RETRY>
			<TD WIDTH=10%><INPUT TYPE=TEXT NAME=soaret VALUE=$RETRY MAXLENGTH=5 SIZE=10 onChange='javascript:check_SOA_ret(soaret,oldsoaret,1,86400)'></TD>
		</TR>
		<TR>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD WIDTH=15% CLASS=dominfo>Expire</TD>
			<INPUT TYPE=HIDDEN NAME=oldsoaexp VALUE=$EXPIRE>
			<TD WIDTH=10%><INPUT TYPE=TEXT NAME=soaexp VALUE=$EXPIRE MAXLENGTH=7 SIZE=10 onChange='javascript:check_SOA_exp(soaexp,oldsoaexp,1,2592000)'></TD>
		</TR>
		<TR>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD WIDTH=15% CLASS=dominfo>Minimum</TD>
			<INPUT TYPE=HIDDEN NAME=oldsoamin VALUE=$MINIMUM>
			<TD WIDTH=10%><INPUT TYPE=TEXT NAME=soamin VALUE=$MINIMUM MAXLENGTH=5 SIZE=10 onChange='javascript:check_SOA_min(soamin,oldsoamin,1,86400)'></TD>
		</TR>
		<TR>
			<TD COLSPAN=7></TD>
		</TR>
		</TABLE>
		</DIV>
        	</TD>
	</TR>
        <TR>
		<TD>
EOB;
	echo "\n";
	if ($ZONEMASTERTYPE == "M")
                $sql2 = "SELECT * FROM recordmaster WHERE iddom=$ID ORDER BY type DESC ,name ASC, priority ASC, hosttarget ASC;";
        else
                $sql2 = "SELECT *, LPAD(ip,7,' ') AS newip FROM recordreverse WHERE iddom=$ID ORDER BY newip;";

	$result2 = mysql_query($sql2,$conn) or die(_SQLQueryError);
	$numelem = mysql_num_rows($result2);

echo <<< EOB
		<INPUT TYPE=HIDDEN NAME='id_domain' 	   VALUE='$ID'>
		<INPUT TYPE=HIDDEN NAME='zone_master_type' VALUE='$ZONEMASTERTYPE'>
		<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=1>
		<TR>
			<TD ALIGN=CENTER WIDTH=5%><SPAN CLASS=TESTONERO NOWRAP>ID</TD>
			<TD ALIGN=CENTER WIDTH=5%><IMG SRC=/icons/trash.gif BORDER=0 ALT='Cancella record'></TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
			<TD>&nbsp;</TD>
		</TR>
EOB;

	echo "\n";
	$index_record = 0; 					// Inizializzazione dell'indice dei record
	$counter = 1;						// Contatore usato per il colore
	$countermx = -1;					// Contatore dei record MX
	$array_id_record = "";                                  // Array (stringa) contenente gli ID dei record
	$length_str = strlen(mysql_num_rows($result2)+9);
	while ($line = mysql_fetch_array($result2)) {
		extract($line);

		$index_record++;				// Indice dei record
		$index_record = str_pad($index_record,$length_str,'0',STR_PAD_LEFT);

		$array_id_record .= $ID.";";

		echo "\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";

		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='record_id'    VALUE='$ID'>\n";			// ID record
		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_record' VALUE='$index_record'>\n";		// Indice record
		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='record_state' VALUE='N'>\n";			// Stato del record (N=nullo, M=modificato, D=cancellato)
		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='record_type'  VALUE='OLD'>\n";			// Tipo del record (OLD=esistente, NEW=aggiunto)

		echo "\t\t\t<TD ALIGN=LEFT WIDTH=5%><SPAN CLASS=TESTONERO NOWRAP>$index_record</TD>\n";
		echo "\t\t\t<TD ALIGN=CENTER WIDTH=5%><INPUT TYPE=CHECKBOX NAME=check_rec VALUE=$ID onClick='javascript:delete_domain_record($ID)'></TD>\n";
		if ($ZONEMASTERTYPE == "M") {
			$ipdot = iplong2ipdot($IP);
			echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrechost' VALUE='$NAME'>\n";
		 	echo "\t\t\t<TD ALIGN=CENTER WIDTH=20%><INPUT TYPE=TEXT NAME='rechost' VALUE='$NAME' MAXLENGTH=60 SIZE=35 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
		} else {
			echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrechost' VALUE='$IP'>\n";
			echo "\t\t\t<TD ALIGN=CENTER WIDTH=20%><INPUT TYPE=TEXT NAME='rechost' VALUE='$IP' MAXLENGTH=60 SIZE=35 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
		}

		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecttl' VALUE='$TTL'>\n";
		echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><INPUT TYPE=TEXT NAME='recttl' VALUE='$TTL' MAXLENGTH=5 SIZE=5' onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
		echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTONERO NOWRAP>IN</TD>\n";

		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='rectype' VALUE='$TYPE'>\n";
		switch ($TYPE) {
			case "MX":
				$countermx++;
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='$countermx'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='$PRIORITY'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTONERO NOWRAP>$TYPE</TD>\n";
				switch ($PRIORITY) {
					case "5" :  $priorities="<OPTION SELECTED>5<OPTION>10<OPTION>15<OPTION>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "10":  $priorities="<OPTION>5<OPTION SELECTED>10<OPTION>15<OPTION>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "15":  $priorities="<OPTION>5<OPTION>10<OPTION SELECTED>15<OPTION>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "20":  $priorities="<OPTION>5<OPTION>10<OPTION>15<OPTION SELECTED>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "25":  $priorities="<OPTION>5<OPTION>10<OPTION>15<OPTION>20<OPTION SELECTED>25<OPTION>30</SELECT>"; 
						    break;
					case "30":  $priorities="<OPTION>5<OPTION>10<OPTION>15<OPTION>20<OPTION>25<OPTION SELECTED>30</SELECT>"; 
						    break;
				}
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%>\n";
				
				echo "\t\t\t\t<SELECT NAME='recmxprior' onChange='javascript:check_delete_domain_record($ID)'>$priorities\n";
				echo "\t\t<INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=30 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "A":
				$ipdot = iplong2ipdot($IP);
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$ipdot'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTONERO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$ipdot' MAXLENGTH=15 SIZE=20 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "NS":
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTONERO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=39 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "CNAME":
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTONERO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=39 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "PTR":
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTONERO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=39 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
		}
		echo "\t</TR>\n";
	}

	if ($ZONEMASTERTYPE == "M") 
		$max_record_added = $session_record_ns + $session_record_mx + $session_record_a + $session_record_cname;
	else
		$max_record_added = $session_record_ns + $session_record_mx + $session_record_ptr;
	$counter_rec1 = $session_record_ns;
	$counter_rec2 = $counter_rec1 + $session_record_mx;
	if ($ZONEMASTERTYPE == "M") {
		$counter_rec3 = $counter_rec2 + $session_record_a;
		$counter_rec4 = $counter_rec3 + $session_record_cname;
	} else
		$counter_rec3 = $counter_rec2 + $session_record_ptr;

	// Inserisco i record di aggiunta
	for ($i=1; $i<=$max_record_added; $i++) {

		$index_record++;									// Indice dei record
		$index_record = str_pad($index_record,$length_str,'0',STR_PAD_LEFT);

		$ID = $i * (-1);
		$array_id_record .= $ID.";";

		echo "\t\t<TR BGCOLOR=".(switchcolor($counter++)).">\n";

		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='record_id'    VALUE='$ID'>\n";			// ID record
		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_record' VALUE='$index_record'>\n";		// Indice record
		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='record_state' VALUE='N'>\n";			// Stato del record (N=nullo, M=modificato, D=cancellato)
		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='record_type'  VALUE='NEW'>\n";			// Tipo del record (OLD=esistente, NEW=aggiunto)

		echo "\t\t\t<TD ALIGN=LEFT WIDTH=5%><SPAN CLASS=TESTOARANCIO NOWRAP>$index_record</TD>\n";
		echo "\t\t\t<TD ALIGN=CENTER WIDTH=5%><INPUT TYPE=CHECKBOX NAME=check_rec VALUE=$ID onClick='javascript:delete_domain_record($ID)'></TD>\n";

		$IP = "";
		$NAME= "";

		if ($ZONEMASTERTYPE == "M") {
			$ipdot = " ";
			echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrechost' VALUE='$NAME'>\n";
		 	echo "\t\t\t<TD ALIGN=CENTER WIDTH=20%><INPUT TYPE=TEXT NAME='rechost' VALUE='$NAME' MAXLENGTH=60 SIZE=35 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
		} else {
			echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrechost' VALUE='$IP'>\n";
			echo "\t\t\t<TD ALIGN=CENTER WIDTH=20%><INPUT TYPE=TEXT NAME='rechost' VALUE='$IP' MAXLENGTH=60 SIZE=35 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
		}
		
		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecttl' VALUE='86400'>\n";
		echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><INPUT TYPE=TEXT NAME='recttl' VALUE='86400' MAXLENGTH=5 SIZE=5' onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
		echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTOARANCIO NOWRAP>IN</TD>\n";

		if ($i <= $counter_rec1)
			$TYPE="NS";	
		else {
			if ($i <= $counter_rec2)
				$TYPE="MX";
			else {
				if ($ZONEMASTERTYPE == "M") {
					if ($i <= $counter_rec3)
						$TYPE="A";
					else {
						if ($i <= $counter_rec4)
							$TYPE="CNAME";
					}
				} else {
					if ($i <= $counter_rec3)
						$TYPE="PTR";
				}
			}
		}


		echo "\t\t\t<INPUT TYPE=HIDDEN NAME='rectype' VALUE='$TYPE'>\n";
		$PRIORITY = 0;
		$HOSTTARGET = "";
		switch ($TYPE) {
			case "MX":
				$PRIORITY = "?";
				$countermx++;
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='$countermx'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='$PRIORITY'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTOARANCIO NOWRAP>$TYPE</TD>\n";
				switch ($PRIORITY) {
					case "?" :  $priorities="<OPTION SELECTED>?<OPTION>5<OPTION>10<OPTION>15<OPTION>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "5" :  $priorities="<OPTION>?<OPTION SELECTED>5<OPTION>10<OPTION>15<OPTION>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "10":  $priorities="<OPTION>?<OPTION>5<OPTION SELECTED>10<OPTION>15<OPTION>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "15":  $priorities="<OPTION>?<OPTION>5<OPTION>10<OPTION SELECTED>15<OPTION>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "20":  $priorities="<OPTION>?<OPTION>5<OPTION>10<OPTION>15<OPTION SELECTED>20<OPTION>25<OPTION>30</SELECT>"; 
						    break;
					case "25":  $priorities="<OPTION>?<OPTION>5<OPTION>10<OPTION>15<OPTION>20<OPTION SELECTED>25<OPTION>30</SELECT>"; 
						    break;
					case "30":  $priorities="<OPTION>?<OPTION>5<OPTION>10<OPTION>15<OPTION>20<OPTION>25<OPTION SELECTED>30</SELECT>"; 
						    break;
				}
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%>\n";
				
				echo "\t\t\t\t<SELECT NAME='recmxprior' onChange='javascript:check_delete_domain_record($ID)'>$priorities\n";
				echo "\t\t<INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=30 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "A":
				$ipdot = "";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$ipdot'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTOARANCIO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$ipdot' MAXLENGTH=15 SIZE=20 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "NS":
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTOARANCIO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=39 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "CNAME":
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTOARANCIO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=39 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
			case "PTR":
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='index_mxprior' VALUE='*'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrecmxprior' VALUE='0'>\n";
				echo "\t\t\t<INPUT TYPE=HIDDEN NAME='oldrectarget' VALUE='$HOSTTARGET'>\n";
				//echo "\t\t\t<INPUT TYPE=HIDDEN NAME='recmxprior' VALUE='0'>\n";
				echo "\t\t\t<TD ALIGN=CENTER WIDTH=10%><SPAN CLASS=TESTOARANCIO NOWRAP>$TYPE</TD>\n";
				echo "\t\t\t<TD ALIGN=LEFT WIDTH=20%><INPUT TYPE=TEXT NAME='rectarget' VALUE='$HOSTTARGET' MAXLENGTH=60 SIZE=39 onChange='javascript:check_delete_domain_record($ID)'></TD>\n";
				break;
		}
		echo "\t</TR>\n";
	}

echo <<< EOB
		<TR>
			<TD COLSPAN=8><HR></TD>
		</TR>
		</TABLE>
		<CENTER>
                        <INPUT TYPE=HIDDEN NAME="id_record" VALUE="$array_id_record">
                        <INPUT TYPE=HIDDEN NAME="id_record_deleted" VALUE="">
			<INPUT TYPE=HIDDEN NAME="iddomain" VALUE="$iddom">
			<INPUT TYPE=HIDDEN NAME="domainstate" VALUE="$STATE">
			<INPUT TYPE=BUTTON NAME="savedomain" VALUE="$Button_Save" onClick='javascript:CheckDomain()'>
			<INPUT TYPE=RESET  VALUE="$Button_Reset" onClick='javascript:initdefault_domain()'>
		</CENTER>
		</FORM>
		</DIV>
        	</TD>
	</TR>
        </TABLE>
        </DIV>
EOB;
	mysql_close($conn);
	exit;
}

#
# Salvataggio del dominio
#
if (isset($savedomain)) {
        $stroutput = _UpdateDomain;
echo <<< EOB
        <BODY BGCOLOR="#FFFFFF" onLoad=setTimeout('history.back();',5000)>
        <BR>
        <DIV ALIGN=CENTER>
        <H3>$stroutput</H3>
        </DIV>
EOB;

	// Aggiorno la struttura SOA del dominio
	if ($domainstate == 'A')
		$sql = "UPDATE domain SET state='A', serial=$soaserial, ttl=$soattl, refresh=$soarefr, retry=$soaret, expire=$soaexp, minimum=$soamin WHERE ID=$id_domain;";
	else
		$sql = "UPDATE domain SET state='M', serial=$soaserial, ttl=$soattl, refresh=$soarefr, retry=$soaret, expire=$soaexp, minimum=$soamin WHERE ID=$id_domain;";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	
	// Elimino i record cancellati
	if ($array_deleted != "") {
		$buffer = explode(";",$array_deleted);
		for ($i=0; $i<(count($buffer)-1); $i++) {
			if ($zmt == "M") 
				$sql = "DELETE FROM recordmaster WHERE id=$buffer[$i];";
			else
				$sql = "DELETE FROM recordreverse WHERE id=$buffer[$i];";
			$result = mysql_query($sql,$conn) or die(_SQLQueryError);
		}
	}

	// Aggiorno i record modificati
	if ($array_changed != "") {
		$buffer = explode(";",$array_changed);
		for ($i=0; $i<(count($buffer)-1); $i++) {
			// Ricavo i dati da aggiungere (ID, SRC, TTL, TIPO, PRIOR, DEST)
			$data = explode(",", $buffer[$i]);
			if ($zmt == "M") {
				if ($data[3] == "A") {
					$data[5] = ipdot2iplong($data[5]);	// Trasformazione dell'indirizzo IP (dot->long)
					$sql = "UPDATE recordmaster SET name='$data[1]', ttl=$data[2], priority=$data[4], ip=$data[5] WHERE id=$data[0];";
				} else
					$sql = "UPDATE recordmaster SET name='$data[1]', ttl=$data[2], priority=$data[4], hosttarget='$data[5]' WHERE id=$data[0];";
			} else 
			 	$sql = "UPDATE recordreverse SET ip='$data[1]', ttl=$data[2], priority=$data[4], hosttarget='$data[5]' WHERE id=$data[0];";
			$result = mysql_query($sql,$conn) or die(_SQLQueryError);
		}
	}

        // Aggiungo i record nuovi
        if ($array_added != "") {
                $buffer = explode(";",$array_added);
                for ($i=0; $i<(count($buffer)-1); $i++) {
                        // Ricavo i dati da aggiungere (ID, SRC, TTL, TIPO, PRIOR, DEST)
                        $data = explode(",", $buffer[$i]);
                        if ($zmt == "M") {
                                if ($data[3] == "A") {
					$data[5] = ipdot2iplong($data[5]);	// Trasformazione dell'indirizzo IP (dot->long)
					$sql = "INSERT INTO recordmaster VALUES(NULL,$id_domain,'$data[1]',$data[2],'$data[3]',$data[4],'',$data[5]);";
                                } else
					$sql = "INSERT INTO recordmaster VALUES(NULL,$id_domain,'$data[1]',$data[2],'$data[3]',$data[4],'$data[5]',0);";
                        } else
				$sql = "INSERT INTO recordreverse VALUES(NULL,$id_domain,'$data[1]',$data[2],'$data[3]',$data[4],'$data[5]');";
			$result = mysql_query($sql,$conn) or die(_SQLQueryError);
                }
        }
}

echo "</BODY>";
echo "</HTML>";
?>
