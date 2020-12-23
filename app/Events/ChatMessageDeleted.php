<?php

namespace App\Events;

use App\Models\Dialog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    public $messageId;

    /**
     * @var Dialog
     */
    public $chat;

    /**
     * Create a new event instance.
     *
     * @param Dialog $chat
     * @param int $messageId
     */
    public function __construct(Dialog $chat, int $messageId)
    {
        $this->chat = $chat;
        $this->messageId = $messageId;
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
        return "chat_message_deleted";
    }
}
