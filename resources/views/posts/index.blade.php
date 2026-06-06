<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis Posts
            </h2>
            <a href="{{ route('posts.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                + Nuevo Post
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @forelse($posts as $post)
                <div class="bg-white shadow rounded p-6 mb-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg">{{ $post->title }}</h3>
                        <span class="text-sm {{ $post->is_published ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $post->is_published ? 'Publicado' : 'Borrador' }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('posts.edit', $post) }}"
                           class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">
                            Editar
                        </a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow rounded p-6 text-gray-500">
                    No tienes posts todavía. ¡Crea el primero!
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
