<?php declare(strict_types=1);

namespace App\Events;

use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\UseCases\Admin\Fixture\FixturesData\FixturesData;


class FixtureInfosRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param FixturesData $data 
     */
    public function __construct(public FixtureInfosData $data)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
