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

    let demoChart = null;

    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('demo-cpu-chart')?.getContext('2d');
        if (ctx && !demoChart) {
            demoChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['-25s','-20s','-15s','-10s','-5s','now'],
                    datasets: [{
                        label: 'CPU %',
                        data: [25,35,42,40,38,44],
                        tension: 0.4,
                        borderWidth: 2,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6,182,212,0.1)',
                        fill: true,
                        pointRadius: 0,
                    }],
                },
                options: {
                    animation: false,
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { x: { display: false }, y: { min: 0, max: 100 } },
                },
            });
        }
    });

    let liveChart = null;

    document.addEventListener('livewire:load', () => {
        const ctx = document.getElementById('live-cpu-chart')?.getContext('2d');
        if (ctx) {
            liveChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'CPU %', data: [], backgroundColor: [] }] },
                options: {
                    animation: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { min: 0, max: 100 } },
                },
            });
        }

        window.addEventListener('devices-updated', e => {
            if (!liveChart) return;
            const devices = e.detail.devices || [];
            liveChart.data.labels = devices.map(d => d.name);
            liveChart.data.datasets[0].data = devices.map(d => d.cpu);
            liveChart.data.datasets[0].backgroundColor = devices.map(d =>
                d.status === 'critical' ? '#ef4444' : d.status === 'warning' ? '#f59e0b' : '#06b6d4'
            );
            liveChart.update('none');
        });
    });
});
