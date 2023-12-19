<div id="player" class="hidden w-full text-center" wire:ignore>
    <div class="flex justify-center">
        @if ($player->img)
            <img src="data:image/png;base64,<?= $player->img ?>"
                class="w-20 h-20 rounded-full cursor-pointer"
                wire:click="toDetail({{ $player->id }})">
        @endif

        @unless($player->img)
            <div class="cursor-pointer bg-sky-950"
                wire:click="toDetail({{ $player->id }})">
                <x-rating.player-default width="80" height="80" />
            </div>
        @endunless
    </div>

    <p class="font-black text-white whitespace-nowrap">{{ $player->name }}</p>
</div>