# PHP document search

## How to run

Install [PGroonga](https://pgroonga.github.io/). See [install document](https://pgroonga.github.io/install/) for details.

Create PostgreSQL user `php_document_search_user` for this application:

```console
% createuser --pwprompt php_document_search_user
```

Create PostgreSQL database `php_document_search` for this application. The database is owned by the created user:

```console
% createdb --owner php_document_search_user php_document_search
```

Install PGroonga to the created database. It needs superuser privilege. You can't use the created user:

```console
% psql --command 'CREATE EXTENSION pgroonga;' php_document_search
```

Create `.env` file based on `.env.example`:

```console
% cp .env.example .env
```

You need to configure the following items in `.env`:

  * `DB_PASSWORD`: The password what you typed for `createuser`.

Setup database schema:

```console
% php artisan migrate
```

Generate PHP document. The following instructions may be broken. See
also: http://doc.php.net/phd/docs/ .

```console
% sudo pear install doc.php.net/phd
% sudo pear install doc.php.net/phd_php
% sudo pear install doc.php.net/phd_pear
% svn co https://svn.php.net/repository/phpdoc/modules/doc-ja ../phpdoc
% cd ../phpdoc
phpdoc% php doc-base/configure.php --with-lang=ja
phpdoc% phd -d doc-base/.manual.xml -P PHP -f xhtml
phpdoc% cd -
% ln -s ../../phpdoc/output/php-chunked-xhtml public/doc
```

Register PHP document:

```console
% php artisan doc:register
```

Register auto complete candidates:

```console
% php artisan term:register
```

Run server:

```console
% php artisan serve
```

Open http://localhost:8000/.

## License

[The MIT license](LICENSE.txt)
