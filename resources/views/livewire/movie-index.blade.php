<div>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Nombre</th>
                <th class="px-4 py-2">Portada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movies as $movie)
            <tr>
                <td class="border px-4 py-2">{{ $movie->id }}</td>
                <td class="border px-4 py-2">{{ $movie->name }}</td>
                <td class="border px-4 py-2"><img src="{{ $movie->cover }}" height="100px" width="100px"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $movies->links() }}
    </div>
</div>