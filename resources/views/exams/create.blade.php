<x-app-layout>
    <x-slot name="title">
        Add New Exam
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Exam') }}
        </h2>
    </x-slot>

    <x-content>
        <form action="{{ route('exams.store') }}" method="POST">
            @csrf
            <div class="sm:grid sm:grid-cols-1 md:grid md:grid-cols-1 lg:grid lg:grid-cols-2 lg:gap-8">
                <div>
                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 dark:text-gray-200">Title</label>
                        <input type="text" id="title" name="title"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('title') border-red-500 @enderror"
                            value="{{ old('title') }}" required>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 dark:text-gray-200">Description</label>
                        <textarea id="description" name="description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="exam_duration" class="block text-gray-700 dark:text-gray-200">Duration (in
                            minutes)</label>
                        <input type="number" id="exam_duration" name="exam_duration"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('exam_duration') border-red-500 @enderror"
                            value="{{ old('exam_duration') }}" required>
                        @error('exam_duration')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="exam_start" class="block text-gray-700 dark:text-gray-200">Start Date and
                            Time</label>
                        <input type="datetime-local" id="exam_start" name="exam_start"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('exam_start') border-red-500 @enderror"
                            value="{{ old('exam_start') }}" required>
                        @error('exam_start')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="exam_end" class="block text-gray-700 dark:text-gray-200">End Date and Time</label>
                        <input type="datetime-local" id="exam_end" name="exam_end"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('exam_end') border-red-500 @enderror"
                            value="{{ old('exam_end') }}" required>
                        @error('exam_end')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Shuffle Questions</label>
                        <input type="checkbox" name="shuffle_questions" id="shuffle_questions"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Shuffle Answers</label>
                        <input type="checkbox" name="shuffle_answers" id="shuffle_answers"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200">Show Score</label>
                        <input type="checkbox" name="show_score" id="show_score"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <label for="topics" class="block text-gray-700 dark:text-gray-200">Topics</label>
                        <select multiple name="topics[]" id="topics"
                            class="rounded-lg w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}"
                                    {{ in_array($topic->id, old('topics', [])) ? 'selected' : '' }}>
                                    {{ $topic->name }} ({{ $topic->questions->count() }} questions)
                                </option>
                            @endforeach
                        </select>
                        @error('topics')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="num_questions" class="block font-bold text-gray-700 dark:text-gray-200">Number of
                            Questions</label>
                        @foreach ($topics as $topic)
                            <div class="mb-2" id="num_questions_input_{{ $topic->id }}">
                                <label for="num_questions_{{ $topic->id }}"
                                    class="text-gray-700 dark:text-gray-200">{{ $topic->name }}</label>
                                <input type="number" name="num_questions[{{ $topic->id }}]"
                                    id="num_questions_{{ $topic->id }}" placeholder="Number of questions"
                                    class="ml-2 px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('num_questions.' . $topic->id) border-red-500 @enderror"
                                    value="{{ old('num_questions.' . $topic->id) }}">
                                @error('num_questions.' . $topic->id)
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <label for="question_types" class="block font-bold text-gray-700 dark:text-gray-200">Question
                            Types</label>
                        @foreach ($topics as $topic)
                            <div class="mb-2" id="question_types_input_{{ $topic->id }}">
                                <label class="block text-gray-700 dark:text-gray-200">{{ $topic->name }}</label>
                                <input type="checkbox" name="question_types[{{ $topic->id }}][]"
                                    value="multiple_choice"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="multiple_choice" class="ml-2 text-gray-700 dark:text-gray-200">Multiple
                                    Choice</label>
                                <input type="checkbox" name="question_types[{{ $topic->id }}][]" value="essay"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="essay" class="ml-2 text-gray-700 dark:text-gray-200">Essay</label>
                                @error('question_types.' . $topic->id)
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <label for="groups" class="block text-gray-700 dark:text-gray-200">Groups</label>
                        <select multiple name="groups[]" id="groups"
                            class="rounded-lg w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}"
                                    {{ in_array($group->id, old('groups', [])) ? 'selected' : '' }}>
                                    {{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('groups')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit"
                    class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">Save</button>
            </div>
        </form>
    </x-content>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Hide all num_questions inputs initially
            $('[id^="num_questions_input"]').hide();
            $('[id^="question_types_input"]').hide();

            // Show num_questions input when topic is selected
            $('#topics').change(function() {
                $('[id^="num_questions_input"]').hide(); // Hide all num_questions inputs first
                $('[id^="question_types_input"]').hide(); // Hide all question_types inputs first
                $('#topics option:selected').each(function() {
                    var topicId = $(this).val();
                    $('#num_questions_input_' + topicId).show();
                    $('#question_types_input_' + topicId).show();
                });
            });
        });
    </script>
</x-app-layout>
