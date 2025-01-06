<x-app-layout>
    <x-slot name="title">
        View Exam
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('View Exam') }}
        </h2>
    </x-slot>

    <x-content>

        <!-- Flash Message Component -->
        <x-flash-message />

        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white shadow-md rounded-lg px-6 py-4 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 underline underline-offset-4">
                    Exam
                    Details</h3>
                <ul class="list-disc space-y-2 pl-4">
                    <li><span class="font-bold">Title:</span> {{ $exam->title }}</li>
                    <li><span class="font-bold">Description:</span> {{ $exam->description }}</li>
                    <li><span class="font-bold">Duration (minutes):</span> {{ $exam->exam_duration }}</li>
                    <li><span class="font-bold">Start Time:</span> {{ $exam->exam_start }}</li>
                    <li><span class="font-bold">End Time:</span> {{ $exam->exam_end }}</li>
                    <li><span class="font-bold">Shuffle Questions:</span> {{ $exam->shuffle_questions ? 'Yes' : 'No' }}
                    </li>
                    <li><span class="font-bold">Shuffle Answers:</span> {{ $exam->shuffle_answers ? 'Yes' : 'No' }}</li>
                    <li><span class="font-bold">Show Score:</span> {{ $exam->show_score ? 'Yes' : 'No' }}</li>
                </ul>
                <span
                    class="inline-flex -space-x-px overflow-hidden rounded-md border bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 mt-4">
                    <a href="{{ route('exams.edit', $exam) }}"
                        class="inline-flex px-4 py-2 text-sm font-medium text-blue-500 hover:bg-gray-50 focus:relative dark:text-blue-400 dark:hover:bg-gray-800"
                        data-question-id="{{ $exam->id }}">
                        <x-content.icon-edit></x-content.icon-edit> Edit
                    </a>
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white shadow-md rounded-lg px-6 py-4 dark:bg-gray-900">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 underline underline-offset-4">
                        Groups</h3>
                    <ul class="space-y-4 text-gray-700 dark:text-gray-200">
                        @foreach ($exam->groups as $group)
                            <li
                                class="border border-gray-300 rounded-lg p-4 {{ $loop->odd ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }}">
                                <div class="font-semibold text-lg mb-2">{{ $group->name }}</div>
                                <ul class="ml-4 space-y-1">
                                    @foreach ($group->users as $user)
                                        <li class="border-b border-gray-300 dark:border-opacity-20">
                                            {{ $user->name }}<span
                                                class="block text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white shadow-md rounded-lg px-6 py-4 dark:bg-gray-900 lg:col-span-2">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 underline underline-offset-4">
                        Topics and Questions</h3>
                    <ul class="space-y-4 text-gray-700 dark:text-gray-200">
                        @foreach ($exam->topics as $topic)
                            <li
                                class="border border-gray-300 rounded-lg p-4 {{ $loop->odd ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }}">
                                <div class="font-semibold text-lg mb-2">{{ $topic->name }} -
                                    {{ $topic->pivot->num_questions }} Questions</div>
                                <ul class="ml-4 space-y-2">
                                    @foreach ($exam->questions()->where('exam_questions.topic_id', $topic->id)->get() as $question)
                                        <li class="border-t border-gray-300 pt-2">
                                            <div class="font-medium">{{ $question->question_text }}</div>
                                            <ul class="ml-4 mt-1 space-y-1">
                                                @foreach ($question->answers as $answer)
                                                    <li
                                                        class="{{ $answer->is_correct ? 'text-green-600 font-semibold' : '' }}">
                                                        {{ $answer->answer_text }} @if ($answer->is_correct)
                                                            <strong>(Correct)</strong>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <a href="{{ route('exams.index') }}"
                class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Back to Exams</a>
        </div>
    </x-content>

</x-app-layout>
