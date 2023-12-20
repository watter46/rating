document.addEventListener('DOMContentLoaded', () => {
    const svgEl     = document.getElementById('field-svg');
    const playerEls = document.querySelectorAll('#player');

    const reversed = [...playerEls].reverse();

    const animationList = ['zoomIn', 'falling', 'bounce'];

    const randomIndex = Math.floor(Math.random() * animationList.length);
    const animation   = animationList[randomIndex];
    
    const interval = 50;
    let index = 0;

    const showPlayers = () => {
        if (index < playerEls.length) {
            reversed[index].classList.remove('hidden');
            reversed[index].classList.add(`${animation}`);

            index++;
            
            setTimeout(showPlayers, interval);
        }
    }

    showPlayers();

    setTimeout(() => svgEl.classList.add('tilted-state'), interval * 11);

    svgEl.classList.remove('hidden');
});