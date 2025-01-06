<x-app-layout>
    <x-slot name="title">
        Exam Results
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exam Results') }}
        </h2>
    </x-slot>

    <x-content>
        <x-flash-message></x-flash-message>
        <div class="container mx-auto p-4 overflow-x-auto text-center">
            <h2 class="text-2xl font-bold mb-4">Exam Results Management</h2>
            <form method="GET" action="{{ route('exam_results.index') }}" class="mb-4">
                <div class="flex flex-wrap -mx-2">
                    <div class="w-full md:w-1/4 px-2 mb-4">
                        <label for="exam"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Exam</label>
                        <select id="exam" name="exam"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Exams</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}"
                                    {{ request('exam') == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/4 px-2 mb-4">
                        <label for="group"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Group</label>
                        <select id="group" name="group"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Groups</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}"
                                    {{ request('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/4 px-2 mb-4">
                        <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date
                            Range</label>
                        <input type="text" id="date_range" name="date_range" value="{{ request('date_range') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="w-full md:w-1/4 px-2 mb-4">
                        <label for="sort_field" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Sort
                            By</label>
                        <select id="sort_field" name="sort_field"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="start_time" {{ request('sort_field') == 'start_time' ? 'selected' : '' }}>
                                Start Time</option>
                            <option value="exam_duration"
                                {{ request('sort_field') == 'exam_duration' ? 'selected' : '' }}>Duration</option>
                            <option value="user.name" {{ request('sort_field') == 'user.name' ? 'selected' : '' }}>User
                                Name</option>
                            <option value="score" {{ request('sort_field') == 'score' ? 'selected' : '' }}>Score
                            </option>
                            <option value="status" {{ request('sort_field') == 'status' ? 'selected' : '' }}>Status
                            </option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/4 px-2 mb-4">
                        <label for="sort_direction"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Sort Direction</label>
                        <select id="sort_direction" name="sort_direction"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending
                            </option>
                            <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>
                                Descending</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">Filter</button>
                </div>

            </form>
            <form method="POST" action="{{ route('exam_results.bulk_action') }}" id="bulkActionForm">
                @csrf
                <div class="flex justify-start mb-4">
                    <select name="action" id="actionSelect"
                        class="mr-2 rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Action</option>
                        <option value="delete">Delete</option>
                        <option value="stop">Stop</option>
                        <option value="open">Open</option>
                        <option value="extend">Extend Time</option>
                    </select>
                    <input type="number" name="extend_minutes" id="extendMinutesInput"
                        class="hidden mr-2 rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Minutes" min="1">
                    <button type="button" id="bulkActionSubmitButton"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">Apply</button>
                </div>

                <table
                    class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm dark:divide-gray-700 dark:bg-gray-900 rounded-lg overflow-hidden">
                    <thead class="ltr:text-left rtl:text-right">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                <input type="checkbox" id="selectAll"
                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded">
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">No</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Exam Name
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Start Time
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Duration
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Group Name
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">User Name
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Score</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examResults as $examResult)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" name="selected[]" value="{{ $examResult->id }}"
                                        class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded">
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $examResults->firstItem() + $loop->index }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $examResult->exam->title }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $examResult->start_time }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $examResult->exam->exam_duration }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $examResult->user->groups->first()->name }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $examResult->user->name }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $examResult->score }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    @if ($examResult->status == 'in_progress')
                                        @php
                                            $endTime = Carbon\Carbon::parse($examResult->start_time)->addMinutes(
                                                $examResult->exam->exam_duration,
                                            );
                                            $remainingTime = $endTime->diffForHumans(null, true);
                                        @endphp
                                        In Progress ({{ $remainingTime }} left)
                                    @else
                                        {{ $examResult->status }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $examResults->appends(request()->input())->links() }}

                </div>
            </form>
            <div class="flex justify-start mb-4">
                <a href="{{ route('exam_results.export', ['exam' => request('exam'), 'group' => request('group'), 'date_range' => request('date_range')]) }}"
                    class="px-4 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">
                    Export to Excel
                </a>
            </div>
        </div>
        <!-- Modal Confirm Bulk Action -->
        <div id="confirmModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Confirm Action</h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6">Are you sure you want to perform this action?</p>
                <div class="flex justify-end">
                    <button type="button" id="cancelButton"
                        class="px-4 py-2 mr-2 bg-gray-300 text-gray-700 rounded-md shadow-sm hover:bg-gray-400">Cancel</button>
                    <button type="button" id="confirmButton"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">Confirm</button>
                </div>
            </div>
        </div>
    </x-content>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Select/Deselect all checkboxes
            $('#selectAll').on('click', function() {
                $('input[name="selected[]"]').prop('checked', this.checked);
            });

            $(document).ready(function() {
                $('#actionSelect').change(function() {
                    const selectedAction = $(this).val();
                    if (selectedAction === 'extend') {
                        $('#extendMinutesInput').removeClass('hidden');
                    } else {
                        $('#extendMinutesInput').addClass('hidden');
                    }
                });


                // Show modal on submit button click
                $('#bulkActionSubmitButton').on('click', function() {
                    $('#confirmModal').removeClass('hidden');
                });

                // Hide modal on cancel button click
                $('#cancelButton').on('click', function() {
                    $('#confirmModal').addClass('hidden');
                });

                // Submit form on confirm button click
                $('#confirmButton').on('click', function() {
                    $('#bulkActionForm').submit();
                });
            });
        });
    </script>
</x-app-layout>
