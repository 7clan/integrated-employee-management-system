<x-layout>
    <h1>Ninja Details</h1>
    <p>You are viewing details for Ninja ID: {{ $ninja->name }}</p>
    <ul>
        <li>Name: {{ $ninja->name }}</li>
        <li>Skill Level: {{ $ninja->skill }}</li>
        <li>Bio: {{ $ninja->bio }}</li>
        <div>
        <li>Workplace: {{ $ninja->workplace->name }}</li>
        <li>Workplace Location: {{ $ninja->workplace->location }}</li>
       <p>Workplace Description: {{ $ninja->workplace->description }}</p>
        </div>


    </ul>
    <form method="POST" action="{{ route('employee.destroy', $ninja->id) }}" class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Delete Ninja</button>
</form>
</x-layout>