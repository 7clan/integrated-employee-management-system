<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>
<body>
    @if (session ('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <header>
        <h1>My Application</h1>
        <nav>
            <a href="/" class="btn">Home</a>
            <a href="{{ route('employee.index') }}" class="btn">Ninjas</a>
          {{-- ✅ GLOBAL RAG CHAT (appears everywhere) --}}
@include('chat')
           @guest
            <a href="{{ route('show.register') }}" class="btn">Register</a>
            <a href="{{ route('show.login') }}" class="btn">Login</a>
              @endguest
            @auth
             <span class="gg">Hello there {{ Auth::user()->name }}</span>
             <a href="{{ route('employee.create') }}" class="btn">Create Ninja</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn">Logout</button>

            </form> 
            @endauth
        </nav>
       
    </header>
    <main class="container">
        {{ $slot }}
    </main>
</body>
</html>