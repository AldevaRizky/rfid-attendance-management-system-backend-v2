<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold">Employee Dashboard</h2>
                    <p class="mt-2 text-gray-600">Welcome, {{ Auth::user()->name }}!</p>

                    <!-- Employee-specific content here -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium">Your Information</h3>
                            <div class="mt-4 space-y-2">
                                <p><strong>NIP:</strong> {{ Auth::user()->nip }}</p>
                                <p><strong>Position:</strong> {{ Auth::user()->position->name ?? 'N/A' }}</p>
                                <p><strong>Division:</strong> {{ Auth::user()->division->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium">Quick Actions</h3>
                            <div class="mt-4 space-y-3">
                                <a href="#" class="block text-blue-600 hover:text-blue-800">View Attendance</a>
                                <a href="#" class="block text-blue-600 hover:text-blue-800">Request Leave</a>
                                <a href="#" class="block text-blue-600 hover:text-blue-800">Update Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
