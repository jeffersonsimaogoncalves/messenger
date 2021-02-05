<?php

namespace RTippin\Messenger\Events;

use Illuminate\Queue\SerializesModels;
use RTippin\Messenger\Models\Message;

class MessageEditedEvent
{
    use SerializesModels;

    /**
     * @var Message
     */
    public Message $message;

    /**
     * @var string
     */
    public string $originalBody;

    /**
     * Create a new event instance.
     *
     * @param Message $message
     * @param string $originalBody
     */
    public function __construct(Message $message, string $originalBody)
    {
        $this->message = $message;
        $this->originalBody = $originalBody;
    }
}
