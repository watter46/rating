export class PositionedPlayers {
    #fieldRect;
    
    #FIELD_LINE_RATIO = Object.freeze({
        'offense-line': 0.20,
        'mid-line'    : 0.45,
        'defense-line': 0.66,
        'keeper-line' : 0.85
    });
    
    constructor() {
        const fieldEl = document.getElementById('infield');

        this.#fieldRect = fieldEl.getBoundingClientRect();
    }

    execute = () => {
        this.#toPosition('offense-line');
        this.#toPosition('mid-line');
        this.#toPosition('defense-line');
        this.#toPosition('keeper-line');
    }

    #toPosition = (id) => {
        const lineEl = document.getElementById(`${id}`);

        lineEl.style.top  = `${this.#calculateTop(id)}px`;
        lineEl.style.left = `${this.#calculateLeft(lineEl)}px`;
    }

    #calculateTop = (id) => {
        const top    = this.#fieldRect.top;
        const height = this.#fieldRect.height;
        
        return top + height * this.#ratio(id);
    }

    #calculateLeft = (lineEl) => {
        const fieldCenter = this.#fieldRect.width / 2;

        const lineRect  = lineEl.getBoundingClientRect();
        const lineWidth = lineRect.width;

        const offsetLineLeft = fieldCenter - lineWidth / 2;

        return this.#fieldRect.left + offsetLineLeft;
    }

    #ratio = (id) => {  
        switch (id) {
            case 'offense-line':
                return this.#FIELD_LINE_RATIO["offense-line"];

            case 'mid-line':
                return this.#FIELD_LINE_RATIO['mid-line'];

            case 'defense-line':
                return this.#FIELD_LINE_RATIO['defense-line'];
            
            case 'keeper-line':
                return this.#FIELD_LINE_RATIO['keeper-line'];
        }
    }
}