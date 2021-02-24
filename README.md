# Application Skeleton

___Warning: This is still work in progress and I don't recommend to use it in production___

_Feel free to create pull requests with optimalizations, new features, fixes, etc._

## Using Phalcon Framework (v4.1)

### Structure
* __app__ - There are core of system and modules
    * __core__ - Core classes using in application
    * __languages__ - Placeholder for language files
    * __modules__ - Place where all modules will be placed (more on module Structure below)
    * __views__ - There are twig/volt templates for frontend coding
* __config__ - config files for app services
    * __*.php.example__ - Example configs for each service
* __var__ - Temporary folder will be crated in first run of system
    * __cache__ - Place for compiled templates, models metadata and other cached data
    * __logs__ - Log files from app are here
    * __sessions__ - PHP Session files are here for avoid permissions on different servers
* __vendor__ - composer packages
* __www__ - DocumentRoot from webserver points here
* __.env.example__ - Environment variables go here. App uses vlucas/phpdotenv package for loading to _ENV variable

### Module Structure
* app/modules/__MODULE_NAME__
    * __Controller__ - There are controllers for module
    * __migrations__ - Migration scripts for each version of module
    * __Model__ - Models for each module.
    * __Task__ - Cli tasks for module

### CLI Tasks
* __php www/index.php core db migrate__ - Run all migration scripts to create your db. Migrations are based on module versions defined in _/config/modules.php_