@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">


            <!-- Right Column - Top 3 Section (unchanged) -->
            <div class="lg:w-1/2 flex flex-col gap-8" style="min-height: 1000px;">
                <div class="bg-white p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-xl">
                    <h2 class="text-xl font-bold mb-6 text-center text-blue-700">🥇 Top 3 Pelanggan 🎁</h2>


                    <div class="flex flex-col items-center space-y-6">
                        <!-- Top 1 -->
                        @if (isset($top3[0]))
                            <div
                                class="w-full max-w-md bg-gradient-to-r from-yellow-400 to-yellow-200 rounded-xl p-6 text-center shadow-lg transform hover:scale-105 transition-transform duration-300 animate-bounce-slow">
                                <div class="text-3xl mb-2 animate-spin-slow">🥇</div>
                                <div class="font-bold text-lg">
                                    {{ $top3[0]->user_id == $userId ? $top3[0]->nama_toko : substr($top3[0]->nama_toko, 0, 3) . '***' }}
                                </div>
                                <div class="text-lg font-semibold mt-2">
                                    @if ($top3[0]->user_id == $userId)
                                        <span
                                            class="bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-purple-600">
                                            Rp{{ number_format($top3[0]->total_pembelian, 0, ',', '.') }}
                                        </span>
                                    @else
                                        Rp XXX
                                    @endif
                                </div>
                                <div class="mt-3 text-sm text-yellow-800 font-medium animate-pulse">🎁 Hadiah khusus untuk
                                    Top 1</div>
                            </div>
                        @endif

                        <!-- Top 2 and 3 -->
                        <div class="flex flex-col md:flex-row gap-6 w-full justify-center">
                            <!-- Top 2 -->
                            @if (isset($top3[1]))
                                <div
                                    class="bg-gradient-to-r from-gray-200 to-gray-100 rounded-lg p-4 text-center shadow-md flex-1 transform hover:scale-105 transition-transform duration-300 hover:shadow-lg">
                                    <div class="text-2xl mb-2 animate-wiggle">🥈</div>
                                    <div class="font-bold">
                                        {{ $top3[1]->user_id == $userId ? $top3[1]->nama_toko : substr($top3[1]->nama_toko, 0, 3) . '***' }}
                                    </div>
                                    <div class="text-md font-semibold mt-2">
                                        @if ($top3[1]->user_id == $userId)
                                            <span
                                                class="bg-clip-text text-transparent bg-gradient-to-r from-gray-600 to-blue-600">
                                                Rp{{ number_format($top3[1]->total_pembelian, 0, ',', '.') }}
                                            </span>
                                        @else
                                            Rp XXX
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Top 3 -->
                            <!-- Top 3 -->
                            @if (isset($top3[2]))
                                <div
                                    class="bg-gradient-to-r from-amber-700 to-amber-600 rounded-lg p-4 text-center shadow-md flex-1 transform hover:scale-105 transition-transform duration-300 hover:shadow-lg">
                                    <div class="text-2xl mb-2 animate-wiggle">🥉</div>
                                    <div class="font-bold text-white">
                                        {{ $top3[2]->user_id == $userId ? $top3[2]->nama_toko : substr($top3[2]->nama_toko, 0, 3) . '***' }}
                                    </div>
                                    <div class="text-md font-semibold mt-2 text-white">
                                        @if ($top3[2]->user_id == $userId)
                                            <span class="text-white">
                                                Rp{{ number_format($top3[2]->total_pembelian, 0, ',', '.') }}
                                            </span>
                                        @else
                                            Rp XXX
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($rewardImage)
                        <div class="mt-8 text-center space-y-4">
                            <h3
                                class="text-md font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">
                                🎁 Reward untuk Juara 1</h3>
                            <img src="{{ $rewardImage }}"
                                class="w-48 mx-auto rounded-lg shadow-lg border-2 border-yellow-400 transform hover:scale-110 transition-transform duration-300 hover:shadow-xl hover:border-yellow-500"
                                alt="Reward Image">
                        </div>
                    @endif
                </div>

                <!-- Bottom Section - Top 3 Vertical Chart -->
                <div class="bg-white p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-xl">
                    <h2
                        class="text-xl font-bold mb-6 text-center text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-blue-600">
                        📊 Leaderboard Top 3
                    </h2>
                    <div class="relative h-[300px]">
                        <canvas id="top3Chart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Left Column - Leaderboard for ranks 4+ (horizontal bars) -->
            <div class="lg:w-1/2 bg-white p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-xl"
                style="min-height: 1000px;">
                <h2
                    class="text-xl font-bold mb-2 text-center text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 animate-pulse">
                    🏆 Leaderboard Pembelian (Rank 4+)
                </h2>
                @if (isset($userRank))
                    <p class="text-center text-sm text-gray-700 mb-4">
                        Anda berada pada urutan <span class="font-semibold text-yellow-600">{{ $userRank }}</span>,
                        lakukan pembelian untuk terus meningkatkan urutan anda.
                    </p>
                @endif

                <!-- Scrollable container for the chart -->
                <div class="relative" style="height: 1000px; overflow-y: auto;">
                    <div style="height: {{ count($generalChartData) * 40 }}px; min-height: 100%;">
                        <!-- Dynamic height based on data -->
                        <canvas id="generalLeaderboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-gradient"></script>
    <style>
        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes spin-slow {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes wiggle {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(5deg);
            }

            75% {
                transform: rotate(-5deg);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 3s infinite;
        }

        .animate-spin-slow {
            animation: spin-slow 10s linear infinite;
        }

        .animate-wiggle {
            animation: wiggle 2s ease-in-out infinite;
        }
    </style>
    <script>
        function formatNumber(value) {
            if (value >= 1000000000000) {
                return 'Rp ' + (value / 1000000000000).toFixed(1) + 't';
            }
            if (value >= 100000000) {
                return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
            }
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        }

        function getColorForAmount(amount, isCurrentUser = false) {
            if (isCurrentUser) {
                return 'rgba(255, 193, 7, 1)';
            }
            if (amount >= 100000000) return 'rgba(0, 82, 204, 0.8)';
            if (amount >= 75000000) return 'rgba(0, 113, 206, 0.8)';
            if (amount >= 50000000) return 'rgba(0, 153, 255, 0.8)';
            if (amount >= 25000000) return 'rgba(64, 191, 255, 0.8)';
            return 'rgba(173, 216, 230, 0.8)';
        }


        const generalChartData = @json($generalChartData);
        const generalCtx = document.getElementById('generalLeaderboardChart').getContext('2d');
        const userId = {{ $userId }};

        let userIndex = -1;
        generalChartData.forEach((data, index) => {
            if (data.user_id === userId) {
                userIndex = index;
            }
        });

        const backgroundColors = generalChartData.map((data, index) =>
            data.user_id === userId ? 'rgba(255, 193, 7, 0.9)' : getColorForAmount(data.total)
        );

        const barValuePlugin = {
            id: 'barValuePlugin',
            afterDraw: (chart) => {
                const ctx = chart.ctx;
                ctx.font = 'bold 14px Arial';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';

                chart.data.datasets.forEach((dataset, datasetIndex) => {
                    const meta = chart.getDatasetMeta(datasetIndex);
                    meta.data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        const formattedValue = formatNumber(value);
                        const isCurrentUser = generalChartData[index].user_id === userId;

                        const valueX = bar.x + 10;
                        const textY = bar.y;

                        ctx.fillStyle = isCurrentUser ? '#000000' : '#2d3748';
                        ctx.font = isCurrentUser ? 'bold 14px Arial' : '14px Arial';
                        ctx.fillText(formattedValue, valueX, textY);

                        if (isCurrentUser) {
                            ctx.shadowColor = 'rgba(255, 193, 7, 0.7)';
                            ctx.shadowBlur = 15;
                            ctx.shadowOffsetX = 0;
                            ctx.shadowOffsetY = 0;

                            ctx.strokeStyle = 'rgba(255, 152, 0, 1)';
                            ctx.lineWidth = 3;
                            ctx.shadowColor = 'transparent';
                        }
                    });
                });
            }
        };

        const chart = new Chart(generalCtx, {
            type: 'bar',
            data: {
                labels: generalChartData.map(data => data.label),
                datasets: [{
                    label: 'Total Pembelian',
                    data: generalChartData.map(data => data.total),
                    backgroundColor: backgroundColors,
                    borderColor: generalChartData.map(data => {
                        return data.user_id === userId ? 'rgba(255, 152, 0, 1)' :
                            'rgba(0, 0, 0, 0.1)';
                    }),
                    borderWidth: generalChartData.map(data => {
                        return data.user_id === userId ? 3 : 1;
                    }),
                    borderRadius: 6,
                    hoverBackgroundColor: generalChartData.map(data => {
                        return data.user_id === userId ? 'rgba(255, 193, 7, 1)' : getColorForAmount(
                            data.total, true);
                    }),
                    barThickness: 25,
                    maxBarThickness: 25
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart',
                    onComplete: () => {
                        delayed = true;
                        if (userIndex >= 0) {
                            setTimeout(() => {
                                const container = document.querySelector(
                                    '.relative[style*="height: 1000px"]');
                                const barHeight = 45;
                                const scrollPosition = userIndex * barHeight - (container.clientHeight /
                                    2) + (barHeight / 2);
                                container.scrollTo({
                                    top: scrollPosition,
                                    behavior: 'smooth'
                                });
                            }, 500);
                        }
                    },
                    delay: (context) => {
                        let delay = 0;
                        if (context.type === 'data' && context.mode === 'default') {
                            delay = context.dataIndex * 100 + context.datasetIndex * 100;
                        }
                        return delay;
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                const isCurrentUser = generalChartData[context.dataIndex].user_id === userId;
                                const prefix = isCurrentUser ? 'Anda: ' : 'Total: ';
                                return prefix + 'Rp' + new Intl.NumberFormat('id-ID').format(context.raw);
                            },
                            afterLabel: function(context) {
                                const isCurrentUser = generalChartData[context.dataIndex].user_id === userId;
                                return isCurrentUser ? '⭐ Akun Anda' : '';
                            }
                        },
                        displayColors: true,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        footerFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        padding: 10,
                        cornerRadius: 6
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 50000000,
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        },
                        grid: {
                            color: '#e5e7eb'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            mirror: false,
                            padding: 10,
                            font: function(context) {
                                if (generalChartData[context.index]?.user_id === userId) {
                                    return {
                                        weight: 'bold',
                                        size: 14
                                    };
                                }
                                return {
                                    weight: 'normal',
                                    size: 12
                                };
                            },
                            color: function(context) {
                                if (generalChartData[context.index]?.user_id === userId) {
                                    return '#000000';
                                }
                                return '#4a5568';
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 10,
                        top: 20,
                        bottom: 20
                    }
                }
            },
            plugins: [barValuePlugin]
        });

        const top3Data = @json($top3ChartData);
        const top3Ctx = document.getElementById('top3Chart').getContext('2d');
        const goldGradient = top3Ctx.createLinearGradient(0, 0, 0, 300);
        goldGradient.addColorStop(0, 'rgba(255, 215, 0, 0.9)');
        goldGradient.addColorStop(1, 'rgba(255, 165, 0, 0.9)');

        const silverGradient = top3Ctx.createLinearGradient(0, 0, 0, 300);
        silverGradient.addColorStop(0, 'rgba(192, 192, 192, 0.9)');
        silverGradient.addColorStop(1, 'rgba(169, 169, 169, 0.9)');

        const bronzeGradient = top3Ctx.createLinearGradient(0, 0, 0, 300);
        bronzeGradient.addColorStop(0, 'rgba(205, 127, 50, 0.9)');
        bronzeGradient.addColorStop(1, 'rgba(210, 105, 30, 0.9)');

        new Chart(top3Ctx, {
            type: 'bar',
            data: {
                labels: top3Data.map(data => data.label),
                datasets: [{
                    label: 'Total Pembelian',
                    data: top3Data.map(data => data.total),
                    backgroundColor: [
                        goldGradient,
                        silverGradient,
                        bronzeGradient
                    ],
                    borderColor: [
                        'rgba(255, 215, 0, 1)',
                        'rgba(192, 192, 192, 1)',
                        'rgba(205, 127, 50, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: [
                        'rgba(255, 215, 0, 1)',
                        'rgba(192, 192, 192, 1)',
                        'rgba(205, 127, 50, 1)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce',
                    onComplete: () => {
                        delayed = true;
                    },
                    delay: (context) => {
                        let delay = 0;
                        if (context.type === 'data' && context.mode === 'default') {
                            delay = context.dataIndex * 300;
                        }
                        return delay;
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        },
                        displayColors: false,
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 10,
                        cornerRadius: 6
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 50000000,
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        },
                        grid: {
                            color: '#e5e7eb'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                onHover: (event, chartElement) => {
                    if (chartElement.length) {
                        event.native.target.style.cursor = 'pointer';
                    } else {
                        event.native.target.style.cursor = 'default';
                    }
                }
            }
        });
    </script>
@endsection
