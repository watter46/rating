<?php declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\UseCases\Admin\Fixture\FixturesData\FixturesData;
use App\UseCases\Admin\Fixture\Processors\FixtureInfos\FixtureInfosBuilder;


class FixtureInfosRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param FixturesData $data 
     */
    public function __construct(public FixtureInfosBuilder $builder)
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
