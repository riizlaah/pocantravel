<x-base-layout>
  <div class="flex min-h-screen">
    <div class="flex flex-grow justify-center items-center bg-sky-600">
      <h1 class="text-2xl font-bold text-white text-center">Selamat datang di PO CAN Travel</h1>
    </div>
    <div class="flex flex-grow justify-center items-center">
      <form action="/register" method="post" class="flex flex-col gap-4 rounded-md shadow-lg shadow-blue-200 w-2/3 p-4">
        @csrf
        <h1 class="text-xl font-bold text-center">Register</h1>
        <x-error-list />
        <label for="email" class="flex flex-col">
          Email
          <input type="email" name="email" id="email" class="rounded-sm border border-gray-200 p-1 transition-all focus:border-gray-300 outline-none" value="{{ old('email') }}" required>
        </label>
        <label for="name" class="flex flex-col">
          Nama Lengkap
          <input type="text" name="name" id="name" class="rounded-sm border border-gray-200 p-1 transition-all focus:border-gray-300 outline-none" value="{{ old('name') }}" required>
        </label>
        <label for="password">
          Password
          <div class="flex rounded-sm border border-gray-200 p-1 transition-all focus:border-gray-300 gap-1 px-1">
            <input type="password" name="password" id="password" class="flex-grow outline-none" value="{{ old('password') }}" required>
            <button type="button"></button>
          </div>
        </label>
        <label for="password2">
          Ulangi Password
          <div class="flex rounded-sm border border-gray-200 p-1 transition-all focus:border-gray-300 gap-1 px-1">
            <input type="password" name="password2" id="password2" class="flex-grow outline-none" value="{{ old('password') }}" required>
            <button type="button"></button>
          </div>
        </label>
        <label for="phone_number" class="flex flex-col">
          Nomor HP
          <input type="text" name="phone_number" id="phone_number" class="rounded-sm border border-gray-200 p-1 transition-all focus:border-gray-300 outline-none" value="{{ old('phone_number') }}">
        </label>
        <button type="submit" class="bg-sky-600 hover:bg-sky-500 transition py-2 rounded-sm w-full text-white">Daftar</button>
        <div class="flex gap-2 mx-auto">
          <span>Sudah punya akun?</span><a href="/" class="text-sky-600">Login</a>
        </div>
      </form>
    </div>
  </div>
  <script src="/js/password-toggler.js"></script>
</x-base-layout>