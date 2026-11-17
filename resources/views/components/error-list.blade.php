@if (!empty($errors->all()))
    <div class="bg-red-200 p-4 rounded-md border border-red-400">
        <ul>
            @foreach ($errors->all() as $errMsg)
                <li>{{ $errMsg }}</li>
            @endforeach
        </ul>
    </div>
@endif
