<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Personalizar Blog
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6">

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="/blog-settings" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- Nombre del blog -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nombre del blog</label>
                        <input type="text" name="blog_name" value="{{ old('blog_name', $settings->blog_name) }}"
                               class="mt-1 block w-full border rounded px-3 py-2">
                        @error('blog_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Biografía -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Biografía</label>
                        <textarea name="bio" rows="3"
                                  class="mt-1 block w-full border rounded px-3 py-2">{{ old('bio', $settings->bio) }}</textarea>
                    </div>

                    <!-- Avatar -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Avatar</label>
                        @if($settings->avatar)
                            <img src="{{ asset('storage/' . $settings->avatar) }}"
                                 class="h-20 w-20 rounded-full object-cover mb-2">
                        @endif
                        <input type="file" name="avatar" accept="image/*" class="mt-1 block w-full">
                    </div>

                    <!-- Colores -->
                    <div class="mb-4 grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Color de fondo</label>
                            <input type="color" name="bg_color" value="{{ old('bg_color', $settings->bg_color ?? '#ffffff') }}"
                                   class="mt-1 block w-full h-10 rounded cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Color de texto</label>
                            <input type="color" name="text_color" value="{{ old('text_color', $settings->text_color ?? '#111111') }}"
                                   class="mt-1 block w-full h-10 rounded cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Color de acento</label>
                            <input type="color" name="accent_color" value="{{ old('accent_color', $settings->accent_color ?? '#3b82f6') }}"
                                   class="mt-1 block w-full h-10 rounded cursor-pointer">
                        </div>
                    </div>

                    <!-- Fuente -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Tipografía</label>
                        <select name="font" class="mt-1 block w-full border rounded px-3 py-2">
                            <option value="serif" {{ ($settings->font ?? 'serif') == 'serif' ? 'selected' : '' }}>Serif (clásica)</option>
                            <option value="sans-serif" {{ ($settings->font ?? '') == 'sans-serif' ? 'selected' : '' }}>Sans-serif (moderna)</option>
                            <option value="monospace" {{ ($settings->font ?? '') == 'monospace' ? 'selected' : '' }}>Monospace (técnica)</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="bg-purple-500 text-white px-6 py-2 rounded hover:bg-purple-600">
                        Guardar cambios
                    </button>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
