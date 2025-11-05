<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        <title>Parton Monitoring System</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- Inter font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css','resources/js/app.js'])
        @livewireStyles
        <style>body{font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial}</style>
    </head>
    <body class="bg-gray-50 text-gray-800 antialiased">
        {{ $slot }}
        @livewireScripts
    </body>
</html>
