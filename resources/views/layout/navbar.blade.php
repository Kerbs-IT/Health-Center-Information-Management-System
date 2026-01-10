<nav class="p-2 p-md-0 bg-neutral-primary fixed w-full z-20 top-0 border-b border-default">
  <div class="px-2 px-lg-3 lg:px-5 w-full flex flex-wrap items-center justify-between">
    <div class="flex flex-wrap align-center justify-between w-100">
      <div class=" flex items-center">
        <a href="{{ route('homepage') }}#home" class="flex items-center space-x-3 rtl:space-x-reverse text-decoration-none order-1 order-md-0">
          <img src="{{ asset('images/hugo_perez_logo.png') }}" class="h-13" alt="HugoPerez Logo" />
          <span class="logo-title self-center text-xl text-heading font-semibold whitespace-nowrap lg:block hidden ">Health Center IMS</span>
        </a>
        <button data-collapse-toggle="navbar-default" type="button"
          class="order-0 order-md-1 inline-flex items-center p-2 w-10 h-10 justify-center
              text-sm text-body rounded-base md:hidden hover:bg-neutral-secondary-soft
              hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary"
          aria-controls="navbar-default" aria-expanded="false">

          <span class="sr-only">Toggle menu</span>

          <!-- Hamburger -->
          <svg class="fa-solid fa-bars text-3xl transition-all duration-300 rotate-0 opacity-100 h-30 w-30" fill="green" id="icon-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"/></svg>

          <!-- X icon -->
           <svg class="fa-solid fa-xmark text-3xl absolute transition-all duration-300 rotate-90 opacity-0 text-red-500 h-7 w-7" id="icon-close" fill="red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M504.6 148.5C515.9 134.9 514.1 114.7 500.5 103.4C486.9 92.1 466.7 93.9 455.4 107.5L320 270L184.6 107.5C173.3 93.9 153.1 92.1 139.5 103.4C125.9 114.7 124.1 134.9 135.4 148.5L278.3 320L135.4 491.5C124.1 505.1 125.9 525.3 139.5 536.6C153.1 547.9 173.3 546.1 184.6 532.5L320 370L455.4 532.5C466.7 546.1 486.9 547.9 500.5 536.6C514.1 525.3 515.9 505.1 504.6 491.5L361.7 320L504.6 148.5z"/></svg>
        </button>

      </div>
      <div class="flex align-center my-auto md:order-3">
        <div class="d-flex">
          @guest
          <!-- User is NOT logged in -->
          <div class="nav-item">
            <a class="btn btn-primary fw-normal btn-success" href="{{ route('login') }}">Login</a>
          </div>
          @else
          <!-- User is logged in -->
          <div class="nav-item mx-1">
            <a class="btn btn-success fw-normal" href="{{ route('login') }}">Dashboard</a>
          </div>
          <div class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="btn btn-danger fw-normal">Logout</button>
            </form>
          </div>
          @endguest
        </div>
      </div>
      <div id="navbar-default" class="transition-all duration-500 ease-in-out overflow-hidden
            max-h-0 -translate-y-0 -translate-y-3 md:-translate-y-0 md:max-h-none hidden md:block w-full md:w-auto flex md:flex-row  align-center  md:order-2">
        <ul class="font-medium flex flex-wrap flex-col md:p-0 p-3  bg-neutral-secondary-soft md:flex-row  rtl:space-x-reverse  md:border-0 md:bg-neutral-primary justify-center w-100 text-center">
          <li>
            <a href="{{ route('homepage') }}#home" class="fs-5 block py-2 px-2 px-lg-3 bg-brand rounded md:bg-transparent md:text-fg-brand md:p-0" aria-current="page">Home</a>
          </li>
          <li>
            <a href="{{ route('homepage') }}#about" class="fs-5 block py-2 px-2 px-lg-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">About</a>
          </li>
          <li>
            <a href="{{ route('homepage') }}#services" class="fs-5 block py-2 px-2 px-lg-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Services</a>
          </li>
          <li>
            <a href="{{ route('homepage') }}#specialist" class="fs-5 block py-2 px-2 px-lg-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Specialist</a>
          </li>
          <li>
            <a href="{{ route('homepage') }}#faq" class="fs-5 block py-2 px-2 px-lg-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">FAQs</a>
          </li>
          <li>
            <a href="{{ route('homepage') }}#events" class="fs-5 block py-2 px-2 px-lg-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Events</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>