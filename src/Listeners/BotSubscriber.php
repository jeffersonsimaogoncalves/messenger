<?php

namespace RTippin\Messenger\Listeners;

use Illuminate\Events\Dispatcher;
use RTippin\Messenger\Events\NewMessageEvent;
use RTippin\Messenger\Facades\Messenger;
use RTippin\Messenger\Jobs\BotActionHandler;

class BotSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(NewMessageEvent::class, [BotSubscriber::class, 'newMessage']);
    }

    /**
     * @param NewMessageEvent $event
     */
    public function newMessage(NewMessageEvent $event): void
    {
        if ($this->shouldDispatch($event)) {
            $data = $event->message->setRelation('thread', $event->thread);
            Messenger::getBotSubscriber('queued')
                ? BotActionHandler::dispatch($data)->onQueue(Messenger::getBotSubscriber('channel'))
                : BotActionHandler::dispatchSync($data);
        }
    }

    /**
     * @param NewMessageEvent $event
     * @return bool
     */
    private function shouldDispatch(NewMessageEvent $event): bool
    {
        return Messenger::isBotsEnabled()
            && Messenger::getBotSubscriber('enabled')
            && $event->message->isText()
            && $event->message->owner_type !== 'bots'
            && $event->thread->hasBotsFeature();
    }
}
