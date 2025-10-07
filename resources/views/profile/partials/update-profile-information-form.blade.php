<section>
    <header>
        <h2 class="text-lg font-medium heading-in-profile-page">
            {{ __('EDIT ACCOUNT INFORMATION') }}
        </h2>

        <p class="mt-1 text-sm description-in-profile-page">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="profileUpdateForm" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" id="profileUpdateButton" class="inline-flex items-center px-4 py-2 bg-[#198f51] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <span id="profileUpdateButtonText">{{ __('Save') }}</span>
                <span id="profileUpdateSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <!-- Success Modal -->
    <x-modal name="profile-update-success" focusable>
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-2">
                Account Updated!
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
                Your account information has been updated successfully.
            </p>

            <div class="flex justify-center">
                <button type="button" 
                        class="inline-flex items-center px-4 py-2 bg-[#198f51] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        x-on:click="$dispatch('close')">
                    Close
                </button>
            </div>
        </div>
    </x-modal>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profileUpdateForm');
        const updateButton = document.getElementById('profileUpdateButton');
        const updateButtonText = document.getElementById('profileUpdateButtonText');
        const updateSpinner = document.getElementById('profileUpdateSpinner');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // Show loading state
                updateButton.disabled = true;
                updateButtonText.textContent = 'Saving...';
                updateSpinner.classList.remove('hidden');

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success modal
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'profile-update-success' }));
                    } else {
                        alert('Error: ' + (data.message || 'Update failed'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.errors) {
                        // Handle validation errors
                        let errorMessage = 'Validation errors:\n';
                        Object.keys(error.errors).forEach(key => {
                            errorMessage += error.errors[key].join('\n') + '\n';
                        });
                        alert(errorMessage);
                    } else {
                        alert('An error occurred during update');
                    }
                })
                .finally(() => {
                    // Reset loading state
                    updateButton.disabled = false;
                    updateButtonText.textContent = 'Save';
                    updateSpinner.classList.add('hidden');
                });
            });
        }
    });
    </script>
</section>
