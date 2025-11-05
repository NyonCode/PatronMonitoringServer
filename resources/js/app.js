import * as THREE from 'three';
import NET from 'vanta/dist/vanta.net.min';

// === VANTA efekt (trojúhelníková síť v Hero) ===
document.addEventListener('DOMContentLoaded', () => {
    const hero = document.getElementById('vanta-bg');
    if (hero) {
        NET({
            el: hero,
            THREE,
            mouseControls: true,
            touchControls: true,
            minHeight: 200.00,
            minWidth: 200.00,
            scale: 1.0,
            scaleMobile: 1.0,
            color: 0x00ffff, // barva linií
            backgroundColor: 0x0b0c10, // tmavé pozadí
            points: 8.0,
            maxDistance: 25.0,
            spacing: 16.0
        });
    }
});
