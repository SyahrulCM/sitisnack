<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah User</h2>

            <a href="{{ route('users.index') }}"
               class="px-4 py-2 rounded bg-white border shadow-sm hover:shadow text-sm">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 rounded shadow">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                               class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}"
                               class="w-full border rounded p-2" required>
                        <p class="text-xs text-gray-500 mt-1">Dipakai untuk identitas internal (unik).</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email (opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full border rounded p-2">
                        <p class="text-xs text-gray-500 mt-1">Breeze login default pakai email. Kalau kamu kosongkan email, user itu tidak bisa login kecuali nanti kita ubah login ke username.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">No HP (opsional)</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Role</label>
                            <select name="role" class="w-full border rounded p-2 bg-white">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role','reseller')===$role?'selected':'' }}>
                                        {{ strtoupper($role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Alamat (opsional)</label>
                        <textarea name="alamat" rows="2" class="w-full border rounded p-2">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Password</label>
                            <input type="password" name="password" class="w-full border rounded p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', 1) ? 'checked' : '' }}>
                        <span class="text-sm">Akun aktif</span>
                    </label>

                    <div class="pt-2">
                        <button class="px-4 py-2 rounded bg-gray-900 text-white shadow-sm hover:shadow">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
