Authorserver
=====================

What?
-----
Authorserver is a server side connection for authoring tools of electronic schoolbooks
created in [E-Math project](http://emath.eu). The authoring is done inside
an [authoring book](https://github.com/e-math/Ebooks) and each modification is
sent to the authorserver. Before each modification the authoring book is syncronized
with the current status of the book from the server. Server takes care of locking
and authentication.

How?
----
Authorserver is very simple and written in PHP-language. It uses MySQL-database (or MariaDB)
as a storage.

Authorserver uses following programs and libraries:
* [PHP](http://php.net/)
* [Mysql](http://www.mysql.com)

An empty MySQL database for authorserver can be created with commands in
`ebook_structure.sql`.

Who?
----
The system was developed in EU-funded [E-Math -project](http://emath.eu) by
* Petri Sallasmaa

and the copyrights are owned by [Four Ferries oy](http://fourferries.fi).

License?
--------
The authorserver is licensed under
[GNU AGPL](http://www.gnu.org/licenses/agpl-3.0.html).

Important!
----------

For testing and debugging purposes, this version users passwords are saved in
clear text! This is not safe for real usage! If you use this system, you
should change the checking the passwords to use safer methods!
