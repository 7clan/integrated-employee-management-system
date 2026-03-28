
    <x-layout>
    <h1>Ninja Dashboard</h1>
    <p>Welcome to the Ninja Dashboard.</p>
    

    <ul>
    <li>Ninja here</li>

    @foreach($ninjas as $ninja)
        <li>
            <x-card href="{{ route('employee.show', $ninja->id) }}" :highlight="$ninja->skill > 70">
               <div>
                {{ $ninja->name }}
                <p>{{ $ninja->workplace->name }}</p>
                </div>
            </x-card>
        </li>
    @endforeach
    </ul>
{{ $ninjas->links() }}
</x-layout>
