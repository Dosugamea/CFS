# Programmed Live! Server

Online since 2014/9/15, the two-year-old infamous SIF private server PLS (aka LLSP), now becomes open source!

Try out our public server at `svc.dash.moe`

**REQUIRE LLSIF Japanese 4.0 client, with XMS disabled** (No we will never provide or tell how to make one).

Client version 3.2 (or lower, as well as other regions' client) is NO LONGER supported.

(Note that most code of PLS is written when I just started to learn PHP, so those "legacy" code is completely in a mess...)

#Requirements

PHP 5.4 or higher

MySQL 5.5 or higher

PDO_mysql and PDO_sqlite3 module

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

#Licensing

PLS is licensed under the terms of the GNU Affero General Public License (AGPL) 3.0, available in the LICENSE file.

For those who don't know about the AGPL license:

If you modified this work AND provided service through the Internet with your modified version, then you are REQUIRED to open source your modified version under the same license.
