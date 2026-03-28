<x-layout>
    <h1>Login</h1>

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
    @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="{{ old('password') }}" required>
    @error('password')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    </div>
    <button type="submit">Login</button>
</form>
</x-layout>