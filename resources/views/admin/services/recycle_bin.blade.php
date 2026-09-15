@extends('layouts.app')

@section('title', $category->category_name . ' - Recycle Bin')

@section('content')
    <script src="{{ asset('assets/js/modal.js') }}"></script>

    <div class="space-y-6">

        <x-card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <x-button
                        icon="ti ti-arrow-narrow-left"
                        color="outline-dark"
                        href="{{ route('admin.services.issues', $category->category_id) }}"
                    >
                        Return
                    </x-button>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $category->category_name }} - Recycle Bin</h1>
                        <p class="text-xs text-gray-500">{{ $category->description }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card class="!p-0">
            <div class="p-4 overflow-x-auto">
                <x-table
                    class="mt-4 min-w-[500px]"
                    :columns="[
                        'Reference',
                        'Description',
                        'Default Priority',
                    ]"
                    :actions="true"
                >
                    <x-slot:body>
                        <tbody id="recycle-bin-table-body" data-category-id="{{ $category->category_id }}">
                        @forelse ($issues as $issue)
                            <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50" data-issue-id="{{ $issue->issue_id }}">
                                <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                                    {{ $issue->issue_ref_num }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-sm max-w-[200px] truncate">
                                    {{ $issue->description ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @php
                                        $priorityColor = match(strtolower($issue->defaultPriority->priority_name ?? '')) {
                                            'critical' => 'red',
                                            'high' => 'orange',
                                            'medium' => 'yellow',
                                            'low' => 'green',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <x-badge
                                        :label="$issue->defaultPriority->priority_name ?? '-'"
                                        :color="$priorityColor"
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <x-action_btn
                                            icon="ti ti-arrow-back-up"
                                            color="green"
                                            title="Restore Issue"
                                            onclick="confirmRestoreIssue({{ $issue->issue_id }}, '{{ $issue->issue_ref_num }}', '{{ addslashes($issue->description) }}')"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No deleted issues found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </x-slot:body>
                </x-table>
            </div>
        </x-card>

    </div>

    {{-- Restore Confirmation Modal --}}
    <x-modal_form
        id="restore-issue-confirm"
        title="Restore Issue"
        icon="ti ti-arrow-back-up"
        width="max-w-sm"
        data-modal-static
    >
        <div class="text-center space-y-2">
            <span class="text-gray-600 text-xs">Are you sure you want to restore this issue?</span>
            <p class="text-sm text-gray-900">Reference: <span id="restore-issue-ref" class="font-medium"></span></p>
            <p class="text-sm text-gray-900">Description: <span id="restore-issue-desc" class="font-medium"></span></p>
        </div>

        <div class="flex w-full items-center justify-end gap-3 mt-3">
            <x-button
                type="button"
                color="outline-red"
                data-modal-close="restore-issue-confirm"
            >
                No, Cancel
            </x-button>

            <x-button
                type="button"
                color="outline-green"
                onclick="proceedRestoreIssue()"
            >
                Yes, Restore
            </x-button>
        </div>
    </x-modal_form>

    <x-loading_modal id="restore-issue-loading" text="Restoring issue..." />
    <x-success_modal id="restore-issue-success" text="Issue restored successfully!" />

    <script src="{{ asset('assets/js/services.js') }}"></script>

@endsection
