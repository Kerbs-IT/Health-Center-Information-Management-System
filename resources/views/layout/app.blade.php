<script>
  // Apply sidebar state early to avoid flicker
  (function() {
    if (localStorage.getItem("sidebar-collapsed") === "true") {
      document.documentElement.classList.add("sidebar-collapsed");
    }
  })();
</script>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap Icons (still CDN since it’s small & convenient) -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


  <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">


  <title>Barangay Health Center System</title>
  @vite(['resources/css/app.css',
  'resources/js/app.js',
  'resources/css/homepage.css',
  'resources/js/homepage.js',
  'resources/css/navbar.css',
  'resources/js/navbar.js'])
</head>

<body>
  <main class="bg-light">
    @include('layout.navbar')
    @yield('content')
  </main>

  <footer>
    @include('layout.footer')
  </footer>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const toggler = document.querySelector(".custom-toggler");
      const navbarCollapse = document.getElementById("navbarContent");

      // Toggle active class when the collapse opens/closes
      if (navbarCollapse) {
        navbarCollapse.addEventListener("shown.bs.collapse", function() {
          toggler.classList.add("active");
        });
        navbarCollapse.addEventListener("hidden.bs.collapse", function() {
          toggler.classList.remove("active");
        });
      }

    });
  </script>
</body>

</html>