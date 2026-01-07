<script setup>
import { computed } from 'vue';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Pie } from 'vue-chartjs';

// 1. Registrasi elemen Chart.js agar bisa dirender
ChartJS.register(ArcElement, Tooltip, Legend);

// 2. Terima data projects dari halaman induk
const props = defineProps({
    projects: Array
});

// 3. Logika Menghitung Bahasa (Magic Happens Here)
const chartData = computed(() => {
    const counts = {};
    
    // Loop setiap project, hitung bahasanya
    props.projects.forEach(repo => {
        const lang = repo.language || 'Others'; // Kalau null, masukkan ke 'Others'
        counts[lang] = (counts[lang] || 0) + 1;
    });

    // Format data sesuai standar Chart.js
    return {
        labels: Object.keys(counts),
        datasets: [
            {
                backgroundColor: [
                    '#FFA500', // Orange
                    '#3b82f6', // Blue
                    '#10b981', // Emerald
                    '#8b5cf6', // Violet
                    '#f43f5e', // Rose
                    '#64748b', // Slate
                    '#eab308'  // Yellow
                ],
                data: Object.values(counts)
            }
        ]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right', // Posisi keterangan di kanan
        }
    }
};
</script>

<template>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-full flex flex-col items-center justify-center">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Top Languages</h3>
        <div class="w-full h-64" v-if="projects.length > 0">
            <Pie :data="chartData" :options="chartOptions" />
        </div>
        <p v-else class="text-gray-400 text-sm">No language data available</p>
    </div>
</template>