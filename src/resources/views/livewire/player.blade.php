<div id="player" class="hidden w-full text-center" wire:ignore>
    <div class="flex justify-center">
        @if ($player->img)
            <img src="data:image/png;base64,<?= $player->img ?>"
                class="w-20 h-20 rounded-full cursor-pointer"
                wire:click="toDetail({{ $player->id }})">
        @endif

        @unless($player->img)
            <div class="w-20 h-20 bg-gray-400 rounded-full cursor-pointer"
                wire:click="toDetail({{ $player->id }})">
            </div>
        @endunless
    </div>

    <p class="font-black text-white whitespace-nowrap">{{ $player->name }}</p>
</div>