<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class RequestCreatedNotification extends Notification
{
    use Queueable;

    private $request;
    public function __construct($request)
    {
        $this->request = $request;
    }


    public function via(object $notifiable): array
    {
        Log::info('Sending notification via email to:', ['email' => $notifiable->email]);
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('A new request has been created.')
            ->action('View Request', url('/requests/' . $this->request->requestId))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
