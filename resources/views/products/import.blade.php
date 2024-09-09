<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Import Produk
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" id="import-form">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700">Pilih File Excel</label>
                        <div id="drop-area" class="border-2 border-dashed border-gray-300 p-6 rounded-md cursor-pointer hover:border-gray-400">
                            <p id="file-name" class="text-center text-gray-500">Seret dan letakkan file di sini, atau klik untuk memilih file</p>
                            <input type="file" name="file" id="file" accept=".xlsx, .xls" class="hidden" required>
                        </div>
                        @error('file')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-green-500 text-white hover:bg-green-600 px-4 py-2 rounded-md flex items-center">
                            <x-heroicon-o-upload class="w-5 h-5 inline-block mr-1" />
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('drop-area').addEventListener('click', function() {
            document.getElementById('file').click();
        });

        document.getElementById('file').addEventListener('change', function() {
            const fileName = this.files[0].name;
            document.getElementById('file-name').textContent = fileName;
        });

        document.getElementById('drop-area').addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('border-blue-500');
        });

        document.getElementById('drop-area').addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('border-blue-500');
        });

        document.getElementById('drop-area').addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('border-blue-500');
            const files = e.dataTransfer.files;
            document.getElementById('file').files = files;
            const fileName = files[0].name;
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</x-app-layout>
