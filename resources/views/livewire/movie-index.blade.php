<div>
    <div class="flex w-full px-4 py-2 space-x-3">
        <div class="p-1">
            <label for="name" class="block">Nombre:</label>
            <input type="text" id="name" wire:model.live="name" class="border rounded p-2">
        </div>
    
        <div class="px-2">
            <label for="categoryId" class="block">Categoria:</label>
            <select id="categoryId" wire:model.live="categoryId" class="border rounded p-2">
                <option value="">Elige una opción &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <table class="table-auto w-full">
        <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Nombre</th>
                <th class="px-4 py-2">Categorías</th>
                <th class="px-4 py-2">Portada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movies as $movie)
            <tr>
                <td class="border px-4 py-2">{{ $movie->id }}</td>
                <td class="border px-4 py-2">{{ $movie->name }}</td>
                <td class="border px-4 py-2">{{$movie->getCategoriesText()}}</td>
                <td class="border px-4 py-2"><img src="{{ $movie->cover }}" height="100px" width="100px"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $movies->links() }}
    </div>
</div>