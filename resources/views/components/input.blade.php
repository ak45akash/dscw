@props([
    'type' => 'text',
    'name' => null,
    'id' => null,
    'value' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<input
    type="{{ $type }}"
    @if($name) name="{{ $name }}" id="{{ $inputId }}" @endif
    @if(! is_null($value)) value="{{ old($name, $value) }}" @endif
    {{ $attributes->merge(['class' => 'form-input']) }}
/>
