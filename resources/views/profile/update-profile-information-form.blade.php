<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: '{{ $this->user->profile_photo_path ? asset('storage/'.$this->user->profile_photo_path) : asset('images/default-profile.png') }}' }" class="col-span-6 sm:col-span-4">
                <input type="file" id="photo" class="hidden"
                    wire:model.live="photo"
                    x-ref="photo"
                    x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.photo.files[0]);
                    " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <div class="mt-2">
                    <img x-bind:src="photoPreview || '{{ $this->user->profile_photo_url }}'"
                         alt="{{ $this->user->name }}"
                         class="rounded-full h-20 w-20 object-cover border-2 border-gray-300 block">
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />
        </div>

        <!-- Additional Fields -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="nip" value="{{ __('NIP') }}" />
            <x-input id="nip" type="text" class="mt-1 block w-full" wire:model="state.nip" required />
            <x-input-error for="nip" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="phone_number" value="{{ __('Phone Number') }}" />
            <x-input id="phone_number" type="text" class="mt-1 block w-full" wire:model="state.phone_number" required />
            <x-input-error for="phone_number" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="gender" value="{{ __('Gender') }}" />
            <select id="gender" class="mt-1 block w-full" wire:model="state.gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            <x-input-error for="gender" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="birth_date" value="{{ __('Birth Date') }}" />
            <x-input id="birth_date"
            type="date"
            class="mt-1 block w-full"
            wire:model.live="state.birth_date"
            :max="now()->format('Y-m-d')"
            :value="optional($this->user->birth_date)->format('Y-m-d')" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="birth_place" value="{{ __('Birth Place') }}" />
            <x-input id="birth_place" type="text" class="mt-1 block w-full" wire:model="state.birth_place" required />
            <x-input-error for="birth_place" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="city" value="{{ __('City') }}" />
            <x-input id="city" type="text" class="mt-1 block w-full" wire:model="state.city" required />
            <x-input-error for="city" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="address" value="{{ __('Address') }}" />
            <textarea id="address" class="mt-1 block w-full" wire:model="state.address" required></textarea>
            <x-input-error for="address" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label value="{{ __('Division') }}" />
            <x-input type="text"
                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                    value="{{ $this->user->division->name ?? '-' }}"
                    readonly />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label value="{{ __('Position') }}" />
            <x-input type="text"
                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                    value="{{ $this->user->position->name ?? '-' }}"
                    readonly />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label value="{{ __('Education') }}" />
            <x-input type="text"
                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                    value="{{ $this->user->education->name ?? '-' }}"
                    readonly />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
