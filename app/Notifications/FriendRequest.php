<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\FriendRequest as FriendRequestModel;
use App\Http\Resources\FriendRequest as FriendRequestResource;

class FriendRequest extends Notification
{
    use Queueable;

    private $friend_request;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FriendRequestModel $friend_request)
    {
        $this->friend_request = $friend_request;
    }

    public function broadcastType()
    {
        return 'FriendRequest';
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
        return new BroadcastMessage(collect(new FriendRequestResource($this->friend_request))->toArray());
    }

    public function toDatabase($notifiable) {
        return ['id' => $this->friend_request->id];
    }

    // /**
    //  * Get the mail representation of the notification.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return \Illuminate\Notifications\Messages\MailMessage
    //  */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    // /**
    //  * Get the array representation of the notification.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return array
    //  */
    // public function toArray($notifiable)
    // {
    //     return [
    //         //
    //     ];
    // }

}
