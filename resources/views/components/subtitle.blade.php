@props([
    'title' => '',
])

<p {{ $attributes->merge(['class' => 'w-full text-xl font-bold p-2']) }} ">{{ $title }}</p>
