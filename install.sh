#!/bin/sh


ERROR="./config.error"
CONFIGFILE="./config.cache"
LIBDIR="/usr/local/lib"
MYLIBDIR="./src/lib"

if test -f $CONFIGFILE; then
	LINE=`cat $CONFIGFILE | wc -l | awk '{print $1}'`
	I=1
else
	echo "config.cache not found! Run ./configure to create it."
	exit
fi

if [ "`whoami | awk '{print $1}'`" != "root" ]; then
	echo "You must be root to execute this script!"
	exit
fi

# File descriptor usage:
# 0 standard input
# 1 file creation
# 2 errors and warnings
# 3 some systems may open it to /dev/tty
# 4 used on the Kubota Titan
# 5 compiler messages saved in config.log
# 6 checking for... messages and results
if test "$silent" = yes; then
        exec 6>/dev/null
else
        exec 6>&1
fi

if (echo "testing\c"; echo 1,2,3) | grep c >/dev/null; then
        if (echo -n testing; echo 1,2,3) | sed s/-n/xn/ | grep xn >/dev/null; then
                ac_n= ac_c='
' ac_t='        '
        else 
                ac_n=-n ac_c= ac_t=
        fi
else
        ac_n= ac_c='\c' ac_t=
fi

echo $ac_n "reading config.cache file""... $ac_c" 1>&6
while [ $I -le $LINE ]
do
	ROW=`sed -n $I'p' $CONFIGFILE`
	ID=`echo $ROW | awk -F= '{print $1}'`
	DATA=`echo $ROW | awk -F= '{print $2}'`
	case $ID in
		PREFIX)		PREFIX=$DATA;;
		MYSQL_BASE_DIR)	MYSQL_BASE_DIR=$DATA;;
		PHP_BASE_DIR)	PHP_BASE_DIR=$DATA;;
		INSTALLDB)	INSTALLDB=$DATA;;
		INSTALLADMIN)	INSTALLADMIN=$DATA;;
		INSTALLFUNCTION)INSTALLFUNCTION=$DATA;;
		LISTFUNCTION)	LISTFUNCTION=$DATA;;
		MYSQLSERVER)	MYSQLSERVER=$DATA;;
		DATABASE)	DATABASE=$DATA;;
		ROOT)		ROOT=$DATA;;
		ROOTPWD)	if [ "$DATA" = "NONE" ]; then
					ROOTPWD=
				else
					ROOTPWD="-p$DATA"
				fi
				;;
		ADMINISTRATOR)	ADMINISTRATOR=$DATA;;
		ADMINPWD)	if [ "$DATA" = "NONE" ]; then
					ADMINPWD=
					wADMINPWD=
				else
					ADMINPWD="$DATA"
					wADMINPWD="-p$DATA"
				fi
				;;
		TOMAILCHECK)	TOMAILCHECK=$DATA;;
		*)		echo "$ac_t"" install.sh: error: run ./configure" 1>&6
				exit
				;;
	esac
	I=`expr $I + 1`
done
echo "$ac_t"" done" 1>&6

if [ "$INSTALLDB" = "YES" ]; then
	echo $ac_n "creating myWebDNS DB""... $ac_c" 1>&6
	exec 2>$ERROR
	EXIST=`$MYSQL_BASE_DIR/bin/mysqladmin -h$MYSQLSERVER -u$ROOT $ROOTPWD create $DATABASE`
	exec 2>&1
	if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
        	ERROR_OUTPUT=`sed -n '1p' $ERROR`
        	echo "$ac_t""configure: error: $ERROR_OUTPUT" 1>&2
        	exit 1
	fi
	exec 2>$ERROR
 	EXIST=`$MYSQL_BASE_DIR/bin/mysql -h$MYSQLSERVER -u$ROOT $ROOTPWD $DATABASE < db.sql`
	        exec 2>&1
        if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
                ERROR_OUTPUT=`sed -n '1p' $ERROR`
                echo "$ac_t""configure: error: $ERROR_OUTPUT" 1>&2
                exit 1
        fi
	echo "$ac_t"" done" 1>&6
fi

if [ "$INSTALLADMIN" = "YES" ]; then
	echo $ac_n "creating myWebDNS administrator""...$ac_c" 1>&6
	exec 2>$ERROR
	EXIST=`$MYSQL_BASE_DIR/bin/mysql -h$MYSQLSERVER -u$ROOT $ROOTPWD -e "GRANT ALL PRIVILEGES ON $DATABASE.* TO $ADMINISTRATOR@$MYSQLSERVER IDENTIFIED BY '$ADMINPWD';"`
        exec 2>&1
        if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
                ERROR_OUTPUT=`sed -n '1p' $ERROR`
                echo "$ac_t""configure: error: $ERROR_OUTPUT" 1>&2
                exit 1
        fi
	exec 2>$ERROR
	EXIST=`$MYSQL_BASE_DIR/bin/mysqladmin -h$MYSQLSERVER -u$ROOT $ROOTPWD flush-privileges`
        exec 2>&1
        if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
                ERROR_OUTPUT=`sed -n '1p' $ERROR`
                echo "$ac_t""configure: error: $ERROR_OUTPUT" 1>&2
                exit 1
        fi
        exec 2>$ERROR
	EXIST=`$MYSQL_BASE_DIR/bin/mysql -h$MYSQLSERVER -u$ADMINISTRATOR $wADMINPWD -D$DATABASE -e "INSERT INTO mysql_auth VALUES ('$ADMINISTRATOR',PASSWORD('$ADMINPWD'),'administration','myWebDNS administrator');"`
        exec 2>&1
        if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
                ERROR_OUTPUT=`sed -n '1p' $ERROR`
                echo "$ac_t""configure: error: $ERROR_OUTPUT" 1>&2
                exit 1
        fi
	echo "$ac_t"" done" 1>&6
fi

if [ "$INSTALLFUNCTION" = "YES" ]; then
	echo "compiling libraries..."
	cd $MYLIBDIR
	make build
	cd ../..
	echo "done"

	echo $ac_n "installing libraries""...$ac_c" 1>&6
	exec 3>&1
	exec 1>/dev/null
	cd $MYLIBDIR
	make install
	cd ../..
	exec 1>&3
	echo "$ac_t"" done" 1>&6

	for FUNCTION in $LISTFUNCTION
	do
		ac_word=$FUNCTION
		echo $ac_n "installing function $ac_word""...$ac_c" 1>&6
		exec 2>$ERROR
		case "$ac_word" in
			"long2ip") $MYSQL_BASE_DIR/bin/mysql -h$MYSQLSERVER -u$ROOT $ROOTPWD -e "CREATE FUNCTION long2ip RETURNS STRING SONAME 'my_libip.so';";;
			"ip2long") $MYSQL_BASE_DIR/bin/mysql -h$MYSQLSERVER -u$ROOT $ROOTPWD -e "CREATE FUNCTION ip2long RETURNS INTEGER SONAME 'my_libip.so';";;
			"masklong2maskdot") $MYSQL_BASE_DIR/bin/mysql -h$MYSQLSERVER -u$ROOT $ROOTPWD -e "CREATE FUNCTION masklong2maskdot RETURNS STRING SONAME 'my_libip.so';";;
			"maskdot2masklong") $MYSQL_BASE_DIR/bin/mysql -h$MYSQLSERVER -u$ROOT $ROOTPWD -e "CREATE FUNCTION maskdot2masklong RETURNS INTEGER SONAME 'my_libip.so';";;
		esac
		exec 2>&1
		if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
        		ERROR_OUTPUT=`sed -n '1p' $ERROR`
        		echo "$ac_t""configure: error: $ERROR_OUTPUT" 1>&2
        		exit 1
		fi
		echo "$ac_t"" done" 1>&6
	done
fi

echo $ac_n "reloading MySQL server""...$ac_c" 1>&6
exec 2>$ERROR
EXIST=`$MYSQL_BASE_DIR/bin/mysqladmin -h$MYSQLSERVER -u$ROOT $ROOTPWD reload`
exec 2>&1
if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
        ERROR_OUTPUT=`sed -n '1p' $ERROR`
        echo "$ac_t""configure: error: $ERROR_OUTPUT" 1>&2
        exit 1
fi
echo "$ac_t"" done" 1>&6

SCRIPTTARGETDIR=$PREFIX/script/
WEBTTARGETDIR=$PREFIX/web/

echo $ac_n "checking for $SCRIPTTARGETDIR directory""...$ac_c" 1>&6
if test ! -d $SCRIPTTARGETDIR; then
	mkdir -p $SCRIPTTARGETDIR
        if [ $? -ne 0 ]; then
                echo "$ac_t"" install.sh: error: Cannot create $SCRIPTTARGETDIR directory" 1>&6
		exit
	fi
fi
echo "$ac_t"" done" 1>&6

echo $ac_n "checking for $WEBTTARGETDIR directory""...$ac_c" 1>&6
if test ! -d $WEBTTARGETDIR; then
	mkdir -p $WEBTTARGETDIR
        if [ $? -ne 0 ]; then
                echo "$ac_t"" install.sh: error: Cannot create $WEBTARGETDIR directory" 1>&6
		exit
	fi
fi
echo "$ac_t"" done" 1>&6

PHP=`echo "#!$PHP_BASE_DIR/bin/php -q" | sed 's/\//\\\\\//g'`

echo $ac_n "installing script file""... $ac_c" 1>&6
SCRIPTLIST=`ls -1 ./src/script/*.php`
for SCRIPTFILE in $SCRIPTLIST
do
	if [ "`basename $SCRIPTFILE`" = "checkdns.php" ]; then
		sed 's/<DATABASE>/'$DATABASE'/1' $SCRIPTFILE | \
		sed 's/<PHPBINPATH>/'"$PHP"'/1' | \
		sed 's/<ADMINISTRATOR>/'$ADMINISTRATOR'/1' | \
		sed 's/<PASSWD>/'$ADMINPWD'/1' | \
		sed 's/<MYSQLSERVER>/'$MYSQLSERVER'/1' | \
		sed 's/<TOMAILCHECK>/'$TOMAILCHECK'/1' > $SCRIPTTARGETDIR`basename $SCRIPTFILE`
	else
		sed 's/<PHPBINPATH>/'"$PHP"'/1' $SCRIPTFILE | \
		sed 's/<DATABASE>/'$DATABASE'/1' | \
		sed 's/<ADMINISTRATOR>/'$ADMINISTRATOR'/1' | \
		sed 's/<PASSWD>/'$ADMINPWD'/1' | \
		sed 's/<MYSQLSERVER>/'$MYSQLSERVER'/1' > $SCRIPTTARGETDIR`basename $SCRIPTFILE`
	fi
	chmod 700 $SCRIPTTARGETDIR`basename $SCRIPTFILE`
done
echo "$ac_t"" done" 1>&6

echo $ac_n "installing web files""... $ac_c" 1>&6
cp -R ./src/web/* $WEBTTARGETDIR
chmod -R 655 $WEBTTARGETDIR*

PREFIX=`echo "$WEBTTARGETDIR" | sed 's/\//\\\\\//g'`
sed 's/<PREFIX>/'$PREFIX'/1' ./src/web/include.php | \
sed 's/<DATABASE>/'$DATABASE'/1' | \
sed 's/<ADMINISTRATOR>/'$ADMINISTRATOR'/1' | \
sed 's/<PASSWD>/'$ADMINPWD'/1' | \
sed 's/<MYSQLSERVER>/'$MYSQLSERVER'/1' > $WEBTTARGETDIR/include.php

echo "$ac_t"" done" 1>&6

cat << EOF

Installation completed.
Now you must change httpd.conf to allow mysql authentication!
Read INSTALL for more info.

EOF
