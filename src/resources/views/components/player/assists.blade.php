@if($assists)
    <div class="flex">
        @if($assists <= 3)
            @foreach(range(1, $assists) as $num)
                <div class="bg-white rounded-full" style="padding: 0.5px;">
                    <x-svg.assist-image />
                </div>
            @endforeach
        @else
            <div class="flex items-center justify-center px-0.5 bg-white rounded-full">
                <p class="pr-1 font-black text-sky-700">{{ $assists }}</p>
                <x-svg.assist-image />
            </div>
        @endif
    </div>
@endif