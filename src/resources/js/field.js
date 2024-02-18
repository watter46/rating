document.addEventListener('DOMContentLoaded', () => {
    const interval = 50;

    showPlayers(interval, getAnimation());
    showField(interval);
});

const showField = (interval) => {    
    const fixtureFieldEls = document.querySelectorAll('#fixture-field');

    [...fixtureFieldEls].forEach((el) => {
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('tilted-state'), interval * 16);
    });
}

const showPlayers = (interval, animation) => {
    let startXIIndex = 0;

    const showStartXI = () => {
        const playerEls = document.querySelectorAll('#startXI');
        const nameEls   = document.querySelectorAll('#startXI-name');

        const reversedPlayers = [...playerEls].reverse();
        const reversedNames   = [...nameEls].reverse();
        
        if (startXIIndex < playerEls.length) {
            reversedPlayers[startXIIndex].classList.remove('hidden');
            reversedPlayers[startXIIndex].classList.add(`${animation}`);

            reversedNames[startXIIndex].classList.remove('invisible');
            reversedNames[startXIIndex].classList.add(`${animation}`);

            startXIIndex++;
            
            setTimeout(showStartXI, interval);
        }
    }

    let substitutesIndex = 0;

    const showSubstitutes = () => {
        const playerEls = document.querySelectorAll('#substitutes');
        const nameEls = document.querySelectorAll('#substitutes-name');

        const reversedPlayers = [...playerEls].reverse();
        const reversedNames   = [...nameEls].reverse();

        if (substitutesIndex < playerEls.length) {
            reversedPlayers[substitutesIndex].classList.remove('hidden');
            reversedPlayers[substitutesIndex].classList.add(`${animation}`);

            reversedNames[substitutesIndex].classList.remove('invisible');
            reversedNames[substitutesIndex].classList.add(`${animation}`);

            substitutesIndex++;
            
            setTimeout(showSubstitutes, interval);
        }
    }

    showStartXI();
    showSubstitutes();
}

const getAnimation = () => {
    const animationList = ['zoomIn'];

    const randomIndex = Math.floor(Math.random() * animationList.length);

    return animationList[randomIndex];
}