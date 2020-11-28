<?php

namespace Ravelino\Wesender;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Ravelino\Wesender\Exceptions\CouldNotSendNotification;

class WesenderChannel
{
    /**
     * @var Wesender
     */
    protected $wesender;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * WesenderChannel constructor.
     *
     * @param Wesender $wesender
     * @param Dispatcher $events
     */
    public function __construct(Wesender $wesender, Dispatcher $events)
    {
        $this->wesender = $wesender;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return mixed
     * @throws Exception
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $to = $this->getTo($notifiable);
            $message = $notification->toWesender($notifiable);
            $useSender = $this->canReceiveAlphanumericSender($notifiable);

            if (is_string($message)) {
                $message = new WesenderSmsMessage($message);
            }

            if (! $message instanceof WesenderMessage) {
                throw CouldNotSendNotification::invalidMessageObject($message);
            }

            return $this->wesender->sendMessage($message, $to, $useSender);
        } catch (Exception $exception) {
            $event = new NotificationFailed(
                $notifiable,
                $notification,
                'wesender',
                ['message' => $exception->getMessage(), 'exception' => $exception]
            );

            $this->events->dispatch($event);

            if ($this->wesender->config->isIgnoredErrorCode($exception->getCode())) {
                return;
            }

            throw $exception;
        }
    }

    /**
     * Get the address to send a notification to.
     *
     * @param mixed $notifiable
     *
     * @return mixed
     * @throws CouldNotSendNotification
     */
    protected function getTo($notifiable)
    {
        if ($notifiable->routeNotificationFor(self::class)) {
            return $notifiable->routeNotificationFor(self::class);
        }
        if ($notifiable->routeNotificationFor('wesender')) {
            return $notifiable->routeNotificationFor('wesender');
        }
        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }

    /**
     * Get the alphanumeric sender.
     *
     * @param $notifiable
     *
     * @return mixed|null
     * @throws CouldNotSendNotification
     */
    protected function canReceiveAlphanumericSender($notifiable)
    {
        return method_exists($notifiable, 'canReceiveAlphanumericSender') &&
            $notifiable->canReceiveAlphanumericSender();
    }
}
