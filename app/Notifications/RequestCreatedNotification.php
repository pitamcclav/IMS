<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class RequestCreatedNotification extends Notification implements ShouldQueue
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

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Request Created')
            ->line('A new request has been created.')
            ->action('View Request', url('/requests/' . $this->request->requestId))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->requestId,
            'request_date' => $this->request->date,
            'request_status' => $this->request->status,
        ];
    }
}
