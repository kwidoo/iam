<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserLoggedIn extends ShouldBeStored implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     * @param User $user
     * @param array<string,mixed> $data
     */
    public function __construct(
        public User $user,
        public array $data,
    ) {
        //
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->uuid);
    }
}
