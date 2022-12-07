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
if (isset($adminconfig)) {
	headerfile(_AdminConfig);

echo <<< EOB
	<HR>
        <DIV ALIGN=LEFT><FORM METHOD=POST ACTION=$PHP_SELF>
	<ALIGN=LEFT>  <FONT COLOR=darkblue><STRONG><B>$Mod_General</B></STRONG></FONT></TD>
        <TABLE WIDTH=20% BORDER=0 CELLSPACING=3 CELLPADDING=3>
        <TR>
                <TD COLSPAN=2></TD>
        </TR>
        <TR>
                <TD WIDTH=80 BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_Language</B></FONT></TD>
                <TD WIDTH=10% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>
EOB;
        echo "\n";
	$i = 0;
	if (count($array_languages) > 0) {
		echo "\t\t\t<SELECT NAME=language>\n";
	        do {
	                if ($i==0) {
				echo "\t\t\t\t<OPTION SELECTED VALUE='$array_languages_symbols[$i]'>$array_languages[$i]</OPTION>\n";
			} else
                        	echo "\t\t\t\t<OPTION VALUE='$array_languages_symbols[$i]'>$array_languages[$i]</OPTION>\n";
	        	$i++;
        	} while ($i < count($array_languages));
		echo "\t\t\t</SELECT></TD>\n";
	} else {
		echo "\t\t\t\t<WIDTH=30% NOWRAP ALIGN=LEFT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3>_ErronNoLanguage</FONT>\n";
	}
echo <<< EOB
        </TR>
        <TR>
                <TD COLSPAN=2></TD>
        </TR>
        </TABLE>
	<BR>
	<ALIGN=LEFT>  <FONT COLOR=darkblue><STRONG><B>$Mod_Dom</B></STRONG></FONT></TD>
	<TABLE WIDTH=50% BORDER=0 CELLSPACING=3 CELLPADDING=3>
	<TR>
                <TD COLSPAN=6></TD>
        </TR>
	<TR>
		<TD>&nbsp;</TD>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><STRONG>NS</STRONG></FONT>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><STRONG>MX</STRONG></FONT>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><STRONG>A</STRONG></FONT>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><STRONG>PTR</STRONG></FONT>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><STRONG>CNAME</STRONG></FONT>
	</TR>
	<TR>
        	<TD WIDTH=10% BGCOLOR="#DDDDDD" WIDTH=60% NOWRAP ALIGN=RIGHT VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3><B>$Mod_New_Record</B></FONT></TD>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=2 SIZE=4 NAME=myrecord_ns VALUE='$_SESSION['session_record_ns']'></TD>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=2 SIZE=4 NAME=myrecord_mx VALUE='$_SESSION['session_record_mx']'></TD>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=2 SIZE=4 NAME=myrecord_a VALUE='$_SESSION['session_record_a']'></TD>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=2 SIZE=4 NAME=myrecord_ptr VALUE='$_SESSION['session_record_ptr']'></TD>
		<TD WIDTH=10% NOWRAP ALIGN=CENTER VALIGN=MIDDLE><FONT FACE=Lucida SIZE=3> <INPUT TYPE=TEXT MAXLENGHT=2 SIZE=4 NAME=myrecord_cname VALUE='$_SESSION['session_record_cname']'></TD>
		
	</TR>
	</TABLE>
	<HR>
	<CENTER><INPUT TYPE=BUTTON VALUE='$Button_Apply' NAME=configuration onClick='javascript:CheckConfiguration()'></CENTER>
        </FORM>
	</DIV>
        </BODY>
        </HTML>
EOB;
}

#
# Configurazione
#
if (isset($configuration)) {
	$stroutput = _ReloadConfig;
echo <<< EOB
	<BR>
	<BODY BGCOLOR="#FFFFFF" onLoad=setTimeout('parent.top.document.location.href="/"',3000)>
	<DIV ALIGN=CENTER>
	<H3>$stroutput</H3>
	</DIV>
EOB;
	$_SESSION['session_language'] = "$language";
	$sql = "UPDATE configuration SET language='$_SESSION['session_language']', record_ns=$myrecord_ns, record_mx=$myrecord_mx, record_a=$myrecord_a, record_ptr=$myrecord_ptr, record_cname=$myrecord_cname WHERE id=1;";
	$result = mysqli_query($conn,$sql) or die(_SQLQueryError);
}
?>
