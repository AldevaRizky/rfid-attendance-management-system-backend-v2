<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Employee Management') }}
            </h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                <i class="fas fa-plus mr-2"></i> Add New Employee
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Employee List</h3>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('admin.employees.index') }}" class="flex items-center space-x-2">
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
                            <a href="{{ route('admin.employees.index') }}"
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

                    <!-- Employee Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($employees as $employee)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($employee->profile_photo_path)
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                 src="{{ asset('storage/'.$employee->profile_photo_path) }}"
                                                 alt="{{ $employee->name }}">
                                        @else
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $employee->profile_photo_url }}" alt="{{ $employee->name }}">
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                                        <div class="text-gray-500">{{ $employee->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $employee->nip }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $employee->position->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $employee->division->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' :
                                               ($employee->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($employee->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="btn btn-sm btn-info edit-btn"
                                                data-id="{{ $employee->id }}"
                                                data-name="{{ $employee->name }}"
                                                data-email="{{ $employee->email }}"
                                                data-nip="{{ $employee->nip }}"
                                                data-division_id="{{ $employee->division_id }}"
                                                data-position_id="{{ $employee->position_id }}"
                                                data-education_id="{{ $employee->education_id }}"
                                                data-rfid_card_id="{{ $employee->rfid_card_id }}"
                                                data-phone_number="{{ $employee->phone_number }}"
                                                data-gender="{{ $employee->gender }}"
                                                data-birth_date="{{ $employee->birth_date->format('Y-m-d') }}"
                                                data-birth_place="{{ $employee->birth_place }}"
                                                data-city="{{ $employee->city }}"
                                                data-address="{{ $employee->address }}"
                                                data-join_date="{{ $employee->join_date->format('Y-m-d') }}"
                                                data-status="{{ $employee->status }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $employee->id }}">
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

    <!-- Create Employee Modal -->
    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Personal Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">Personal Information</h3>
                                <div>
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div>
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div>
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div>
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                                <div>
                                    <label for="nip" class="form-label">NIP</label>
                                    <input type="text" class="form-control" id="nip" name="nip" required>
                                </div>
                                <div>
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                </div>
                                <div>
                                    <label for="birth_place" class="form-label">Birth Place</label>
                                    <input type="text" class="form-control" id="birth_place" name="birth_place" required>
                                </div>
                            </div>

                            <!-- Employment Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">Employment Information</h3>
                                <div>
                                    <label for="division_id" class="form-label">Division</label>
                                    <select class="form-select" id="division_id" name="division_id" required>
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="position_id" class="form-label">Position</label>
                                    <select class="form-select" id="position_id" name="position_id" required>
                                        <option value="">Select Position</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="education_id" class="form-label">Education</label>
                                    <select class="form-select" id="education_id" name="education_id" required>
                                        <option value="">Select Education</option>
                                        @foreach($educations as $education)
                                            <option value="{{ $education->id }}">{{ $education->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="rfid_card_id" class="form-label">RFID Card</label>
                                    <select class="form-select" id="rfid_card_id" name="rfid_card_id">
                                        <option value="">No RFID Card</option>
                                        @foreach($rfidCards as $card)
                                            <option value="{{ $card->id }}">{{ $card->card_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="join_date" class="form-label">Join Date</label>
                                    <input type="date" class="form-control" id="join_date" name="join_date" required>
                                </div>
                                <div>
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Resigned">Resigned</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="profile_photo" class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="md:col-span-2 space-y-4">
                                <h3 class="text-lg font-medium">Contact Information</h3>
                                <div>
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                                </div>
                                <div>
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div>
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Personal Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">Personal Information</h3>
                                <div>
                                    <label for="edit_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                                <div>
                                    <label for="edit_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                                <div>
                                    <label for="edit_nip" class="form-label">NIP</label>
                                    <input type="text" class="form-control" id="edit_nip" name="nip" required>
                                </div>
                                <div>
                                    <label for="edit_gender" class="form-label">Gender</label>
                                    <select class="form-select" id="edit_gender" name="gender" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="edit_birth_date" name="birth_date" required>
                                </div>
                                <div>
                                    <label for="edit_birth_place" class="form-label">Birth Place</label>
                                    <input type="text" class="form-control" id="edit_birth_place" name="birth_place" required>
                                </div>
                            </div>

                            <!-- Employment Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">Employment Information</h3>
                                <div>
                                    <label for="edit_division_id" class="form-label">Division</label>
                                    <select class="form-select" id="edit_division_id" name="division_id" required>
                                        <option value="">Select Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_position_id" class="form-label">Position</label>
                                    <select class="form-select" id="edit_position_id" name="position_id" required>
                                        <option value="">Select Position</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_education_id" class="form-label">Education</label>
                                    <select class="form-select" id="edit_education_id" name="education_id" required>
                                        <option value="">Select Education</option>
                                        @foreach($educations as $education)
                                            <option value="{{ $education->id }}">{{ $education->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_rfid_card_id" class="form-label">RFID Card</label>
                                    <select class="form-select" id="edit_rfid_card_id" name="rfid_card_id">
                                        <option value="">No RFID Card</option>
                                        @foreach($rfidCards as $card)
                                            <option value="{{ $card->id }}">{{ $card->card_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_join_date" class="form-label">Join Date</label>
                                    <input type="date" class="form-control" id="edit_join_date" name="join_date" required>
                                </div>
                                <div>
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Resigned">Resigned</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_profile_photo" class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control" id="edit_profile_photo" name="profile_photo">
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="md:col-span-2 space-y-4">
                                <h3 class="text-lg font-medium">Contact Information</h3>
                                <div>
                                    <label for="edit_phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="edit_phone_number" name="phone_number" required>
                                </div>
                                <div>
                                    <label for="edit_city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="edit_city" name="city" required>
                                </div>
                                <div>
                                    <label for="edit_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this employee? This action cannot be undone.</p>
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
                const employeeId = this.getAttribute('data-id');
                const employeeName = this.getAttribute('data-name');
                const employeeEmail = this.getAttribute('data-email');
                const employeeNip = this.getAttribute('data-nip');
                const employeeDivisionId = this.getAttribute('data-division_id');
                const employeePositionId = this.getAttribute('data-position_id');
                const employeeEducationId = this.getAttribute('data-education_id');
                const employeeRfidCardId = this.getAttribute('data-rfid_card_id');
                const employeePhoneNumber = this.getAttribute('data-phone_number');
                const employeeGender = this.getAttribute('data-gender');
                const employeeBirthDate = this.getAttribute('data-birth_date');
                const employeeBirthPlace = this.getAttribute('data-birth_place');
                const employeeCity = this.getAttribute('data-city');
                const employeeAddress = this.getAttribute('data-address');
                const employeeJoinDate = this.getAttribute('data-join_date');
                const employeeStatus = this.getAttribute('data-status');

                // Set form values
                document.getElementById('edit_name').value = employeeName;
                document.getElementById('edit_email').value = employeeEmail;
                document.getElementById('edit_nip').value = employeeNip;
                document.getElementById('edit_division_id').value = employeeDivisionId;
                document.getElementById('edit_position_id').value = employeePositionId;
                document.getElementById('edit_education_id').value = employeeEducationId;
                document.getElementById('edit_rfid_card_id').value = employeeRfidCardId;
                document.getElementById('edit_phone_number').value = employeePhoneNumber;
                document.getElementById('edit_gender').value = employeeGender;
                document.getElementById('edit_birth_date').value = employeeBirthDate;
                document.getElementById('edit_birth_place').value = employeeBirthPlace;
                document.getElementById('edit_city').value = employeeCity;
                document.getElementById('edit_address').value = employeeAddress;
                document.getElementById('edit_join_date').value = employeeJoinDate;
                document.getElementById('edit_status').value = employeeStatus;

                // Set form action
                const form = document.getElementById('editForm');
                form.action = `/admin/employees/${employeeId}`;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                modal.show();
            });
        });

        // Handle delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-id');

                const form = document.getElementById('deleteForm');
                form.action = `/admin/employees/${employeeId}`;

                const modal = new bootstrap.Modal(document.getElementById('deleteEmployeeModal'));
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
                const createModal = new bootstrap.Modal(document.getElementById('createEmployeeModal'));
                createModal.show();
            @elseif(request()->isMethod('put'))
                const editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                editModal.show();
            @endif
        @endif
    </script>
    @endpush
</x-app-layout>
