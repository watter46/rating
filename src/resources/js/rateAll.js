const selectMom = (players, index) => {
    return players.map((player, i) => {
        if (i === index) {
            player.mom = !player.mom;

            return player;
        }

        player.mom = false;

        return player;
    });
}

const getResultRatings = (players) => {
    return players.map(player => {
        return {
            id: player.id,
            mom: player.mom,
            rating: player.rating
        };
    });
}

const activeEl = (el) => {
    el.classList.remove('pointer-events-none', 'opacity-30');
}

const activeRating = (index) => {
    const el = document.getElementById(`rating-${index}`);

    activeEl(el);
}

window.selectMom = (players, index) => selectMom(players, index);
window.getResultRatings = (players) => getResultRatings(players);
window.activeEl = (el) => activeEl(el);
window.activeRating = (index) => activeRating(index);