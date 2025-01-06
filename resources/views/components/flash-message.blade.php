<div>
    @if (session('success'))
        <div id="success-message"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-gray-900 dark:border-gray-800 dark:text-white"
            role="alert">
            <strong class="font-bold flex items-center">
                <span class="text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-6 w-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                Success!
            </strong>
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500 close-button" role="button"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path
                        d="M14.348 5.652a.5.5 0 0 0-.707 0L10 9.293 6.354 5.646a.5.5 0 1 0-.707.707L9.293 10l-3.646 3.646a.5.5 0 1 0 .707.707L10 10.707l3.646 3.646a.5.5 0 1 0 .707-.707L10.707 10l3.646-3.646a.5.5 0 0 0 0-.707z" />
                </svg>
            </span>
        </div>
    @endif

    @if (session('error'))
        <div id="error-message"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-200 dark:border-red-500 dark:text-red-800"
            role="alert">
            <strong class="font-bold flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 mr-2">
                    <path fill-rule="evenodd"
                        d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                        clip-rule="evenodd" />
                </svg>
                Error!
            </strong>
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500 close-button" role="button"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path
                        d="M14.348 5.652a.5.5 0 0 0-.707 0L10 9.293 6.354 5.646a.5.5 0 1 0-.707.707L9.293 10l-3.646 3.646a.5.5 0 1 0 .707.707L10 10.707l3.646 3.646a.5.5 0 1 0 .707-.707L10.707 10l3.646-3.646a.5.5 0 0 0 0-.707z" />
                </svg>
            </span>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.close-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    this.closest('div[role="alert"]').style.display = 'none';
                });
            });
        });
    </script>
</div>
