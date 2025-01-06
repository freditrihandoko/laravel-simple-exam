<x-app-layout>
    <x-slot name="title">
        Exams
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exams') }}
        </h2>
    </x-slot>

    <x-content>
        <x-flash-message> </x-flash-message>
        @if ($errors->any())
            <div class="text-red-500 font-semibold text-lg">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <div class="overflow-x-auto text-center">
            <div class="flex justify-between mb-4">
                <div class="flex">
                    <form action="{{ route('exams.index') }}" method="GET" class="flex items-center">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="mr-2 px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200"
                            placeholder="Search by title...">
                        <input type="text" name="date_range" id="date_range" value="{{ $dateRange ?? '' }}"
                            class="mr-2 px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200"
                            placeholder="Select date range">
                        <button type="submit"
                            class="inline-block rounded bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700">
                            Search
                        </button>
                    </form>
                </div>
                <div>
                    <a href="{{ route('exams.create') }}"
                        class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                        Add New Exam
                    </a>
                </div>
            </div>
            <form id="bulk-reschedule-form" action="{{ route('exams.bulkReschedule') }}" method="POST">
                @csrf
                <div class="flex justify-start mb-4">
                    <select id="reschedule-action" name="reschedule_action"
                        class="ml-1 mr-2 h-10 w-28 border rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="+1day">+1 Day</option>
                        <option value="-1day">-1 Day</option>
                        <option value="+1hour">+1 Hour</option>
                        <option value="-1hour">-1 Hour</option>
                        <option value="+7days">+1 Week</option>
                        <option value="-7days">-1 Week</option>
                    </select>
                    <button type="button" id="bulk-reschedule-button"
                        class="inline-block rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Reschedule
                    </button>
                </div>
                <table
                    class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm dark:divide-gray-700 dark:bg-gray-900 rounded-lg overflow-hidden">
                    <thead class="ltr:text-left rtl:text-right">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                <input type="checkbox" id="select-all"
                                    class="text-indigo-600 transition duration-150 ease-in-out rounded">
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">No
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Title
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                Description</th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                Duration
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Start
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">End
                            </th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($exams as $exam)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2">
                                    <input type="checkbox" name="exam_ids[]" value="{{ $exam->id }}"
                                        class="text-indigo-600 transition duration-150 ease-in-out rounded">
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                    {{ $exams->firstItem() + $loop->index }} </td>
                                <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                    {{ $exam->title }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ Str::limit($exam->description, 20) }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $exam->exam_duration }} minutes</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $exam->exam_start }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $exam->exam_end }}</td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    <a href="{{ route('exams.show', $exam) }}"
                                        class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">View</a>
                                    <a href="{{ route('exams.edit', $exam) }}"
                                        class="inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600">Edit</a>
                                    <button type="button"
                                        class="delete-button inline-block rounded bg-red-600 px-4 py-2 text-xs font-medium text-white hover:bg-red-700"
                                        data-exam-id="{{ $exam->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
            {{ $exams->appends(request()->query())->links() }}
        </div>
    </x-content>

    <div id="delete-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-1/4 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Confirmation</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this exam?</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirm-delete"
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Yes, Delete
                    </button>
                    <button id="cancel-delete"
                        class="mt-2 px-4 py-2 bg-gray-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        No, Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="reschedule-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-1/4 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reschedule Confirmation</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to reschedule the selected exams?</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirm-reschedule"
                        class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Yes, Reschedule
                    </button>
                    <button id="cancel-reschedule"
                        class="mt-2 px-4 py-2 bg-gray-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        No, Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#select-all').on('click', function() {
                $('input[name="exam_ids[]"]').prop('checked', this.checked);
            });

            $('.delete-button').on('click', function() {
                var examId = $(this).data('exam-id');
                var form = $('#delete-form');
                form.attr('action', '/exams/' + examId);
                $('#delete-modal').removeClass('hidden');
            });

            $('#confirm-delete').on('click', function() {
                $('#delete-form').submit();
            });

            $('#cancel-delete').on('click', function() {
                $('#delete-modal').addClass('hidden');
            });

            $('#bulk-reschedule-button').on('click', function() {
                $('#reschedule-modal').removeClass('hidden');
            });

            $('#confirm-reschedule').on('click', function() {
                $('#bulk-reschedule-form').submit();
            });

            $('#cancel-reschedule').on('click', function() {
                $('#reschedule-modal').addClass('hidden');
            });
        });
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

            $('#bulk-reschedule-button').on('click', function() {
                $('#bulk-reschedule-form').submit();
            });

            $('#select-all').on('click', function() {
                $('input[name="exam_ids[]"]').prop('checked', this.checked);
            });
        });
    </script>
</x-app-layout>
