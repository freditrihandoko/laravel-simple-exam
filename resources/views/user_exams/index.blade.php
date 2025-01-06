<x-app-layout>
    <x-slot name="title">
        Exams
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Available Exams') }}
        </h2>
        <p id="current-time" class="text-gray-600 dark:text-gray-400 mt-2"></p>
    </x-slot>

    <x-content>
        <x-flash-message></x-flash-message>
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Available Exams</h3>
            @forelse ($availableExams as $exam)
                <div class="bg-white shadow-md rounded-lg px-4 py-5 dark:bg-gray-900 mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">{{ $exam->title }}</h3>
                    <p>{{ $exam->description }}</p>
                    <p><strong>Start Time:</strong> <span id="start-time-{{ $exam->id }}"></span></p>
                    <p><strong>End Time:</strong> <span id="end-time-{{ $exam->id }}"></span></p>
                    <p><strong>Topics:</strong> {{ $exam->topics->pluck('name')->join(', ') }}</p>
                    <p><strong>Duration:</strong> {{ $exam->exam_duration }} minutes</p>
                    <p><strong>Status:</strong>
                        @if ($exam->status == 'Not Started')
                            Not Started
                            <button data-exam-id="{{ $exam->id }}" data-action="start"
                                class="bg-blue-500 text-white px-4 py-2 rounded mt-2 inline-block open-confirm-modal">
                                Start Exam
                            </button>
                        @elseif ($exam->status == 'In Progress')
                            In Progress
                            <button data-exam-id="{{ $exam->id }}" data-action="continue"
                                class="bg-yellow-500 text-white px-4 py-2 rounded mt-2 inline-block open-confirm-modal">
                                Continue Exam
                            </button>
                        @elseif ($exam->status == 'Completed')
                            Completed
                            @if ($exam->show_score)
                                <p class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block">
                                    Exam Completed - Score: {{ $exam->score }}
                                </p>
                                @if ($exam->has_essay)
                                    <p class="text-yellow-500 mt-2">Note: Final score pending manual correction of essay
                                        questions.</p>
                                @endif
                            @else
                                <p class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block">
                                    Exam Completed
                                </p>
                            @endif
                        @endif
                    </p>
                </div>
            @empty
                <p>No available exams.</p>
            @endforelse
        </div>

        <div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Upcoming Exams</h3>
            @forelse ($upcomingExams as $exam)
                <div class="bg-white shadow-md rounded-lg px-4 py-5 dark:bg-gray-900 mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">{{ $exam->title }}</h3>
                    <p>{{ $exam->description }}</p>
                    <p><strong>Start Time:</strong> <span id="start-time-{{ $exam->id }}"></span></p>
                    <p><strong>End Time:</strong> <span id="end-time-{{ $exam->id }}"></span></p>
                    <p><strong>Topics:</strong> {{ $exam->topics->pluck('name')->join(', ') }}</p>
                    <p><strong>Duration:</strong> {{ $exam->exam_duration }} minutes</p>
                    <p><strong>Status:</strong> Upcoming</p>
                </div>
            @empty
                <p>No upcoming exams.</p>
            @endforelse
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Past Exams</h3>
            @forelse ($pastExams as $exam)
                <div class="bg-white shadow-md rounded-lg px-4 py-5 dark:bg-gray-900 mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">{{ $exam->title }}</h3>
                    <p>{{ $exam->description }}</p>
                    <p><strong>Start Time:</strong> <span id="start-time-{{ $exam->id }}"></span></p>
                    <p><strong>End Time:</strong> <span id="end-time-{{ $exam->id }}"></span></p>
                    <p><strong>Topics:</strong> {{ $exam->topics->pluck('name')->join(', ') }}</p>
                    <p><strong>Duration:</strong> {{ $exam->exam_duration }} minutes</p>
                    <p><strong>Status:</strong> {{ $exam->status }}</p>
                </div>
            @empty
                <p>No past exams available.</p>
            @endforelse
        </div>
    </x-content>

    <!-- Modal -->
    <div id="confirmModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                                Start Exam
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to start this exam? Please ensure a stable internet
                                    connection.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a id="confirmButton" href="#"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Start
                    </a>
                    <button id="cancelButton" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateCurrentTime() {
                const currentTimeElement = document.getElementById('current-time');
                const now = new Date();
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                    timeZoneName: 'short'
                };
                const formattedDate = now.toLocaleDateString('id-ID', options);
                currentTimeElement.textContent = `Current Time: ${formattedDate}`;
            }

            updateCurrentTime();
            setInterval(updateCurrentTime, 1000);

            // Fungsi untuk mengatur waktu mulai dan waktu berakhir dari Blade
            function setExamTimes() {
                const availableExams = @json($availableExams);
                const upcomingExams = @json($upcomingExams);
                const pastExams = @json($pastExams);

                setTimes(availableExams);
                setTimes(upcomingExams);
                setTimes(pastExams);
            }

            function setTimes(exams) {
                exams.forEach(exam => {
                    const startTimeElement = document.getElementById(`start-time-${exam.id}`);
                    const endTimeElement = document.getElementById(`end-time-${exam.id}`);

                    const startTime = new Date(exam.exam_start);
                    const endTime = new Date(exam.exam_end);

                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        timeZoneName: 'short'
                    };

                    startTimeElement.textContent = startTime.toLocaleDateString('id-ID', options);
                    endTimeElement.textContent = endTime.toLocaleDateString('id-ID', options);
                });
            }

            setExamTimes();

            // Event listener for modal
            document.querySelectorAll('.open-confirm-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const examId = this.getAttribute('data-exam-id');
                    const action = this.getAttribute('data-action');
                    const modalTitle = action === 'start' ? 'Start Exam' : 'Continue Exam';
                    const confirmButtonText = action === 'start' ? 'Start' : 'Continue';
                    const confirmButtonHref = `{{ url('/user_exams/start') }}/${examId}`;

                    document.getElementById('modalTitle').textContent = modalTitle;
                    document.getElementById('confirmButton').textContent = confirmButtonText;
                    document.getElementById('confirmButton').setAttribute('href',
                        confirmButtonHref);
                    document.getElementById('confirmModal').classList.remove('hidden');
                });
            });

            document.getElementById('cancelButton').addEventListener('click', function() {
                document.getElementById('confirmModal').classList.add('hidden');
            });
        });
    </script>
</x-app-layout>
