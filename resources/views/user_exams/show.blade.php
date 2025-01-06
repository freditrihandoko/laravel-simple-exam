<x-app-layout>
    <x-slot name="title">
        Exam Details
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exam Details') }}
        </h2>
    </x-slot>

    <x-content>
        <div class="bg-white shadow-md rounded-lg px-4 py-5 dark:bg-gray-900">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $exam->title }}
            </h3>
            <p class="text-gray-700 dark:text-gray-200">{{ $exam->description }}</p>
            <p class="text-gray-700 dark:text-gray-200">Duration: {{ $exam->exam_duration }} minutes</p>
            <p class="text-gray-700 dark:text-gray-200">Start Time: {{ $exam->exam_start }}</p>
            <p class="text-gray-700 dark:text-gray-200">End Time: {{ $exam->exam_end }}</p>
            <p class="text-gray-700 dark:text-gray-200">Shuffle Questions: {{ $exam->shuffle_questions ? 'Yes' : 'No' }}
            </p>
            <p class="text-gray-700 dark:text-gray-200">Shuffle Answers: {{ $exam->shuffle_answers ? 'Yes' : 'No' }}</p>
            <hr class="my-4 border-gray-300 dark:border-gray-700">
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Topics</h4>
            <div class="grid grid-cols-1 gap-4">
                @foreach ($exam->topics as $topic)
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow">
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $topic->name }}</p>
                        <p class="text-gray-700 dark:text-gray-300">Number of Questions:
                            {{ $topic->pivot->num_questions }}</p>
                        <hr class="my-2 border-gray-300 dark:border-gray-700">
                        @foreach ($topic->questions as $question)
                            <div class="mb-2">
                                <p class="text-gray-900 dark:text-gray-100">{{ $question->question_text }}</p>
                                <ul class="ml-4 list-disc">
                                    @foreach ($question->answers as $answer)
                                        <li class="text-gray-700 dark:text-gray-300">{{ $answer->answer_text }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route('user_exams.index') }}"
                class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Back to Exams</a>
        </div>
    </x-content>
</x-app-layout>
