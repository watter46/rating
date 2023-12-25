<div class="flex items-center justify-center">
    @if ($img)
        <img src="{{ $img }}" width="100px" height="100px" class="rounded-full">
    @endif

    @unless($img)
        <x-rating.player-default />     
    @endunless
</div>