<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium heading-in-profile-page">
            {{ __('PROFILE IMAGE') }}
        </h2>
        <p class="mt-1 text-sm description-in-profile-page">
            {{ __('Upload an image to use as profile picture for your account') }}
        </p>
    </header>

    <form id="profilePictureForm" method="POST" action="{{ route('profile.picture.upload') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="profile_picture" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload from your files</label>
            <input type="file" name="profile_picture" id="profile_picture" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-300 border-[#198f51] rounded-md shadow-sm focus:ring-[#198f51] focus:border-[#198f51]">
        </div>

        <div class="flex items-center justify-start">
            <button type="submit" id="uploadButton" class="inline-flex items-center px-4 py-2 bg-[#198f51] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <span id="uploadButtonText">Upload</span>
                <span id="uploadSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>
    </form>

    <!-- Success Modal -->
    <x-modal name="profile-picture-success" focusable>
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-2">
                Success!
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
                Your profile picture has been updated successfully.
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
        const form = document.getElementById('profilePictureForm');
        const uploadButton = document.getElementById('uploadButton');
        const uploadButtonText = document.getElementById('uploadButtonText');
        const uploadSpinner = document.getElementById('uploadSpinner');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            // Show loading state
            uploadButton.disabled = true;
            uploadButtonText.textContent = 'Uploading...';
            uploadSpinner.classList.remove('hidden');

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    form.reset();
                    
                    // Show success modal
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'profile-picture-success' }));
                    
                    // Update any profile pictures on the page
                    const profileImages = document.querySelectorAll('img[alt="Profile Picture"]');
                    profileImages.forEach(img => {
                        img.src = data.profile_picture_url;
                    });
                } else {
                    alert('Error: ' + (data.message || 'Upload failed'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during upload');
            })
            .finally(() => {
                // Reset loading state
                uploadButton.disabled = false;
                uploadButtonText.textContent = 'Upload';
                uploadSpinner.classList.add('hidden');
            });
        });
    });
    </script>
</section>
