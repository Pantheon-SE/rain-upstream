# Composer template for Drupal projects
## Package features
### Asset packagist support
The Asset packagist package acts as a bridge between Composer and the popular NPM and Bower repositories, which catalog
thousands of useful front-end and JavaScript packages. This allows developers to easily pull in packages like DropZoneJS
and many others without requiring local Node.js to be installed.

Read more: https://lightning.acquia.com/blog/round-your-front-end-javascript-libraries-composer

### Guardr core security
Guardr is a Drupal distribution with a combination of modules and settings to enhance a Drupal application's security
and availability to meet enterprise security requirements. This project leverages Guardr's package management and
module configuration.

### Sub-profile support
A Drupal core patch has been included to add support for "Sub-profiles".

See: https://www.drupal.org/node/1356276

### Rain base profile features
The [Mediacurrent Rain base install profile](https://bitbucket.org/mediacurrent/mis_rain/) includes many of the most
common packages pre-configured for rapid site development and optional content features.

# Setup & Installation Steps

## 1. Setting up a [DDEV-Local](https://ddev.readthedocs.io/en/stable/) environment

### Install DDEV-Local on host machine
* Follow installation instructions at https://ddev.readthedocs.io/en/stable/

### Install composer on host machine
- On MacOS ```brew install composer```
- Otherwise, see instructions here https://getcomposer.org/

### Clone this project:
- `$ git clone https://bitbucket.org/mediacurrent/drupal-project.git`
- `$ cd drupal-project`

### Initialize Project:
- `$ composer install`
- `rm web/sites/default/.gitignore`

### Configure DDEV-Local
* Non-interactive configuration. Project names must be alphanumeric and/or hyphenated.
`$ ddev config --docroot=web --project-name="example" --project-type=drupal9 --webserver-type="nginx-fpm" --create-docroot`
* Interactive configuration alternative
`$ ddev config`

#### Using NFS for Better Performance
On both MacOS and Windows hosts, Docker performance can be sluggish on large projects with many directories. In those cases, it's recommended that you use DDEV's NFS volume feature to mitigate the performance losses. If you're on Linux, you shouldn't have to enable NFS due to its native support for Docker volumes.

For more information about DDEV's NFS feature, see:

- https://ddev.readthedocs.io/en/stable/users/performance/#using-nfs-to-mount-the-project-into-the-container
- https://github.com/drud/ddev/blob/master/scripts/macos_ddev_nfs_setup.sh#L41


### Start DDEV
- `$ ddev start`
* The domain is shown in the output of ddev start,  It is also available via ddev describe.

### Initialize Project:
- `$ ./scripts/hobson project:init example.ddev.site`
- `$ ddev restart`
* This command ensures the config/config.yml is in place and has the domain set.
- For continued project development, proceed to the next section: "Rename & configure sample 'mis_profile' install profile".

## 2. Configure the Site
After configuring either DDEV, complete the remaining configuration steps.

### Configure Visual Regression test urls
- Edit "tests/visual-regression/backstop.js" environments to use the correct urls for any of the local, dev, staging, or prod urls known.

### Rename & configure sample 'mis_profile' install profile
- Change this to the name of your project name ( Copy contrib/mis_profile directory to custom/profilename)
- Find and replace all instances of 'mis_profile' with your project name
- Enable desired base profile features and modules (see mis_profile.install for more instructions).
Note: This command will assist the above steps: `./scripts/hobson project:create-profile --name="example"`

### Run the build script.
- `$ ./scripts/build.sh`

This script automates the following steps:

* Runs composer install
* Ensures vagrant is available
* Starts vagrant if required
* Installs the project Drupal site

The initial pass of the build script downloads several dependencies and an intermittent internet connection will affect the initial build process.

### Compile the theme (required).
Note that the theme will not render correctly without running npm.

Follow the [rain_theme project README](https://bitbucket.org/mediacurrent/rain_theme/src/4.x/README.md) for build instructions.

### Troubleshooting
* Ensure Vagrant has provisioned without errors. Correct errors before proceeding. After vagrant provision is successful it maybe be helpful to vagrant halt && vagrant up

## 4. Logging In
* Use `ddev drush uli` to login to your local installation.

## 5. Adding the sync folder to be used with new installs
* Confirm the sync directory in settings.php. This should be done in the sites/default/settings.php file.
Example:
`
$settings['config_sync_directory'] = '../config/sync';`

* Export configuration ( drush config:export)
* Add the existing-config option to site-install in scripts/build.sh `CMD="site:build -Dexisting_config"`
* Run ./scripts/build.sh to test

## 6. Development Settings
* The ./web/sites/example.mcdev/settings.local.php contains settings for customizing the development environment.  This disables Drupal's built in caching and additionally activates sites/development.services.yml for further customizing the development environment.

# Development Workflow

* [Use Composer](https://www.drupal.org/docs/develop/using-composer/using-composer-to-manage-drupal-site-dependencies#managing-contributed) to add 3rd party dependencies and patches.
* Write custom modules, themes etc. to the ./web/ directory.
* Run `drush @example.mcdev cex` to export Drupal configuration to the profile/profilename/config/sync folder.
* Run `$ ./scripts/build.sh` before starting a new ticket. Run build.sh again to test work completed prior to submitting a pull request.

## 5. Demo Content
* TBD

## Tests

### Run coding standards tests.

*NOTE* Tests will not run until modules are in the "web/modules/custom" directory.

- phpcs - `./tests/code-sniffer.sh ./web`
- phpcbf - `./tests/code-fixer.sh ./web`

### Drupal-check custom development for 10 readiness.

*NOTE* Checks will not run until modules are in the "web/modules/custom" directory.

- `./vendor/mediacurrent/ci-tests/tests/drupal-check.sh web`

### Run BDD tests.

- `ddev . tests/behat/behat-run.sh https://example.ddev.site`

### Run phpunit tests.

- unit tests - `composer robo test:phpunit-tests`
- kernel and functional tests - `ddev composer robo test:phpunit-tests -- --filter="/Kernel|Functional/"`

### Run VRT.

* Documentation in tests/visual-regression/README.md
* Start at "Quick Start with Docker"

### Run a11y tests.

*NOTE* Requires [pa11y](https://github.com/pa11y/pa11y#command-line-interface)

- `./tests/pa11y/pa11y-review.sh https://example.ddev.site`

### OWASP Zap Baseline Scan.

- `docker run --net=ddev_default -v $(pwd):/zap/wrk/:rw -t owasp/zap2docker-weekly zap-baseline.py -d -c owasp-zap.conf -p owasp-zap-progress.json -t https://ddev-<projectname>-web`

### GrumPHP

* [GrumPHP](https://github.com/phpro/grumphp) will run some tests on code to be committed. The file grumphp.yml is used to configure.
    * Coding Standards
    * Deny committing a list of debug keywords
    * json and yaml linting
    * Composer lock file validation
    * Enlightn Security Checker

- - -

### How can I apply patches to downloaded modules?

If you need to apply patches (depending on the project being modified, a pull
request is often a better solution), you can do so with the
[composer-patches](https://github.com/cweagans/composer-patches) plugin.

To add a patch to drupal module foobar insert the patches section in the extra
section of composer.json:

```
"extra": {
    "patches": {
        "drupal/foobar": {
            "Patch description": "URL or local path to patch"
        }
    }
}
```

### How do I specify a PHP version ?

This project supports PHP 7.3 as minimum version (see Environment requirements of Drupal 9), however it's possible that a composer update will upgrade some package that will then require PHP 7.3+.

To prevent this you can add this code to specify the PHP version you want to use in the config section of composer.json:

```
"config": {
    "sort-packages": true,
    "platform": {
        "php": "7.3.19"
    }
},
```
## Additional Links
* [Project Drupal Theme Guide](https://bitbucket.org/mediacurrent/drupal-project.git/src/HEAD/web/themes/custom/project_theme/README.md?fileviewer=file-view-default)
* This repository created from [Composer template for Drupal projects](https://github.com/drupal-composer/drupal-project/blob/8.x/README.md) which has some addition information on usage.
* [Using Composer](https://www.drupal.org/docs/develop/using-composer) with Drupal.
