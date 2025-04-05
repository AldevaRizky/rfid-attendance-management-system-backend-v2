<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Absensi') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Pilih Bulan -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold">{{ $currentMonthName }}</h3>
                    <input
                        type="month"
                        class="border p-2 rounded"
                        value="{{ $selectedMonth }}"
                        onchange="window.location.href = '?month=' + this.value.split('-')[1] + '&year=' + this.value.split('-')[0]"
                    >
                </div>

                <!-- Card Statistik Horizontal -->
                <div class="flex flex-wrap gap-4 mb-8">
                    <!-- Hadir + Terlambat -->
                    <div class="flex-1 min-w-[200px] bg-green-100 border border-green-300 rounded-lg p-4 shadow-md">
                        <h2 class="text-xl font-semibold text-green-700">Hadir: {{ $stats['Hadir'] }}</h2>
                        <p class="text-gray-600">Terlambat: {{ $stats['Terlambat'] }}</p>
                    </div>

                    <!-- Izin -->
                    <div class="flex-1 min-w-[200px] bg-blue-100 border border-blue-300 rounded-lg p-4 shadow-md">
                        <h2 class="text-xl font-semibold text-blue-700">Izin: {{ $stats['Izin'] }}</h2>
                        <p class="text-gray-600">Izin/Cuti</p>
                    </div>

                    <!-- Sakit -->
                    <div class="flex-1 min-w-[200px] bg-purple-100 border border-purple-300 rounded-lg p-4 shadow-md">
                        <h2 class="text-xl font-semibold text-purple-700">Sakit: {{ $stats['Sakit'] }}</h2>
                        <p class="text-gray-600">Surat Dokter</p>
                    </div>

                    <!-- Absen -->
                    <div class="flex-1 min-w-[200px] bg-red-100 border border-red-300 rounded-lg p-4 shadow-md">
                        <h2 class="text-xl font-semibold text-red-700">Absen: {{ $stats['Absen'] }}</h2>
                        <p class="text-gray-600">Tidak/Belum Hadir</p>
                    </div>
                </div>

               <!-- Kalender Tabel -->
    <div class="overflow-x-auto mt-8">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="p-2 border text-red-600">Minggu</th>
                    <th class="p-2 border">Senin</th>
                    <th class="p-2 border">Selasa</th>
                    <th class="p-2 border">Rabu</th>
                    <th class="p-2 border">Kamis</th>
                    <th class="p-2 border">Jumat</th>
                    <th class="p-2 border">Sabtu</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $firstDay = Carbon\Carbon::parse($selectedMonth)->startOfMonth();
                    $startDayOfWeek = $firstDay->dayOfWeek;
                    $totalDays = $firstDay->daysInMonth;
                    $dayCounter = 1;
                @endphp

                @for ($week = 0; $week < 6; $week++)
                    <tr>
                        @for ($d = 0; $d < 7; $d++)
                            @if ($week == 0 && $d < $startDayOfWeek)
                                <td class="p-2 border"></td>
                            @elseif ($dayCounter > $totalDays)
                                <td class="p-2 border"></td>
                            @else
                                @php
                                    $currentDate = $firstDay->copy()->addDays($dayCounter - 1);
                                    $isSunday = $currentDate->dayOfWeek == 0;
                                    $dateKey = $currentDate->format('Y-m-d');
                                    $status = $dates[$dateKey]['status'] ?? '-';
                                    $leaveData = $dates[$dateKey]['leave'] ?? null;

                                    // Warna teks
                                    $textColor = match($status) {
                                        'H' => 'text-green-600',
                                        'S' => 'text-yellow-600',
                                        'I' => 'text-blue-600',
                                        'A' => 'text-red-600',
                                        default => 'text-gray-400'
                                    };
                                @endphp
                                    <td class="p-2 border text-center
                                    {{ $textColor }}
                                    {{ $isSunday ? 'font-bold' : '' }}
                                    {{ in_array($status, ['S','I']) ? 'cursor-pointer hover:bg-gray-100' : '' }}"
                                    @if(in_array($status, ['S','I']))
                                        data-date="{{ $dateKey }}"
                                        data-status="{{ $status }}"
                                    @endif>
                                    <div class="font-medium">{{ $dayCounter }}</div>
                                    <div class="text-sm">{{ $status }}</div>
                                    </td>
                                @php $dayCounter++ @endphp
                            @endif
                        @endfor
                    </tr>
                    @if ($dayCounter > $totalDays)
                        @break
                    @endif
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="text-xl font-bold mb-4">Detail Izin/Sakit</h3>
            <div class="space-y-3">
                <div>
                    <span class="font-semibold">Jenis:</span>
                    <span id="modalType"></span>
                </div>
                <div>
                    <span class="font-semibold">Periode:</span>
                    <span id="modalPeriod"></span>
                </div>
                <div>
                    <span class="font-semibold">Alasan:</span>
                    <p id="modalReason" class="mt-1 text-gray-600"></p>
                </div>
                <div>
                    <span class="font-semibold">Lampiran:</span>
                    <a id="modalAttachment" href="#" target="_blank" class="text-blue-600 hover:underline block mt-1">
                        Lihat Dokumen
                    </a>
                </div>
            </div>
            <button
            onclick="closeModal()"
            class="mt-6 w-full bg-black text-white py-2 rounded"
        >
            Tutup
        </button>

        </div>
    </div>

    <script>
        async function showDetail(date) {
        try {
            const response = await fetch(`/employee/leave-requests/${date}`);

            if (!response.ok) {
                throw new Error('Gagal mengambil data, pastikan tanggal memiliki izin/sakit');
            }

            const data = await response.json();

            document.getElementById('modalType').textContent = data.type;

            document.getElementById('modalPeriod').textContent =
                `${data.start_date} - ${data.end_date}`;

            document.getElementById('modalReason').textContent = data.reason || 'Tidak ada alasan';

            const attachmentLink = document.getElementById('modalAttachment');
            if (data.attachment) {
                attachmentLink.href = data.attachment;
                attachmentLink.style.display = 'block';
            } else {
                attachmentLink.style.display = 'none';
            }

            document.getElementById('detailModal').classList.remove('hidden');

        } catch (error) {
            console.error('Error:', error);
            alert(error.message);
        }
    }


    // Inisialisasi event listener setelah DOM siap
    document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-date]').forEach(cell => {
        cell.addEventListener('click', () => {
            const date = cell.dataset.date;
            showDetail(date);
        });
    });
});


    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

</script>
</x-app-layout>
