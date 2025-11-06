<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Pending Users</h2>

        <div class="flex items-center space-x-3">
            <input wire:model.debounce.300ms="search" type="text" placeholder="Search name or email" class="border rounded px-3 py-1" />
            <select wire:model="perPage" class="border rounded px-2 py-1">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
            </select>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-3 text-green-700 bg-green-100 px-3 py-2 rounded">{{ session('success') }}</div>
    @endif

    @if($pendingUsers->isEmpty())
        <p>No users awaiting approval.</p>
    @else
        <div class="mb-2 text-sm text-gray-600">Total pending: <span class="font-medium">{{ $pendingUsers->total() }}</span></div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @foreach($pendingUsers as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
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
                <div class="flex justify-end space-x-2">
                    <button type="button" wire:click="approveUser" wire:loading.attr="disabled" wire:target="approveUser" class="px-4 py-2 bg-green-600 text-white rounded">Yes, approve</button>
                    <button type="button" wire:click="$set('confirmingUserId', null)" wire:loading.attr="disabled" class="px-4 py-2 border rounded">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
