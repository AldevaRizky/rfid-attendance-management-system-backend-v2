<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengajuan Izin') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Form Pengajuan Izin</h3>

                @if(session('error'))
                    <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if($existingLeave)
                    <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg mb-4">
                        <strong>Anda sudah mengajukan izin</strong> hingga <span class="font-bold">{{ \Carbon\Carbon::parse($existingLeave->end_date)->translatedFormat('d F Y') }}</span>.
                        Anda tidak dapat mengajukan izin baru sampai tanggal tersebut.
                    </div>
                @else
                    <form action="{{ route('employee.leave-request.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label for="type" class="block text-gray-700">Tipe Izin</label>
                                <select name="type" id="type" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="sick">Sakit</option>
                                    <option value="personal">Pribadi</option>
                                </select>
                                @error('type')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="start_date" class="block text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" class="w-full p-2 border border-gray-300 rounded-md" required>
                                @error('start_date')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-gray-700">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date" class="w-full p-2 border border-gray-300 rounded-md" required>
                                @error('end_date')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="reason" class="block text-gray-700">Alasan</label>
                                <textarea name="reason" id="reason" rows="4" class="w-full p-2 border border-gray-300 rounded-md" required></textarea>
                                @error('reason')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="attachment" class="block text-gray-700">Lampiran (Opsional)</label>
                                <input type="file" name="attachment" id="attachment" class="w-full p-2 border border-gray-300 rounded-md">
                                @error('attachment')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 text-black font-bold py-3 px-6 rounded-lg shadow-md">
                                    Kirim Pengajuan
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
