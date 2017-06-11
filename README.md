# Custom Festival Server

Try out our public server at `www.plserver.xyz`

**REQUIRE LLSIF Japanese 5.0.2 Client, with public key modified** (No we will never provide or tell how to make one).

Client version under 5.0.2 as well as other regions' client is NO LONGER supported.

#Requirements

PHP 7.0 stable

MySQL 5.6 or higher

PDO_mysql and PDO_sqlite3 module

php5-mcrypt

# Installation
> Put all the files into web root

> Rename config.sample to config and fill out all required fields (code.php and database.php)

> Import install.sql (which frequently breaks on schema changes. If error occurs report issue immediately)

> Import data.sql, which contains song data and download data.

> Put **DECRYPTED** dbs from game data into the path spicified in config/database.php.

> Done!


# Update
Check `upgrade_scripts` folder to see if there is any schema changes. If so, import the new ones IN ORDER (see the filename).

# Troubleshooting
**Q: A Japanese message pop up saying I need to update the client version.**

A: You connected to the official server. Check your HOSTS to see if you pointed `prod-jp.lovelive.ge.klabgames.net` and `prod-2-jp.lovelive.ge.klabgames.net` to your server IP correctly. Turn off any proxies, network accelerators, etc. If you are using mobile network, please check your APN settings: If you are using CMWAP/UNIWAP/3GWAP/CTWAP, switch to CMNET/UNINET/3GNET/CTNET instead.

**Q: "服务器爆炸了 (Server Bombed)" pops out with error info xxxxxx**

A: Report issue with the error message included. The problem will be fixed ASAP.

**Q: Client crashed**

A: You may be tring to use unimplemented APIs. Also check if your client version matches the server version. If you are sure that your client version is OK and the API is implemented, please report an issue. P.S. I will appreciate it if you can provide the stack trace of the crash (can be seen with the Win32 client).

**Q: I want the latest song / update data from your public server.**

A: Contact the developer to update data.sql.

**Q: What is $code / How to decrypt xxxx.db_ file / How to remove XMS check**

A: No. We will never tell you. Disassemble the game and figure them out yourself. The $code is easy to find, and the decrpytion algorithm is not difficult too.


