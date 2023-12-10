<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/css/app.css', 'resources/js/field.js'])
    
    <title>Rating</title>
</head>
<body style="background-color: #2a437c;">    
    <x-field.field-svg />

    <div class="relative w-screen">
        <div class="absolute inset-y-0 right-0 flex items-center justify-center w-1/2 h-screen">
            <div class="w-5/6 bg-gray-400 h-3/6 rounded-3xl">
                {{-- <div class="text-center cursor-pointer">
                    @if ($player->img)
                        <img src="data:image/png;base64,<?= $player->img ?>" class="w-20 h-20 rounded-full">
                    @endif
            
                    @unless($player->img)
                        <div class="w-20 h-20 bg-gray-400"></div>
                    @endunless
            
                    <p class="font-bold text-gray-100 whitespace-nowrap">{{ $player->name }}</p>
                </div> --}}
            </div>
        </div>
    </div>

    @props(['lineClass' => 'absolute flex flex-row-reverse gap-10 justify-evenly'])
    
    <div class="flex justify-center">
        @foreach($players as $line => $position)
            @if($line === 'FW')
                <div id="offense-line" class="{{ $lineClass }}">
                    @foreach($position as $player)
                        <x-field.player :player="$player" />
                    @endforeach
                </div>
            @endif

            @if($line === 'MID')
                @if(count($position['line']) === 1)
                    <div id="mid-line" class="{{ $lineClass }}">
                        @foreach($position['line'][0] as $player)
                            <x-field.player :player="$player" />
                        @endforeach
                    </div>
                @else
                    <div class="absolute flex flex-col z-index bg-violet-500">
                        @foreach($position as $index => $midPlayers)
                            <div id="mid-{{ $index + 1 }}" class="{{ $lineClass }}">
                                @foreach($midPlayers as $player)
                                    <x-field.player :player="$player" />
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            @if($line === 'DF')
                <div id="defense-line" class="{{ $lineClass }}">
                    @foreach($position as $player)
                        <x-field.player :player="$player" />
                    @endforeach
                </div>
            @endif

            @if($line === 'GK')
                <div id="keeper-line" class="{{ $lineClass }}">
                    @foreach($position as $player)
                        <x-field.player :player="$player" />
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</body>
</html>