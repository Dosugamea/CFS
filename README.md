# Custom Festival Server

	
Try our public server at `prod.customfe.su`

**REQUIRE LLSIF Japanese 5.0.2 Client, with public key modified** (We will never provide or tell the method how to make one).

We only supported the JP client which above the 5.0.2

# Agreement

The project, developed by the enthusiasts of personal behavior, does not contain any commercial use. The code is closed source code, which must comply with the code confidentiality agreement. If the user uses any of the items in the other project and leaks the source code, we will close your access and request to stop using it.

# Requirements

* PHP 7.0 stable
* Maria DB 10.6 or higher / Mysql 5.7 or higher , both must including with InnoDB and MyISAM
* Curl, OpenSSL, PDO_mysql and PDO_sqlite3 module , exec , system
* php7-mcrypt
* Node.js up to 7
* Python
* 7zip(in Path)
* MiraiNoHana(in Path) `https://github.com/MikuAuahDark/MiraiNoHana`
* HonokaMiku(in Path) `https://github.com/MikuAuahDark/HonokaMiku`
* Itsudemo(in Path) `https://github.com/MikuAuahDark/Itsudemo`

# Installation

1. Put all the files into web root
2. Rename `config.sample` to `config` and fill out all required fields (`code.php` and `database.php`)
3. Import `install.sql` (which frequently breaks on schema changes. If error occurs report issue immediately)
4. Import `data.sql`, which contains song data and download data.
5. Put **DECRYPTED** dbs from game data into the path spicified in `config/database.php`.
6. Enable the PHP pathinfo() and test the database conetion.
6. Done!


# Update
Check `upgrade_scripts` folder to see if there is any schema changes. If so, import the new ones IN ORDER (see the filename).

# Troubleshooting
**Q: A Japanese message pop up saying I need to update the client version.**

A: Please check if your client version matches the server version.  

**Q: "服务器爆炸了 (Server Bombed)" pops out with error info xxxxxx**

A: Report issue with the error message included. The problem will be fixed ASAP.

**Q: Client crashed**

A: You may be tring to use unimplemented APIs. Also check if your client version matches the server version. If you are sure that your client version is OK and the API is implemented, please report an issue. P.S. I will appreciate it if you can provide the stack trace of the crash (can be seen with the Win32 client).

**Q: I want the latest song / update data from your public server.**

A: Contact the developer to update data.sql.

**Q: What is $code / How to decrypt xxxx.db_ file / How to remove XMS check**

A: No. We will never tell you that. Disassemble the game and figure them out yourself. The $code is easy to find, and the decrpytion algorithm is not difficult too.


