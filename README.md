# Servidor

[![GitHub release](https://img.shields.io/github/tag/dshoreman/servidor.svg?label=release)](https://github.com/dshoreman/servidor/releases)
[![Build Status](https://travis-ci.com/dshoreman/servidor.svg?branch=develop)](https://travis-ci.com/dshoreman/servidor)
[![codecov](https://codecov.io/gh/dshoreman/servidor/branch/develop/graph/badge.svg)](https://codecov.io/gh/dshoreman/servidor)
[![Depfu](https://badges.depfu.com/badges/2c958ee33ec51367189f2762a8814dc5/count.svg)](https://depfu.com/github/dshoreman/servidor?project_id=5912)

A modern web application for managing servers. Built on Laravel, using Semantic-UI-Vue for the frontend.

Servidor is still very much a work in progress, but what has been [added so far] is mostly functional.

## Table of Contents

* [Introduction]
* [Table of Contents]
* [Installation]
* [Development]
  * [Running Tests]
* [Contributing]

## What it Does

Currently there is basic support for projects and management of Linux users and groups. When you add a site, Servidor will
take care of cloning the repository, creating the relevant NginX configs and even reloading the web server. Starting in v0.5
you also have the ability to manually trigger a `git pull` on any given project without ever having to touch SSH.

## Installation

> **WARNING!** Servidor is not yet ready for production use!

#### Interactive Setup

To install Servidor, first ensure you're logged in as root to a fresh server, then run the following in SSH:

```sh
# Save the installer first. Piping to Bash may lead to unexpected results in interactive mode
curl -sSL https://raw.githubusercontent.com/dshoreman/servidor/installer/setup.sh > /tmp/setup \
  && bash /tmp/setup.sh
```

When Servidor has finished installing, you'll see the default login credentials with links to the Servidor backend below them.
In case you don't have DNS pointing at the server yet, both IP and hostname-based links are listed.

Running locally? Follow the [Development] instructions below to set up your local test environment.

#### Startup Script

If your server provider supports startup scripts for fully automated installation, you can pipe the installer directly to bash:
```sh
#/bin/sh
curl -s https://raw.githubusercontent.com/dshoreman/servidor/installer/setup.sh | bash
```

Options can be passed to the installer by appending them after `-s --` like so:
```sh
#/bin/sh
curl -s https://raw.githubusercontent.com/dshoreman/servidor/installer/setup.sh | bash -s -- -v --branch develop
```

## Development

To get started, run `make dev-env` in the project root. Servidor relies on [Vagrant] for development, so this command  
takes care of creating the VM, running the necessary prep, and installation of Servidor within the dev environment.  
After the initial setup, standard Vagrant commands can be used to `up`, `suspend`, `reload` and so on.

Due to memory constraints, static assets are not built during install in Vagrant.  
Instead, install and build them separately once `make dev-env` has completed:

```sh
# Clean-install NPM packages from the lock file
npm ci

# Compile the frontend assets for development
npm run dev
```

Alternatively, you can use `npm run watch` or `npm run hot` to have assets automatically rebuilt during development.

By default, Servidor can be accessed at http://servidor.local:8042. If you have [vagrant-hostsupdater] or similar, this will be
mapped automatically. Alternatively you can use http://192.168.10.100:8042, or run the following to update /etc/hosts:

```sh
echo '192.168.10.100 servidor.local' | sudo tee -a /etc/hosts
```

### Running Tests

With many tests relying on certain system utilities, it's best to run them in Vagrant as the web server user to avoid any issues.  
To run the PHPUnit tests, use `make test` which will automatically SSH into the Vagrant VM and run phpunit as www-data.

Other make commands are available such as `make syntax` to run other CI tools. For a complete list, check the Makefile.

## Contributing

As noted above, Servidor is still very young. Your ideas, code and overall feedback are all highly valued, so please feel free to
open an issue or pull request - the latter should go to the develop branch. Thanks! :heart:

[Introduction]: #servidor
[Table of Contents]: #table-of-contents
[What it Does]: #what-it-does
[added so far]: #what-it-does
[Installation]: #installation
[Development]: #development
[Running Tests]: #running-tests
[Contributing]: #contributing
[Vagrant]: https://vagrantup.com
[vagrant-hostsupdater]: https://github.com/agiledivider/vagrant-hostsupdater#installation
