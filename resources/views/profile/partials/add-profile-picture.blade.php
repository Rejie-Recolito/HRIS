<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Picture') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('choose a picture to use for your account') }}
        </p>
    </header>


<form method="POST" action="{{ route('profile.picture.upload') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-4">
        <label for="profile_picture" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profile Picture</label>
        <input type="file" name="profile_picture" id="profile_picture" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-300 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
    </div>

    <div class="flex items-center justify-end">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Upload
        </button>
    </div>
</form>
