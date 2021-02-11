# Servidor

[![GitHub release](https://img.shields.io/github/tag/dshoreman/servidor.svg?label=release)](https://github.com/dshoreman/servidor/releases)
[![Build Status](https://github.com/actions/servidor/workflows/build/badge.svg)](https://github.com/dshoreman/servidor/actions?query=workflow:build)
[![codecov](https://codecov.io/gh/dshoreman/servidor/branch/develop/graph/badge.svg)](https://codecov.io/gh/dshoreman/servidor)
[![Depfu](https://badges.depfu.com/badges/2c958ee33ec51367189f2762a8814dc5/count.svg)](https://depfu.com/github/dshoreman/servidor?project_id=5912)

A modern web application for managing servers. Built on Laravel, using Semantic-UI-Vue for the frontend.

Servidor is still very much a work in progress, but what has been [added so far] is mostly functional.

## Table of Contents

* [Introduction]
* [Installation]
  * [Interactive Setup]
  * [Startup Script]
* [Development]
  * [Running Tests]
* [Contributing]

## What it Does

Currently there is basic support for projects and management of Linux users and groups. When you add a site, Servidor will
take care of cloning the repository, creating the relevant NginX configs and even reloading the web server. Starting in v0.5
you also have the ability to manually trigger a `git pull` on any given project without ever having to touch SSH.

## Installation

> **NOTE: Servidor is still a work-in-progress!**  
> As such, there are some parts that likely aren't as secure as they could be, so  
> **exercise appropriate caution if you intend to use it on a public-facing server!**  
> If you find anything that can be improved, PRs are open and greatly appreciated.

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

```sh
# tl;dr:
git clone https://github.com/dshoreman/servidor.git
cd servidor && make dev-env
```

Due to memory constraints within the VM, static assets are initially built during `make dev-env`.  
To recompile assets automatically when you make changes, run `npm run hot` or `npm run watch`.

### Running Tests

With many tests relying on certain system utilities, it's best to run them in Vagrant as the web server user to avoid any issues.  
To run the PHPUnit tests, use `make test` which will automatically SSH into the Vagrant VM and run phpunit as www-data.

Other make commands are available such as `make syntax` to run other CI tools. For a complete list, check the Makefile.

## Contributing

Where possible, issues are grouped into one of various projects based on the page/section they apply to, so if you want to
find something to work on in a certain part of Servidor, then the Projects tab is a good place to start. Questions, bug reports,
ideas and PRs are all welcome and highly appreciated, so don't be afraid to ask if there's something you're not sure of!

If you use IRC, find me in *#servidor* on Freenode where I'll be happy to answer questions in a more real-time fashion.

[Introduction]: #servidor
[What it Does]: #what-it-does
[added so far]: #what-it-does
[Installation]: #installation
[Interactive Setup]: #interactive-setup
[Startup Script]: #startup-script
[Development]: #development
[Running Tests]: #running-tests
[Contributing]: #contributing
[Vagrant]: https://vagrantup.com
[vagrant-hostsupdater]: https://github.com/agiledivider/vagrant-hostsupdater#installation
