<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent App</title>
    @vite('resources/css/main.css')
</head>

<body>
    <div id="app" class="w-full">
        @yield('content')
    </div>
    @vite('resources/js/main.js')
</body>

</html>
