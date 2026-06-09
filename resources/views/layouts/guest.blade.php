<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel Login') }} — Tabungan Paket Lebaran</title>

        {{-- Tailwind CSS CDN --}}
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            indigo: {
                                50:  '#fdf8ee',
                                100: '#faefd0',
                                200: '#f5dc9d',
                                300: '#efc263',
                                400: '#e8a63a',
                                500: '#e08c1e',
                                600: '#c56c14',
                                700: '#a34e13',
                                800: '#853e16',
                                900: '#6e3315',
                            },
                            primary: {
                                50:  '#fdf8ee',
                                100: '#faefd0',
                                200: '#f5dc9d',
                                300: '#efc263',
                                400: '#e8a63a',
                                500: '#e08c1e',
                                600: '#c56c14',
                                700: '#a34e13',
                                800: '#853e16',
                                900: '#6e3315',
                            },
                        },
                        fontFamily: {
                            sans: ['Inter', 'system-ui', 'sans-serif'],
                        },
                    }
                }
            }
        </script>

        {{-- Google Fonts --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            body { font-family: 'Inter', sans-serif; }
            .btn-primary {
                background: linear-gradient(135deg, #e08c1e 0%, #c56c14 100%);
                transition: all 0.2s ease;
            }
            .btn-primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(224, 140, 30, 0.35);
            }
        </style>
    </head>
    <body class="h-full bg-gradient-to-br from-amber-400 via-orange-500 to-red-600 text-gray-800 flex items-center justify-center relative overflow-hidden antialiased">
        
        <!-- Light decorative glows for depth -->
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent pointer-events-none"></div>

        <div class="min-h-screen w-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0 z-10 px-4">
            <div class="mb-8 text-center">
                <a href="/" class="flex flex-col items-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center text-white text-3xl shadow-2xl hover:scale-105 transition-transform duration-200">
                        🌙
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight drop-shadow-sm">Tabungan Paket Lebaran</h2>
                        <p class="text-amber-100 text-xs font-semibold tracking-wider uppercase mt-0.5 drop-shadow-sm">Back-Office Owner Portal</p>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white border border-white/20 px-8 py-8 shadow-2xl shadow-orange-950/20 rounded-3xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
