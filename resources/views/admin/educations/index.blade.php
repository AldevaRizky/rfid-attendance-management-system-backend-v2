<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Education Management') }}
            </h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEducationModal">
                <i class="fas fa-plus mr-2"></i> Add New Education
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Education List</h3>
                    </div>
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Education Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($educations as $education)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $education->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $education->level }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="btn btn-sm btn-info edit-btn"
                                                data-id="{{ $education->id }}"
                                                data-name="{{ $education->name }}"
                                                data-level="{{ $education->level }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $education->id }}">
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

    <!-- Create Education Modal -->
    <div class="modal fade" id="createEducationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.educations.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Education Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <input type="number" class="form-control" id="level" name="level" min="1" required>
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

    <!-- Edit Education Modal -->
    <div class="modal fade" id="editEducationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Education Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_level" class="form-label">Level</label>
                            <input type="number" class="form-control" id="edit_level" name="level" min="1" required>
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
    <div class="modal fade" id="deleteEducationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this education? This action cannot be undone.</p>
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
                const educationId = this.getAttribute('data-id');
                const educationName = this.getAttribute('data-name');
                const educationLevel = this.getAttribute('data-level');

                document.getElementById('edit_name').value = educationName;
                document.getElementById('edit_level').value = educationLevel;

                const form = document.getElementById('editForm');
                form.action = `/admin/educations/${educationId}`;

                const modal = new bootstrap.Modal(document.getElementById('editEducationModal'));
                modal.show();
            });
        });

        // Handle delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const educationId = this.getAttribute('data-id');

                const form = document.getElementById('deleteForm');
                form.action = `/admin/educations/${educationId}`;

                const modal = new bootstrap.Modal(document.getElementById('deleteEducationModal'));
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
                const createModal = new bootstrap.Modal(document.getElementById('createEducationModal'));
                createModal.show();
            @elseif(request()->isMethod('put'))
                const editModal = new bootstrap.Modal(document.getElementById('editEducationModal'));
                editModal.show();
            @endif
        @endif
    </script>
    @endpush
</x-app-layout>
