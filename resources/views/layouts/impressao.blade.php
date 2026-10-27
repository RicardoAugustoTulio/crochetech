<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $titulo ?? config('app.name', 'CrocheTech') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-white text-gray-900">
        <div class="max-w-4xl mx-auto p-6">
            <div class="no-print mb-6 flex items-center justify-between">
                <a href="{{ url()->previous() }}" class="text-rose-700 underline text-lg">&larr; Voltar</a>
                <button type="button" onclick="window.print()" class="inline-flex items-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md hover:bg-rose-800">
                    Imprimir
                </button>
            </div>

            {{ $slot }}
        </div>
    </body>
</html>
