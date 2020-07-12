# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Unreleased]
### Added
* Deleting system users and groups now triggers a confirmation prompt
* Home directory can now be purged when deleting a system user
* Files and folders can now be deleted through the File Manager
* Files can be created in the file browser/viewer

### Fixed
* File contents no longer have whitespace and newlines trimmed when saved
* Listing a directory that doesn't exist will now show a friendly error


## [0.8.0] - 2020-02-02
### Added
* Home directories can now be created, changed and moved
* Users can now be created for projects when updating them
* You can now set or modify the default shell for users
* System GID/UID can now be enabled when creating groups/users

### Changed
* Login page is now styled to match dark mode
* Validation errors for system user's names are more specific again

### Fixed
* All users can now be removed from a group, not just some
* File browser now shows "???" when owner or group don't exist
* Bulleted user lists under group names no longer update prematurely
* Login form no longer breaks out of the layout to full page width
* Stats bar no longer polls for data after logging out


## [0.7.0] - 2019-12-31
### Added
* Files can now actually be edited and saved in the editor
* Path bar sections are now also clickable in the file viewer

### Changed
* Upgraded PHP and supporting tools to support 7.4
* Updated symfony/http-foundation to fix CVE-2019-18888

### Fixed
* Version info in footer no longer scrolls with content on long pages


## [0.6.0] - 2019-11-05
### Added
* Servidor gets Dark Mode! Toggle the theme with the light bulb
* There's now a loading spinner while files load in the viewer
* Highlight for current item in the list when editing a system user/group
* Brand-spanking new file viewer using CodeMirror for themes etc
* Databases can finally be created and listed on the Databases page
* Current branches for a Site's repo are now listed in a dropdown
* Files are now checked for a text MIME type before loading contents

### Changed
* Stats bar updated to refresh every 60s while the tab is visible
* Creating a site now opens it in the editor instead of just filtering the list

### Fixed
* Previous file no longer flickers when loading the next
* Version information is no longer shown on public pages
* Opening a user after updating its groups no longer throws an undefined error
* The rogue `:` group in the User editor on Ubuntu machines is now gone, along
   with the bugs it caused such as being unable to update the groups


## [0.5.0] - 2019-10-03
### Added
* When saving a Site you'll now see a success message
* Disk usage of the root mountpoint is now shown in Stats Bar
* Extra information added in tooltips on the stats bar items
* File browser now shows `-rwxrw-rw-` form permissions in a tooltip
* Site files can now be updated from a new details page

### Changed
* Main layout and stats bar completely redesigned

### Fixed
* Errors weren't being cleared between Site form submissions
* Icon for CPU usage in the Stats Bar no longer matches Free RAM
* Permissions weren't being loaded for hidden items in the file browser
* Site cards in the list view now use the full container width
* You can finally click 'Remember Me' to toggle the switch at Login
* Login is now enforced correctly when auth tokens change or expire
* Clearing search now works again for System users and groups
* Errors preventing System user/group creation are resolved
* Filtering Sites/Users/Groups no longer affects other pages


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


[Unreleased]: https://github.com/dshoreman/servidor/compare/v0.8.0...develop
[0.8.0]: https://github.com/dshoreman/servidor/compare/v0.7.0...v0.8.0
[0.7.0]: https://github.com/dshoreman/servidor/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/dshoreman/servidor/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/dshoreman/servidor/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/dshoreman/servidor/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/dshoreman/servidor/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/dshoreman/servidor/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/dshoreman/servidor/releases/tag/v0.1.0
