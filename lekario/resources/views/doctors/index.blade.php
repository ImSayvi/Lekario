<x-app-layout>
    @section('content')
        <h1>Lista lekarzy</h1>
        @foreach($doctors as $doctor)
            <p>{{ $doctor->name }} - {{ $doctor->specialization }}</p>
        @endforeach
    @endsection
</x-app-layout>
