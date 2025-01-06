<x-app-layout>
    <x-slot name="title">
        Groups
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Groups') }}
        </h2>
    </x-slot>

    <x-content>
        <div class="overflow-x-auto text-center">
            <div class="flex justify-between mb-4">
                <div class="flex">
                    <form action="{{ route('groups.index') }}" method="GET" class="flex items-center">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="mr-2 px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200"
                            placeholder="Search by name...">
                        <button type="submit"
                            class="inline-block rounded bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700">
                            Search
                        </button>
                    </form>
                </div>
                <div>
                    <button id="addGroupButton"
                        class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                        Add New Group
                    </button>
                </div>
            </div>
            <table
                class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm dark:divide-gray-700 dark:bg-gray-900 rounded-lg overflow-hidden">
                <thead class="ltr:text-left rtl:text-right">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">No</th>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                            Name
                        </th>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                            Status
                        </th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($groups as $group)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                {{ $groups->firstItem() + $loop->index }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                {{ $group->name }}</td>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                {{ $group->status }}</td>
                            <td class="whitespace-nowrap px-4 py-2">
                                <button
                                    class="editGroupButton inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600"
                                    data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                    data-status="{{ $group->status }}">
                                    Edit
                                </button>
                                <a href="groups/{{ $group->id }}"
                                    class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $groups->appends(request()->query())->links() }}
        </div>
    </x-content>
    <!-- Modal for Adding and Editing Group -->
    <div id="groupModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded-lg shadow-lg w-1/2">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Add New Group</h2>
            <form id="groupForm" novalidate>
                <div class="mb-4">
                    <label for="group_name" class="block text-gray-700 dark:text-gray-200">Group Name</label>
                    <input type="text" id="group_name" name="group_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>
                </div>
                <div class="mb-4">
                    <label for="group_status" class="block text-gray-700 dark:text-gray-200">Status</label>
                    <select id="group_status" name="group_status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelButton"
                        class="mr-4 px-4 py-2 rounded bg-gray-500 text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Save Group</button>
                </div>
                <input type="hidden" id="group_id" name="group_id">
            </form>
        </div>
    </div>

    <!-- Flasher -->
    <div id="flasher" class="hidden fixed top-4 right-4 z-50 max-w-sm w-full px-4 py-3 shadow-md" role="alert">
        <div class="flex">
            <div id="flasher-icon" class="py-1"></div>
            <div>
                <p class="font-bold">Notification</p>
                <p class="text-sm" id="flasher-message"></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#addGroupButton').click(function() {
                $('#groupModal').removeClass('hidden');
                $('#modalTitle').text('Add New Group');
                $('#groupForm')[0].reset();
                $('#group_id').val('');
            });

            $('.editGroupButton').click(function() {
                $('#groupModal').removeClass('hidden');
                $('#modalTitle').text('Edit Group');
                $('#group_name').val($(this).data('name'));
                $('#group_status').val($(this).data('status'));
                $('#group_id').val($(this).data('id'));
            });

            $('#cancelButton').click(function() {
                $('#groupModal').addClass('hidden');
            });

            $('#groupForm').submit(function(e) {
                e.preventDefault();

                const formData = $(this).serializeArray();
                const groupId = $('#group_id').val();
                let url = '{{ route('groups.store') }}';
                let method = 'POST';

                if (groupId) {
                    url = `groups/${groupId}`;
                    method = 'PUT';
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showFlasher(response.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            for (const key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessages += errors[key][0] + '\n';
                                }
                            }
                            showFlasher('Validation Error:\n' + errorMessages, 'error');
                        } else {
                            showFlasher('Error saving group', 'error');
                        }
                    }
                });
            });
        });

        function showFlasher(message, type = 'success') {
            const flasher = $('#flasher');
            const flasherIcon = $('#flasher-icon');
            const flasherMessage = $('#flasher-message');

            if (type === 'success') {
                flasher.removeClass('bg-red-100 border-red-500 text-red-900');
                flasherIcon.removeClass('text-red-500');
                flasher.addClass('bg-green-100 border-green-500 text-green-900');
                flasherIcon.addClass('text-green-500');
                flasherIcon.html(
                    `<svg class="fill-current h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm0-4h-2V7h2v8z"/></svg>`
                );
            } else {
                flasher.removeClass('bg-green-100 border-green-500 text-green-900');
                flasherIcon.removeClass('text-green-500');
                flasher.addClass('bg-red-100 border-red-500 text-red-900');
                flasherIcon.addClass('text-red-500');
                flasherIcon.html(
                    `<svg class="fill-current h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm0-4h-2V7h2v8z"/></svg>`
                );
            }

            flasherMessage.text(message);
            flasher.removeClass('hidden');
            setTimeout(function() {
                flasher.addClass('hidden');
            }, 3000);
        }
    </script>
</x-app-layout>
