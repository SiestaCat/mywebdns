<?php

require("./include.php");
$conn = connect_db();

if (!isset($PHP_AUTH_USER)) {
	Header("WWW-Authenticate: Basic realm=\"WebDNS\"");
	Header("HTTP/1.0 401 Accesso negato!!!");
	exit;
} else {
	session_register("session_username");
        session_register("session_groups");
	$sql = "SELECT * FROM mysql_auth WHERE username='$PHP_AUTH_USER';";
	$result = mysql_query($sql,$conn) or die(_SQLQueryError);
	$line = mysql_fetch_array($result);
	extract($line);
	$session_username = "$PHP_AUTH_USER";
	$session_groups = "$GROUPS";

echo <<< EOB

<HTML>
<HEAD>
<TITLE>myWebDNS</TITLE>
<META CONTENT=NO-CACHE HTTP-EQUIV=PRAGMA>
<META CONTENT=0 HTTP-EQUIV=EXPIRES>
<LINK REL="STYLESHEET" HREF="/style.css" TYPE="TEXT/CSS">
</HEAD>

<FRAMESET COLS="210,*" FRAMEBORDER="NO" BORDER="0" FRAMESPACING="0" ROWS="*"> 
	<FRAME NAME="leftFrame" NORESIZE SCROLLING="auto" SRC="/menu.php">
		<FRAMESET ROWS="*" FRAMEBORDER="no" BORDER="0" FRAMESPACING="0"> 
			<FRAME NAME="mainFrame" SRC="/main.php">
		</FRAMESET>
</FRAMESET>

<NOFRAMES>
	<BODY BGCOLOR="#FFFFFF" TEXT="#000000"></BODY>
</NOFRAMES> 
</HTML>

EOB;
}

?>
