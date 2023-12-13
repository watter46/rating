document.addEventListener('DOMContentLoaded', () => {
    const svgEl     = document.getElementById('field-svg');
    const playerEls = document.querySelectorAll('#player');

    [...playerEls].forEach(playerEl => {
        playerEl.classList.remove('hidden');
    });

    setTimeout(() => svgEl.classList.add('tilted-state'), 500);

    svgEl.classList.remove('hidden');
});