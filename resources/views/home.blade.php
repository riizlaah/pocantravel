<x-base-layout>
  <div class="flex w-screen py-3 px-5 gap-4 border-b border-gray-200">
    <span class="font-semibold text-sky-700">PO CAN Travel</span>
    <div class="flex-grow"></div>
    <a href="/logout" class="font-bold text-red-500" onclick="return confirm('Yakin ingin logout?')">Logout</a>
  </div>
  {{-- @dd($latest_schedules) --}}
  <div class="mb-2">
    <h1 class="text-xl font-semibold">Halo, {{ auth()->user()->name }}!</h1>
    <span>Mau pesan tiket apa hari ini?</span>
  </div>
  <div class="flex gap-2 w-full">
    <div class="flex-grow border-e border-gray-200">
      <h2>Tiket Terbaru</h2>
      <div>
        
      </div>
    </div>
    <div class="flex-grow border-e border-gray-200">
      <h2>Tiket Yang Dibeli</h2>
      <div>
        
      </div>
    </div>
    <div class="flex-grow">
      <h2>Tiket Yang Disimpan</h2>
      <div>
        
      </div>
    </div>
  </div>
</x-base-layout>