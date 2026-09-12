@extends('layouts.app')

@section('title', 'Archived Staff')

@section('content')

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/staff.js') }}"></script>

    <x-card>
        <div class="flex w-full items-center inline-flew justify-between gap-4">

            {{-- Return Button --}}
            <x-button
                color="outline-dark"
                iconPosition="center"
                icon="ti ti-arrow-narrow-left"
                href="{{ route('admin.staff') }}"
                class="w-fit text-xs"
            >
                Return
            </x-button>

            {{-- Note --}}
            <x-card
                color="red"
                class="w-fit"
            >
                <div class="flex items-center justify-end gap-2">

                    <span class="text-xs font-semibold whitespace-nowrap">
                        *Note:
                    </span>

                    <span class="text-xs text-center">
                        Archived Staff will be permanently deleted in 30 days after being archived.
                    </span>

                </div>
            </x-card>

        </div>

        <div class="space-y-6 mt-6">
            
            
            <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center lg:w-auto">
                {{-- Search --}}
                <x-input_white
                    name="search"
                    type="text"
                    placeholder="Search by name or id"
                    icon="ti ti-search"
                    class="w-full h-8 sm:w-[20rem]"
                />
            
                {{-- Search Button --}}
                <x-button
                    color="d-blue"
                    type="button"
                >
                    Search
                </x-button>
            </div>

            

            <div class="border-t border-gray-200"></div>

            

            <x-table
                :columns="[
                    'ID',
                    'Full Name',
                    'Archived Date',
                    'Remaining Days',
                ]"
                :actions="true"
            >

                <x-slot:body>

                    @forelse ($archived as $member)
                    <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            {{ $member->staff_ref_num }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            {{ $member->information->last_name }}, {{ $member->information->first_name }} {{ $member->information->middle_name ? $member->information->middle_name[0] . '.' : '' }} {{ $member->information->suffix }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            {{ $member->updated_at->format('m/d/Y') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            {{ max(0, 30 - intdiv($member->updated_at->diffInMilliseconds(now()), 86400000)) }} days
                        </td>
                        
                        <td class="px-4 py-3">
                            <div class="flex items-center">

                                <x-action_btn
                                    icon="ti ti-archive-off"
                                    color="green"
                                    title="Unarchive Staff"
                                    data-modal-open="unarchive-confirmation-modal"
                                    data-id="{{ $member->user_id }}"
                                    data-ref="{{ $member->staff_ref_num }}"
                                    data-name="{{ $member->information->last_name }}, {{ $member->information->first_name }} {{ $member->information->middle_name ? $member->information->middle_name[0] . '.' : '' }} {{ $member->information->suffix }}"
                                />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                            No archived staff found.
                        </td>
                    </tr>
                    @endforelse

                </x-slot:body>

            </x-table>

        </div>
    </x-card>

{{--- Archive Confirmation ---}}
    <x-modal_form
        id="unarchive-confirmation-modal"
        title="Unarchive Staff"
        icon="ti ti-archive-off"
        width="max-w-sm"
    >
        <div class="w-fit mx-auto space-y-5">

            <input type="hidden" id="unarchive-staff-id" value="">

            {{-- Confirmation Message --}}
            <p class="text-sm text-gray-600 text-center">
                Are you sure you want to unarchive this staff?
            </p>

            {{-- Staff Information --}}
            <x-card color="green" class="w-full">
                
                <div class="space-y-2">

                    {{-- ID No. --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            ID No:
                        </span>

                        <span id="unarchive-staff-ref" class="text-xs font-medium">
                        </span>
                    </div>

                    {{-- Full Name --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            Full Name:
                        </span>

                        <span id="unarchive-staff-name" class="text-xs font-medium">
                        </span>
                    </div>

                </div>

            </x-card>

            {{-- Buttons --}}
            <div class="flex justify-center gap-2">

                <x-button
                    type="button"
                    color="outline-gray"
                    data-modal-close="archive-confirmation-modal"
                >
                   No, Cancel
                </x-button>

                <x-button
                    type="button"
                    color="outline-red"
                    onclick="confirmUnarchivedStaff()"
                >
                    Yes, Unarchive
                </x-button>

            </div>

        </div>
    </x-modal_form>

    
    {{-- Loading Modal --}}
    <x-loading_modal id="unarchive-staff-loading" text="Unarchiving staff..." />

    {{-- Success Modal --}}
    <x-success_modal id="unarchive-staff-success" text="Staff removed from archived successfully!" />
@endsection