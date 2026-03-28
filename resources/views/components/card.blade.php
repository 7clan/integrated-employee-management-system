@props(['highlight' => false])
<div @class(['highlight'=> $highlight,'card'])class="card">
    {{ $slot }}
    <a {{ $attributes }} class="btn">Show Details</a>
</div>
