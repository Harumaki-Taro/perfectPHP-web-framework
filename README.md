# Perfect PHP Inproved Web Framework

## Getting stated

1. Clone this repository to an appropriate directory and set the `web` directory as the document root.
2. Rewrite `configure()` method of `config/WebApplication.php` to set up the database connection.
3. Run `composer install` and access `localhost`. If you see `No route found for /, method: get`, you have successfully installed. If you are running in a mode other than debug mode, you will see Page not found. By default, when the PHP module version is used in the implementation environment, it will be run in debug mode.
