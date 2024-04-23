<?php declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoData;


class FixtureInfoRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param FixtureData $data
     */
    public function __construct(public FixtureInfoData $data)
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
