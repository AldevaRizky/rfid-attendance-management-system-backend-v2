<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manajemen Absensi Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                <!-- Filter Section (Diperbarui) -->
                <div class="p-4 bg-white border-b border-gray-200">
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <!-- Jenis Filter (Diperlebar) -->
                        <select name="filter_type" class="p-2 text-sm border rounded-md w-48" onchange="updateDateInput(this)">
                            <option value="month" {{ $filterType === 'month' ? 'selected' : '' }}>Bulanan</option>
                            <option value="week" {{ $filterType === 'week' ? 'selected' : '' }}>Mingguan</option>
                            <option value="day" {{ $filterType === 'day' ? 'selected' : '' }}>Harian</option>
                        </select>

                        <!-- Input Tanggal -->
                        <input type="{{ $filterType === 'month' ? 'month' : ($filterType === 'week' ? 'week' : 'date') }}"
                               name="date"
                               value="{{ request('date') }}"
                               class="p-2 text-sm border rounded-md">

                        <!-- Divisi -->
                        <select name="division" class="p-2 text-sm border rounded-md w-60">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Jabatan -->
                        <select name="position" class="p-2 text-sm border rounded-md w-60">
                            <option value="">Semua Jabatan</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Tombol Aksi -->
                        <div class="flex gap-2 ml-auto">
                            <button type="submit" class="px-4 py-2 text-white bg-gray-800 rounded-md hover:bg-gray-900">
                                Terapkan
                            </button>
                            <a href="{{ route('admin.attendance') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Reset
                            </a>
                            <a href="{{ route('admin.attendance.export.pdf') }}?{{ http_build_query(request()->query()) }}"
                               class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">
                                Ekspor PDF
                            </a>
                            <a href="{{ route('admin.attendance.export.excel') }}?{{ http_build_query(request()->query()) }}"
                               class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">
                                Ekspor Excel
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Attendance Table -->
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="p-2 border">Nama</th>
                                    <th class="p-2 border">NIP</th>
                                    <th class="p-2 border">Divisi</th>
                                    <th class="p-2 border">Jabatan</th>
                                    @foreach($dateRange as $day)
                                        <th class="p-2 text-center border
                                            {{ $day['is_weekend'] ? 'bg-red-50' : '' }}
                                            {{ $day['is_sunday'] ? 'text-red-600' : '' }}">
                                            {{ Carbon\Carbon::parse($day['date'])->format('d/m') }}
                                            <div class="text-xs text-gray-500">
                                                {{ Carbon\Carbon::parse($day['date'])->isoFormat('ddd') }}
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="p-2 border">H</th>
                                    <th class="p-2 border">T</th>
                                    <th class="p-2 border">I</th>
                                    <th class="p-2 border">S</th>
                                    <th class="p-2 border">A</th>
                                    <th class="p-2 border">L</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceData as $data)
                                    <tr>
                                        <td class="p-2 border">{{ $data['name'] }}</td>
                                        <td class="p-2 border">{{ $data['nip'] }}</td>
                                        <td class="p-2 border">{{ $data['division'] }}</td>
                                        <td class="p-2 border">{{ $data['position'] }}</td>

                                        @foreach($data['dates'] as $date => $status)
                                        <td class="p-2 text-center border
                                            @if(in_array($status, ['S','I'])) cursor-pointer hover:bg-gray-100 @endif
                                            @if($status === 'H') bg-green-100
                                            @elseif($status === 'T') bg-yellow-100
                                            @elseif($status === 'I') bg-blue-100
                                            @elseif($status === 'S') bg-purple-100
                                            @elseif($status === 'A') bg-red-100
                                            @elseif($status === 'L') bg-red-50 @endif"
                                            @if(in_array($status, ['S','I']))
                                            onclick="showLeaveDetail('{{ $data['user_id'] }}', '{{ $date }}')"
                                            @endif>
                                            {{ $status }}
                                        </td>
                                        @endforeach

                                        <td class="p-2 text-center border">{{ $data['stats']['H'] }}</td>
                                        <td class="p-2 text-center border">{{ $data['stats']['T'] }}</td>
                                        <td class="p-2 text-center border">{{ $data['stats']['I'] }}</td>
                                        <td class="p-2 text-center border">{{ $data['stats']['S'] }}</td>
                                        <td class="p-2 text-center border">{{ $data['stats']['A'] }}</td>
                                        <td class="p-2 text-center border">{{ $data['stats']['L'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($dateRange) + 7 }}" class="p-4 text-center">
                                            Tidak ada data yang ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Detail Modal -->
    <div id="leaveDetailModal" class="fixed inset-0 hidden bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="p-6 bg-white rounded-lg w-96">
                <h3 class="mb-4 text-xl font-bold">Detail Izin/Sakit</h3>
                <div class="space-y-3">
                    <div>
                        <span class="font-semibold">Jenis:</span>
                        <span id="modalLeaveType"></span>
                    </div>
                    <div>
                        <span class="font-semibold">Periode:</span>
                        <span id="modalLeavePeriod"></span>
                    </div>
                    <div>
                        <span class="font-semibold">Alasan:</span>
                        <p id="modalLeaveReason" class="text-gray-600"></p>
                    </div>
                    <div id="attachmentSection" class="hidden">
                        <span class="font-semibold">Lampiran:</span>
                        <a id="modalLeaveAttachment"
                           class="block text-blue-600 underline hover:text-blue-800"
                           target="_blank">
                            Lihat Dokumen
                        </a>
                    </div>
                </div>
                <button onclick="closeLeaveModal()"
                        class="w-full px-4 py-2 mt-6 text-white bg-black rounded ">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function updateDateInput(select) {
            const type = select.value;
            const dateInput = document.querySelector('input[name="date"]');
            const now = new Date();

            let value = '';
            if(type === 'month') {
                value = now.toISOString().slice(0, 7);
                dateInput.type = 'month';
            } else if(type === 'week') {
                const weekNumber = this.getWeekNumber(now);
                value = `${now.getFullYear()}-W${weekNumber.toString().padStart(2, '0')}`;
                dateInput.type = 'week';
            } else {
                value = now.toISOString().slice(0, 10);
                dateInput.type = 'date';
            }

            dateInput.value = value;
        }

        async function showLeaveDetail(userId, date) {
        try {
            const response = await fetch(`/admin/attendance/leave-details/${userId}/${date}`);
            const data = await response.json();

            if (response.ok) {
                document.getElementById('modalLeaveType').textContent = data.type;
                document.getElementById('modalLeavePeriod').textContent = `${data.start_date} - ${data.end_date}`;
                document.getElementById('modalLeaveReason').textContent = data.reason;

                const attachmentSection = document.getElementById('attachmentSection');
                if (data.attachment) {
                    attachmentSection.classList.remove('hidden');
                    document.getElementById('modalLeaveAttachment').href = data.attachment;
                } else {
                    attachmentSection.classList.add('hidden');
                }

                document.getElementById('leaveDetailModal').classList.remove('hidden');
            } else {
                alert(data.error || 'Data tidak ditemukan');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal memuat detail: ' + error.message);
        }
    }

        function closeLeaveModal() {
            document.getElementById('leaveDetailModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
