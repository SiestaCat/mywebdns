#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <sys/param.h>

#include <my_global.h>
#include <my_sys.h>
#include <mysql.h>

my_bool         ip2long_init(UDF_INIT *, UDF_ARGS *, char *);
void            ip2long_deinit(UDF_INIT *);
long long       ip2long(UDF_INIT *, UDF_ARGS *, char *, char *);

my_bool         long2ip_init(UDF_INIT *, UDF_ARGS *, char *);
void            long2ip_deinit(UDF_INIT *);
char           *long2ip(UDF_INIT *, UDF_ARGS *, char *, unsigned long *, char *, char *);

my_bool         maskdot2masklong_init(UDF_INIT *, UDF_ARGS *, char *);
void            maskdot2masklong_deinit(UDF_INIT *);
long long       maskdot2masklong(UDF_INIT *, UDF_ARGS *, char *, char *);

my_bool         masklong2maskdot_init(UDF_INIT *, UDF_ARGS *, char *);
void            masklong2maskdot_deinit(UDF_INIT *);
char           *masklong2maskdot(UDF_INIT *, UDF_ARGS *, char *, unsigned long *, char *, char *);

void            dec2bin(u_long, char *);

void
dec2bin(u_long n, char *bin)
{
	u_long          r;
	r = n % 2;
	if (n >= 2)
		dec2bin(n / 2, bin);
	if (r == 1)
		bin = (char *)strcat(bin, "1");
	else
		bin = (char *)strcat(bin, "0");
	return;
}

my_bool
ip2long_init(UDF_INIT * initid, UDF_ARGS * args, char *message)
{
	if (args->arg_count != 1 || args->arg_type[0] != STRING_RESULT) {
		strcpy(message, "Illegal parameter count or mismatch type! ip2long(string)");
		return 1;
	}
	return 0;
}

void
ip2long_deinit(UDF_INIT * initid)
{
}

long long
ip2long(UDF_INIT * initid, UDF_ARGS * args, char *is_null, char *error)
{
	in_addr_t       address;

	address = inet_addr(args->args[0]);
	return ntohl(address);
}


my_bool
long2ip_init(UDF_INIT * initid, UDF_ARGS * args, char *message)
{
	if (args->arg_count != 1 || args->arg_type[0] != INT_RESULT) {
		strcpy(message, "Illegal parameter count or mismatch type! long2ip(int)");
		return 1;
	}
	return 0;
}

void
long2ip_deinit(UDF_INIT * initid)
{
}

char           *
long2ip(UDF_INIT * initid, UDF_ARGS * args, char *result, unsigned long *res_length, char *null_value, char *error)
{
	struct in_addr  myaddr;

	myaddr.s_addr = htonl(*((long long *)args->args[0]));
	sprintf(result, "%s", inet_ntoa(myaddr));
	*res_length = strlen(result);

	return result;
}

my_bool
maskdot2masklong_init(UDF_INIT * initid, UDF_ARGS * args, char *message)
{
	if (args->arg_count != 1 || args->arg_type[0] != STRING_RESULT) {
		strcpy(message, "Illegal parameter count or mismatch type! maskdot2masklong(string)");
		return 1;
	}
	return 0;
}

void
maskdot2masklong_deinit(UDF_INIT * initid)
{
}

long long
maskdot2masklong(UDF_INIT * initid, UDF_ARGS * args, char *is_null, char *error)
{
	int             dec, masklong, i;
	char            binmask[32] = "";
	in_addr_t       address;
	u_long          ipmask;

	address = inet_addr(args->args[0]);
	ipmask = ntohl(address);
	dec2bin(ipmask, binmask);
	masklong = strspn(binmask, "1");
	for (i = masklong; i < 32; i++) {
		if (binmask[i] == '1') {
			strcpy(error, "1");
		}
	}
	return ((long long)masklong);
}

my_bool
masklong2maskdot_init(UDF_INIT * initid, UDF_ARGS * args, char *message)
{
	if (args->arg_count != 1 || args->arg_type[0] != INT_RESULT) {
		strcpy(message, "Illegal parameter count or mismatch type! masklong2maskdot(int)");
		return 1;
	}
	if (((*((int *)args->args[0])) < 0) || ((*((int *)args->args[0])) > 32)) {
		strcpy(message, "Invalid netmask!");
		return 1;
	}
	return 0;
}

void
masklong2maskdot_deinit(UDF_INIT * initid)
{
}

char           *
masklong2maskdot(UDF_INIT * initid, UDF_ARGS * args, char *result, unsigned long *res_length, char *null_value, char *error)
{
	struct in_addr  myaddr;
	char            maskdot[32] = "";
	long long       masklong;
	int             iplong, i;

	iplong = *((int *)args->args[0]);
	sprintf(maskdot, "%032d", 0);
	memset(maskdot,'1',iplong);

	for (i = 0; i < 32; i++)
		masklong = 2 * masklong + (int)(maskdot[i]) - (int)'0';

	myaddr.s_addr = htonl(masklong);
	strcpy(maskdot, inet_ntoa(myaddr));
	result = &maskdot[0];
	*res_length = strlen(result);
	return (result);
}
