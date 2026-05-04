<x-admin-layout>
    <x-slot name="pageTitle">Detail Log Aktivitas</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Detail Log Aktivitas</h2>
                <p class="mt-1 text-sm text-gray-600">Informasi lengkap tentang aktivitas admin</p>
            </div>
            <a
                href="{{ route('admin.logs.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        {{-- Log Information --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Log</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Waktu</label>
                        <p class="text-sm text-gray-900">
                            {{ $log->created_at->format('d M Y, H:i:s') }}
                            <span class="text-gray-500">({{ $log->created_at->diffForHumans() }})</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Admin</label>
                        <p class="text-sm text-gray-900">
                            {{ $log->admin->name ?? 'Unknown' }}
                            <span class="text-gray-500">({{ $log->admin->email ?? '-' }})</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Aksi</label>
                        <p class="text-sm">
                            @if ($log->action === 'created')
                                <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Dibuat
                                </span>
                            @elseif ($log->action === 'updated')
                                <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    Diperbarui
                                </span>
                            @elseif ($log->action === 'deleted')
                                <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    Dihapus
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($log->action) }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Model</label>
                        <p class="text-sm text-gray-900">
                            {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Old Values --}}
        @if ($log->old_values)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Nilai Lama</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-gray-900">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        @endif

        {{-- New Values --}}
        @if ($log->new_values)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Nilai Baru</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-gray-900">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        @endif

        {{-- Changes Summary (for updates) --}}
        @if ($log->action === 'updated' && $log->old_values && $log->new_values)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Ringkasan Perubahan</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Field
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai Lama
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai Baru
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($log->new_values as $key => $newValue)
                                    @if (isset($log->old_values[$key]) && $log->old_values[$key] !== $newValue)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $key }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                <code class="bg-red-50 text-red-700 px-2 py-1 rounded">
                                                    {{ is_array($log->old_values[$key]) ? json_encode($log->old_values[$key]) : $log->old_values[$key] }}
                                                </code>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                <code class="bg-green-50 text-green-700 px-2 py-1 rounded">
                                                    {{ is_array($newValue) ? json_encode($newValue) : $newValue }}
                                                </code>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
