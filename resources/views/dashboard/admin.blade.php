<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold">Admin Dashboard</h2>
                    <p class="mt-2 text-gray-600">Welcome to the admin dashboard!</p>

                    <!-- Admin-specific content here -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium">Total Employees</h3>
                            <p class="text-3xl font-bold mt-2">{{ $totalEmployees ?? 0 }}</p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium">Active Employees</h3>
                            <p class="text-3xl font-bold mt-2">{{ $activeEmployees ?? 0 }}</p>
                        </div>
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium">Recent Activity</h3>
                            <p class="text-3xl font-bold mt-2">{{ $recentActivities ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
