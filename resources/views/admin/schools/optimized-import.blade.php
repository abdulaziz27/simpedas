@extends('layouts.app')

@section('title', 'Optimized School Import')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">🚀 Optimized School Import</h1>

            <!-- Import Type Selection -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Choose Import Method</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Optimized Import -->
                    <div class="border-2 border-green-200 rounded-lg p-6 hover:border-green-400 transition-colors">
                        <div class="text-center">
                            <div class="text-4xl mb-4">⚡</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Optimized Import</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Fastest method using bulk operations. Best for 100-500 records.
                            </p>
                            <div class="text-xs text-gray-500">
                                ⏱️ ~30-60 seconds for 200 records<br>
                                💾 Memory: 1GB<br>
                                🗄️ Queries: ~10-20
                            </div>
                        </div>
                    </div>

                    <!-- Chunked Import -->
                    <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 transition-colors">
                        <div class="text-center">
                            <div class="text-4xl mb-4">📦</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Chunked Import</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Memory-efficient processing. Best for 500+ records.
                            </p>
                            <div class="text-xs text-gray-500">
                                ⏱️ ~60-120 seconds for 500 records<br>
                                💾 Memory: 256MB<br>
                                🗄️ Queries: ~50-100
                            </div>
                        </div>
                    </div>

                    <!-- Queue Import -->
                    <div class="border-2 border-purple-200 rounded-lg p-6 hover:border-purple-400 transition-colors">
                        <div class="text-center">
                            <div class="text-4xl mb-4">🔄</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Queue Import</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Background processing. Best for 1000+ records.
                            </p>
                            <div class="text-xs text-gray-500">
                                ⏱️ ~2-5 minutes for 1000 records<br>
                                💾 Memory: Unlimited<br>
                                🗄️ Queries: Optimized
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Form -->
            <form action="{{ route('admin.schools.optimized-import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <div class="mb-6">
                    <label for="import_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Import Method
                    </label>
                    <select name="import_type" id="import_type" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Choose import method...</option>
                        <option value="optimized">⚡ Optimized Import (100-500 records)</option>
                        <option value="chunked">📦 Chunked Import (500+ records)</option>
                        <option value="queue">🔄 Queue Import (1000+ records)</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Excel File
                    </label>
                    <input type="file" name="file" id="file" required
                           accept=".xlsx,.xls"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-green-50 file:text-green-700
                                  hover:file:bg-green-100">
                    <p class="mt-1 text-sm text-gray-500">
                        Maximum file size: 10MB. Supported formats: .xlsx, .xls
                    </p>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('dinas.schools.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">
                        Cancel
                    </a>

                    <button type="submit" id="submitBtn"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="submitText">Start Import</span>
                        <span id="loadingText" class="hidden">Processing...</span>
                    </button>
                </div>
            </form>

            <!-- Performance Tips -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">💡 Performance Tips</h3>
                <ul class="text-sm text-blue-700 space-y-2">
                    <li>• <strong>Optimized Import:</strong> Use for small to medium datasets (100-500 records)</li>
                    <li>• <strong>Chunked Import:</strong> Use for large datasets (500+ records) to avoid memory issues</li>
                    <li>• <strong>Queue Import:</strong> Use for very large datasets (1000+ records) for background processing</li>
                    <li>• Ensure your Excel file has proper column headers matching the template</li>
                    <li>• Close other applications to free up system resources</li>
                    <li>• Use a stable internet connection for queue imports</li>
                </ul>
            </div>

            <!-- System Requirements -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">⚠️ System Requirements</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-yellow-700">
                    <div>
                        <h4 class="font-semibold mb-2">Optimized Import:</h4>
                        <ul class="space-y-1">
                            <li>• Memory: 1GB available</li>
                            <li>• Time: 5 minutes max</li>
                            <li>• Records: 100-500</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Chunked/Queue Import:</h4>
                        <ul class="space-y-1">
                            <li>• Memory: 256MB available</li>
                            <li>• Time: 10 minutes max</li>
                            <li>• Records: 500+</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('importForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');

    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    loadingText.classList.remove('hidden');
});

// Show method details on selection
document.getElementById('import_type').addEventListener('change', function() {
    const method = this.value;
    // You can add dynamic content here based on selection
});
</script>
@endsection
