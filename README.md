![alt text](https://cdn.marshmallow-office.com/media/images/logo/marshmallow.transparent.red.png "marshmallow.")

# Project usage
Deze package maakt het mogelijk om gegevens over het project naar een endpoint naar keuze te sturen zodat je statistieken bij kan houden over bijvoorbeeld hoe groot de database is, hoeveel geheugen wordt er gebruikt en welke packages worden er gebruikt.

### Installatie
```bash
composer require marshmallow/server-project-usage
```

### Usage
First publish the config or update your `.env` file with the values below.
```
PROJECT_USAGE_API_ENDPOINT=XXXXX
PROJECT_USAGE_CUSTOMER_ID=XXXXXX
PROJECT_USAGE_PROJECT_ID=XXXXXX
```

### Setup automatic updating
Add the following command to your `composer.json` so on every composer update we will update the information in your panel.

```php
"scripts": {
    "post-autoload-dump": [
        // ...
        "@php artisan marshmallow:publish-package-usage --ansi"
    ],
    // ...
}
```

### Schedule your updates
Add the command to your schedule in every project and handle the request on the endpoint in your config file to store the data that will be send.
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('marshmallow:publish-project-usage')->daily();
}
```

### Commands
```bash
# This will show the data that will be send to the endpoint
php artisan marshmallow:show-project-usage
```

```bash
# This will do a post request to the endpoint in the config
php artisan marshmallow:publish-project-usage
```

```bash
# This will only post the package information to your endpoint
php artisan marshmallow:publish-package-usage
```

### The data
Below you will find the data that will be sent to the API endpoint.
```json
{
	"customer_id": "XXXXXX",
	"project_id": "XXXXXX",
	"data": {
		"server": {
			"php_version": "7.4.8"
		},
		"database": {
			"size": 14155776,		// bytes
			"table_count": 31
		},
		"storage": {
			"root": 284579340,		// bytes
			"storage": 53010322		// bytes
		},
		"packages": {
			"composer": {
				"marshmallow/package-novastyling": "v1.1.1",
				"marshmallow/server-project-usage": "v1.0.4"
				// ...
			},
			"dependencies": {
				"doctrine/inflector": "2.0.3",
				"laravel/ui": "v2.1.0"
				// ...
			}
		}
	}
}
```
