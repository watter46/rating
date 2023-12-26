<div id="player" class="hidden w-full text-center" wire:ignore>
    <div class="flex justify-center">
        <div wire:click="toDetail({{ $player['id'] }})" class="player">
            <x-rating.player-image
                :number="$player['number']"
                :img="$player['img']"
                type="field" />
        </div>
    </div>

    <p class="font-black text-white whitespace-nowrap">{{ $player['name'] }}</p>

    @vite(['resources/css/player.css'])
</div>