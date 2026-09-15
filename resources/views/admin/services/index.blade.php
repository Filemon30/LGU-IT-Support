@extends('layouts.app')

@section('title', 'Services')

@section('content')
    <script src="{{ asset('assets/js/modal.js') }}"></script>

    <div class="space-y-6">

        <x-card>

            <div class="grid grid-cols-1 lg:grid-cols-2 md:grid-cols-2 gap-6">

                <x-overview_card
                    icon="ti ti-category"
                    label="Categories"
                    total="{{ $totalCategories }}"
                    color="blue"
                />

                <x-overview_card
                    icon="ti ti-list"
                    label="Issues"
                    total="{{ $totalIssues }}"
                    color="red"
                />

            </div>

            <div class="border-t border-gray-200 mt-4 mb-4"></div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <x-input_white
                        name="search"
                        type="text"
                        placeholder="Search by name or id"
                        icon="ti ti-search"
                        class="w-full sm:w-[20rem]"
                    />
                    <x-button
                        color="d-blue"
                        type="button"
                        class="w-full sm:w-auto h-9 text-xs"
                    >
                        Search
                    </x-button>
                </div>

            </div>
        </x-card>

        <x-card>
            {{--- Categories Container ---}}
            <div id="categories-container" class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-4 gap-4">

                @forelse ($categoryData as $category)
                    {{--- Category Card ---}}
                    <a href="{{ route('admin.services.issues', $category['id']) }}" class="block">
                        <x-card class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 cursor-pointer">

                            <div class="flex items-center gap-3">
                                <x-icon_container icon="{{ $category['icon'] }}" color="{{ $category['color'] }}" />
                                <span class="text-lg font-semibold">{{ $category['name'] }}</span>
                                <div class="flex flex-col gap-1">
                                    <x-badge
                                    icon="ti ti-ticket"
                                    color="{{ $category['color'] }}"
                                    label="{{ $category['issues_count'] }} Issues"
                                    />
                                </div>
                            </div>
                            @if(!empty($category['description']))
                                <span class="text-xs text-gray-500 mt-2">{{ $category['description'] }}</span>
                            @endif

                        </x-card>
                    </a>
                @empty
                    <div class="col-span-4 text-center py-8 text-gray-500 text-sm">
                        No categories found.
                    </div>
                @endforelse

            </div>
        </x-card>
    </div>

    <script src="{{ asset('assets/js/services.js') }}"></script>
@endsection
