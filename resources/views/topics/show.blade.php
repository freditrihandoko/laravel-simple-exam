<x-app-layout>
    <x-slot name="title">
        Topic {{ $topic->name }}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $topic->name }}
        </h2>
    </x-slot>
    <x-content>
        <x-flash-message></x-flash-message>
        <!-- Flasher -->
        <div id="flasher" class="hidden fixed top-4 right-4 z-50 max-w-sm w-full px-4 py-3 shadow-md" role="alert">
            <div class="flex">
                <div id="flasher-icon" class="py-1"></div>
                <div>
                    <p class="font-bold">Notification</p>
                    <p class="text-sm" id="flasher-message"></p>
                </div>
            </div>
        </div>

        <x-content.title-content>{{ $topic->name }}</x-content.title-content>
        <!-- Button to Open Modal -->
        <div class="flex justify-end">
            <button id="importQuestionsButton"
                class="inline-block rounded bg-green-500 px-12 py-3  text-sm font-medium text-white hover:bg-green-600">Import
                Questions from Excel</button>
        </div>
        <div id="questionForm">
            <h3 class="text-lg font-bold mt-6">Add a New Question</h3>
            <form id="addQuestionForm" novalidate enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="question_text">Question Text</label>
                    <textarea type="text" id="question_text" name="question_text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 resize-y"
                        oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"' required></textarea>
                </div>
                
                <!-- Add question image input -->
                <div class="mt-4">
                    <label for="question_image">Question Image (optional)</label>
                    <input type="file" id="question_image" name="question_image" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                    <div id="question_image_preview" class="mt-2 hidden">
                        <img src="" alt="Question preview" class="max-w-xs h-auto"/>
                    </div>
                </div>
        
                <div class="mt-4">
                    <label for="question_type">Question Type</label>
                    <select id="question_type" name="question_type"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>
        
                <div id="answersSection" class="mt-4">
                    <label>Answers</label>
                    <div class="answer flex items-center mb-4">
                        <input type="checkbox" class="mr-2 text-indigo-600 transition duration-150 ease-in-out rounded"
                            name="answers[0][correct]" value="1">
                        <input type="text" name="answers[0][text]"
                            class="flex-grow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <!-- Add answer image input -->
                        <input type="file" name="answers[0][image]" accept="image/*" class="ml-2 text-sm text-gray-500">
                        <button type="button" class="removeAnswer text-red-500 ml-2">X</button>
                    </div>
                    <button type="button" id="addAnswerNew" class="mt-2 text-blue-500">Add Answer</button>
                </div>
                <button type="submit"
                    class="mt-4 inline-block rounded border border-indigo-600 bg-indigo-600 px-12 py-3 text-sm font-medium text-white hover:bg-blue-500 hover:text-white focus:outline-none focus:ring active:text-indigo-500">
                    Save Question
                </button>
            </form>
        </div>

        <span class="flex items-center py-6" id="questionsAndAnswersList">
            <span class="h-px flex-1 bg-gray-800 dark:bg-white"></span>
            <span class="shrink-0 px-6 text-gray-900 dark:text-gray-100">Questions and Answers List</span>
            <span class="h-px flex-1 bg-gray-800 dark:bg-white"></span>
        </span>
        <!-- Search Form -->
        <div class="flex justify-end my-4">
            <form action="{{ route('topics.show', $topic->id) }}" method="GET" id="searchForm"
                class="flex items-center">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    class="mr-2 px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200"
                    placeholder="Search questions or answers...">
                <button type="submit"
                    class="inline-block rounded bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700">
                    Search
                </button>
            </form>
        </div>

        @foreach ($questions as $question)
            <div
                class="rounded-lg border border-gray-100 bg-white p-2 shadow-sm transition hover:shadow-lg sm:p-6 dark:border-gray-800 dark:bg-gray-900 dark:shadow-gray-700/25">
                @if ($question->question_type == 'multiple_choice')
                    <x-content.icon-multiple-choice></x-content.icon-multiple-choice>
                @else
                    <x-content.icon-essay></x-content.icon-essay>
                @endif

                <a href="#" class="edit-question" data-question-id="{{ $question->id }}">
                    <h3 title="Click to edit Question" class="mt-0.5 text-lg font-medium text-gray-900 dark:text-white">
                        {{ $question->question_text }}
                    </h3>
                </a>
                <div class="mt-2 text-sm/relaxed text-gray-500 dark:text-gray-400">
                    @if($question->question_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/question_images/' . $question->question_image) }}"
                                 alt="Question image"
                                 class="max-w-md h-auto rounded-lg shadow-md mx-auto block"> {{-- Opsi 1: max-w-md --}}
                                 {{-- class="max-w-sm h-auto rounded-lg shadow-md mx-auto block"> Opsi 2: max-w-sm --}}
                                 {{-- class="w-64 h-auto rounded-lg shadow-md mx-auto block"> Opsi 3: w-64 (lebar tetap) --}}
                                 {{-- class="w-64 h-48 object-cover rounded-lg shadow-md mx-auto block"> Opsi 4: w & h tetap, object-cover --}}
                                 {{-- class="max-w-md h-48 object-contain rounded-lg shadow-md mx-auto block"> Opsi 5: max-w, h tetap, object-contain --}}
                        </div>
                    @endif
                
                    @if ($question->question_type == 'multiple_choice')
                        <ul class="list-none pl-0 space-y-2">
                            @foreach ($question->answers as $answer)
                                <li class="flex flex-col md:flex-row items-start md:items-center space-y-2 md:space-y-0 md:space-x-4 rtl:space-x-reverse">
                                    @if($answer->answer_image)
                                        <div class="mb-2 md:mb-0 w-full md:w-auto">
                                            <img src="{{ asset('storage/answer_images/' . $answer->answer_image) }}"
                                                 alt="Answer image"
                                                 class="max-w-xs h-auto rounded-lg shadow-sm block"> {{-- Opsi 1: max-w-xs --}}
                                                 {{-- class="w-32 h-auto rounded-lg shadow-sm block"> Opsi 2: w-32 (lebar tetap) --}}
                                                 {{-- class="w-32 h-24 object-cover rounded-lg shadow-sm block"> Opsi 3: w & h tetap, object-cover --}}
                                        </div>
                                    @endif
                
                                    <div class="flex-grow">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                            @if ($answer->is_correct === 1)
                                                <x-content.icon-checklist class="shrink-0"></x-content.icon-checklist>
                                            @else
                                                <x-content.icon-uncheck class="shrink-0"></x-content.icon-uncheck>
                                            @endif
                                            <span>{{ $answer->answer_text }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm font-light">Essay question. No predefined answers.</p>
                    @endif
                </div>
                <span
                    class="inline-flex -space-x-px overflow-hidden rounded-md border bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 mt-4">
                    <button
                        class="edit-question inline-flex px-4 py-2 text-sm font-medium text-blue-500 hover:bg-gray-50 focus:relative dark:text-blue-400 dark:hover:bg-gray-800"
                        data-question-id="{{ $question->id }}">
                        <x-content.icon-edit></x-content.icon-edit> Edit
                    </button>

                    <button
                        class="delete-question inline-flex px-4 py-2 text-sm font-medium text-red-500 hover:bg-gray-50 focus:relative dark:text-red-400 dark:hover:bg-gray-800"
                        data-question-id="{{ $question->id }}">
                        <x-content.icon-delete></x-content.icon-delete> Delete
                    </button>
                </span>
            </div>
        @endforeach

        <div class="py-6">
            {{ $questions->appends(request()->query())->links() }}
        </div>
    </x-content>

    {{-- Modal for Delete Confirmation --}}
    <div id="deleteModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M10 18a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 10-2 0v1h-1v-1a1 1 0 10-2 0v2zm4-6a1 1 0 10-2 0v4a1 1 0 102 0v-4z"
                                    clip-rule="evenodd" />
                                <path fill-rule="evenodd"
                                    d="M18 8v10a2 2 0 01-2 2H8a2 2 0 01-2-2V8H5V6h1.293L7 5.293V5h10v.293L17.707 6H19v2h-1zm-2 2H8v8h8v-8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3
                                class="text-lg leading-6 font-medium bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                Delete Question
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    Are you sure you want to delete this question? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDelete"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button type="button" id="cancelDelete"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Question -->
    <div id="editQuestionModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form id="editQuestionForm" data-question-id="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <h3 class="text-lg leading-6 font-medium text-gray-700 dark:text-gray-200 mb-4 sm:mb-0">
                                Edit Question
                            </h3>
                            <button id="closeEditModal" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div id="questionContent"></div>
                        <div id="editAnswersSection" class="mt-4">
                            <!-- Answers will be populated here dynamically -->
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="submitEditQuestion"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                        <button type="button" id="cancelEditQuestion"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Excel Import -->
    <div id="importQuestionsModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800  rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-200"
                                id="modal-title">
                                Import Questions
                            </h3>
                            <div class="mt-2">
                                <form id="importQuestionsForm" method="POST" enctype="multipart/form-data"
                                    action="{{ route('topics.importQuestions', $topic->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="excel_file"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Excel
                                            File</label>
                                        <input type="file" name="excel_file" id="excel_file"
                                            class="form-control mt-1 block w-full text-gray-700 dark:text-gray-200"
                                            required>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit"
                                            class="btn btn-success bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Import</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="closeModalButton"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Close</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#question_type').change(function() {
                if ($(this).val() === 'multiple_choice') {
                    $('#answersSection').show();
                } else {
                    $('#answersSection').hide();
                }
            });

            let answerIndex = 1;

            $('#addAnswerNew').click(function() {
                const answerIndex = $('.answer').length;
                const newAnswer = `
                    <div class="answer flex items-center mb-4">
                        <input type="checkbox" class="mr-2 text-indigo-600 transition duration-150 ease-in-out rounded" 
                            name="answers[${answerIndex}][correct]" value="1">
                        <input type="text" name="answers[${answerIndex}][text]" 
                            class="flex-grow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <input type="file" name="answers[${answerIndex}][image]" accept="image/*" 
                            class="ml-2 text-sm text-gray-500">
                        <button type="button" class="removeAnswer text-red-500 ml-2">X</button>
                    </div>`;
                $('#answersSection').append(newAnswer);
            });

            $(document).on('click', '.removeAnswer', function() {
                $(this).closest('.answer').remove();
            });

            $('#addQuestionForm').submit(function(e) {
                e.preventDefault();
    
                const formData = new FormData(this);
                
                $.ajax({
                    url: '{{ route('topics.addQuestion', $topic->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showFlasher(response.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            for (const key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessages += errors[key][0] + '\n';
                                }
                            }
                            showFlasher('Validation Error:\n' + errorMessages, 'error');
                        } else {
                            showFlasher('Error adding question', 'error');
                        }
                    }
                });
            });

            // Image preview for question
            $('#question_image').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#question_image_preview').removeClass('hidden')
                            .find('img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });


            $('#question_type').trigger('change'); // Initialize based on default selection

            let questionIdToDelete;
            $('.delete-question').click(function() {
                questionIdToDelete = $(this).data('question-id');
                $('#deleteModal').removeClass('hidden');
            });

            $('#confirmDelete').click(function() {
                $.ajax({
                    url: `/topics/{{ $topic->id }}/questions/${questionIdToDelete}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showFlasher(response.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let errorMessage = 'Error deleting question';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showFlasher(errorMessage, 'error');
                    }
                });
                $('#deleteModal').addClass('hidden');
            });

            $('#cancelDelete').click(function() {
                $('#deleteModal').addClass('hidden');
            });

            //handle edit
            // Edit Question Modal
            function populateEditModal(question) {
                const editForm = $('#editQuestionForm');
                const questionContent = $('#questionContent');
                const editAnswersSection = $('#editAnswersSection');

                editForm.data('question-id', question.id);
                questionContent.html(`
                    <div>
                        <label for="edit_question_text">Question Text</label>
                        <textarea type="text" id="edit_question_text" name="edit_question_text"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"' required>${question.question_text}</textarea>
                    </div>
                    
                    <div class="mt-4">
                        <label for="edit_question_image">Question Image</label>
                        ${question.question_image ? 
                            `<div class="mb-2">
                                <img src="/storage/question_images/${question.question_image}" 
                                    alt="Current question image" class="max-w-xs h-auto"/>
                            </div>` : ''
                        }
                        <input type="file" id="edit_question_image" name="edit_question_image" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                        <div id="edit_question_image_preview" class="mt-2 hidden">
                            <img src="" alt="New question preview" class="max-w-xs h-auto"/>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="edit_question_type">Question Type</label>
                        <select id="edit_question_type" name="question_type"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            disabled>
                            <option value="multiple_choice" ${question.question_type === 'multiple_choice' ? 'selected' : ''}>Multiple Choice</option>
                            <option value="essay" ${question.question_type === 'essay' ? 'selected' : ''}>Essay</option>
                        </select>
                    </div>
                `);

                if (question.question_type === 'multiple_choice') {
                    editAnswersSection.html('<label>Answers</label>');
                    question.answers.forEach((answer, index) => {
                        editAnswersSection.append(`
                            <div class="answer flex items-center mb-4">
                                <input type="checkbox" class="mr-2 text-indigo-600 transition duration-150 ease-in-out rounded" 
                                    name="answers[${index}][correct]" value="1" ${answer.is_correct ? 'checked' : ''}>
                                <input type="text" name="answers[${index}][text]" value="${answer.answer_text}"
                                    class="flex-grow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <input type="file" name="answers[${index}][image]" accept="image/*" 
                                    class="ml-2 text-sm text-gray-500">
                                ${answer.answer_image ? 
                                    `<div class="ml-2">
                                        <img src="/storage/answer_images/${answer.answer_image}" 
                                            alt="Answer image" class="w-16 h-16 object-cover"/>
                                    </div>` : ''
                                }
                                <button type="button" class="removeAnswer text-red-500 ml-2">X</button>
                            </div>
                        `);
                    });
                    editAnswersSection.append(
                        '<button type="button" id="editAddAnswer" class="mt-2 text-blue-500">Add Answer</button>'
                    );
                } else {
                    editAnswersSection.empty();
                }

                // Add image preview for edit form
                $('#edit_question_image').change(function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#edit_question_image_preview').removeClass('hidden')
                                .find('img').attr('src', e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Fetch question data for editing
            $('.edit-question').click(function(e) {
                e.preventDefault();
                const questionId = $(this).data('question-id');
                $.ajax({
                    url: `/topics/{{ $topic->id }}/questions/${questionId}`,
                    type: 'GET',
                    success: function(response) {
                        populateEditModal(response);
                        $('#editQuestionModal').removeClass('hidden');
                    },
                    error: function(error) {
                        showFlasher('Failed to fetch question data.', 'error');
                    }
                });
            });

            // Close modal
            $('#closeEditModal, #cancelEditQuestion').click(function() {
                $('#editQuestionModal').addClass('hidden');
            });

            // Save edited question
            $('#editQuestionForm').submit(function(e) {
                e.preventDefault();
                const questionId = $(this).data('question-id');
                const formData = new FormData(this);

                $.ajax({
                    url: `/topics/{{ $topic->id }}/questions/${questionId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        $('#editQuestionModal').addClass('hidden');
                        showFlasher(response.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        const errorMessage = response && response.message ? response.message : 'Failed to update question';
                        showFlasher(errorMessage, 'error');
                    }
                });
            });


            // Add answer dynamically in edit modal
            $('#editAnswersSection').on('click', '#editAddAnswer', function() {
                const index = $('#editAnswersSection .answer').length;
                $('#editAnswersSection').append(`
                    <div class="answer flex items-center mb-4">
                        <input type="checkbox" class="mr-2 text-indigo-600 transition duration-150 ease-in-out rounded" 
                            name="answers[${index}][correct]" value="1">
                        <input type="text" name="answers[${index}][text]"
                            class="flex-grow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <input type="file" name="answers[${index}][image]" accept="image/*" 
                            class="ml-2 text-sm text-gray-500">
                        <button type="button" class="removeAnswer text-red-500 ml-2">X</button>
                    </div>
                `);
            });

            // Remove answer dynamically
            $('#editAnswersSection').on('click', '.removeAnswer', function() {
                $(this).closest('.answer').remove();
            });

            // Add new answer dynamically in add question form
            $('#addAnswer').click(function() {
                const index = $('#answersSection .answer').length;
                $('#answersSection').append(`
                <div class="answer flex items-center mb-4">
                    <input type="checkbox" class="mr-2 text-indigo-600 transition duration-150 ease-in-out rounded" name="answers[${index}][correct]" value="1" class="mr-2">
                    <input type="text" name="answers[${index}][text]"
                        class="flex-grow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <button type="button" class="removeAnswer text-red-500 ml-2">X</button>
                </div>
            `);
            });

            // Remove answer dynamically in add question form
            $('#answersSection').on('click', '.removeAnswer', function() {
                $(this).closest('.answer').remove();
            });


        });

        function showFlasher(message, type = 'success') {
            const flasher = $('#flasher');
            const flasherIcon = $('#flasher-icon');
            const flasherMessage = $('#flasher-message');

            if (type === 'success') {
                flasher.removeClass('bg-red-100 border-red-500 text-red-900');
                flasherIcon.removeClass('text-red-500');
                flasher.addClass('bg-green-100 border-green-500 text-green-900');
                flasherIcon.addClass('text-green-500');
                flasherIcon.html(
                    `<svg class="fill-current h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M0 0h24v24H0z" fill="none" />
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm0-4h-2V7h2v8z" />
    </svg>`
                );
            } else {
                flasher.removeClass('bg-green-100 border-green-500 text-green-900');
                flasherIcon.removeClass('text-green-500');
                flasher.addClass('bg-red-100 border-red-500 text-red-900');
                flasherIcon.addClass('text-red-500');
                flasherIcon.html(
                    `<svg class="fill-current h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M0 0h24v24H0z" fill="none" />
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm0-4h-2V7h2v8z" />
    </svg>`
                );
            }

            flasherMessage.text(message);
            flasher.removeClass('hidden');
            setTimeout(function() {
                flasher.addClass('hidden');
            }, 3000);
        }

        //import excel
        $(document).ready(function() {
            $('#importQuestionsButton').click(function() {
                $('#importQuestionsModal').removeClass('hidden');
            });

            $('#closeModalButton').click(function() {
                $('#importQuestionsModal').addClass('hidden');
            });
        });

        $('#importExcelForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: '{{ route('topics.importQuestions', $topic->id) }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#importExcelModal').addClass('hidden');
                    showFlasher(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (const key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                errorMessages += errors[key][0] + '\n';
                            }
                        }
                        showFlasher('Validation Error:\n' + errorMessages, 'error');
                    } else {
                        showFlasher('Error importing questions', 'error');
                    }
                }
            });
        });

        $('#cancelImport').click(function() {
            $('#importExcelModal').addClass('hidden');
        });


        document.addEventListener('DOMContentLoaded', function() {
            @if (request()->has('search'))
                document.getElementById('questionsAndAnswersList').scrollIntoView({
                    behavior: 'smooth'
                });
            @endif
        });
    </script>

</x-app-layout>