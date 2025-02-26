# Here I document some problems that encountered me during the proejct development and their solutions . 

1. PDO can't be accessed from the CLI on running php Database.php
   and the error "SQLSTATE[HY000] [2002] No such file or
   directory" is returned . 

=> [Solution] Since I was using xampp, and since I mentioned in
the DSN that the host is localhost, the file should be opened
from the browser, otherwise the PDO connection won't be
established . 

Chat recommended writing localhost as 127.0.0.1 . 
Why?
localhost makes PDO use a Unix socket (which might not exist).
127.0.0.1 forces a TCP/IP connection, which is usually more
reliable.

But on reading the config file of mysql from xampp, they
mentioned this : 
> Don't listen on a TCP/IP port at all. This can be a security enhancement,
> if all processes that need to connect to mysqld run on the same host.
> All interaction with mysqld must be made via Unix sockets or named pipes.
> Note that using this option without enabling named pipes on Windows
> (via the "enable-named-pipe" option) will render mysqld useless!

What is mysqld ?
=> From chat : 
mysqld is the MySQL Server Daemon—it’s the background process (service) that runs MySQL and handles database operations.

When you start MySQL, mysqld:
- ✅ Listens for database queries from clients (PHP, MySQL CLI, Workbench, etc.)
- ✅ Manages database storage and transactions
- ✅ Handles user authentication and security
- ✅ Processes SQL queries (SELECT, INSERT, UPDATE, DELETE, etc.)

Bottom line : If you want to connect to PDO from both cli and
browser, use **127.0.0.1** as the host, otherwise use **localhost**

