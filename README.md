Timeouts

COMPOSER_PROCESS_TIMEOUT=2000 php composer install


# Goals:
1. Allow a developer fetch a working development copy of Myplace with a single command (i.e. install for "new" feature development)
2. Allow a developer to fetch a specific Myplace release for development (e.g. install for bug fixing)
3. Allow build manager to fetch a specific Myplace release for testing / packaging (NB: in both methods below, we assume --prefer-dist will get the plugin code without .svn metadata - this may require a much more complex repo setup)

# "Post install script" method

## Scenario 1:
    wget https://svn.strath.ac.uk/repos/moodle/core2/releases/quack/composer.json
    composer require myplace-plugin/mod-strathcom @dev
    composer install --prefer-source
    composer exec builddevenv

Result is /vendor/moodle/moodle directory containing quack moodle component, and each
plugin is symlinked to it's own plugin directory in /vendor/myplace-plugin/<pluginname>

Mod-strathcom plugin is fetched at the "trunk" version instead of the version defined in the composer.json file

## Scenario 2:

    wget https://svn.strath.ac.uk/repos/moodle/core2/releases/quack/composer.json
    composer install --prefer-source
    composer exec builddevenv

Result is /vendor/moodle/moodle directory containing quack moodle component, and each
plugin is symlinked to it's own plugin directory in /vendor/myplace-plugin/<pluginname>



## Scenario 3:
    wget https://svn.strath.ac.uk/repos/moodle/core2/releases/quack/composer.json
    composer install --prefer-dist
    composer exec package /moodle/web/

Result is a static file system containing quack Moodle component and all plugins for quack release

# "Hack composer.json" method

## Scenario 1

    svn checkout https://svn.strath.ac.uk/repos/moodle/core2/releases/quack
    cd quack
    composer install --prefer-source
    composer require myplace-plugin/mod-strathcom @dev

## Scenario 2

    svn checkout https://svn.strath.ac.uk/repos/moodle/core2/releases/quack
    cd quack
    composer install --prefer-source

## Scenario 3

    svn checkout https://svn.strath.ac.uk/repos/moodle/core2/releases/quack
    cd quack
    composer install --prefer-dist


# Managing A Build
    composer require myplace-plugin/<pluginname>

Adds a Strathclyde Myplace Plugin to the build
