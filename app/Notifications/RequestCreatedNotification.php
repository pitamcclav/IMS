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
            ->view('vendor.notifications.request-created', [
                'greeting' => 'Hello!',
                'request_id' => $this->request->requestId,
                'request_date' => $this->request->date,
                'request_status' => $this->request->status,
                'introLines' => ['Your request has been created successfully.'],
                'actionText' => 'View Request',
                'actionUrl' => url('/requests/' . $this->request->id),
                'outroLines' => ['Thank you for using our application!'],
                'salutation' => 'Best regards,'
            ])
            ->subject('New Request Created');
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
