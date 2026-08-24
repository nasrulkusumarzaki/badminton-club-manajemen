<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Program Latihan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('programs.update', $program) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Program</label>
                        <input type="text" name="nama_program" value="{{ old('nama_program', $program->nama_program) }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        @error('nama_program') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Jenis</label>
                        <input type="text" name="jenis" value="{{ old('jenis', $program->jenis) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('jenis') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Level</label>
                        <select name="level" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="">-- Tidak spesifik --</option>
                            <option value="pemula" @selected(old('level', $program->level) === 'pemula')>Pemula</option>
                            <option value="beginner" @selected(old('level', $program->level) === 'beginner')>Beginner</option>
                            <option value="senior" @selected(old('level', $program->level) === 'senior')>Senior</option>
                        </select>
                        @error('level') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', optional($program->tanggal)->toDateString()) }}" class="mt-1 block border-gray-300 rounded-md">
                        @error('tanggal') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Pilih Atlet (assign ke program)</label>
                        <select name="atlets[]" multiple class="mt-1 block w-full border-gray-300 rounded-md" size="6">
                            @foreach($atlets as $a)
                                                            <option value="{{ $a->id }}" data-level="{{ $a->level }}" @selected(collect(old('atlets', $program->atlets->pluck('id')->toArray()))->contains($a->id))>{{ $a->nama }} ({{ $a->level }})</option>
                            @endforeach
                        </select>
                        @error('atlets') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        @error('atlets.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" class="mt-1 block w-full border-gray-300 rounded-md" rows="4">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                        @error('deskripsi') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-5 py-2 bg-gray-800 text-white rounded-md">Simpan Perubahan</button>
                        <a href="{{ route('programs.index') }}" class="text-sm text-gray-500 ml-4">Batal</a>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const levelSelect = document.querySelector('select[name="level"]');
                        const atletsSelect = document.querySelector('select[name="atlets[]"]');

                        if (!levelSelect || !atletsSelect) return;

                        function applyGroupSelection(value) {
                            if (!value) {
                                // clear selection
                                for (const o of atletsSelect.options) o.selected = false;
                                return;
                            }

                            for (const o of atletsSelect.options) {
                                if (o.dataset.level === value) o.selected = true; else o.selected = false;
                            }
                        }

                        // If page loaded with a level selected, apply selection
                        if (levelSelect.value) {
                            applyGroupSelection(levelSelect.value);
                        }

                        levelSelect.addEventListener('change', function () {
                            applyGroupSelection(this.value);
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
