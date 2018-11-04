# servidor
A server manager for managing servers. Built on Laravel, using Semantic-UI-Vue for the frontend.

[![Build Status](https://travis-ci.com/dshoreman/servidor.svg?branch=develop)](https://travis-ci.com/dshoreman/servidor) [![Depfu](https://badges.depfu.com/badges/2c958ee33ec51367189f2762a8814dc5/count.svg)](https://depfu.com/github/dshoreman/servidor?project_id=5912)

## Installation
...is pretty much the same as any other Laravel app, except the user running Servidor (probably www-data) will need special access to some commands.

Run `sudo visudo` and add a line that grants passwordless access:
```
www-data ALL=(ALL)NOPASSWD:/usr/sbin/groupadd,/usr/sbin/groupmod
```

Don't forget to `composer install`, `npm run build` and all that jazz.

## Contributing
Servidor is very much a work in progress. If you'd like to help out, pull requests can be posted to the develop branch.
