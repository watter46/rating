import { PositionedPlayers } from "./positionedPlayers";

document.addEventListener('DOMContentLoaded', () => {
    const svgEl     = document.getElementById('field-svg');
    const playerEls = document.querySelectorAll('#player');

    [...playerEls].forEach(playerEl => {
        playerEl.classList.remove('hidden');
    });

    svgEl.classList.remove('hidden');

    setTimeout(() => svgEl.classList.add('tilted-state'), 500);

    (new PositionedPlayers).execute();
});