<x-guest-layout>
    <!-- Session Status as modal -->
    @if(session('status'))
        <div id="status-modal" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 z-50 max-w-md w-full">
                <h3 class="text-lg font-medium mb-2">
                    @if(session('status') === 'approved')
                        Account Approved
                    @else
                        Registration submitted
                    @endif
                </h3>
                <p class="mb-4">
                    @if(session('status') === 'approved')
                        Your employee ID has been verified and your account has been approved. You may now login to access the LGU-Bulusan Human Resource Information System.
                    @else
                        {{ session('status') }}
                    @endif
                </p>
                <div class="flex justify-end">
                    <button id="status-dismiss" class="px-4 py-2 bg-green-600 text-white rounded">OK</button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('status-dismiss').addEventListener('click', function () {
                    document.getElementById('status-modal').style.display = 'none';
                });
            });
        </script>
    @endif
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Employee ID -->
        <div class="mt-4">
            <x-input-label for="employee_id" :value="__('Employee ID')" />
            <x-text-input id="employee_id" class="block mt-1 w-full" type="text" name="employee_id" :value="old('employee_id')" required />
            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
    <div class="text-sm mt-6 text-center" style="color: #198f51;">
        After registering, please wait for verification of your Employee ID by an administrator before you can log in.
    </div>
    </form>
</x-guest-layout>
