@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-100 to-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="mx-auto h-12 w-12 text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
        </div>
        <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
            Import Data
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Update your electrical network map
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-gray-100">
            
            @if(session('success'))
                <div class="rounded-lg bg-green-50 p-4 mb-6 border-l-4 border-green-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg bg-red-50 p-4 mb-6 border-l-4 border-red-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Upload Failed
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="importForm">
                @csrf
                
                <!-- Section: Waypoints -->
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        GPS Data Sources
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- LJUCA -->
                        <div class="relative group">
                            <label for="ljuca_file" class="block text-sm font-medium text-gray-700 mb-1">Standard Waypoints (LJUCA)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors bg-gray-50 hover:bg-indigo-50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-indigo-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="ljuca_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2">
                                            <span>Upload a file</span>
                                            <input id="ljuca_file" name="ljuca_file" type="file" class="sr-only" required accept=".txt">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">.txt (Standard)</p>
                                    <p class="text-xs text-indigo-600 mt-2 font-semibold" id="ljuca_name"></p>
                                </div>
                            </div>
                        </div>

                        <!-- SELIM -->
                        <div class="relative group">
                            <label for="selim_file" class="block text-sm font-medium text-gray-700 mb-1">"S" Waypoints (SELIM)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors bg-gray-50 hover:bg-indigo-50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-indigo-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="selim_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2">
                                            <span>Upload a file</span>
                                            <input id="selim_file" name="selim_file" type="file" class="sr-only" required accept=".txt">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">.txt (S-Suffix)</p>
                                    <p class="text-xs text-indigo-600 mt-2 font-semibold" id="selim_name"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Consumers -->
                <div>
                     <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2 mb-4 flex items-center">
                         <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Consumer Database
                    </h3>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors bg-gray-50 hover:bg-indigo-50 group">
                         <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-indigo-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="consumers_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2">
                                    <span>Upload Excel File</span>
                                    <input id="consumers_file" name="consumers_file" type="file" class="sr-only" required accept=".xls,.xlsx">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">.xls, .xlsx</p>
                            <p class="text-xs text-indigo-600 mt-2 font-semibold" id="consumers_name"></p>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" id="submitBtn"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-[1.01]">
                        <span id="btnText">Import Data</span>
                        <svg id="btnSpinner" class="animate-spin ml-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-2">This process may take a few moments depending on file size.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Simple script to show selected filenames and loading state
    function handleFileSelect(inputId, displayId) {
        document.getElementById(inputId).addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.getElementById(displayId).textContent = "Selected: " + fileName;
            }
        });
    }

    handleFileSelect('ljuca_file', 'ljuca_name');
    handleFileSelect('selim_file', 'selim_name');
    handleFileSelect('consumers_file', 'consumers_name');

    document.getElementById('importForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const text = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');
        
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        text.textContent = 'Processing Import...';
        spinner.classList.remove('hidden');
    });
</script>
@endsection