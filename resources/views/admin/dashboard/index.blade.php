@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mt-2.5 space-y-6 ">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            <x-card class="lg:col-span-2 h-full">

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 h-full">

                    <x-overview_card
                        icon="ti ti-ticket"
                        label="Tickets"
                        total="0"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-refresh-dot"
                        label="Pending Tickets"
                        total="0"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-checklist"
                        label="Confirmed Tickets"
                        total="0"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-loader"
                        label="On Progress Tickets"
                        total="0"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-ticket-off"
                        label="Cancelled Tickets"
                        total="0"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-rosette-discount-check"
                        label="Closed Tickets"
                        total="0"
                        color="d-blue"
                    />
                </div>
            </x-card>

            <x-card class="h-full">
                <x-bar_graph
                    label="Ticket Priorities"
                    :items="[
                        ['label' => 'Critical', 'count' => 10, 'color' => '#dc2626'],
                        ['label' => 'High', 'count' => 5, 'color' => '#f97316'],
                        ['label' => 'Medium', 'count' => 2, 'color' => '#eab308'],
                        ['label' => 'Low', 'count' => 0, 'color' => '#22c55e'],
                    ]"
                />
            </x-card>
        </div>
            
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2">
                <livewire:monthly-ticket-request />
            </div>

            <div>
                <livewire:weekly-ticket-request />
            </div>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-card
                label="RECENT TICKET REQUEST"
            >
                <x-table
                    :columns="['Ticket No.', 'From', 'Category', 'Status', 'Relative Time']"
                >
                    <x-slot:body>
                        <tr>
                            <td class="px-4 py-3 text-gray-900"> TCKT-00001</td>
                            <td class="px-4 py-3 text-gray-900"> Brgy. Amihan</td>
                            <td class="px-4 py-3 text-gray-900"> 
                                <x-badge icon="ti ti-devices-2" color="blue"> 
                                    Hardware 
                                </x-badge>
                            </td> 
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-loader" 
                                label="Pending"
                                color="orange"
                                /> 
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 2mins ago </td>
                        </tr>
                        <td class="px-4 py-3 text-gray-900"> TCKT-00002</td>
                            <td class="px-4 py-3 text-gray-900"> HR Department </td>
                            <td class="px-4 py-3 text-gray-900"> 
                                <x-badge icon="ti ti-apps" color="yellow"> 
                                    Software 
                                </x-badge>
                            </td> 
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Confirmed"
                                color="green"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>
                        <td class="px-4 py-3 text-gray-900"> TCKT-00003</td>
                            <td class="px-4 py-3 text-gray-900"> HR Department </td>
                            <td class="px-4 py-3 text-gray-900"> 
                                <x-badge icon="ti ti-apps" color="yellow"> 
                                    Software 
                                </x-badge>
                            </td> 
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Confirmed"
                                color="green"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>
                        <td class="px-4 py-3 text-gray-900"> TCKT-00004</td>
                            <td class="px-4 py-3 text-gray-900"> HR Department </td>
                            <td class="px-4 py-3 text-gray-900"> 
                                <x-badge icon="ti ti-apps" color="yellow"> 
                                    Software 
                                </x-badge>
                            </td> 
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Confirmed"
                                color="green"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>
                    </x-slot:body>
                </x-table>
            </x-card>

            <x-card
                label="REQUEST TICKET REASSIGNMENT"
            >
                <x-table
                    :columns="['Ticket No.', 'Assigned Staff', 'Status', 'Relative Time']"
                >
                    <x-slot:body>
                        <td class="px-4 py-3 text-gray-900"> TCKT-00002</td>
                            <td class="px-4 py-3 text-gray-900"> Dela Cruz, Juan</td>
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Pending"
                                color="orange"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>

                        <td class="px-4 py-3 text-gray-900"> TCKT-00002</td>
                            <td class="px-4 py-3 text-gray-900"> Dela Cruz, Juan</td>
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Pending"
                                color="orange"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>

                        <td class="px-4 py-3 text-gray-900"> TCKT-00002</td>
                            <td class="px-4 py-3 text-gray-900"> Dela Cruz, Juan</td>
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Pending"
                                color="orange"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>

                        <td class="px-4 py-3 text-gray-900"> TCKT-00002</td>
                            <td class="px-4 py-3 text-gray-900"> Dela Cruz, Juan</td>
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Pending"
                                color="orange"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>

                        <td class="px-4 py-3 text-gray-900"> TCKT-00002</td>
                            <td class="px-4 py-3 text-gray-900"> Dela Cruz, Juan</td>
                            <td class="px-4 py-3, text-gray-900"> 
                                <x-badge 
                                icon="ti ti-circle-check" 
                                label="Pending"
                                color="orange"
                                />
                            </td>
                            <td class="px-4 py-3 text-gray-400"> 1hour ago </td>
                        </tr>
                    </x-slot:body>
                </x-table>
            </x-card>
        </div>
    </div>

@endsection
