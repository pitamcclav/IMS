<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $request;
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('vendor.notifications.status-changed', [
                'greeting' => 'Hello!',
                'level' => 'success', // or 'error' or any other level based on your logic
                'introLines' => ['The status of your request has changed to ' . $this->request->status . '.'],
                'actionText' => 'View Request',
                'actionUrl' => url('/requests/' . $this->request->id),
                'outroLines' => ['Thank you for using our application!'],
                'salutation' => 'Best regards,'
            ])
            ->subject('Request Status Updated');
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
