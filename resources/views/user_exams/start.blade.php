<x-app-layout>
    <x-slot name="title">
        {{ $exam->title }}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $exam->title }}
        </h2>
    </x-slot>

    <x-content>
        <div class="bg-white shadow-md rounded-lg px-4 py-5 dark:bg-gray-900 mb-4">
            <div class="flex justify-between items-center mb-4">
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Remaining Time: <span id="timer">{{ $exam->exam_duration * 60 }}</span> minutes
                </div>
            </div>
            {{-- <form id="examForm" method="POST" action="{{ route('user_exams.submit', $exam->id) }}">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <input type="hidden" id="examResultId" name="exam_result_id" value="{{ $examResult->id }}">
                <div id="questions-container">
                    @foreach ($exam->questions as $index => $question)
                        <div class="question-card @if ($index !== 0) hidden @endif"
                            data-question-index="{{ $index }}">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                                {{ $index + 1 }}. {{ $question->question_text }}
                            </h3>
                            @if ($question->question_type === 'multiple_choice')
                                @foreach ($question->answers as $answer)
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="answers[{{ $question->id }}]"
                                                value="{{ $answer->id }}" class="form-radio"
                                                @if ($examResult->details->where('question_id', $question->id)->first() && $examResult->details->where('question_id', $question->id)->first()->answer_id == $answer->id) checked @endif>
                                            <span class="ml-2">{{ $answer->answer_text }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            @elseif ($question->question_type === 'essay')
                                <textarea name="answers[{{ $question->id }}]" rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
@if ($examResult->details->where('question_id', $question->id)->first() && $examResult->details->where('question_id', $question->id)->first()->essay_answer)
{{ $examResult->details->where('question_id', $question->id)->first()->essay_answer }}
@endif
</textarea>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <div class="flex justify-between items-center">
                        <button type="button" id="prevQuestion"
                            class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Previous</button>
                        <button type="button" id="nextQuestion"
                            class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Next</button>
                    </div>
                    <div class="flex flex-wrap justify-center mt-4">
                        @foreach ($exam->questions as $index => $question)
                            <button type="button"
                                class="question-nav-btn rounded w-10 h-10 m-1 dark:text-black @if ($examResult->details->where('question_id', $question->id)->first() && ($examResult->details->where('question_id', $question->id)->first()->answer_id || $examResult->details->where('question_id', $question->id)->first()->essay_answer)) bg-green-500 @else bg-gray-200 @endif"
                                data-question-index="{{ $index }}">{{ $index + 1 }}</button>
                        @endforeach
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" id="submitExamButton"
                            class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Submit Exam</button>
                    </div>
                </div>

            </form> --}}
            <form id="examForm" method="POST" action="{{ route('user_exams.submit', $exam->id) }}">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <input type="hidden" id="examResultId" name="exam_result_id" value="{{ $examResult->id }}">
                <div id="questions-container">
                    @foreach ($questions as $index => $question)
                        <div class="question-card @if ($index !== 0) hidden @endif"
                            data-question-index="{{ $index }}">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                                {{ $index + 1 }}. {{ $question->question_text }}
                            </h3>
                            @if ($question->question_type === 'multiple_choice')
                                @foreach ($question->answers as $answer)
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="answers[{{ $question->id }}]"
                                                value="{{ $answer->id }}" class="form-radio"
                                                @if (
                                                    $examResult->details->where('question_id', $question->id)->first() &&
                                                        $examResult->details->where('question_id', $question->id)->first()->answer_id == $answer->id) checked @endif>
                                            <span class="ml-2">{{ $answer->answer_text }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            @elseif ($question->question_type === 'essay')
                                <textarea name="answers[{{ $question->id }}]" rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                                @if (
                                    $examResult->details->where('question_id', $question->id)->first() &&
                                        $examResult->details->where('question_id', $question->id)->first()->essay_answer)
                                {{ $examResult->details->where('question_id', $question->id)->first()->essay_answer }}
                                @endif
                                </textarea>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <div class="flex justify-between items-center">
                        <button type="button" id="prevQuestion"
                            class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Previous</button>
                        <button type="button" id="nextQuestion"
                            class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Next</button>
                    </div>
                    <div class="flex flex-wrap justify-center mt-4">
                        @foreach ($questions as $index => $question)
                            <button type="button"
                                class="question-nav-btn rounded w-10 h-10 m-1 dark:text-black @if (
                                    $examResult->details->where('question_id', $question->id)->first() &&
                                        ($examResult->details->where('question_id', $question->id)->first()->answer_id ||
                                            $examResult->details->where('question_id', $question->id)->first()->essay_answer)) bg-green-500 @else bg-gray-200 @endif"
                                data-question-index="{{ $index }}">{{ $index + 1 }}</button>
                        @endforeach
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" id="submitExamButton"
                            class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Submit Exam</button>
                    </div>
                </div>
            </form>



        </div>
        <!-- Modal Konfirmasi -->
        <div class="fixed z-10 inset-0 overflow-y-auto hidden" id="confirmSubmitModal">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div
                        class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.683-.76 3.313-1.887.63-1.126.63-2.665 0-3.81L15.175 4.887C14.545 3.76 13.402 3 11.862 3H6.926c-1.54 0-2.683.76-3.313 1.887L.313 16.113c-.63 1.126-.63 2.665 0 3.81.63 1.127 1.773 1.887 3.313 1.887zm0 0V21m0 0V21">
                                    </path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-200"
                                    id="modal-headline">Submit Exam
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-900 dark:text-gray-200">Are you sure you want to submit
                                        the exam? This
                                        action cannot be undone.</p>
                                    <div class="mt-4">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="confirmCheckbox" class="form-checkbox">
                                            <span class="ml-2">I confirm that I have filled all the answers.</span>
                                        </label>
                                    </div>
                                    <div class="mt-2 text-red-500 hidden" id="confirmationError">
                                        Please confirm that you have filled all the answers.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="confirmSubmitButton"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Submit
                            Exam</button>
                        <button type="button" id="cancelButton"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Admin Terminate Exam -->
        <div id="examStatusModal"
            class="hidden fixed inset-0 flex items-center justify-center z-50 bg-gray-500 bg-opacity-75 backdrop-blur-md">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Exam Status Update</h2>
                <p class="text-gray-700 dark:text-gray-300 mb-4">The exam has been forcefully stopped by admin</p>
                <button id="closeExamStatusModal"
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Close</button>
            </div>
        </div>


    </x-content>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let intervalId;
        $(document).ready(function() {
            let currentQuestionIndex = 0;
            const $questions = $('.question-card');
            const totalQuestions = $questions.length;
            const $timerElement = $('#timer');
            let remainingTime = parseInt($timerElement.text());
            const startTime = new Date('{{ $examResult->start_time }}').getTime();
            const examDuration = {{ $exam->exam_duration }} * 60; // Convert minutes to seconds


            function showQuestion(index) {
                $questions.each(function(i) {
                    $(this).toggleClass('hidden', i !== index);
                });
                updateNavButtons(index);
                updateURLHash(index);
            }

            function updateNavButtons(index) {
                $('#prevQuestion').prop('disabled', index === 0);
                $('#nextQuestion').prop('disabled', index === totalQuestions - 1);

                $('.question-nav-btn').each(function() {
                    const btnIndex = parseInt($(this).data('question-index'));
                    $(this).toggleClass('bg-indigo-600 text-white', btnIndex === index);
                });
            }

            function updateURLHash(index) {
                window.location.hash = `question-${index}`;
            }

            function getQuestionIndexFromHash() {
                const hash = window.location.hash;
                if (hash.startsWith('#question-')) {
                    const index = parseInt(hash.replace('#question-', ''));
                    if (!isNaN(index) && index >= 0 && index < totalQuestions) {
                        return index;
                    }
                }
                return 0;
            }

            function startTimer() {
                intervalId = setInterval(() => {
                    const now = new Date().getTime();
                    const elapsedSeconds = Math.floor((now - startTime) / 1000);
                    remainingTime = examDuration - elapsedSeconds;

                    // Pengecekan setiap 1 menit apakah status sudah 'completed'
                    if (elapsedSeconds % 60 === 0) {
                        checkExamStatus(); // Fungsi untuk mengecek status ujian
                    }


                    if (remainingTime <= 0) {
                        clearInterval(intervalId);
                        remainingTime = 0;
                        $timerElement.text(formatTime(remainingTime));
                        $('#examForm').submit();
                    } else {
                        $timerElement.text(formatTime(remainingTime));

                        if (remainingTime <= 120) { // Peringatan waktu tersisa 2 menit
                            $timerElement.addClass('text-red-500 text-2xl font-bold');
                            $timerElement.text(`Time is almost up! ${formatTime(remainingTime)}`);
                        }
                    }
                }, 1000);
            }

            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
            }

            function saveAnswer(questionId, answer) {
                $.ajax({
                    url: '{{ route('user_exams.save_answer') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        exam_id: '{{ $exam->id }}',
                        exam_result_id: $('#examResultId').val(),
                        question_id: questionId,
                        answer: answer
                    },
                    success: function(response) {
                        console.log(response.message);
                        updateQuestionNavButton(questionId);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $('input[type="radio"]').on('change', function() {
                const questionId = $(this).attr('name').match(/\d+/)[0];
                const answer = $(this).val();
                saveAnswer(questionId, answer);
            });

            $('textarea').on('blur', function() {
                const questionId = $(this).attr('name').match(/\d+/)[0];
                const answer = $(this).val();
                saveAnswer(questionId, answer);
            });

            $('.question-nav-btn').on('click', function() {
                const questionIndex = parseInt($(this).data('question-index'));
                showQuestion(questionIndex);
                currentQuestionIndex = questionIndex;
            });

            $('#prevQuestion').on('click', function() {
                if (currentQuestionIndex > 0) {
                    currentQuestionIndex--;
                    showQuestion(currentQuestionIndex);
                }
            });

            $('#nextQuestion').on('click', function() {
                if (currentQuestionIndex < totalQuestions - 1) {
                    currentQuestionIndex++;
                    showQuestion(currentQuestionIndex);
                }
            });

            function updateQuestionNavButton(questionId) {
                const navButton = $(`.question-nav-btn[data-question-index="${currentQuestionIndex}"]`);

                if (navButton) {
                    navButton.addClass('bg-green-500').removeClass('bg-gray-200 bg-indigo-600');
                }
            }

            currentQuestionIndex = getQuestionIndexFromHash();
            showQuestion(currentQuestionIndex);
            startTimer();

            const $submitExamButton = $('#submitExamButton');
            const $confirmSubmitModal = $('#confirmSubmitModal');
            const $confirmSubmitButton = $('#confirmSubmitButton');
            const $cancelButton = $('#cancelButton');
            const $confirmCheckbox = $('#confirmCheckbox');
            const $confirmationError = $('#confirmationError');
            const $examForm = $('#examForm');

            $submitExamButton.on('click', function() {
                $confirmSubmitModal.removeClass('hidden');
            });

            $cancelButton.on('click', function() {
                $confirmSubmitModal.addClass('hidden');
            });

            $confirmSubmitButton.on('click', function() {
                if ($confirmCheckbox.is(':checked')) {
                    clearInterval(intervalId);
                    $examForm.submit();
                } else {
                    $confirmationError.removeClass('hidden');
                }
            });

            $confirmCheckbox.on('change', function() {
                if ($confirmCheckbox.is(':checked')) {
                    $confirmationError.addClass('hidden');
                }
            });

            $('#closeExamStatusModal').on('click', function() {
                $('#examStatusModal').addClass('hidden');
                // window.location.href =
                //     '{{ route('user_exams.index') }}'; // Redirect ke halaman index ujian
                $examForm.submit();
            });
        });

        function checkExamStatus() {
            $.ajax({
                url: '{{ route('user_exams.check_status') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    exam_result_id: '{{ $examResult->id }}',
                },
                success: function(response) {
                    if (response.status === 'completed') {
                        clearInterval(intervalId);
                        // Tambahkan aksi lain yang sesuai, misalnya memberikan notifikasi atau mengubah UI
                        // Show the modal
                        $('#examStatusModal').show();
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });

        }
    </script>
</x-app-layout>
