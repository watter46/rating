document.addEventListener('DOMContentLoaded', () => {
    const fixtureFieldEl = document.getElementById('fixture-field');
    const startXIEls = document.querySelectorAll('#startXI');
    const substituteEls = document.querySelectorAll('#substitutes');

    const reversed = [...startXIEls].reverse();

    const animationList = ['zoomIn', 'falling', 'bounce'];

    const randomIndex = Math.floor(Math.random() * animationList.length);
    const animation   = animationList[randomIndex];
    
    const interval = 50;
    let index = 0;

    const showStartXI = () => {
        if (index < startXIEls.length) {
            reversed[index].classList.remove('hidden');
            reversed[index].classList.add(`${animation}`);

            index++;
            
            setTimeout(showStartXI, interval);
        }
    }

    const showSubstitutes = () => {
        const substitutes = [...substituteEls];

        substitutes.forEach((el) => {
            el.classList.remove('hidden');
            el.classList.add(`${animation}`);
        })
    }

    showStartXI();

    showSubstitutes();

    setTimeout(() => fixtureFieldEl.classList.add('tilted-state'), interval * 16);
    
    fixtureFieldEl.classList.remove('hidden');
});