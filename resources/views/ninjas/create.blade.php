<x-layout>
    <form method="POST" action="{{ route('employee.store') }}" class="max-w-md mx-auto mt-10">
    @csrf
    
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="border border-gray-200 rounded p-2 w-full" value="{{ old('name') }}" required>
    @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    <label for="skill">Skill Level</label>
    <input type="number" name="skill" id="skill" class="border border-gray-200 rounded p-2 w-full" value="{{ old('skill') }}" required>
    @error('skill')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    <label for="bio">Bio</label>
    <textarea name="bio" id="bio" class="border border-gray-200 rounded p-2 w-full" required>{{ old('bio') }}</textarea>
    @error('bio')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    <lable for ="workplace_id">Workplace</lable>
    <select name="workplace_id" id="workplace_id" class="border border-gray-200 rounded p-2 w-full" required>
        <option value="">Select a workplace</option>
        @foreach($workplaces as $workplace)
            <option value="{{ $workplace->id }}" {{ old('workplace_id') == $workplace->id ? 'selected' : '' }}>{{ $workplace->name }}</option>
        @endforeach
    </select>
    @error('workplace_id')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    <button type="submit" class="bg-blue-500 text-white rounded py-2 px-4 hover:bg-blue-600">Create Ninja</button>
</x-layout>
    