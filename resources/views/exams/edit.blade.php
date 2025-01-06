<x-app-layout>
    <x-slot name="title">
        Edit Exam
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Exam') }}
        </h2>
    </x-slot>

    <x-content>
        @if ($errors->any())
            <div class="text-red-500">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <form action="{{ route('exams.update', $exam->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-gray-700 dark:text-gray-200">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $exam->title) }}"
                    class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 dark:text-gray-200">Description</label>
                <textarea id="description" name="description"
                    class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $exam->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="exam_duration" class="block text-gray-700 dark:text-gray-200">Duration (minutes)</label>
                <input type="number" id="exam_duration" name="exam_duration"
                    value="{{ old('exam_duration', $exam->exam_duration) }}"
                    class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('exam_duration') border-red-500 @enderror">
                @error('exam_duration')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="exam_start" class="block text-gray-700 dark:text-gray-200">Start Time</label>
                <input type="datetime-local" id="exam_start" name="exam_start"
                    value="{{ old('exam_start', $exam->exam_start) }}"
                    class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('exam_start') border-red-500 @enderror">
                @error('exam_start')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="exam_end" class="block text-gray-700 dark:text-gray-200">End Time</label>
                <input type="datetime-local" id="exam_end" name="exam_end"
                    value="{{ old('exam_end', $exam->exam_end) }}"
                    class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('exam_end') border-red-500 @enderror">
                @error('exam_end')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="shuffle_questions" class="block text-gray-700 dark:text-gray-200">Shuffle Questions</label>
                <input type="checkbox" id="shuffle_questions" name="shuffle_questions"
                    {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }}
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                @error('shuffle_questions')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="shuffle_answers" class="block text-gray-700 dark:text-gray-200">Shuffle Answers</label>
                <input type="checkbox" id="shuffle_answers" name="shuffle_answers"
                    {{ old('shuffle_answers', $exam->shuffle_answers) ? 'checked' : '' }}
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                @error('shuffle_answers')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="show_score" class="block text-gray-700 dark:text-gray-200">Show Score</label>
                <input type="checkbox" id="show_score" name="show_score"
                    {{ old('show_score', $exam->show_score) ? 'checked' : '' }}
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                @error('show_score')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="groups" class="block text-gray-700 dark:text-gray-200">Groups</label>
                <select id="groups" name="groups[]" multiple
                    class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('groups') border-red-500 @enderror">
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}"
                            {{ in_array($group->id, old('groups', $exam->groups->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                @error('groups')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Existing Topics</label>
                @foreach ($exam->topics as $topic)
                    <div class="mb-2 flex flex-col">
                        <div>
                            <span class="bg-indigo-600 text-white p-2 rounded  leading-none flex items-center">
                                {{ $topic->name }} <span
                                    class="bg-white p-1 rounded text-indigo-600 text-xs ml-2">{{ $topic->pivot->num_questions }}</span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div> --}}
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200">Existing Topics</label>
                @foreach ($exam->topics as $topic)
                    <div class="mb-2 flex flex-col">
                        <div>
                            <button type="button"
                                class="bg-indigo-600 text-white p-2 rounded leading-none flex items-center">
                                {{ $topic->name }}
                                <span class="bg-white p-1 rounded text-indigo-600 text-xs ml-2">
                                    {{ $topic->pivot->num_questions }}
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($examHasResults)
                <p class="text-red-500">You cannot add new topics because this exam has results.</p>
            @else
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Add New Topics</label>
                    @foreach ($topics as $topic)
                        @if (!$exam->topics->contains($topic->id))
                            <div class="mb-2">
                                <input type="checkbox" id="topic_{{ $topic->id }}" name="topics[]"
                                    value="{{ $topic->id }}"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded @error('topics.' . $topic->id) border-red-500 @enderror"
                                    {{ in_array($topic->id, old('topics', [])) ? 'checked' : '' }}>
                                <label for="topic_{{ $topic->id }}"
                                    class="text-gray-700 dark:text-gray-200">{{ $topic->name }}</label>
                                <div class="ml-2">
                                    <input type="number" name="num_questions[{{ $topic->id }}]"
                                        placeholder="Number of Questions"
                                        value="{{ old('num_questions.' . $topic->id, '') }}"
                                        class="ml-6 w-24 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('num_questions.' . $topic->id) border-red-500 @enderror">
                                    <div class="ml-2">
                                        <input type="checkbox" id="multiple_choice_{{ $topic->id }}"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                            name="question_type[{{ $topic->id }}][]" value="multiple_choice"
                                            {{ is_array(old('question_type.' . $topic->id)) && in_array('multiple_choice', old('question_type.' . $topic->id)) ? 'checked' : '' }}>
                                        <label for="multiple_choice_{{ $topic->id }}"
                                            class="text-gray-700 dark:text-gray-200">Multiple Choice</label>
                                        <input type="checkbox" id="essay_{{ $topic->id }}"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                            name="question_type[{{ $topic->id }}][]" value="essay"
                                            {{ is_array(old('question_type.' . $topic->id)) && in_array('essay', old('question_type.' . $topic->id)) ? 'checked' : '' }}>
                                        <label for="essay_{{ $topic->id }}"
                                            class="text-gray-700 dark:text-gray-200">Essay</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('exams.index') }}"
                    class="rounded bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">Cancel</a>
                <button type="submit"
                    class="ml-4 rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Update Exam</button>
            </div>
        </form>
    </x-content>
</x-app-layout>
