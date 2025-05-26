@extends('layouts.app')

@section('content')
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f72585;
            --glass-bg: rgba(255, 255, 255, 0.85);
        }

        .progress-bar {
            transition: width 1s ease-in-out;
            height: 10px;
            border-radius: 5px;
        }

        .glass-box {
            backdrop-filter: blur(12px);
            background: var(--glass-bg);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
        }

        .glass-box:hover {
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
            transform: translateY(-2px);
        }

        .animated-glow {
            animation: glow 3s infinite ease-in-out;
            background: linear-gradient(45deg, #f72585, #b5179e, #7209b7, #4361ee, #4cc9f0);
            background-size: 300% 300%;
        }

        @keyframes glow {
            0% {
                background-position: 0% 50%;
                box-shadow: 0 0 15px rgba(247, 37, 133, 0.6);
            }

            50% {
                background-position: 100% 50%;
                box-shadow: 0 0 25px rgba(72, 149, 239, 0.8);
            }

            100% {
                background-position: 0% 50%;
                box-shadow: 0 0 15px rgba(247, 37, 133, 0.6);
            }
        }

        /* Journey Visualization Styles */
        .journey-container {
            width: 90%;
            margin: 40px auto;
            position: relative;
            height: 140px;
            background: linear-gradient(to right, #f0f9ff, #e0f2fe);
            border-radius: 20px;
            overflow: visible;
            padding: 0 30px;
            box-shadow: inset 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .journey-path {
            position: absolute;
            top: 50%;
            left: 30px;
            right: 30px;
            height: 10px;
            background: #bae6fd;
            transform: translateY(-50%);
            border-radius: 5px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .journey-marker {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: white;
            border: 4px solid;
            z-index: 2;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .journey-flag {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            width: 36px;
            height: 36px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .flag-label {
            position: absolute;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 13px;
            font-weight: bold;
            color: #333;
            background: white;
            padding: 2px 8px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .avatar {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 4px solid white;
            z-index: 3;
            transition: left 1s ease-in-out;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-label {
            position: absolute;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 13px;
            font-weight: bold;
            color: #333;
            background: white;
            padding: 2px 8px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .achievement-card {
            transition: all 0.3s ease;
            border-left: 6px solid;
            border-radius: 18px;
        }

        .achievement-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .badge-icon {
            width: 60px;
            height: 60px;
            margin-right: 20px;
            transition: all 0.3s ease;
        }

        .reward-locked {
            filter: grayscale(100%);
            opacity: 0.7;
        }

        .level-indicator {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 4px solid;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .level-indicator:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .level-indicator img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .reward-image {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 16px;
            border: 3px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .reward-image:hover {
            transform: scale(1.1) rotate(5deg);
        }

        /* Chart Styles */
        .chart-container {
            background: var(--glass-bg);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            margin-bottom: 40px;
        }

        #pembelianChart {
            width: 100% !important;
            height: 350px !important;
        }

        /* Floating Elements */
        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* Neon Text */
        .neon-text {
            text-shadow: 0 0 5px rgba(67, 97, 238, 0.5),
                0 0 10px rgba(67, 97, 238, 0.4),
                0 0 15px rgba(67, 97, 238, 0.3);
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(45deg, #f72585, #7209b7, #4361ee);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .journey-container {
                height: 120px;
                padding: 0 15px;
            }

            .avatar,
            .journey-marker {
                width: 36px;
                height: 36px;
            }

            .level-indicator {
                width: 80px;
                height: 80px;
            }
        }
    </style>

    <div class="container mt-5">
        <!-- Floating Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute top-20 left-10 w-40 h-40 bg-blue-300 rounded-full filter blur-3xl opacity-20"></div>
            <div class="absolute bottom-10 right-10 w-60 h-60 bg-purple-300 rounded-full filter blur-3xl opacity-20"></div>
            <div class="absolute top-1/2 left-1/4 w-80 h-80 bg-pink-300 rounded-full filter blur-3xl opacity-15"></div>
        </div>

        <div
            class="card p-6 shadow-2xl bg-gradient-to-br from-blue-50/90 via-pink-50/90 to-yellow-50/90 border-0 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full filter blur-xl opacity-20 -mr-16 -mt-16">
            </div>
            <div
                class="absolute bottom-0 left-0 w-48 h-48 bg-purple-200 rounded-full filter blur-xl opacity-20 -ml-24 -mb-24">
            </div>

            <div class="container mx-auto px-4 relative">
                <h2 class="text-4xl font-extrabold text-center mb-8 gradient-text neon-text floating">🎯 Pencapaian Pembelian
                    Anda</h2>

                @php
                    $target_aktif =
                        $totalPembelian < $target1 ? $target1 : ($totalPembelian < $target2 ? $target2 : $target2);
                    $progress_target1 = $target1 > 0 ? ($totalPembelian / $target1) * 100 : 0;
                    $progress_target2 = $target2 > 0 ? ($totalPembelian / $target2) * 100 : 0;
                    $progress_target1 = min(100, round($progress_target1, 1));
                    $progress_target2 = min(100, round($progress_target2, 1));
                    $sisa_transaksi = max(0, $target_aktif - $totalPembelian);

                    // Calculate journey progress
                    $max_target = max($target1, $target2);
                    $journey_progress_percent = min(100, ($totalPembelian / $max_target) * 100);
                    $target1_position_percent = ($target1 / $max_target) * 100;
                    $target2_position_percent = ($target2 / $max_target) * 100;

                    $user_name = auth()->user()->name ?? 'Anda';
                @endphp

                <!-- Level Indicator -->
                <div class="text-center mb-10">
                    @if ($totalPembelian < 50000000)
                        <!-- Bronze: 0-49jt -->
                        <div class="level-indicator bg-amber-200 border-amber-400 floating">
                            <img src="{{ asset('images/bronze-medal.png') }}" alt="Bronze Badge">
                        </div>
                        <h3 class="text-2xl font-bold text-amber-700 mb-2">Bronze Shopper</h3>
                        <p class="text-gray-600 text-lg">
                            Belanjakan Rp {{ number_format(50000000 - $totalPembelian, 0, ',', '.') }} lagi untuk mencapai
                            Silver!
                        </p>
                    @elseif($totalPembelian < 100000000)
                        <!-- Silver: 50-99jt -->
                        <div class="level-indicator bg-gray-200 border-gray-400 floating">
                            <img src="{{ asset('images/silver-medal.png') }}" alt="Silver Badge">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-2">Silver Shopper</h3>
                        <p class="text-gray-600 text-lg">
                            Belanjakan Rp {{ number_format(100000000 - $totalPembelian, 0, ',', '.') }} lagi untuk mencapai
                            Gold!
                        </p>
                    @else
                        <!-- Gold: 100jt+ -->
                        <div class="level-indicator bg-yellow-200 border-yellow-400 floating">
                            <img src="{{ asset('images/gold-badge.png') }}" alt="Gold Badge">
                        </div>
                        <h3 class="text-2xl font-bold text-yellow-700 mb-2">Gold Shopper</h3>
                        <p class="text-gray-600 text-lg">Anda telah mencapai level tertinggi!</p>
                    @endif

                    <div class="text-center text-xl font-semibold text-gray-800 mt-6 mb-8">
                        Total Pembelian Anda: <span class="text-indigo-600 font-bold">Rp
                            {{ number_format($totalPembelian, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="chart-container glass-box mb-10">
                    <h3 class="text-2xl font-bold text-center mb-6 text-purple-700">📊 Grafik Pembelian Bulanan</h3>
                    <canvas id="pembelianChart"></canvas>
                </div>

                <!-- Journey Visualization -->
                <div class="journey-container mb-14">
                    <div class="journey-path"></div>

                    @php
                        $containerWidth = 90;
                        $pathStart = 30;
                        $pathEnd = 30;
                        $pathLength = $containerWidth * 10 - $pathStart - $pathEnd;

                        $userPosition = min(($totalPembelian / $max_target) * $pathLength, $pathLength);
                        $target1Position = ($target1 / $max_target) * $pathLength;
                        $target2Position = $pathLength;

                        $userPositionPercent = (($pathStart + $userPosition) / ($containerWidth * 10)) * 100;
                        $target1PositionPercent = (($pathStart + $target1Position) / ($containerWidth * 10)) * 100;
                        $target2PositionPercent = 100 - ($pathEnd / ($containerWidth * 10)) * 100;
                    @endphp

                    <!-- Avatar position -->
                    <div class="avatar" style="left: {{ $userPositionPercent }}%">
                        <img src="{{ asset('images/user.png') }}" alt="Your Progress">
                        <div class="avatar-label">
                            {{ $user_name }}<br>
                            <small class="text-xs">{{ auth()->user()->store_name ?? 'Toko Anda' }}</small>
                        </div>
                    </div>

                    <!-- Target 1 Marker -->
                    <div class="journey-marker" style="left: {{ $target1PositionPercent }}%; border-color: #4361ee;">
                        <div class="flag-label" style="top: -70px; font-size: 14px; background: #4361ee; color: white;">
                            Target 1</div>
                        <div class="journey-flag" style="background-image: url('{{ asset('images/blue-flag.png') }}')">
                            <div class="flag-label" style="top: 70px; background: #4361ee; color: white;">
                                Rp {{ number_format($target1, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Target 2 Marker -->
                    <div class="journey-marker" style="left: {{ $target2PositionPercent }}%; border-color: #f72585;">
                        <div class="flag-label" style="top: -70px; font-size: 14px; background: #f72585; color: white;">
                            Target 2</div>
                        <div class="journey-flag" style="background-image: url('{{ asset('images/gold-flag.png') }}')">
                            <div class="flag-label" style="top: 70px; background: #f72585; color: white;">
                                Rp {{ number_format($target2, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Progress Banner -->
                <div
                    class="flex items-center justify-between gap-6 text-white font-bold py-4 px-6 rounded-2xl mb-8 glass-box shadow-lg animated-glow">
                    <div class="text-center flex-1">
                        <div class="text-xl">🔥 Transaksikan <span class="font-extrabold">IDR
                                {{ number_format($sisa_transaksi, 0, ',', '.') }}</span> untuk mencapai target
                            {{ $totalPembelian < $target1 ? '1' : '2' }}</div>
                        <div class="text-sm font-normal text-white/90">dan klaim hadiah spesialmu!</div>
                        @if ($deskripsi_hadiah)
                            <p class="text-center text-md text-white/90 italic mt-2">
                                🎁 Hadiah: <strong>{{ $deskripsi_hadiah }}</strong>
                            </p>
                        @endif
                    </div>
                    @if ($gambar_hadiah)
                        <img src="{{ asset($gambar_hadiah) }}" alt="Hadiah" class="reward-image floating">
                    @else
                        <img src="{{ asset('images/reward.png') }}" alt="Hadiah" class="reward-image floating">
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 justify-items-center mb-10">
                    <!-- Target 1 Card -->
                    <div
                        class="glass-box p-8 rounded-2xl border-l-6 border-blue-500 w-full max-w-md shadow-xl achievement-card">
                        <div class="flex items-start">
                            <div class="badge-icon {{ $totalPembelian < $target1 ? 'reward-locked' : '' }}">
                                <img src="{{ asset('images/blue-flag.png') }}" alt="Target 1" class="floating">
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-blue-600 mb-3">🎯 Target 1</h4>
                                <p class="text-base text-gray-600 mb-3">IDR {{ number_format($target1, 0, ',', '.') }}</p>
                                <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden mb-3">
                                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 progress-bar"
                                        style="width: {{ $progress_target1 }}%;">
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-base text-gray-600">
                                    <span>{{ $progress_target1 }}%</span>
                                    @if ($totalPembelian >= $target1)
                                        <button
                                            class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-5 py-2 rounded-full text-sm hover:from-blue-600 hover:to-blue-800 transition shadow-md">
                                            Klaim
                                        </button>
                                    @else
                                        <span class="text-sm text-gray-500">Belum tercapai</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 italic mt-3">
                                    Reward: {{ $deskripsi_hadiah_target1 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Target 2 Card -->
                    <div
                        class="glass-box p-8 rounded-2xl border-l-6 border-purple-500 w-full max-w-md shadow-xl achievement-card">
                        <div class="flex items-start">
                            <div class="badge-icon {{ $totalPembelian < $target2 ? 'reward-locked' : '' }}">
                                <img src="{{ asset('images/gold-flag.png') }}" alt="Target 2" class="floating">
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-purple-600 mb-3">🏆 Target 2</h4>
                                <p class="text-base text-gray-600 mb-3">IDR {{ number_format($target2, 0, ',', '.') }}</p>
                                <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden mb-3">
                                    <div class="h-full bg-gradient-to-r from-purple-400 to-purple-600 progress-bar"
                                        style="width: {{ $progress_target2 }}%;">
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-base text-gray-600">
                                    <span>{{ $progress_target2 }}%</span>
                                    @if ($totalPembelian >= $target2)
                                        <button
                                            class="bg-gradient-to-r from-purple-500 to-purple-700 text-white px-5 py-2 rounded-full text-sm hover:from-purple-600 hover:to-purple-800 transition shadow-md">
                                            Klaim
                                        </button>
                                    @else
                                        <span class="text-sm text-gray-500">Belum tercapai</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 italic mt-3">
                                    Reward: {{ $deskripsi_hadiah_target2 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quest Board Section -->
                <div class="glass-box p-8 rounded-2xl border-l-6 border-pink-500 w-full max-w-4xl mx-auto shadow-xl mb-10">
                    <h3 class="text-2xl font-bold text-pink-700 mb-6">📜 Misi Pembelian Anda</h3>

                    <div class="space-y-5">
                        <div class="flex items-center p-4 bg-white/90 rounded-xl shadow-md hover:shadow-lg transition">
                            <div class="mr-5 text-3xl text-blue-500">1.</div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-800">Kumpulkan Rp
                                    {{ number_format($target1, 0, ',', '.') }}</h4>
                                <p class="text-base text-gray-600 mt-1">
                                    {{ $deskripsi_hadiah_target1 }}
                                </p>
                            </div>
                            <div class="ml-5">
                                @if ($totalPembelian >= $target1)
                                    <span
                                        class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium shadow-sm">
                                        Selesai! 🎉
                                    </span>
                                @else
                                    <span
                                        class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium shadow-sm">
                                        {{ $progress_target1 }}% Progress
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-white/90 rounded-xl shadow-md hover:shadow-lg transition">
                            <div class="mr-5 text-3xl text-purple-500">2.</div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-800">Capai Rp
                                    {{ number_format($target2, 0, ',', '.') }}
                                </h4>
                                <p class="text-base text-gray-600 mt-1">
                                    {{ $deskripsi_hadiah_target2 }}</p>
                            </div>
                            <div class="ml-5">
                                @if ($totalPembelian >= $target2)
                                    <span
                                        class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium shadow-sm">
                                        Selesai! 🎉
                                    </span>
                                @else
                                    <span
                                        class="px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-medium shadow-sm">
                                        {{ $progress_target2 }}% Progress
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('pembelianChart').getContext('2d');

            const bulanLabels = @json($bulanLabels);
            const chartData = @json(array_values($chartData));

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: bulanLabels,
                    datasets: [{
                        label: 'Total Pembelian per Bulan (Rp)',
                        data: chartData,
                        backgroundColor: 'rgba(67, 97, 238, 0.7)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(103, 114, 229, 0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    family: "'Inter', sans-serif",
                                    weight: 'bold'
                                },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ' Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
@endsection
