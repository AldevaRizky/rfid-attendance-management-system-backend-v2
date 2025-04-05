<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('RFID Card Management') }}
            </h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRfidCardModal">
                <i class="fas fa-plus mr-2"></i> Add New RFID Card
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">RFID Card List</h3>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('admin.rfid-cards.index') }}" class="flex items-center space-x-2">
                            <div class="relative">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48"
                                >
                            </div>
                            @if(request('search'))
                            <a href="{{ route('admin.rfid-cards.index') }}"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                            style="margin-left: 10px;"
                            title="Clear search">
                             <i class="fas fa-times fa-sm"></i>
                         </a>
                         @endif
                        </form>
                    </div>
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- RFID Card Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rfidCards as $card)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $card->card_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $card->user->name ?? 'Unassigned' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $card->status === 'active' ? 'bg-green-100 text-green-800' :
                                               ($card->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($card->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($card->issued_date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($card->expired_date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="btn btn-sm btn-info edit-btn"
                                                data-id="{{ $card->id }}"
                                                data-card_number="{{ $card->card_number }}"
                                                data-user_id="{{ $card->user_id }}"
                                                data-user_name="{{ $card->user->name ?? '' }}"
                                                data-user_nip="{{ $card->user->nip ?? '' }}"
                                                data-status="{{ $card->status }}"
                                                data-issued_date="{{ $card->issued_date ? $card->issued_date->format('Y-m-d') : '' }}"
                                                data-expired_date="{{ $card->expired_date ? $card->expired_date->format('Y-m-d') : '' }}"
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $card->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create RFID Card Modal -->
    <div class="modal fade" id="createRfidCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New RFID Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.rfid-cards.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="card_number" name="card_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Assign to User</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="issued_date" class="form-label">Issued Date</label>
                            <input type="date" class="form-control" id="issued_date" name="issued_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="expired_date" class="form-label">Expired Date</label>
                            <input type="date" class="form-control" id="expired_date" name="expired_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit RFID Card Modal -->
    <div class="modal fade" id="editRfidCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit RFID Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_card_number" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="edit_card_number" name="card_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_user_id" class="form-label">Assign to User</label>
                            <select class="form-select" id="edit_user_id" name="user_id" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_issued_date" class="form-label">Issued Date</label>
                            <input type="date" class="form-control" id="edit_issued_date" name="issued_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_expired_date" class="form-label">Expired Date</label>
                            <input type="date" class="form-control" id="edit_expired_date" name="expired_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteRfidCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this RFID card? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handle edit button click
        document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const cardId = this.getAttribute('data-id');
            const cardNumber = this.getAttribute('data-card_number');
            const userId = this.getAttribute('data-user_id');
            const userName = this.getAttribute('data-user_name');
            const userNip = this.getAttribute('data-user_nip');
            const status = this.getAttribute('data-status');
            const issuedDate = this.getAttribute('data-issued_date');
            const expiredDate = this.getAttribute('data-expired_date');

            // Tambahkan opsi user jika belum ada
            const userSelect = document.getElementById('edit_user_id');
            if (userId && !userSelect.querySelector(`option[value="${userId}"]`)) {
                const newOption = document.createElement('option');
                newOption.value = userId;
                newOption.textContent = `${userName} (${userNip})`;
                userSelect.appendChild(newOption);
            }

            // Set nilai form
            document.getElementById('edit_card_number').value = cardNumber;
            userSelect.value = userId;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_issued_date').value = issuedDate;
            document.getElementById('edit_expired_date').value = expiredDate;

            const form = document.getElementById('editForm');
            form.action = `/admin/rfid-cards/${cardId}`;

            const modal = new bootstrap.Modal(document.getElementById('editRfidCardModal'));
            modal.show();
        });
    });

        // Handle delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const cardId = this.getAttribute('data-id');

                const form = document.getElementById('deleteForm');
                form.action = `/admin/rfid-cards/${cardId}`;

                const modal = new bootstrap.Modal(document.getElementById('deleteRfidCardModal'));
                modal.show();
            });
        });

        // Show SweetAlert for success messages
        @if(session('toast'))
            Swal.fire({
                icon: '{{ session('toast.type') }}',
                title: '{{ session('toast.message') }}',
                position: 'center',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6',
                timer: 3000,
                timerProgressBar: true,
                toast: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        @endif

        // Show SweetAlert for error messages
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: `<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                timer: 5000,
                showConfirmButton: true
            });

            // Show the modal again if there are errors
            @if(request()->isMethod('post'))
                const createModal = new bootstrap.Modal(document.getElementById('createRfidCardModal'));
                createModal.show();
            @elseif(request()->isMethod('put'))
                const editModal = new bootstrap.Modal(document.getElementById('editRfidCardModal'));
                editModal.show();
            @endif
        @endif
    </script>
    @endpush
</x-app-layout>
