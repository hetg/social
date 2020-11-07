# Heals Social Network
**Installation**

You need to install composer dependencies

`composer install`

Than create file .env with settings:

* _You need to configure mail settings (recommended Mailhog)_
* _You need to configure database settings_

After, you will need to generate APP key

`php artisan key:gen`

After that you need to migrate database

`php artisan migrate --seed`

Also you need to create symlinks

`php artisan storage:link`

**Hurray!!!**
