<?php

declare(strict_types=1);

namespace Ravelino\Wesender\Exceptions;

use NotificationChannels\Wesender\WesenderSmsMessage;

class CouldNotSendNotification extends \Exception
{
    public static function invalidMessageObject($message): self
    {
        $className = is_object($message) ? get_class($message) : 'Unknown';

        return new static(
            "Notification was not sent. Message object class `{$className}` is invalid. It should
            be either `".WesenderSmsMessage::class);
    }

    public static function invalidReceiver(): self
    {
        return new static(
            'The notifiable did not have a receiving phone number. Add a routeNotificationForWesender
            method or a phone_number attribute to your notifiable.'
        );
    }

 
}
