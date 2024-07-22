<div {{ $attributes->merge(['class' => 'flex items-center justify-center rounded-full bg-white']) }}>
    @if($img['exists'])
        <img src="{{ asset($img['img']) }}" class="rounded-full">
    @endif

    @unless($img['exists'])
        <img src="{{ asset($img['img']) }}" class="relative rounded-full">
        <p class="absolute text-lg font-black text-white">{{ $img['number'] }}</p>
    @endunless
</div>