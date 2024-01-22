@if($assists)
    <div class="flex justify-center -space-x-2">
        @foreach(range(1, $assists) as $num)
            <div class="{{ 'z-'.$loop->iteration * 10 }} bg-white rounded-full p-0.5">
                <x-svg.assist-image />
            </div>
        @endforeach
    </div>
@endif