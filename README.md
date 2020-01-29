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

Currently there is basic support for projects and management of Linux users and groups. When sites are added, Servidor will
take care of cloning the repository, creating the relevant NginX configs and even reloading the web server. Starting in v0.5
you also have the ability to manually trigger a `git pull` on any given project without ever having to touch SSH.

## Installation

> **WARNING!** Servidor is not yet ready for production use!

There's no automatic installer yet but, if you do want to test it on a real server, you'll find a lot of helpful hints in
vagrant/bootstrap.sh. Otherwise, follow the [Development] instructions below to set up your local environment.

## Development

Servidor is setup to use Vagrant for development. To get started, first clone the repository and run `vagrant up`.  
The files are mounted at `/var/servidor` within the VM, so you can open the project locally with your usual editor.

Once Vagrant has finished spinning up the VM and installed everything, you'll need to install JS deps and compile assets:

```sh
# Clean-install NPM packages from the lock file
npm ci

# Compile the frontend assets for development
npm run dev
```

Alternatively, you can use `npm run watch` or `npm run hot` to have assets automatically rebuilt during development.

By default, Vagrant is configured to listen on 192.168.10.100 which you'll need to map in your hosts file:

```sh
echo '192.168.10.100 servidor.local' | sudo tee -a /etc/hosts
```

Now point your browser to http://servidor.local and use *`admin@servidor.local`* to login with the password *`servidor`*.

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
