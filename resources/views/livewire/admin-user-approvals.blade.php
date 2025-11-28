<div>
    <div style="height: 32px;"></div>
    <div style="width: 70%; margin: 0 auto;" class="mb-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-lg font-semibold mb-2 md:mb-0">Pending Users for Approval</h2>
            <div class="flex items-center space-x-3">
                <input wire:model.debounce.300ms="search" type="text" placeholder="Search name or email" class="border rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-green-600" style="min-width: 220px;" />
                <select wire:model="perPage" class="border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="5">5 / page</option>
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                </select>
            </div>
        </div>
        <div class="mb-2 text-md text-black" style="text-align: right;">Pending Accounts: <span class="font-medium">{{ $pendingUsers->total() }}</span></div>
    </div>

    <!-- Success Modal -->
    @if(session('success'))
        <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-[#1c1c1d] p-8 rounded-lg shadow-lg border-2" style="border-color: #2bb16b; min-width: 400px;">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-3">Success!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 text-base">{{ session('success') }}</p>
                    <button onclick="document.getElementById('successModal').style.display='none'" class="custom-submit-btn px-6 py-2 rounded-md">Continue</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Approval Error Modal -->
    @if(session('approval_error'))
        <div id="errorModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-[#1c1c1d] p-8 rounded-lg shadow-lg border-2" style="border-color: #d9534f; min-width: 400px;">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-3">Approval blocked</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 text-base">{{ session('approval_error') }}</p>
                    <button onclick="document.getElementById('errorModal').style.display='none'" class="custom-submit-btn px-6 py-2 rounded-md">Continue</button>
                </div>
            </div>
        </div>
    @endif

    @if($pendingUsers->isEmpty())
        <p>No users awaiting approval.</p>
    @else
        <div style="height: 24px;"></div>
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow" style="width: 70%; margin: 0 auto;">
            <table class="admin-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead style="background-color: #198f51;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="background-color: #198f51; color: #fff;">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="background-color: #198f51; color: #fff;">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="background-color: #198f51; color: #fff;">Employee ID</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="background-color: #198f51; color: #fff;">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @foreach($pendingUsers as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->employee_id ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button wire:click="confirmApprove({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded mr-2">Approve</button>
                                <button wire:click="denyUser({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded">Deny</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pendingUsers->links() }}
        </div>
    @endif

    <!-- Confirmation Modal -->
    @if($confirmingUserId)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="absolute inset-0 bg-black opacity-50 z-40"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg p-6 z-50 max-w-md w-full">
                <h3 class="text-lg font-medium mb-2">Confirm approval</h3>
                <p class="mb-4">Are you sure you want to approve <strong>{{ $confirmingUserName }}</strong>?</p>
                @if($confirmingUserEmployeeId)
                    <p class="mb-4 text-sm">Employee ID: <strong>{{ $confirmingUserEmployeeId }}</strong></p>
                @endif
                <div class="flex justify-end space-x-2">
                    <button type="button" wire:click="approveUser" wire:loading.attr="disabled" wire:target="approveUser" class="px-4 py-2 bg-green-600 text-white rounded">Yes, approve</button>
                    <button type="button" wire:click="$set('confirmingUserId', null)" wire:loading.attr="disabled" class="px-4 py-2 border rounded">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
