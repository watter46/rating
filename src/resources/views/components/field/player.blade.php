<div id="player" class="hidden w-20 h-20 rounded-full">
    <div class="text-center cursor-pointer" onclick="console.log('{{ $player->name }}')">
        @if ($player->img)
            <img src="data:image/png;base64,<?= $player->img ?>" class="w-20 h-20 rounded-full">
        @endif

        @unless($player->img)
            <div class="w-20 h-20 bg-gray-400"></div>
        @endunless

        <p class="font-bold text-gray-100 whitespace-nowrap">{{ $player->name }}</p>
    </div>
</div>