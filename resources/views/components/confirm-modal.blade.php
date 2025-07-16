@props(['id', 'title' => 'Konfirmasi', 'message' => 'Apakah Anda yakin ingin melanjutkan?', 'action', 'actionLabel' => 'Hapus', 'cancelLabel' => 'Batal'])

<x-modal :name="$id" :show="false" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h2>
        <p class="text-gray-600 mb-6">{{ $message }}</p>
        <div class="flex justify-end gap-2">
            <button type="button" @click="$dispatch('close-modal', '{{ $id }}')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold hover:bg-gray-300">{{ $cancelLabel }}</button>
            <form method="POST" action="{{ $action }}" class="inline">
                @csrf
                @method('DELETE')
                <x-danger-button>{{ $actionLabel }}</x-danger-button>
            </form>
        </div>
    </div>
</x-modal>
