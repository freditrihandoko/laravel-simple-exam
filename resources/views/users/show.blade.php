<x-app-layout>
    <x-slot name="title">
        User Details
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>

    <x-content>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8">
            <div class="h-auto rounded-lg mb-6">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight mb-1">
                    {{ $user->name }}
                </h3>
                <h4 class="font-semibold text-base text-gray-700 dark:text-gray-200 leading-tight mb-4">
                    {{ $user->username }} - {{ $user->email }}
                </h4>

                <div class="mb-4">
                    <h4 class="font-semibold text-md text-gray-800 dark:text-gray-200 leading-tight">
                        Exam Results
                    </h4>
                    <ul class="list-disc list-inside">
                        @forelse ($examResults as $examResult)
                            <li>{{ $examResult->exam->title }}: {{ $examResult->score }}</li>
                        @empty
                            <li>No exam results found</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="h-auto rounded-lg mb-6">
                <div class="mb-4">
                    <h4 class="font-semibold text-md text-gray-800 dark:text-gray-200 leading-tight">
                        Groups
                    </h4>
                    <ul class="list-disc list-inside">
                        @foreach ($userGroups as $group)
                            <li>{{ $group->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-md text-gray-800 dark:text-gray-200 leading-tight">
                        Add to Group
                    </h4>
                    <form id="addToGroupForm" method="POST">
                        @csrf
                        <select id="group_id" name="group_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="mt-2 px-4 py-2 rounded bg-indigo-600 text-white">Add to
                            Group</button>
                    </form>
                </div>
            </div>
        </div>

    </x-content>

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
            $('#addToGroupForm').submit(function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const userId = '{{ $user->id }}';

                $.ajax({
                    url: `{{ url('users/${userId}/add-to-group') }}`,
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
                            showFlasher('Error adding user to group', 'error');
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
