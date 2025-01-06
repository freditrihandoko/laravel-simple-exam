<x-app-layout>
    <x-slot name="title">
        Exam Result Details
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exam Result Details') }}
        </h2>
    </x-slot>

    <x-content>
        <div class="bg-white shadow-md rounded-lg px-4 py-5 dark:bg-gray-900">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $examResult->exam->title }} - {{ $examResult->user->name }}
            </h3>
            <p class="text-gray-700 dark:text-gray-200">Status: {{ ucfirst($examResult->status) }}</p>
            <p class="text-gray-700 dark:text-gray-200">Score: {{ $examResult->score ?? 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-200">Start Time: {{ $examResult->start_time }}</p>
            <p class="text-gray-700 dark:text-gray-200">End Time: {{ $examResult->end_time }}</p>
            <hr class="my-4 border-gray-300 dark:border-gray-700">
            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Questions</h4>
            <div class="grid grid-cols-1 gap-4">
                @foreach ($examResult->details as $detail)
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow">
                        <p class="font-bold text-gray-900 dark:text-gray-100">{{ $detail->question->question_text }}</p>
                        <p class="text-gray-700 dark:text-gray-300">
                            Answer: {{ $detail->answer->answer_text ?? 'No Answer' }}
                            @if ($detail->is_correct)
                                <span class="text-green-500">(Correct)</span>
                            @else
                                <span class="text-red-500">(Incorrect)</span>
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-content>
</x-app-layout>
