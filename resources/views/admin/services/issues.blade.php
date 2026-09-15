@extends('layouts.app')

@section('title', $category->category_name . ' - Issues')

@section('content')
    <script src="{{ asset('assets/js/modal.js') }}"></script>

    <div class="space-y-6">

        <x-card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <x-button
                        icon="ti ti-arrow-narrow-left"
                        color="outline-dark"
                        href="{{ route('admin.services') }}"
                    >
                        Return
                    </x-button>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $category->category_name }}</h1>
                        <p class="text-xs text-gray-500">{{ $category->description }}</p>
                    </div>
                </div>


                <div class="flex inline-flex gap-2">
                    <x-button
                        color="outline-green"
                        icon="ti ti-plus"
                        data-modal-open="add-issue-modal"
                    >
                        Add Issue
                    </x-button>

                    <x-button
                        color="outline-red"
                        icon="ti ti-trash"
                        href="{{ route('admin.services.recycle_bin', $category->category_id) }}"
                    >
                        Recycle Bin
                    </x-button>

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
                        <tbody id="issues-table-body" data-category-id="{{ $category->category_id }}">
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
                                            icon="ti ti-trash"
                                            color="red"
                                            title="Delete Issue"
                                            onclick="confirmDeleteIssue({{ $issue->issue_id }}, '{{ $issue->description }}')"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No issues found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </x-slot:body>
                </x-table>
            </div>
        </x-card>

    </div>

    {{-- Add Issue Modal --}}
    <x-modal_form
        id="add-issue-modal"
        title="Add Issue"
        icon="ti ti-plus"
        width="max-w-md"
    >
        <form id="add-issue-form" onsubmit="return false;">
            <div class="space-y-4">
                <div>
                    <x-input_white
                        name="description"
                        type="text"
                        placeholder="Enter description"
                        icon="ti ti-message"
                        label="Description"
                    />
                    <p id="error-description" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <div>
                    <x-dropdown
                        name="default_priority_level_id"
                        placeholder="Select Priority"
                        label="Default Priority"
                        size="md"
                        :options="['1' => 'Critical', '2' => 'High', '3' => 'Medium', '4' => 'Low']"
                    />
                    <p id="error-default_priority_level_id" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
            </div>

            <div class="flex justify-end mt-6 gap-3">
                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="add-issue-modal"
                >
                    Cancel
                </x-button>
                <x-button
                    type="button"
                    color="outline-green"
                    onclick="event.stopPropagation(); submitAddIssue()"
                >
                    Proceed
                </x-button>
            </div>
        </form>
    </x-modal_form>

    {{-- Confirmation Modal --}}
    <x-modal_form
        id="add-issue-confirm"
        title="Add Issue"
        icon="ti ti-alert-circle"
        width="max-w-sm"
        data-modal-static
    >
        <div class="text-center space-y-2">
            <span class="text-gray-600 text-xs">Are you sure you want to add this issue?</span>
            <p class="text-sm text-gray-900">Description: <span id="confirm-issue-desc" class="font-medium"></span></p>
            <p class="text-sm text-gray-900">Priority: <span id="confirm-issue-priority" class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold"></span></p>
        </div>

        <div class="flex w-full items-center justify-end gap-3 mt-3">
            <x-button
                type="button"
                color="outline-red"
                data-modal-close="add-issue-confirm"
                data-modal-open="add-issue-modal"
            >
                No, Back
            </x-button>

            <x-button
                type="button"
                color="outline-green"
                onclick="proceedAddIssue()"
            >
                Yes, Confirm
            </x-button>
        </div>
    </x-modal_form>

    <x-loading_modal id="add-issue-loading" text="Adding issue..." />
    <x-success_modal id="add-issue-success" text="Issue added successfully!" />

    {{-- Delete Confirmation Modal --}}
    <x-modal_form
        id="delete-issue-confirm"
        title="Delete Issue"
        icon="ti ti-alert-circle"
        width="max-w-sm"
        data-modal-static
    >
        <div class="text-center space-y-2">
            <span class="text-gray-600 text-xs">Are you sure you want to delete this issue?</span>
            <p class="text-sm text-gray-900">Description: <span id="delete-issue-desc" class="font-medium"></span></p>
        </div>

        <div class="flex w-full items-center justify-end gap-3 mt-3">
            <x-button
                type="button"
                color="outline-red"
                data-modal-close="delete-issue-confirm"
            >
                No, Back
            </x-button>

            <x-button
                type="button"
                color="outline-green"
                onclick="proceedDeleteIssue()"
            >
                Yes, Confirm
            </x-button>
        </div>
    </x-modal_form>

    <x-loading_modal id="delete-issue-loading" text="Deleting issue..." />
    <x-success_modal id="delete-issue-success" text="Issue deleted successfully!" />

    <script src="{{ asset('assets/js/services.js') }}"></script>

@endsection
