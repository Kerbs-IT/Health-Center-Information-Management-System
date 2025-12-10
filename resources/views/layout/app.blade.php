<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add in the <head> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">


    <title>Barangay Health Center System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js','resources/css/homepage.css', 'resources/js/homepage.js', 'resources/css/navbar.css', 'resources/css/service-page.css', 'resources/js/navbar.js'])
</head>

<body>
    <main class="bg-light">
     @include('layout.navbar')
    @yield('content')
    </main>
    <footer>
        @include('layout.footer')
    </footer>

</body>
</html>