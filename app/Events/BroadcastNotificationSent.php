<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastNotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(
        public $notification
    ) {}


    public function broadcastOn()
    {
        return new Channel('notifications');
    }


    public function broadcastAs()
    {
        return 'BroadcastNotificationSent';
    }


    public function broadcastWith()
    {
        return [
            'title'=>$this->notification->title,
            'body'=>$this->notification->body,
            'type'=>$this->notification->type,
            'data'=>$this->notification->data
        ];
    }
}