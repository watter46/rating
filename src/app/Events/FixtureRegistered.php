<?php declare(strict_types=1);

namespace App\Events;

use App\Models\Fixture;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FixtureRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 試合に必要なデータがあるか判定する
     * データが欠けていればAPIを取得して保存する
     */
    public function __construct(public Fixture $model)
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
