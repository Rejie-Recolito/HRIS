<section>
    <header>
        <h2 class="text-lg font-medium heading-in-profile-page">
            {{ __('CHANGE PASSWORD') }}
        </h2>

        <p class="mt-1 text-sm description-in-profile-page">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form id="passwordUpdateForm" method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <div class="relative">
                <input id="update_password_current_password" 
                       name="current_password" 
                       type="password" 
                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 pr-10" 
                       autocomplete="current-password" />
                <button type="button" 
                        class="absolute inset-y-0 right-0 flex items-center px-3 focus:outline-none"
                        onclick="togglePasswordVisibility('update_password_current_password', 'current_eye')">
                    <svg id="current_eye" class="w-5 h-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <div class="relative">
                <input id="update_password_password" 
                       name="password" 
                       type="password" 
                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 pr-10" 
                       autocomplete="new-password" />
                <button type="button" 
                        class="absolute inset-y-0 right-0 flex items-center px-3 focus:outline-none"
                        onclick="togglePasswordVisibility('update_password_password', 'new_eye')">
                    <svg id="new_eye" class="w-5 h-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <input id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       type="password" 
                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 pr-10" 
                       autocomplete="new-password" />
                <button type="button" 
                        class="absolute inset-y-0 right-0 flex items-center px-3 focus:outline-none"
                        onclick="togglePasswordVisibility('update_password_password_confirmation', 'confirm_eye')">
                    <svg id="confirm_eye" class="w-5 h-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" id="passwordUpdateButton" class="inline-flex items-center px-4 py-2 bg-[#198f51] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <span id="passwordUpdateButtonText">{{ __('Apply Changes') }}</span>
                <span id="passwordUpdateSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Password successfully changed.') }}</p>
            @endif
        </div>
    </form>

    <!-- Success Modal -->
    <x-modal name="password-update-success" focusable>
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-2">
                Password Updated!
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
                Your password has been successfully changed. Make sure to remember your new password.
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
    // Password visibility toggle function
    function togglePasswordVisibility(inputId, eyeId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Change to eye-slash icon when password is visible
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
            `;
        } else {
            passwordInput.type = 'password';
            // Change back to eye icon when password is hidden
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('passwordUpdateForm');
        const updateButton = document.getElementById('passwordUpdateButton');
        const updateButtonText = document.getElementById('passwordUpdateButtonText');
        const updateSpinner = document.getElementById('passwordUpdateSpinner');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // Show loading state
                updateButton.disabled = true;
                updateButtonText.textContent = 'Updating...';
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
                        // Reset form
                        form.reset();
                        
                        // Show success modal
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'password-update-success' }));
                    } else {
                        alert('Error: ' + (data.message || 'Password update failed'));
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
                        alert('An error occurred during password update');
                    }
                })
                .finally(() => {
                    // Reset loading state
                    updateButton.disabled = false;
                    updateButtonText.textContent = 'Apply Changes';
                    updateSpinner.classList.add('hidden');
                });
            });
        }
    });
    </script>
</section>
