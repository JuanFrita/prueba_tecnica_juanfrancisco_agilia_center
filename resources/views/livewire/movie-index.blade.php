<div>
    <div>
        <label for="name">Nombre:</label>
        <input type="text" wire:model.live="name">
    </div>

    <div>
        <label for="categoryId">Categoria:</label>
        <select wire:model.live="categoryId">
            <option value="">Elige una opción</option>
            @foreach ($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
        </select>
    </div>

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