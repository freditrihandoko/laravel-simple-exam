<x-app-layout>
    <x-slot name="title">
        Export Group Exam Recap
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Export Group Exam Recap') }}
        </h2>
    </x-slot>

    <x-content>
        <x-flash-message></x-flash-message>
        <div class="container mx-auto p-4 overflow-x-auto text-center">
            <h2 class="text-2xl font-bold mb-4">Export Group Exam Recap</h2>
            <form method="GET" action="{{ route('group_exam_export.export') }}" class="mb-4">
                <div class="flex flex-wrap -mx-2">
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label for="group"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Group</label>
                        <select id="group" name="group"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Group</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date
                            Range</label>
                        <input type="text" id="date_range" name="date_range"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">Export to
                        Excel</button>
                </div>
            </form>
        </div>
    </x-content>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $('input[name="date_range"]').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });
    </script>
</x-app-layout>
