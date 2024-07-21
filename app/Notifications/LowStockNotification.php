<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $item;

    public function __construct($item)
    {
        Log::info('Low stock notification', $item->toArray());
        $this->item = $item;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert')
            ->line('The stock for item ' . $this->item->itemName . ' has dropped below 3/4 of its original quantity.')
            ->action('View Inventory', url('/inventory/' . $this->item->itemId))
            ->line('Please consider restocking.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->itemId,
            'item_name' => $this->item->itemName,
            'current_quantity' => $this->item->quantity,
        ];
    }
}
