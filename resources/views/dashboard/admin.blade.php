<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-10 bg-white border-b border-gray-200">
                    <!-- Header with Total Employees -->
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Absensi Hari Ini</h1>
                        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                            <span class="font-semibold">Total Karyawan:</span> {{ $stats['total'] }}
                        </div>
                    </div>

                    <!-- Horizontal Attendance Summary Cards -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <!-- Hadir -->
                        <div class="flex-1 min-w-[200px] bg-green-100 border border-green-300 rounded-lg p-4 shadow-md">
                            <h2 class="text-xl font-semibold text-green-700">Hadir: {{ $stats['present'] }}</h2>
                            <p class="text-gray-600">Terlambat: {{ $stats['late'] }}</p>
                        </div>

                        <!-- Izin -->
                        <div class="flex-1 min-w-[200px] bg-blue-100 border border-blue-300 rounded-lg p-4 shadow-md">
                            <h2 class="text-xl font-semibold text-blue-700">Izin: {{ $stats['leave'] }}</h2>
                            <p class="text-gray-600">Izin/Cuti</p>
                        </div>

                        <!-- Sakit -->
                        <div class="flex-1 min-w-[200px] bg-purple-100 border border-purple-300 rounded-lg p-4 shadow-md">
                            <h2 class="text-xl font-semibold text-purple-700">Sakit: {{ $stats['sick'] }}</h2>
                            <p class="text-gray-600">Surat Dokter</p>
                        </div>

                        <!-- Tidak Hadir -->
                        <div class="flex-1 min-w-[200px] bg-red-100 border border-red-300 rounded-lg p-4 shadow-md">
                            <h2 class="text-xl font-semibold text-red-700">Tidak Hadir: {{ $stats['absent'] }}</h2>
                            <p class="text-gray-600">Tidak/Belum Hadir</p>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-[900px] w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">NIP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Divisi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Jabatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Shift</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Waktu Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Waktu Keluar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $user['name'] }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $user['nip'] }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $user['division'] }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $user['position'] }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">{{ $user['shift'] }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        @php
                                            $statusClasses = [
                                                'present' => 'bg-green-100 text-green-800 border border-green-200',
                                                'late' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                                'leave' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                                'sick' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                                'absent' => 'bg-red-100 text-red-800 border border-red-200',
                                                'holiday' => 'bg-gray-100 text-gray-800 border border-gray-300',
                                            ];

                                            $statusText = [
                                                'present' => 'Hadir',
                                                'late' => 'Terlambat',
                                                'leave' => 'Izin',
                                                'sick' => 'Sakit',
                                                'absent' => 'Absen',
                                                'holiday' => 'Libur',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$user['status']] }}">
                                            {{ $statusText[$user['status']] }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 border-b border-gray-200">
                                        {{ $user['status'] === 'holiday' ? 'Libur' : $user['check_in'] }}
                                    </td>

                                    <td class="px-6 py-4 border-b border-gray-200">
                                        {{ $user['status'] === 'holiday' ? 'Libur' : $user['check_out'] }}
                                    </td>

                                    <td class="px-6 py-4 border-b border-gray-200 text-center">
                                        @if(in_array($user['status'], ['sick', 'leave']))
                                            <button
                                                class="bg-black text-white px-3 py-2 rounded text-xs font-semibold"
                                                onclick="showLeaveDetails({{ $user['id'] }})">
                                                Detail
                                            </button>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Detail Izin -->
                    <div id="leaveDetailModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                            <h2 class="text-xl font-bold mb-4">Detail Izin</h2>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-semibold">Jenis:</span>
                                    <span id="leaveType"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Periode:</span>
                                    <span id="leaveStart"></span> - <span id="leaveEnd"></span>
                                </div>
                                <div>
                                    <span class="font-semibold">Alasan:</span>
                                    <p id="leaveReason" class="mt-1 text-gray-600"></p>
                                </div>
                                <div>
                                    <span class="font-semibold">Lampiran:</span>
                                    <a id="leaveAttachment" href="#" target="_blank" class="text-blue-600 hover:underline block mt-1">
                                        Lihat Dokumen
                                    </a>
                                </div>
                            </div>
                            <button
                                onclick="closeModal()"
                                class="mt-6 w-full bg-black text-white py-2 rounded hover:bg-gray-800"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showLeaveDetails(userId) {
            fetch(`/admin/leave-details/${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('leaveType').textContent = data.type;
                    document.getElementById('leaveStart').textContent = data.start_date;
                    document.getElementById('leaveEnd').textContent = data.end_date;
                    document.getElementById('leaveReason').textContent = data.reason || 'Tidak ada alasan';

                    const attachmentLink = document.getElementById('leaveAttachment');
                    if (data.attachment) {
                        attachmentLink.href = data.attachment;
                        attachmentLink.style.display = 'block';
                    } else {
                        attachmentLink.style.display = 'none';
                    }

                    document.getElementById('leaveDetailModal').classList.remove('hidden');
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal() {
            document.getElementById('leaveDetailModal').classList.add('hidden');
        }
    </script>

    <!-- Auto-refresh script -->
    <script>
        setTimeout(function(){
            window.location.reload();
        }, 30000); // Refresh every 30 seconds
    </script>
</x-app-layout>
