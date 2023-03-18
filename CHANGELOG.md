# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Unreleased]


## [0.16.0] - 2023-03-18
### Added
* [accounts] Name, login email and password can now be changed from the User menu
* [installer] A new systemd unit `servidor.service` is now auto-enabled
* [projects] PHP version can now be set when creating a project
* [projects] Added support for enabling HTTPS and setting SSL certificates
* [projects] The presence or absence of the 'www.' can now optionally be forced

### Changed
* [chore] Bumped default PHP version for services to 8.1
* [chore] Upgraded Laravel framework to version 10
* [chore] PHPStan reporting increased from level 5 to 6
* [projects] App/redirect creation steps are now added to progress modal together
* [projects] Project Apps and Project Redirects are now simply Project Services

### Fixed
* [installer] Curl snippet in README now saves setup.sh to the right place
* [projects] The logs header is now correctly displayed when there are logs
* [projects] Values on the Creation form no longer fail to reset when navigating away
* [projects] Missing steps when saving project apps are now correctly added
* [projects] Progress bar no longer jumps around somewhat eratically during creation
* [projects] Deleting a project is now possible even when it has a service attached

### Removed
* PHP 8.0 is no longer supported for Servidor itself (but services can still use it)


## [0.15.3] - 2023-01-08
### Changed
* [installer] PHP 7.0-7.4 now get preinstalled, with mcrypt for 7.0-7.1

### Fixed
* [installer] Composer install would fail when `$HOME` isn't set in cloud-init
* [installer] Cleaned up syntax that prevented Pusher config being set in .env
* [projects] Adjusted permissions and ownerships to solve 404 in homedir-based projects
* [projects] Solved potential issue where code wouldn't be updated if branch was set


## [0.15.2] - 2022-12-30
### Added
* [installer] Pusher app credentials can now be set with `--pusher id:key:secret[:cluster]`


## [0.15.1] - 2022-12-27
### Fixed
* [auth] Added port to `APP_URL` when used as stateful domain to fix CSRF Token Mismatch
* [installer] Build static assets later to avoid pusher-related console errors


## [0.15.0] - 2022-12-27
### Added
* [installer] Both PHP 8.0 and 8.1 are now installed to allow local testing in either
* [projects] The `www.` subdomain can now be auto-included in `server_name`
* [projects] Fully custom target domains can now be set when creating redirects
* [projects] Redirect targets can now have `$request_uri` appended during creation
* [projects] When creating redirects, there is now a preview of the generated rule

### Fixed
* [installer] Fixed code coverage being unavailable in Vagrant
* [projects] The `sites-enabled/` symlink is now set correctly when saving projects
* [projects] Fixed 502 on PHP/Laravel projects caused by running with old PHP version 


## [0.14.0] - 2021-11-12

This is the first version with a "true" GitHub Release. For older releases, check the
[tags prior to v0.14.0](https://github.com/dshoreman/servidor/tags?after=v0.4.0).

### Added
* [projects] Renaming can now be cancelled by pressing escape in the input
* [projects] Retry is now supported with new Back/Fix buttons when creation fails
* [projects] Progress step icons are now coloured (green, grey or red) based on step status
* [projects] New 'working' `ProgressStep` state enables showing a progress spinner on start
* [projects] Source Selector now supports v-model to prevent old form values being lost
* [workflow] There's now a release action since GitHub decided to stop showing tags

### Changed
* [chore] Dropped support for PHP 7.4
* [installer] Updated to install (and use) `php8.0-` packages and fpm socket
* [projects] Project and app/redirect errors are now handled separately for better reporting

### Fixed
* [auth] Opening Servidor in a new tab no longer requires you to login again
* [installer] Fixed issue where password generation could trigger pipefail
* [projects] Fixed app/redirect not getting saved with new projects
* [projects] Resolved an issue where cancelling a rename didn't reset the name
* [projects] Logs should now always be loaded when ProjectViewer mounts
* [projects] Fixed some bad syntax that broke the 'Pull Latest Code' button
* [projects] Updated progress/completed actions to prevent premature step completion
* [projects] Tweaked progress values reported by steps to avoid reverse progress
* [projects] Updated form keys to fix 'jump to error' and field errors not showing
* [projects] Reset modal state on subsequent loads to prevent duplicate progress steps
* [projects] Prevented rename/recreate when only app/redirect has errors to avoid failure
* [projects] Fixed branch dropdrown not showing placeholder text when unset
* [projects] Prevented progress showing when clicking 'New Project' after creating a project
* [projects] Fixed an issue where projects weren't discarded if the leave handler is bypassed


## [0.13.0] - 2021-05-25
### Added
* [chore] Added phpinsights to the CI workflow. Run with `make insights`
* [database] Added new page to list tables with some basic details
* [database] Default collation, charset and table count added to databases page
* [database] Validation is now handled by its own FormRequest class
* [projects] New progress modal showing realtime creation progress

### Changed
* [database] Create and list endpoints return DatabaseData objects, not just names

### Fixed
* [database] Tests no longer create databases using the real connection
* [projects] Removed a rogue '>' from the New Project confirmation page
* [projects] Successful project creation no longer prompts to discard changes


## [0.12.0] - 2021-04-04
### Added
* Support for PHP 8.0
* Integration with Clockwork browser extension

### Changed
* [auth] Now utilises Sanctum instead of Passport
* [chore] Laravel and core framework files updated from L6 to 8.x
* [chore] Migrated CI from Travis to GitHub Actions

### Removed
* PHP 7.3 is no longer tested, Servidor now requires PHP >= 7.4

### Fixed
* [accounts] Fixed an issue where missing request data prevented registration
* [accounts] The register page was completely unstyled. Now it looks not-terrible
* [editor] Permission denied errors weren't being caught on PHP 8.x
* [files] Prevented tests failing on Ubuntu 20.04 where /bin and /lib* are symlinks
* [installer] Resolved an issue in Makefile that prevented running on GHA
* [sys-users] Clicking delete on a user would also submit the update form


## [0.11.0] - 2021-01-28
### Added
* [projects] New project creation page showing only relevant steps
* [projects] New table-based list view with template icons
* [workflow] Added husky to auto-build installer on commit when changed
* [workflow] Support for automatic hostfile config via vagrant-hostmanager

### Changed
* [editor] Improved scrollbars
* [editor] Added emphasis to trailing spaces
* [editor] Most brackets and quotes will now auto-close and highlight matches
* [editor] Pressing `>` or `/` will now auto-close tags
* [editor] Added highlight for the currently active line(s)
* [editor] Added support for smart continuation of code comments
* [projects] Attempting to set a repository will now verify it exists
* [projects] Document root and public dir are now defined in the Template
* [projects] System user creation is now fully automatic

### Removed
* [sites] All functionality relating to Sites has been replaced by Projects.  
   The old `sites` table can be removed from the database

### Fixed
* [editor] Nginx config files are now correctly detected
* [ui] Missing/invalid theme value no longer requires extra click to toggle


## [0.10.0] - 2020-10-06
### Added
* An install script! Installation is now the same across dev *and* "prod"
* Public folder can be defined separately from docroot for Laravel projects

### Changed
* Project folder paths will fall back to site name when domain isn't set

### Fixed
* Nginx sites no longer try to use the old PHP 7.3 socket
* Overflowing dropdowns in site editor don't overflow past the grid any more
* Duplicated core styles in dark mode are finally a thing of the past
* Input icons on the login page are now aligned to the correct side
* Script and style paths weren't being set based on environment
* Dark theme styles served via `npm run hot` no longer cause conflicts in light mode


## [0.9.0] - 2020-08-11
### Added
* Home directories can now be purged when deleting system users
* File Manager now supports creating, renaming and deleting dirs and files
* Site overview now supports viewing logs for the site and related services

### Changed
* Deleting system users and groups now triggers a confirmation prompt

### Fixed
* File contents no longer have whitespace and newlines trimmed when saved
* Listing a directory that doesn't exist will now show a friendly error
* Updating sites now correctly creates docroot when it doesn't already exist


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


[Unreleased]: https://github.com/dshoreman/servidor/compare/v0.16.0...develop
[0.16.0]: https://github.com/dshoreman/servidor/compare/v0.15.3...v0.16.0
[0.15.3]: https://github.com/dshoreman/servidor/compare/v0.15.2...v0.15.3
[0.15.2]: https://github.com/dshoreman/servidor/compare/v0.15.1...v0.15.2
[0.15.1]: https://github.com/dshoreman/servidor/compare/v0.15.0...v0.15.1
[0.15.0]: https://github.com/dshoreman/servidor/compare/v0.14.0...v0.15.0
[0.14.0]: https://github.com/dshoreman/servidor/compare/v0.13.0...v0.14.0
[0.13.0]: https://github.com/dshoreman/servidor/compare/v0.12.0...v0.13.0
[0.12.0]: https://github.com/dshoreman/servidor/compare/v0.11.0...v0.12.0
[0.11.0]: https://github.com/dshoreman/servidor/compare/v0.10.0...v0.11.0
[0.10.0]: https://github.com/dshoreman/servidor/compare/v0.9.0...v0.10.0
[0.9.0]: https://github.com/dshoreman/servidor/compare/v0.8.0...v0.9.0
[0.8.0]: https://github.com/dshoreman/servidor/compare/v0.7.0...v0.8.0
[0.7.0]: https://github.com/dshoreman/servidor/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/dshoreman/servidor/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/dshoreman/servidor/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/dshoreman/servidor/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/dshoreman/servidor/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/dshoreman/servidor/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/dshoreman/servidor/releases/tag/v0.1.0
