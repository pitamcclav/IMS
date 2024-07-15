<?php

namespace App\Jobs;

use App\Models\Staff;
use App\Models\Store;
use App\Notifications\RequestCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendRequestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $inventoryRequest;
    protected $staffId;
    protected $storeId;

    public function __construct($inventoryRequest, $staffId, $storeId)
    {
        $this->inventoryRequest = $inventoryRequest;
        $this->staffId = $staffId;
        $this->storeId = $storeId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Retrieve staff
        $staff = Staff::where('staffId', $this->staffId)->first();

        // Retrieve store
        $store = Store::find($this->storeId);

        // If store exists, retrieve manager
        if ($store) {
            $manager = Staff::where('staffId', $store->managerId)->first();
            Log::info('Sending notification to:', ['staff' => $staff, 'manager' => $manager]);

            // Prepare the notifiable users list
            $notifiableUsers = array_filter([$staff, $manager]); // Remove null values

            // Send notification if there are notifiable users
            if (!empty($notifiableUsers)) {
                Notification::send($notifiableUsers, new RequestCreatedNotification($this->inventoryRequest));
            } else {
                Log::warning('No notifiable users found.');
            }
        } else {
            Log::warning('Store not found for storeId:', ['storeId' => $this->storeId]);
        }
    }
}
