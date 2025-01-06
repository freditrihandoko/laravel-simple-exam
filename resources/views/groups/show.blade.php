<x-app-layout>
    <x-slot name="title">
        Group Details
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $group->name }}
        </h2>
    </x-slot>

    <x-content>
        <div class="overflow-x-auto text-center">
            <div class="flex justify-end mb-4">
                <button id="importExcelButton"
                    class="mr-2 inline-block rounded bg-green-600 px-4 py-2 text-xs font-medium text-white hover:bg-green-700">
                    Import Users from Excel
                </button>
                <button id="addUserButton"
                    class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                    Add Users to Group
                </button>
            </div>
            <table class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm dark:divide-gray-700 dark:bg-gray-900">
                <thead class="ltr:text-left rtl:text-right">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">No</th>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Name</th>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Email</th>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Username</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($group->users as $user)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                {{ $loop->iteration }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                {{ $user->name }}</td>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                {{ $user->email }}</td>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                {{ $user->username }}</td>
                            <td class="whitespace-nowrap px-4 py-2">
                                <button
                                    class="removeUserButton inline-block rounded bg-red-600 px-4 py-2 text-xs font-medium text-white hover:bg-red-700"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-content>

    <!-- Modal for Adding Users to Group -->
    <div id="userModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded-lg shadow-lg w-2/3">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Users to Group</h2>
            <form id="userForm">
                <div class="mb-4">
                    <input type="text" id="userSearch" placeholder="Search Users"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="mb-4 max-h-96 overflow-y-auto">
                    @foreach ($users as $user)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="user_{{ $user->id }}" name="user_ids[]"
                                value="{{ $user->id }}"
                                class="mr-2 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="user_{{ $user->id }}" class="text-gray-700 dark:text-gray-200">
                                {{ $user->name }} ({{ $user->email }})
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelButton"
                        class="mr-4 px-4 py-2 rounded bg-gray-500 text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Add Users</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Importing Users to Group -->
    <div id="importUserModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded-lg shadow-lg w-2/3">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Import Users to Group</h2>
            <form id="importUserForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <input type="file" id="importFile" name="file"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="mb-4">
                    <div id="importErrorMessages" class="text-red-600"></div>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelImportButton"
                        class="mr-4 px-4 py-2 rounded bg-gray-500 text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Import Users</button>
                </div>
            </form>
            {{-- <div id="importResultMessages" class="text-green-600 mt-4"></div> --}}
            <div id="importErrorMessages" class="text-red-500 hidden mt-3 hidden"></div>
            <div id="importValidRows" class=" mt-3 hidden"></div>
        </div>
    </div>

    <!-- Modal for Removing User Confirmation -->
    <div id="removeUserModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Confirm Remove User</h2>
            <p id="confirmMessage" class="mb-4"></p>
            <div class="flex justify-end">
                <button type="button" id="cancelRemoveButton"
                    class="mr-4 px-4 py-2 rounded bg-gray-500 text-white">Cancel</button>
                <button type="button" id="confirmRemoveButton"
                    class="px-4 py-2 rounded bg-red-600 text-white">Remove</button>
            </div>
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
            let userIdToRemove = null;

            $('#addUserButton').click(function() {
                $('#userModal').removeClass('hidden');
                $('#modalTitle').text('Add Users to Group');
                $('#userForm')[0].reset();
                $('#userSearch').val('');
                filterUsers();
            });

            $('#cancelButton').click(function() {
                $('#userModal').addClass('hidden');
            });

            $('#cancelRemoveButton').click(function() {
                $('#removeUserModal').addClass('hidden');
                userIdToRemove = null;
            });

            $('.removeUserButton').click(function() {
                userIdToRemove = $(this).data('id');
                $('#confirmMessage').text(
                    `Are you sure you want to remove ${$(this).data('name')} from the group?`);
                $('#removeUserModal').removeClass('hidden');
            });

            $('#confirmRemoveButton').click(function() {
                if (userIdToRemove) {
                    const url = `{{ route('groups.removeUser', [$group->id, ':userId']) }}`.replace(
                        ':userId', userIdToRemove);

                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            showFlasher(response.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        },
                        error: function() {
                            showFlasher('Error removing user from group', 'error');
                        }
                    });

                    $('#removeUserModal').addClass('hidden');
                    userIdToRemove = null;
                }
            });

            $('#userForm').submit(function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const groupId = '{{ $group->id }}';
                const url = `{{ route('groups.addUsers', ':groupId') }}`.replace(':groupId', groupId);

                $.ajax({
                    url: url,
                    method: 'POST',
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
                            showFlasher('Error adding users to group', 'error');
                        }
                    }
                });
            });

            $('#userSearch').on('input', function() {
                filterUsers();
            });

            function filterUsers() {
                const search = $('#userSearch').val().toLowerCase();
                $('div.mb-2').each(function() {
                    const label = $(this).find('label').text().toLowerCase();
                    if (label.includes(search)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            };

            $('#importExcelButton').click(function() {
                $('#importUserModal').removeClass('hidden');
            });

            $('#cancelImportButton').click(function() {
                $('#importUserModal').addClass('hidden');
                location.reload();
            });

            // Submit the import users form
            $('#importUserForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route('groups.importUsers', $group->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let message = response.message;
                        if (response.errors.length) {
                            let errorMessages = '<ul>';
                            response.errors.forEach(function(error) {
                                errorMessages += `<li>${error}</li>`;
                            });
                            errorMessages += '</ul>';
                            $('#importErrorMessages').html(errorMessages);
                            $('#importErrorMessages').removeClass('hidden');

                            if (response.validRows.length) {
                                message += '\nValid Rows:\n';
                                response.validRows.forEach(function(row, index) {
                                    // message +=
                                    //     `Row ${index + 1}: ${row.name} (${row.email}, ${row.username})\n`;
                                    // Build valid row message with styling
                                    const validRowMessage = `
                                            Row ${index + 1}: 
                                            <span class="text-green-600 mb-2">${row.name} 
                                            (${row.email}, ${row.username})</span>
                                        `;
                                    message += validRowMessage;
                                });
                            }
                            $('#importValidRows').html(message.replace(/\n/g, '<br>'));
                            $('#importValidRows').removeClass('hidden');
                        } else {
                            showFlasher(message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '<ul>';
                            let totalValid = xhr.responseJSON.validRows.length;
                            errors.forEach(function(error) {
                                errorMessages += `<li>${error}</li>`;
                            });
                            errorMessages +=
                                `</ul><p class="mt-2 text-green-500"><b>${totalValid}</b> valid data has been imported.</p>`;
                            $('#importErrorMessages').html(errorMessages);
                            $('#importErrorMessages').removeClass('hidden');
                        } else {
                            showFlasher('Error uploading file', 'error');
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
