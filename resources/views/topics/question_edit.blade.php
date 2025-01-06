<form id="editQuestionForm">
    @csrf
    @method('PUT')

    <label for="edit_question_text">Question Text</label>
    <input type="text" id="edit_question_text" name="edit_question_text" value="{{ $question->question_text }}"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        required>

    @if ($question->question_type === 'multiple_choice')
        <!-- Menampilkan opsi untuk pertanyaan pilihan ganda -->
        <label>Answers</label>
        <div id="editAnswersSection" class="mt-4">
            @foreach ($question->answers as $answer)
                <input type="text" name="answers[]" value="{{ $answer->answer_text }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    required>
            @endforeach
            <button type="button" id="addEditAnswer" class="mt-2 text-blue-500">Add Answer</button>
        </div>
    @endif

    <button type="submit2"
        class="mt-4 inline-block rounded border border-indigo-600 bg-indigo-600 px-12 py-3 text-sm font-medium text-white hover:bg-blue-500 hover:text-white focus:outline-none focus:ring active:text-indigo-500">Save
        Changes</button>
</form>
