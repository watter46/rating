<?php declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\UseCases\Fixture\FixtureDataProcessor;


class FixtureRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param FixtureDataProcessor $processor
     */
    public function __construct(public FixtureDataProcessor $processor)
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
