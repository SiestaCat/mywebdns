<?php
require("./include.php");
$conn = connect_db();

echo <<< EOB
<HTML>
<HEAD>
<META CONTENT=NO-CACHE HTTP-EQUIV=PRAGMA>
<META CONTENT=0 HTTP-EQUIV=EXPIRES>
<LINK REL="stylesheet" HREF="/style.css" TYPE="TEXT/CSS">
</HEAD>

<BODY BGCOLOR="#000000" TEXT="#000000">
<DIV ALIGN="LEFT">
	<TABLE WIDTH="100%" BORDER="0">
		<TR><TD ALIGN=CENTER><A HREF="main.php" TARGET="mainFrame"><img SRC="/images/logo.jpg" ALT="Homepage" BORDER=0 WIDTH=181 HEIGHT=58></TD></TR> 
		<TR><TD ALIGN=RIGHT><FONT COLOR="#DDDDDD"><TT><B>ver. $session_version</B></TT></FONT></TD></TR>
		<TR><TD>&nbsp;</TD></TR> 

		<TR BGCOLOR="#DDDDDD"> <TD><IMG ALT="" BORDER=0 HEIGHT=1 SRC="/images/spacer.gif" WIDTH=1><BR></TD></TR> 
		<TR><TD><FONT COLOR="#DDDDDD"><TT><B>$M_user:</B> $session_username</TT></FONT></TD></TR>
		<TR><TD><FONT COLOR="#DDDDDD"><TT><B>$M_group:</B> $session_groups  </TT></FONT></TD></TR>
		<TR BGCOLOR="#DDDDDD"> <TD><IMG ALT="" BORDER=0 HEIGHT=1 SRC="/images/spacer.gif" WIDTH=1><BR></TD></TR> 
		<TR><TD>&nbsp;</TD></TR> 

		<TR><TD><SPAN CLASS=TESTOARANCIO>$M1_title</SPAN></TD></TR> 
		<TR BGCOLOR="#DDDDDD"> <TD><IMG ALT="" BORDER=0 HEIGHT=1 SRC="/images/spacer.gif" WIDTH=1><BR></TD></TR> 
EOB;
		if ($session_groups == "administration") {
			echo "\t\t\<TR><TD><A CLASS=LINKGRIGIO HREF=\"dns/adddns.php\"	TARGET=\"mainFrame\">$M1_item1</A></TD></TR>\n";
		}
echo <<< EOB
		<TR><TD><A CLASS=LINKGRIGIO HREF="dns/configdns.php"	TARGET="mainFrame">$M1_item2</A></TD></TR>
		<TR><TD><A CLASS=LINKGRIGIO HREF="dns/reloaddns.php"	TARGET="mainFrame">$M1_item3</A></TD></TR>
		<TR><TD>&nbsp;</TD></TR> 

		<TR><TD><SPAN CLASS=TESTOARANCIO>$M2_title</SPAN></TD></TR> 
		<TR BGCOLOR="#DDDDDD">  <TD><IMG ALT="" BORDER=0 HEIGHT=1 SRC="/images/spacer.gif" WIDTH=1><BR></TD></TR> 
		<TR><TD><A CLASS=LINKGRIGIO HREF="domain/adddom.php?adddomain=ok"		TARGET="mainFrame">$M2_item1</A></TD></TR/>
		<TR><TD><A CLASS=LINKGRIGIO HREF="domain/domain.php?selectdomain=ok"		TARGET="mainFrame">$M2_item2</A></TD></TR>
		<TR><TD><A CLASS=LINKGRIGIO HREF="domain/recoverdom.php?recoverdomain=ok"	TARGET="mainFrame">$M2_item3</A></TD></TR>
		<TR><TD>&nbsp;</TD></TR>
EOB;
		if ($session_groups == "administration") {
			echo "\t\t<TR><TD><SPAN CLASS=TESTOARANCIO>$M3_title</SPAN></TD></TR>\n";
			echo "\t\t<TR BGCOLOR=\"#DDDDDD\">  <TD><IMG ALT=\"\" BORDER=0 HEIGHT=1 SRC=\"/images/spacer.gif\" WIDTH=1><BR></TD></TR>\n";
			echo "\t\t<TR><TD><A CLASS=LINKGRIGIO HREF=\"admin/config.php?adminconfig=ok\"	TARGET=\"mainFrame\">$M3_item1</A></TD></TR/>\n";
			echo "\t\t<TR><TD><A CLASS=LINKGRIGIO HREF=\"admin/users.php?adminusers=ok\"	TARGET=\"mainFrame\">$M3_item2</A></TD></TR/>\n";
			echo "\t\t<TR><TD><A CLASS=LINKGRIGIO HREF=\"admin/groups.php?admingroups=ok\"	TARGET=\"mainFrame\">$M3_item3</A></TD></TR/>\n";
		} else {
			echo "\t\t<TR><TD><A CLASS=LINKGRIGIOITALIC HREF=\"users/changepw.php?setpwd=ok\"	TARGET=\"mainFrame\">$M3_item4</A></TD></TR/>\n";
		}

echo <<< EOB
	</TABLE>
</DIV>
</BODY>
</HTML>
EOB;
