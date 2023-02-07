<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="shortcut icon" href="http://localhost:80/BlueComet/public/BlueComet.ico" type="image/x-icon">

    <!-- Javascript Files Links -->
    <script src="http://localhost:80/BlueComet/public/tailwindcss.js"></script>

    <!-- Title -->
    <title>Welcome - BlueComet</title>
</head>
<body class="bg-zinc-900 w-full h-full font-sans">
    <div class="max-w-2xl w-full mx-auto">
        <div class="flex flex-col pt-16 pb-4 px-4">
            <div class="mb-32">
                <p class="text-gray-300 font-medium text-3xl pb-8">Welcome to <span class="text-blue-500">BlueComet</span></p>
                <span class="flex flex-col space-y-4 text-gray-300 font-normal text-lg">
                    <p>It is a simple MVC Routing Framework project I tried to do during the 2-week school holiday. The start date is January 25, 2023, and the end date is February 3, 2023.</p>
                </span>
                <br>
                <br>
                <span class="flex flex-col space-y-2 text-gray-300 font-normal text-lg">
                    <p>If you would like to edit this page you will find it located at:</p>
                    <code class="p-3 rounded-sm text-base bg-zinc-800 hover:bg-zinc-800/80 text-blue-400 hover:text-blue-500">app/Views/welcome_message.php</code>
                </span>
                <br>
                <br>
                <span class="flex flex-col space-y-2 text-gray-300 font-normal text-lg">
                    <p>The corresponding controller for this page can be found at:</p>
                    <code class="p-3 rounded-sm text-base bg-zinc-800 hover:bg-zinc-800/80 text-blue-400 hover:text-blue-500">app/Controllers/Home.php</code>
                </span>
            </div>
            <div class="text-gray-300 font-normal text-lg space-y-1.5">
                <p>Page rendered in <?= bcsub(microtime(TRUE), $_SERVER['REQUEST_TIME_FLOAT'], 4);?> seconds.</p>
                <p>Environment: <b><?= strtoupper(ENVIRONMENT); ?></b></p>
                <p class="inline-flex items-center gap-x-2">Developer: 
                    <a href="https://github.com/devayberk" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-500 hover:underline-offset-2 inline-flex items-center gap-x-1">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22">
                            </path>
                        </svg>
                        <span>Ayberk</span>
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>