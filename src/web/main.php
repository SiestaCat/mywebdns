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

<BODY BGCOLOR="#FFFFFF" TEXT="#000000"></BODY>

<BR>
<LEFT><H1>$MainTitle</H1></LEFT>
<BR>

<DIV ALIGN="LEFT">
$Description<BR>
<BR>
<I><B>$License</I></B><BR>
<BR>
<RIGHT><B>$Info<A HREF=http://sourceforge.net/projects/mywebdns/ TARGET=new>http://sourceforge.net/projects/mywebdns/</A></B></RIGHT><BR><BR>
<RIGHT><B>$Comment<A HREF="mailto:pasaff@tin.it">Pasquale Affinito</A></B></RIGHT>

</DIV>
</HTML>

EOB;
?>
