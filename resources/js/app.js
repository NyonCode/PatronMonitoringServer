// Chart.js
import Chart from 'chart.js/auto';

// Vanta (net mesh)
import * as THREE from 'three';
import NET from 'vanta/dist/vanta.net.min';

document.addEventListener('DOMContentLoaded', () => {
    // Vanta inicializace (jemné pro světlé pozadí)
    const el = document.getElementById('vanta-bg');
    if (el) {
        NET({
            el,
            THREE,
            mouseControls: true,
            touchControls: true,
            minHeight: 200.0,
            minWidth: 200.0,
            scale: 1.0,
            scaleMobile: 1.0,
            color: 0x9dd7ff,        // linky barva (světle modrá)
            backgroundColor: 0xf8fbff, // velmi světlé pozadí
            points: 8.0,
            maxDistance: 22.0,
            spacing: 20.0,
        });
    }
});
