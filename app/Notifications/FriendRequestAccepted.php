<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Carbon\Carbon;

class FriendRequestAccepted extends Notification
{
    use Queueable;
    // friend name
    private $user_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user_name)
    {
        $this->user_name = $user_name;
    }

    public function broadcastType()
    {
        return 'FriendRequestAccepted';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable) {
        return new BroadcastMessage([
            'data' => ['name' => $this->user_name],
            'created_at' => Carbon::now()->format('H:i d-m-Y'),
            'read_at' => null
        ]);
    }

    public function toDatabase($notifiable) {
        return ['name' => $this->user_name];
    }
}
