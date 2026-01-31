<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data User</h2>

            <a href="{{ route('users.create') }}"
               class="px-4 py-2 rounded bg-gray-900 text-white text-sm shadow-sm hover:shadow">
                + Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 rounded shadow">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Nama</th>
                                <th class="text-left py-2">Username</th>
                                <th class="text-left py-2">Email</th>
                                <th class="text-left py-2">Role</th>
                                <th class="text-left py-2">Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                <tr class="border-b">
                                    <td class="py-2">{{ $u->nama_lengkap }}</td>
                                    <td class="py-2">{{ $u->username }}</td>
                                    <td class="py-2">{{ $u->email ?? '-' }}</td>
                                    <td class="py-2">{{ strtoupper($u->role) }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs {{ $u->status_aktif ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $u->status_aktif ? 'AKTIF' : 'NONAKTIF' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Belum ada user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
