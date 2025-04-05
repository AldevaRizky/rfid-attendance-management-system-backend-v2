<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Karyawan') }}
        </h2>
    </x-slot>

    <div class="container mx-auto py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Tanggal & Hari Ini -->
                <div class="bg-gray-100 p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-700">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </h3>
                    <p class="text-sm text-gray-600" id="live-time"></p>
                </div>

                <!-- Absensi -->
                <div class="mt-6 flex gap-4 justify-center">
                    @if($status === 'leave' || $status === 'sick')
                        <div class="flex-1 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg p-6 shadow-md text-center">
                            <h2 class="text-lg font-bold">Absen Masuk</h2>
                            <p class="text-gray-600">
                                @if($status === 'sick')
                                    Sakit
                                @elseif($status === 'leave')
                                    Izin
                                @else
                                    {{ ucfirst($status) }}
                                @endif
                            </p>
                        </div>
                        <div class="flex-1 bg-yellow-200 border border-yellow-400 text-yellow-900 rounded-lg p-6 shadow-md text-center">
                            <h2 class="text-lg font-bold">Absen Keluar</h2>
                            <p class="text-gray-600">
                                @if($status === 'sick')
                                    Sakit
                                @elseif($status === 'leave')
                                    Izin
                                @else
                                    {{ ucfirst($status) }}
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="flex-1 bg-green-100 border border-green-300 text-green-800 rounded-lg p-6 shadow-md text-center">
                            <h2 class="text-lg font-bold">Absen Masuk</h2>
                            <p class="text-gray-600">{{ $check_in ?? '-' }}</p>
                        </div>
                        <div class="flex-1 bg-red-100 border border-red-300 text-red-800 rounded-lg p-6 shadow-md text-center">
                            <h2 class="text-lg font-bold">Absen Keluar</h2>
                            <p class="text-gray-600">{{ $check_out ?? '-' }}</p>
                        </div>
                    @endif
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-6 flex gap-4 justify-center">
                    <a href="{{ route('employee.leave-request.create') }}"
                        class="bg-orange-500 hover:bg-orange-600 text-black font-bold py-3 px-6 rounded-lg shadow-md flex items-center gap-2">
                        &#9993; Ajukan Izin
                    </a>
                    <a href="{{ route( 'employee.attendance.history') }}" class="bg-blue-600 hover:bg-blue-700 text-black font-bold py-3 px-6 rounded-lg shadow-md flex items-center gap-2">
                        &#128339; Riwayat Absen
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('live-time').textContent = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</x-app-layout>
