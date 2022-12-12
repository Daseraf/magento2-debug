# Magento 2 - Debug module
Module for debugging Magento 2 performance. It works without overwriting any core files and it can be installed with composer.

## Installation
1. Enable developer mode `php bin/magento deploy:mode:set developer`
2. Register git repository `composer config repositories.dbg git https://github.com/daseraf/magento2-debug.git`
3. Install module via composer `composer require daseraf/magento2-debug [--update-no-dev]`
4. Register module `php bin/magento setup:upgrade`
5. Enable profiler in configuration: `Stores -> Configuration -> Advanced -> Debug`
6. Clear cache `php bin/magento c:c`

## Enable Callmap collector
> pecl install xhprof
### Xhprof extension configuration
Just enable extension:
> extension=xhprof.so

Xhprof flags are set from the Magento admin panel
> Advanced -> Debug -> Data collectors -> Xhprof Flags

## Configuration
All settings have only default scope and config type pool is set to environment for better integration with `php bin/magento app:config:dump`

## Compatibility
* Magento 2.2 - 2.4
* PHP 7.0 - 8.1

## Profiler collectors
- Ajax
- Cache
- Config
- Customer
- Database
- Events
- Layout
- Memory
- Models
- Plugins
- Request/Response
- Performance
- Translations
- Call map - [XHprof extension required](https://www.php.net/manual/en/book.xhprof.php)

## For logging plugin performance setup this patch: 
https://github.com/Daseraf/magento2-debug/blob/master/core_patches/module-framwork.diff
    
## Additional features
- [Whoops error handler](http://filp.github.io/whoops/)

## Credits
- [Magento 2.x Web Profiler](https://github.com/clawrock/magento2-debug)
- [Magento 1.x Web Profiler](https://github.com/ecoco/magento_profiler)
- [Symfony WebProfilerBundle](https://github.com/symfony/web-profiler-bundle)
