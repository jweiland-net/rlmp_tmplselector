.. include:: ../Includes.txt


.. _changelog:

ChangeLog
=========

**Version 3.0.0**

- Added TYPO3 9.5 compatibility
- Add new Extension Icon as SVG
- Move content of ext_tables.php to TCA/Overrides
- Convert array() to []
- Use EXT: instead of relative paths with siteRelPath (deprecated)
- Update documentation
- Convert sxw documentation to new RST based documentation
- Add configuration for Travis CI integration
- You now can use GIF,JPG,JPEG,PNG,SVG as icon file for selector. Only TS configuration (EXT:...)
- You can use FLUIDTEMPLATE as template configuration

**Version 2.2.0**

- Added TYPO3 8.7 compatibility, trimming some paths

**Version 2.1.0**

- Added TYPO3 7.6 compatibility

**Verison 2.0.0**

- Added TYPO3 6.2 compatibility

**Version 1.2.3**

- Bugfix: When creating a new page, the templates can't be selected because no page uid exists yet. Thanks to Michael Fritz for the fix.
- Added Changelog

**Some versions in between**

- not documented. Sorry.

**Version 1.1.7**

- Added the long awaited new minor features: You may now define the default filename / object number for main and subtemplates independently. Template selections may be inherited by pages deeper in the page tree (must be enabled, see reference). Provided two example sys_templates (see samples/ folder).

**Version 1.1.5**

- Bugfix: Weird that nobody sent me a complaint about this: In HTML-file mode the setting for the default template file didn't have any effect! Because I used the TS mode, I didn't realize that ... Fixed now.

**Version 1.1.3 - 1.1.4**

- Merged new languages

**Version 1.1.2**

- Bugfix: Setting templatePathMain and templatePathSub remained without any effect. Fixed that. Included the recent translations into this package.

**Version 1.1.0**

- now supports TS-only templates. The path configuration has been moved from the ExtensionManager to TypoScript.

**Version 1.0.3**

- bug fix: automatic detection of html templates didn't work at all!

**Version 1.0.2**

- published as a public extension with some minor changes

**Version 0.0.0**

- first published by Kasper Skårhøj as part of the Modern Template Building tutorial
