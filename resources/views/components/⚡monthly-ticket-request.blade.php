<?php

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Category;

new class extends Component
{
    public int $year;

    public function mount(): void
    {
        $this->year = (int) date('Y');
    }

    public function with(): array
    {
        return [
            'years' => array_combine(range((int) date('Y'), 2020), range((int) date('Y'), 2020)),

            'currentYear' => (int) date('Y'),

            'chart' => $this->chartData(),
        ];
    }

    private function chartData(): array
    {
        $months = [
            'January', 'February', 'March', 'April',
            'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December',
        ];

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $hwId = Category::where('category_name', 'Hardware')->value('category_id');
        $swId = Category::where('category_name', 'Software')->value('category_id');
        $netId = Category::where('category_name', 'Network')->value('category_id');
        $knownIds = array_filter([$hwId, $swId, $netId]);

        $hardware = [];
        $software = [];
        $network = [];
        $others = [];
        $totals = [];

        for ($i = 1; $i <= 12; $i++) {
            if ($this->year === $currentYear && $i > $currentMonth) {
                $hardware[] = 0;
                $software[] = 0;
                $network[] = 0;
                $others[] = 0;
                $totals[] = 0;
                continue;
            }

            $h = $hwId
                ? Ticket::where('issue_id', '!=', null)
                    ->whereHas('issue.category', fn ($q) => $q->where('category_id', $hwId))
                    ->whereYear('created_at', $this->year)
                    ->whereMonth('created_at', $i)
                    ->count()
                : 0;

            $s = $swId
                ? Ticket::whereHas('issue.category', fn ($q) => $q->where('category_id', $swId))
                    ->whereYear('created_at', $this->year)
                    ->whereMonth('created_at', $i)
                    ->count()
                : 0;

            $n = $netId
                ? Ticket::whereHas('issue.category', fn ($q) => $q->where('category_id', $netId))
                    ->whereYear('created_at', $this->year)
                    ->whereMonth('created_at', $i)
                    ->count()
                : 0;

            $o = $knownIds
                ? Ticket::whereHas('issue.category', fn ($q) => $q->whereNotIn('category_id', $knownIds))
                    ->whereYear('created_at', $this->year)
                    ->whereMonth('created_at', $i)
                    ->count()
                : Ticket::whereYear('created_at', $this->year)
                    ->whereMonth('created_at', $i)
                    ->count();

            $hardware[] = $h;
            $software[] = $s;
            $network[] = $n;
            $others[] = $o;
            $totals[] = $h + $s + $n + $o;
        }

        return [
            'year' => $this->year,
            'labels' => $months,
            'hardware' => $hardware,
            'software' => $software,
            'network' => $network,
            'others' => $others,
            'totals' => $totals,
        ];
    }
};

?>

<div class="h-full">
    @assets
        <script
            src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"
        ></script>
    @endassets

    <div class="flex h-full flex-col gap-4 rounded-lg bg-white p-4 shadow-sm">

        <div class="flex items-center justify-between gap-3">

            <p class="text-xs font-bold text-gray-800">
                MONTHLY TICKET REQUEST
            </p>

            <div wire:ignore class="flex items-center gap-2">

                {{-- Reset year button --}}
                <button
                    type="button"
                    data-reset-range
                    title="Back to current year"
                    style="display: none;"
                    class="
                        flex
                        h-7
                        w-7
                        shrink-0
                        items-center
                        justify-center
                        rounded-full
                        text-gray-500
                        transition-colors
                        hover:bg-gray-100
                        hover:text-gray-800
                    "
                >
                    <i class="ti ti-reload text-sm"></i>
                </button>

                {{-- Year dropdown --}}
                <x-dropdown
                    name="year"
                    :options="$years"
                    placeholder="Year"
                    :selected="$year"
                    size="sm"
                    data-default="{{ $currentYear }}"
                    class="w-24"
                />

            </div>

        </div>

        {{-- Chart data bridge --}}
        <script type="application/json" data-chart-data>
            @json($chart)
        </script>

        <div class="relative w-full flex-1">
            <div class="relative h-72 w-full">
                <canvas data-monthly-chart></canvas>
            </div>
        </div>

    </div>

    @script
        <script>
            const root = document
                .querySelector('[data-monthly-chart]')
                .closest('[wire\\:id]');

            const canvas = root.querySelector(
                '[data-monthly-chart]'
            );

            const resetButton = root.querySelector(
                '[data-reset-range]'
            );

            let chart = null;

            /**
             * Get the latest chart data from Livewire.
             */
            function chartPayload() {
                return JSON.parse(
                    root
                        .querySelector('[data-chart-data]')
                        .textContent
                );
            }

            /**
             * Create a vertical gradient.
             *
             * Top = 100% opacity.
             * Bottom = 20% opacity.
             */
            function createGradient(context, chartArea) {

                const blue = '#2c51ec';

                const gradient = context.createLinearGradient(
                    0,
                    chartArea.top,
                    0,
                    chartArea.bottom
                );

                gradient.addColorStop(
                    0,
                    'rgb(98, 128, 245, 1)'
                );

                gradient.addColorStop(
                    1,
                    'rgba(98, 128, 245, 0.1)'
                );

                return gradient;
            }

            /**
             * Draw / redraw the chart.
             */
            function drawChart() {

                const payload = chartPayload();

                if (
                    typeof Chart === 'undefined' ||
                    !payload
                ) {
                    return;
                }

                if (chart) {
                    chart.destroy();
                }

                const orange = '#fb923c';
                const blue   = '#1E4079';
                const green  = '#22c55e';
                const gray   = '#9ca3af';

                chart = new Chart(
                    canvas.getContext('2d'),
                    {
                        type: 'line',

                        data: {
                            labels: payload.labels,

                            datasets: [
                                {
                                    label: 'Hardware',
                                    data: payload.hardware,
                                    borderColor: orange,
                                    backgroundColor: 'transparent',
                                    tension: 0.35,
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: orange,
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                },
                                {
                                    label: 'Software',
                                    data: payload.software,
                                    borderColor: blue,
                                    backgroundColor: 'transparent',
                                    tension: 0.35,
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: blue,
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                },
                                {
                                    label: 'Network',
                                    data: payload.network,
                                    borderColor: green,
                                    backgroundColor: 'transparent',
                                    tension: 0.35,
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: green,
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                },
                                {
                                    label: 'Others',
                                    data: payload.others || [],
                                    borderColor: gray,
                                    backgroundColor: 'transparent',
                                    tension: 0.35,
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: gray,
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                },
                            ],
                        },

                        options: {
                            responsive: true,

                            maintainAspectRatio: false,

                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },

                            plugins: {

                                legend: {
                                    position: 'bottom',

                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'circle',

                                        boxWidth: 6,
                                        boxHeight: 6,

                                        padding: 16,
                                    },
                                },

                                tooltip: {

                                    callbacks: {

                                        title: (items) => {
                                            return `${items[0].label} ${payload.year}`;
                                        },
                                    },

                                    boxPadding: 3,
                                },
                            },

                            scales: {

                                x: {
                                    ticks: {
                                        autoSkip: false,
                                    },

                                    grid: {
                                        display: false,
                                    },
                                },

                                y: {
                                    beginAtZero: true,

                                    ticks: {
                                        precision: 0,
                                    },
                                },
                            },
                        },
                    }
                );
            }

            /**
             * Send selected year to Livewire.
             */
            function syncYearToComponent() {

                const yearInput = root.querySelector(
                    '[data-dropdown-input][name="year"]'
                );

                if (!yearInput) {
                    return;
                }

                const yearValue = parseInt(
                    yearInput.value,
                    10
                );

                if (Number.isNaN(yearValue)) {
                    return;
                }

                $wire
                    .set('year', yearValue)
                    .then(() => {

                        updateResetVisibility();

                        drawChart();
                    });
            }

            /**
             * Show the reset button only when
             * the selected year is different
             * from the current year.
             */
            function updateResetVisibility() {

                const yearDropdown = root.querySelector(
                    'details[data-dropdown][data-default]'
                );

                if (!yearDropdown) {
                    return;
                }

                const input = yearDropdown.querySelector(
                    '[data-dropdown-input]'
                );

                if (!input) {
                    return;
                }

                const changed =
                    input.value !== '' &&
                    input.value !==
                    yearDropdown.dataset.default;

                resetButton.style.display =
                    changed ? '' : 'none';
            }

            /**
             * Detect year dropdown changes.
             */
            document.addEventListener(
                'dropdown-change',
                (event) => {

                    if (!root.contains(event.target)) {
                        return;
                    }

                    syncYearToComponent();
                }
            );

            /**
             * Reset back to the current year.
             */
            resetButton.addEventListener(
                'click',
                () => {

                    const yearDropdown = root.querySelector(
                        'details[data-dropdown][data-default]'
                    );

                    if (!yearDropdown) {
                        return;
                    }

                    const input = yearDropdown.querySelector(
                        '[data-dropdown-input]'
                    );

                    if (!input) {
                        return;
                    }

                    const defaultYear =
                        yearDropdown.dataset.default;

                    if (input.value === defaultYear) {
                        return;
                    }

                    window.xDropdownSelect(
                        yearDropdown,
                        defaultYear
                    );
                }
            );

            /**
             * Initial state.
             */
            updateResetVisibility();
            drawChart();

            /**
             * Reverb real-time update.
             */
            if (window.Echo) {
                window.Echo.channel('tickets')
                    .listen('.new-ticket', (e) => {
                        $wire.$refresh().then(() => {
                        drawChart();
                    });

                    });
            }
        </script>
    @endscript

</div>