<x-base-layout>
  <div class="flex min-h-screen">
    <div class="flex flex-grow justify-center items-center bg-sky-600">
      <h1 class="text-2xl font-bold text-white text-center">Selamat datang di PO CAN Travel</h1>
    </div>
    <div class="flex flex-grow justify-center items-center">
      <form action="/login" method="post" class="flex flex-col gap-4 rounded-md shadow-lg shadow-blue-200 w-2/3 p-4">
        <h1 class="text-xl font-bold text-center">Login</h1>
        @csrf
        <x-error-list />
        <x-flash-message />
        <label for="email" class="flex flex-col">
          Email
          <input type="email" name="email" id="email" class="rounded-sm border border-gray-200 p-1 transition-all focus:border-gray-300 outline-none" value="{{ old('name') }}" required>
        </label>
        <label for="password">
          Password
          <div class="flex rounded-sm border border-gray-200 p-1 transition-all focus:border-gray-300 gap-1 px-1">
            <input type="password" name="password" id="password" class="flex-grow outline-none" value="{{ old('password') }}" required>
            <button type="button"></button>
          </div>
        </label>
        <button type="submit" class="bg-sky-600 hover:bg-sky-500 transition py-2 rounded-sm w-full text-white">Login</button>
        <div class="flex gap-2 mx-auto">
          <span>Belum punya akun?</span><a href="/register" class="text-sky-600">Daftar sekarang!</a>
        </div>
      </form>
    </div>
  </div>
  <script src="/js/password-toggler.js"></script>
</x-base-layout>