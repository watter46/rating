<svg id="{{ $id }}" {{ $attributes }} xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 74 111">
    <rect width="74" height="111" fill="#082F49"/>
    <g id="infield" fill="none" stroke="#fff"  stroke-width="0.3" transform="translate(3 3)">
        <path id="Border" d="M 0 0 h 68 v 105 h -68 Z"/>
        <path id="Centre line" d="M 0 52.5 h 68"/>
        <circle id="Centre circle" r="9.15" cx="34" cy="52.5"/>
        <circle id="Centre mark" r="0.75" cx="34" cy="52.5" fill="#fff" stroke="none"/>
        <g id="Penalty area">
            <path id="Penalty area line" d="M 13.84 0 v 16.5 h 40.32 v -16.5"/>
            <path id="Goal area line" d="M 24.84 0 v 5.5 h 18.32 v -5.5"/>
            <path id="Penalty arc" d="M 26.733027 16.5 a 9.15 9.15 0 0 0 14.533946 0"/>
        </g>
        <use xlink:href="#Penalty area" transform="rotate(180,34,52.5)"/>
        <path id="Corner arcs" d="M 0 2 a 2 2 0 0 0 2 -2M 66 0 a 2 2 0 0 0 2 2M 68 103 a 2 2 0 0 0 -2 2M 2 105 a 2 2 0 0 0 -2 -2"/>
    </g>
</svg>