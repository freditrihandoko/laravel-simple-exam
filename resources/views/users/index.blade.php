<x-app-layout>
    <x-slot name="title">
        Users Management
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users Management') }}
        </h2>
    </x-slot>

    <x-content>
        <div class="overflow-x-auto text-center">
            <div class="flex justify-between mb-4">
                <div class="flex">
                    <form action="{{ route('users.index') }}" method="GET" class="flex items-center">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="mr-2 px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200"
                            placeholder="Search...">
                        <button type="submit"
                            class="inline-block rounded bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700">
                            Search
                        </button>
                    </form>
                </div>
                <div>
                    <button id="importUsersButton"
                        class="mr-2 inline-block rounded bg-green-600 px-4 py-2 text-xs font-medium text-white hover:bg-green-700">
                        Import Users
                    </button>
                    <button id="addUserButton"
                        class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                        Add New User
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
                            Email
                        </th>
                        <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                            Username
                        </th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($users as $user)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                {{ $user->name }}</td>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                {{ $user->email }}</td>
                            <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                {{ $user->username }}</td>
                            <td class="whitespace-nowrap px-4 py-2">
                                <button
                                    class="editUserButton inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}" data-username="{{ $user->username }}">
                                    Edit
                                </button>
                                <a href="users/{{ $user->id }}"
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
            {{ $users->appends(request()->query())->links() }}
        </div>

    </x-content>
    <!-- Modal for Adding and Editing User -->
    <div id="userModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded-lg shadow-lg w-1/2">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Add New User</h2>
            <form id="userForm" novalidate>
                <div class="mb-4">
                    <label for="user_name" class="block text-gray-700 dark:text-gray-200">Name</label>
                    <input type="text" id="user_name" name="user_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>
                </div>
                <div class="mb-4">
                    <label for="user_email" class="block text-gray-700 dark:text-gray-200">Email</label>
                    <input type="email" id="user_email" name="user_email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>
                </div>
                <div class="mb-4">
                    <label for="user_username" class="block text-gray-700 dark:text-gray-200">Username</label>
                    <input type="text" id="user_username" name="user_username"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>
                </div>
                <div class="mb-4">
                    <label for="user_password" class="block text-gray-700 dark:text-gray-200">Password</label>
                    <input type="password" id="user_password" name="user_password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="mb-4">
                    <label for="user_password_confirmation" class="block text-gray-700 dark:text-gray-200">Confirm
                        Password</label>
                    <input type="password" id="user_password_confirmation" name="user_password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancelButton"
                        class="mr-4 px-4 py-2 rounded bg-gray-500 text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Save User</button>
                </div>
                <input type="hidden" id="user_id" name="user_id">
            </form>
        </div>
    </div>

    <!-- Modal for Import Users -->
    <div id="importModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded-lg shadow-lg w-1/2">
            <h2 class="text-xl font-bold mb-4">Import Users</h2>
            <form id="importForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="import_file" class="block text-gray-700 dark:text-gray-200">Choose Excel File</label>
                    <input type="file" id="import_file" name="file"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        accept=".xlsx,.xls,.csv" required>
                </div>
                <div id="importErrorMessages" class="text-red-500 hidden"></div>
                <div class="flex justify-end">
                    <button type="button" id="cancelImportButton"
                        class="mr-4 px-4 py-2 rounded bg-gray-500 text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white">Import Users</button>
                </div>
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
            // Open the add user modal
            $('#addUserButton').click(function() {
                $('#userModal').removeClass('hidden');
                $('#modalTitle').text('Add New User');
                $('#userForm')[0].reset();
                $('#user_id').val('');
                $('#user_password').attr('required', true);
                $('#user_password_confirmation').attr('required', true);
            });

            // Open the edit user modal
            $('.editUserButton').click(function() {
                $('#userModal').removeClass('hidden');
                $('#modalTitle').text('Edit User');
                $('#user_name').val($(this).data('name'));
                $('#user_email').val($(this).data('email'));
                $('#user_username').val($(this).data('username'));
                $('#user_id').val($(this).data('id'));
                $('#user_password').attr('required', false);
                $('#user_password_confirmation').attr('required', false);
            });

            // Open the import users modal
            $('#importUsersButton').click(function() {
                $('#importModal').removeClass('hidden');
                $('#importForm')[0].reset();
            });

            // Close the add/edit user modal
            $('#cancelButton').click(function() {
                $('#userModal').addClass('hidden');
            });

            // Close the import users modal
            $('#cancelImportButton').click(function() {
                $('#importModal').addClass('hidden');
            });

            // Submit the add/edit user form
            $('#userForm').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                const userId = $('#user_id').val();
                let url = '{{ route('users.store') }}';
                let method = 'POST';

                if (userId) {
                    url = `users/${userId}`;
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
                            showFlasher('Error saving user', 'error');
                        }
                    }
                });
            });

            // Submit the import users form
            $('#importForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route('users.import') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let message = response.message;
                        if (response.validRows.length) {
                            message += '\n\nValid Rows:\n';
                            response.validRows.forEach(function(row, index) {
                                message +=
                                    `Row ${index + 1}: ${row.name} (${row.email}, ${row.username})\n`;
                            });
                        }
                        showFlasher(message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
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
