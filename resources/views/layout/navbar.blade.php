
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
              <i class="fa-solid fa-bars text-3xl transition-all duration-300 rotate-0 opacity-100" id="icon-open"></i>

              <!-- X icon -->
              <i class="fa-solid fa-xmark text-3xl absolute transition-all duration-300 rotate-90 opacity-0 text-red-500" id="icon-close"></i>
          </button>

      </div>
      <div class="flex align-center my-auto md:order-3">
        <div class="">
              <a href="{{ route('login') }}" class="login-text text-white font-[Poppins] duration-500 px-6 py-2 mx-4  rounded  text-decoration-none">Login</a>
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
