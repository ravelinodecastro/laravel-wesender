# Wesender for Laravel



This package allows you to use [Wesenderons](https://wesender.co.ao) with Laravel (tested in version 8) to send SMS.


## Contents

- [Installation](#installation)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

``` bash
composer require ravelino/laravel-wesender
```

### Configuration

Add your ApKey to your `.env`:

```dotenv
WESENDER_API_KEY=******************************
WESENDER_SPECIAL_CHARACTERS=true
```

### Advanced configuration

Run `php artisan vendor:publish --provider="Ravelino\Wesender\WesenderProvider"`
```
/config/wesender.php
```

## Usage

Now you can use the channel in your `via()` method inside the notification:

``` php
use Ravelino\Wesender\WesenderChannel;
use Ravelino\Wesender\WesenderSmsMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [WesenderChannel::class];
    }

    public function toWesender($notifiable)
    {
        return (new WesenderSmsMessage())
            ->content("Hello world!");
    }
}
```


In order to let your Notification know which phone are you sending, the channel will look for the `phone_number` attribute of the Notifiable model. If you want to override this behaviour, add the `routeNotificationForWesender` method to your Notifiable model.

```php
public function routeNotificationForWesender()
{
    return '+1234567890';
}
```

### Available Message methods

#### WesenderSmsMessage

- `content('')`: Accepts a string value for the notification body.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email gregoriohc@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Ravelino de Castro](https://github.com/ravelinodecastro)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
