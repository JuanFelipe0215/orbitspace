<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Post
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6">

                <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Título -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Título</label>
                        <input type="text" name="title" value="{{ old('title', $post->title) }}"
                               class="mt-1 block w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                        @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Contenido -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Contenido</label>
                        <textarea name="content" rows="8"
                                  class="mt-1 block w-full border rounded px-3 py-2 focus:outline-none focus:ring">{{ old('content', $post->content) }}</textarea>
                        @error('content') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Imagen de portada -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Imagen de portada</label>
                        @if($post->cover_image)
                            <img src="{{ asset('storage/' . $post->cover_image) }}"
                                 class="h-32 object-cover rounded mb-2">
                        @endif
                        <input type="file" name="cover_image" accept="image/*"
                               class="mt-1 block w-full">
                        @error('cover_image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Publicar -->
                    <div class="mb-6 flex items-center gap-2">
                        <input type="checkbox" name="is_published" id="is_published" value="1"
                            {{ $post->is_published ? 'checked' : '' }}>
                        <label for="is_published" class="text-sm text-gray-700">Publicar</label>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                            Actualizar
                        </button>
                        <a href="{{ route('posts.index') }}"
                           class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
