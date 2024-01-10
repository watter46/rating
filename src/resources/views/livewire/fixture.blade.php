<div wire:click="toFixture">
    <x-score.score
        :fixture="$fixture['fixture']"
        :teams="$fixture['teams']"
        :league="$fixture['league']"
        :score="$fixture['score']"
        class="cursor-pointer" />
</div>