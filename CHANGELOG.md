# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Unreleased]
### Added
* When saving a Site you'll now see a success message
* Disk usage of the root mountpoint is now shown in Stats Bar
* Extra information added in tooltips on the stats bar items

### Changed
* Main layout and stats bar completely redesigned

### Fixed
* Errors weren't being cleared between Site form submissions
* Icon for CPU usage in the Stats Bar no longer matches Free RAM
* Site cards in the list view now use the full container width
* You can finally click 'Remember Me' to toggle the switch at Login


## [0.4.0] - 2019-08-24
### Added
* Buttons to navigate between Sites and their files
* CPU usage and current free RAM added to Stats Bar

### Changed
* Icons in the File Manager are now coloured based on type (file or folder)
* Error handling when opening files in File Manager is greatly improved
* Segments in the File Browser's path bar will now jump to that path

### Fixed
* Path wouldn't update in File Manager URL while changing folders
* Domain name is now required when updating a Site


## [0.3.0] - 2019-08-13
### Added
* File Manager with basic text file viewing capability


## [0.2.0] - 2019-08-02
### Added
* This changelog!
* Support for adding/editing Sites/Applications
* Vagrant setup for easier development
* Automatic configuration of Nginx server configs
* Custom fallback "404" page for sites that aren't configured or enabled
* Global footer showing current Servidor version


## [0.1.0] - 2019-03-30
### Added
* System section with pages for managing Linux users and groups
* "Stats bar" showing system hostname and distro
* Ability to add/remove users to/from groups and vice-versa
* Basic login/logout auth stuff


[Unreleased]: https://github.com/dshoreman/servidor/compare/v0.4.0...develop
[0.4.0]: https://github.com/dshoreman/servidor/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/dshoreman/servidor/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/dshoreman/servidor/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/dshoreman/servidor/releases/tag/v0.1.0
