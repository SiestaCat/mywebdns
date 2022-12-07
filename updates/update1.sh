#!/bin/sh

# Aggiornamento n. 1 (UPDATES --> 11)

ERROR="./config.error"

MYDB=mywebdns
LIBDIR=/usr/local/lib
silent=
MYSQLSERVER=`hostname`
ROOTLOGIN=root
ROOTPWD=NONE
wROOTPWD=
ADMINLOGIN=admin
ADMINPWD=NONE
wADMINPWD=
MYSQL_BASE_DIR=/usr/local

ac_prev=
for ac_option
do
	if test -n "$ac_prev"; then
		eval "$ac_prev=\$ac_option"
		ac_prev=
		continue
	fi

	case "$ac_option" in
		-*=*) ac_optarg=`echo "$ac_option" | sed 's/[-_a-zA-Z0-9]*=//'` ;;
		*) ac_optarg= ;;
	esac

	case "$ac_option" in
		--hostserver=*)
			MYSQLSERVER="$ac_optarg" ;;
                --rootlogin=*)
                        ROOTLOGIN="$ac_optarg" ;;
		--adminlogin=*)
			ADMINLOGIN="$ac_optarg" ;;
                --rootpwd=*)
                        ROOTPWD="$ac_optarg" 
                        wROOTPWD="-p$ac_optarg" ;;
		--adminpwd=*)
			ADMINPWD="$ac_optarg" 
			wADMINPWD="-p$ac_optarg" ;;
		--with-mysql=*)
			MYSQL_BASE_DIR="$ac_optarg";;
		-q | -quiet | --quiet | --quie | --qui | --qu | --q \
  		   | -silent | --silent | --silen | --sile | --sil)
    			silent=yes ;;
  		-help | --help | --hel | --he)
cat << EOF
Usage: updates1 [options]
Options: [defaults in brackets after descriptions]
Configuration:
  --help                   print this message
  --quiet, --silent        do not print \`checking...' messages
Directory and file names:
  --hostserver=MYSQLSERVER MySQL host server
                           [$MYSQLSERVER]
  --rootlogin=ROOTLOGIN    root login
                           [$ROOTLOGIN]
  --rootpwd=ROOTPWD        root password
                           [$ROOTPWD]
  --adminlogin=ADMINLOGIN  mywebdns administrator login
                           [$ADMINLOGIN]
  --adminpwd=ADMINPWD      mywebdns administrator password
                           [$ADMINPWD]
  --with-mysql=DIR	   Specify the MySQL installation directory.
			   [$MYSQL_BASE_DIR]
EOF
			exit 0 ;;
                -*)
                        echo "updates1: error: $ac_option: invalid option; use --help to show usage" 1>&2;
                        exit 1  
                        ;;
		*)
    			if test -n "`echo $ac_option| sed 's/[-a-z0-9.]//g'`"; then
      				echo "updates1: warning: $ac_option: invalid host type" 1>&2
    			fi
    			if test "x$nonopt" != xNONE; then
      				{ echo "updates1: error: can only updates1 for one host and one target at a time" 1>&2; exit 1; }
    			fi
    			nonopt="$ac_option"
    			;;
	esac
done
if test -n "$ac_prev"; then
	{ echo "updates1: error: missing argument to --`echo $ac_prev | sed 's/_/-/g'`" 1>&2; exit 1; }
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

COMMAND_LIST="awk wc"
for COMMAND in $COMMAND_LIST
do
	FOUND=
	ac_word=$COMMAND
	echo $ac_n "checking for $ac_word""... $ac_c" 1>&6
	IFS="${IFS=   }"; ac_save_ifs="$IFS"; IFS="${IFS}:"
	for ac_dir in $PATH; do
    		test -z "$ac_dir" && ac_dir=.
        	if test -f $ac_dir/$ac_word; then
        		FOUND="yes"
                	break
        	fi
  		IFS="$ac_save_ifs"
	done
	if test -z $FOUND; then
		echo "$ac_t""not found!" 1>&6
		exit 1
	else
		echo "$ac_t""yes" 1>&6
	fi
done

echo $ac_n "checking for MySQL directory""... $ac_c" 1>&6
if test ! -d $MYSQL_BASE_DIR; then
        echo "$ac_t""updates1: error: $MYSQL_BASE_DIR not is a valid directory!" 1>&6
	exit 1
else
        echo "$ac_t""$MYSQL_BASE_DIR" 1>&6
fi

if test -f $MYSQL_BASE_DIR/include/mysql/mysql.h; then
	MYSQL_BINDIR=$MYSQL_BASE_DIR/bin
elif test -f $MYSQL_BASE_DIR/include/mysql.h; then
	MYSQL_BINDIR=$MYSQL_BASE_DIR/bin
else
        { echo "updates1: error: Invalid MySQL directory - unable to find mysql.h under $MYSQL_BASE_DIR" 1>&2; exit 1; }
fi

echo $ac_n "checking for myWebDNS DB""... $ac_c" 1>&6
exec 2>$ERROR
EXIST=`$MYSQL_BINDIR/mysqlshow -h$MYSQLSERVER -u$ROOTLOGIN $wROOTPWD | grep $MYDB  | awk '{print $2}'`
exec 2>&1
if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
	ERROR_OUTPUT=`sed -n '1p' $ERROR`
	echo "$ac_t""updates1: error: $ERROR_OUTPUT" 1>&2
	exit 1
fi
if test -z "$EXIST"; then
        echo "$ac_t""not found!" 1>&6
else
	echo "$ac_t""found!" 1>&6
fi

echo $ac_n "checking for myWebDNS administrator""... $ac_c" 1>&6
if [ "$ADMINPWD" = "NONE" ]; then 
	PWD=
else
	PWD=$ADMINPWD
fi
exec 2>$ERROR
EXIST=`$MYSQL_BINDIR/mysql -h$MYSQLSERVER -u$ROOTLOGIN $wROOTPWD -Dmysql -e "SELECT * FROM mysql.db WHERE host='$MYSQLSERVER' AND db='$MYDB' AND user='$ADMINLOGIN';"`
exec 2>&1
if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
	ERROR_OUTPUT=`sed -n '1p' $ERROR`
	echo "$ac_t""updates1: error: $ERROR_OUTPUT" 1>&2
	exit 1
fi
exec 2>$ERROR
EXIST=`$MYSQL_BINDIR/mysql -h$MYSQLSERVER -u$ROOTLOGIN $wROOTPWD -Dmysql -e "SELECT * FROM mysql.user WHERE host='$MYSQLSERVER' AND user='$ADMINLOGIN' AND password=PASSWORD('$ADMINPWD');"`
exec 2>&1
if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
        ERROR_OUTPUT=`sed -n '1p' $ERROR`
        echo "$ac_t""updates1: error: $ERROR_OUTPUT" 1>&2
        exit 1
fi
if test -z "$EXIST"; then
	echo "$ac_t""not found!" 1>&6
	exit 1
else
        echo "$ac_t""found!" 1>&6
fi

echo $ac_n "updating myWebDNS""... $ac_c" 1>&6
exec 2>$ERROR
EXIST=`$MYSQL_BINDIR/mysql -h$MYSQLSERVER -u$ADMINLOGIN $wADMINPWD -D$MYDB -e "SHOW TABLES;"  | grep -i configuration | awk '{print $1}'`
exec 2>&1
if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
        ERROR_OUTPUT=`sed -n '1p' $ERROR`
        echo "$ac_t""updates1: error: $ERROR_OUTPUT" 1>&2
        exit 1
fi
if test -z "$EXIST"; then
	exec 2>$ERROR
	$MYSQL_BINDIR/mysql -h$MYSQLSERVER -u$ADMINLOGIN $wADMINPWD -D$MYDB -e "CREATE TABLE configuration ( \
											ID		int(2) unsigned NOT NULL auto_increment, \
											VERSION 	char(7)		NOT NULL, \
											UPDATES		int(2) unsigned	NOT NULL, \
											LANGUAGE	char(2)		NOT NULL, \
											RECORD_NS	int(2) unsigned NOT NULL, \
											RECORD_MX	int(2) unsigned NOT NULL, \
											RECORD_A	int(2) unsigned NOT NULL, \
											RECORD_PTR	int(2) unsigned NOT NULL, \
											RECORD_CNAME	int(2) unsigned NOT NULL, \
											PRIMARY KEY (ID) \
											) TYPE=MyISAM;"
	exec 2>&1
	if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
		ERROR_OUTPUT=`sed -n '1p' $ERROR`
        	echo "$ac_t""updates1: error: $ERROR_OUTPUT" 1>&2
        	exit 1
	fi
	exec 2>$ERROR
	$MYSQL_BINDIR/mysql -h$MYSQLSERVER -u$ADMINLOGIN $wADMINPWD -D$MYDB -e "INSERT INTO configuration VALUES (NULL,'1.1',11,'uk',2,2,5,5,2);"
	exec 2>&1
	if [ `cat $ERROR | wc -l | awk '{print $1}'` -gt 0 ]; then
		ERROR_OUTPUT=`sed -n '1p' $ERROR`
        	echo "$ac_t""updates1: error: $ERROR_OUTPUT" 1>&2
        	exit 1
	fi
	echo "$ac_t""done!" 1>&6
else
	echo "$ac_t""already update!" 1>&6
fi
