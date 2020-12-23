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

class ChatMessageUpdated implements ShouldBroadcastNow
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
     * Create a new event instance.
     *
     * @param Dialog $chat
     * @param Message $message
     * @param User $sender
     */
    public function __construct(Dialog $chat, Message $message, User $sender)
    {
        $this->chat = $chat;
        $this->message = $message;
        $this->sender = $sender;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat.' . $this->chat->id);
    }

    /**
     * @return string
     */
    public function broadCastAs(){
        return "chat_message_updated";
    }
}
