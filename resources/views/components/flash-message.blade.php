@if(session()->has('message'))
    <div class="bg-sky-200 p-3 rounded-md border border-sky-400">
        <span>{{ session('message') }}</span>
    </div>
@endif