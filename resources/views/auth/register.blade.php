<x-layout>
    <h1>Register</h1>


<form method="POST" action="{{ route('register') }}">
    @csrf
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
    @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
    @error('email')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror   
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="{{ old('password') }}" required>
    @error('password')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
     <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" required>
    @error('password_confirmation')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    </div>
    <button type="submit">Register</button>
</form>
</x-layout>