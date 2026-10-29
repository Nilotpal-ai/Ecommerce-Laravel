@props(['title' => null])

@include('layouts.storefront', [
    'title' => $title,
    'slot'  => $slot
])