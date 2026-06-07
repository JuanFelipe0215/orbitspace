<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel Super Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded p-6">
                <h3 class="font-bold text-lg mb-4">Blogs registrados ({{ $tenants->count() }})</h3>

                <table class="w-full text-left">
                    <thead>
                    <tr class="border-b">
                        <th class="py-2">ID / Slug</th>
                        <th class="py-2">Dominio</th>
                        <th class="py-2">Creado</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tenants as $tenant)
                        <tr class="border-b">
                            <td class="py-3 font-mono">{{ $tenant->id }}</td>
                            <td class="py-3">
                                {{ $tenant->domains->first()?->domain ?? 'Sin dominio' }}
                            </td>
                            <td class="py-3 text-sm text-gray-500">
                                {{ $tenant->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3">
                                @if($tenant->suspended ?? false)
                                    <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:4px;font-size:0.8rem;font-weight:bold;">
                                        Suspendido
                                    </span>
                                @else
                                    <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:4px;font-size:0.8rem;font-weight:bold;">
                                        Activo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($tenant->suspended ?? false)
                                    <form action="/admin/{{ $tenant->id }}/unsuspend" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button style="background:#22c55e;color:white;padding:4px 12px;border-radius:4px;font-size:0.8rem;cursor:pointer;">
                                            Reactivar
                                        </button>
                                    </form>
                                @else
                                    <form action="/admin/{{ $tenant->id }}/suspend" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button style="background:#ef4444;color:white;padding:4px 12px;border-radius:4px;font-size:0.8rem;cursor:pointer;">
                                            Suspender
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-gray-500">No hay blogs registrados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
