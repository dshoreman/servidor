# Servidor

[![GitHub release](https://img.shields.io/github/tag/dshoreman/servidor.svg?label=release)](https://github.com/dshoreman/servidor/releases)
[![Build Status](https://travis-ci.com/dshoreman/servidor.svg?branch=develop)](https://travis-ci.com/dshoreman/servidor)
[![Depfu](https://badges.depfu.com/badges/2c958ee33ec51367189f2762a8814dc5/count.svg)](https://depfu.com/github/dshoreman/servidor?project_id=5912)

A modern web application for managing servers.
Built on Laravel, using Semantic-UI-Vue for the frontend.

## What it Does
Servidor is still very much a work in progress, so right now? Not a great deal.

What it *can* do so far is help manage the users and groups on a Linux system.
It can create new ones, edit existing ones or even add/remove users to/from groups
and vice-versa. Oh, and it can delete them too.

## Installation

> **Note:** For development, use Apache or Nginx.
> Servidor will not be fully functional under the PHP/Artisan development servers
> due to single-threaded constraints where API requests cause PHP to hang.

Clone from git, create a database and setup your `.env` file with the applicable config, then:
```sh
composer install
php artisan migrate
npm ci
```

You'll also need to grant special access to some commands for the user (probably `www-data`) running your web server, be that Apache or Nginx.

Run `sudo visudo` and add a line that grants passwordless access:
```
www-data ALL=(ALL)NOPASSWD:/usr/sbin/groupadd,/usr/sbin/groupmod,/usr/sbin/gpasswd,/usr/sbin/groupdel,/usr/sbin/useradd,/usr/sbin/usermod,/usr/sbin/userdel
```

If this line has alarm bells ringing, you're right to think it's dangerous...
*Especially* if you're also running Wordpress or any other dodgy PHP stuff
on the same machine. Eventually it'd be nice to have some kind of daemon that handles
all these system-level parts so PHP doesn't have to go anywhere *near* `sudo`.

If you have suggestions, I'd love to hear them! Which brings us to...

## Contributing

As noted above, Servidor is still very young. Your ideas, code and overall feedback are all
highly valued, so please feel free to open an issue or pull request - the latter should
go to the develop branch. Thanks! :heart:
