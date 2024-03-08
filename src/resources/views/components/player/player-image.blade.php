<div {{ $attributes->merge(['class' => 'flex items-center justify-center rounded-full']) }}>
    @if($img['exists'])
        <img src="{{ $img['data'] }}" class="rounded-full">
    @endif

    @if(!$img['exists'])
        <img src="{{ $img['data'] }}" class="rounded-full">
            
        <div class="absolute flex items-center justify-center">
            <p class="text-xl font-black text-white">{{ $number }}</p>
        </div>
    @endif
</div>