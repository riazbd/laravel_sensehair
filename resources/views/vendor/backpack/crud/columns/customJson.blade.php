{{-- customJson --}}
@php
    $data = data_get($entry, $column['name']);
    $values = json_decode($data, true);
@endphp
<span>
    @foreach ($values as $key => $value)
        {{ ucfirst($key) }}: {{ $value }}<br>
    @endforeach
</span>
