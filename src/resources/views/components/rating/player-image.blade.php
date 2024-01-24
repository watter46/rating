@props([
    'util_class' => 'rounded-full cursor-pointer',
    'img_class' => $attributes['type'] === 'field'
        ? 'w-16 h-16'
        : 'w-20 h-20',
    'number_class' => $attributes['type'] === 'field'
        ? 'text-3xl'
        : 'text-3xl',
    'evaluated_class' => $isEvaluated
        ? 'border-4 border-cyan-600'
        : ''
])

<div class="flex items-center justify-center">
    @if($img['exists'])
        <img src="{{ $img['data'] }}"
            class="{{ $util_class }} {{ $img_class }} {{ $evaluated_class }}">
    @endif

    @if(!$img['exists'])
        <img src="{{ $img['data'] }}"
            class="{{ $util_class }} {{ $img_class }} {{ $evaluated_class }}">
            
        <div class="absolute flex items-center justify-center cursor-pointer">
            <p class="{{ $number_class }} font-black text-white">{{ $number }}</p>
        </div>
    @endif
</div>