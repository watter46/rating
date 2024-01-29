<div {{ $attributes->merge(['class' => 'flex items-center justify-center bg-orange-500 rounded-full']) }}>
    @if($img['exists'])
        <img src="{{ $img['data'] }}" class="rounded-full">
    @endif

    @if(!$img['exists'])
        <img src="{{ $img['data'] }}" class="rounded-full">
            
        <div class="absolute flex items-center justify-center">
            <p class="text-3xl font-black text-white">{{ $number }}</p>
        </div>
    @endif
</div>