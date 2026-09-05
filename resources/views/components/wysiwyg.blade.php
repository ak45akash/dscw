@props([
    'name',
    'id' => null,
    'value' => '',
    'required' => false,
    'rows' => 10,
    'profile' => 'full',
])

@php
    $inputId = $id ?? $name;
    $editorConfig = [
        'id' => $inputId,
        'uploadUrl' => route('admin.editor.uploads.store'),
        'profile' => $profile,
        'csrf' => csrf_token(),
    ];
@endphp

<div
    class="wysiwyg-field"
    data-wysiwyg-engine="quill"
    x-data="wysiwygEditor(@js($editorConfig))"
>
    <div
        data-quill-host
        @class([
            'quill-host',
            'quill-host-full' => $profile === 'full',
            'quill-host-simple' => $profile !== 'full',
        ])
    ></div>

    <textarea
        name="{{ $name }}"
        id="{{ $inputId }}"
        rows="{{ $rows }}"
        class="sr-only"
        @if($required) required @endif
        {{ $attributes }}
    >{{ $value }}</textarea>

    <p data-quill-error class="mt-2 text-sm text-red-600 dark:text-red-400" hidden></p>

    <x-form-hint>
        Use the toolbar for formatting.
        @if($profile === 'full')
            The image button accepts multiple files; you can also paste images.
        @endif
    </x-form-hint>
</div>
