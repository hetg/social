<?php

namespace App\Events;

use App\Models\Dialog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Message
     */
    public $message;

    /**
     * @var Dialog
     */
    public $chat;

    /**
     * @var User
     */
    public $sender;

    /**
     * @var User
     */
    public $receiver;

    /**
     * Create a new event instance.
     *
     * @param Dialog $chat
     * @param Message $message
     * @param User $sender
     * @param User $receiver
     */
    public function __construct(Dialog $chat, Message $message, User $sender, User $receiver)
    {
        $this->chat = $chat;
        $this->message = $message;
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('new_message.'.$this->receiver->id);
    }

    /**
     * @return string
     */
    public function broadCastAs(){
        return "new_message_received";
    }
}
