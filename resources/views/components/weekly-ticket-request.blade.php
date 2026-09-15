<?php

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Ticket;
use App\Models\Category;

new class extends Component
{
    public int $week;

    /**
     * Mount component with the current week selected.
     */
    public function mount(): void
    {
        $this->week = $this->getCurrentWeek();
    }

    /**
     * Data passed to the view.
     */
    public function with(): array
    {
        return [
            'weeks' => [
                1 => 'Week 1',
                2 => 'Week 2',
                3 => 'Week 3',
                4 => 'Week 4',
            ],

            'currentWeek' => $this->getCurrentWeek(),

            'chart' => $this->chartData(),
        ];
    }

    /**
     * Calculate the current week of the month.
     *
     * Week 1 = days 1-7
     * Week 2 = days 8-14
     * Week 3 = days 15-21
     * Week 4 = days 22-28
     */
    private function getCurrentWeek(): int
    {
        $day = Carbon::now('Asia/Manila')->day;

        return min(
            4,
            max(
                1,
                (int) ceil($day / 7)
            )
        );
    }

    /**
     * Generate chart data for the selected week.
     */
    private function chartData(): array
    {
        // Keep selected week within available dropdown range.
        $this->week = max(1, min(4, $this->week));

        $days = [
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat',
            'Sun',
        ];

        $now = Carbon::now('Asia/Manila');

        $currentWeek = $this->getCurrentWeek();

        /*
        |--------------------------------------------------------------------------
        | Week date range
        |--------------------------------------------------------------------------
        |
        | Week 1 = 1-7
        | Week 2 = 8-14
        | Week 3 = 15-21
        | Week 4 = 22-28
        |
        */

        $weekStartDay = (($this->week - 1) * 7) + 1;
        $weekEndDay = min(
            $this->week * 7,
            $now->daysInMonth
        );

        $start = Carbon::create(
            $now->year,
            $now->month,
            $weekStartDay,
            0,
            0,
            0,
            'Asia/Manila'
        );

        /*
        |--------------------------------------------------------------------------
        | Category IDs
        |--------------------------------------------------------------------------
        */

        $hwId = Category::where(
            'category_name',
            'Hardware'
        )->value('category_id');

        $swId = Category::where(
            'category_name',
            'Software'
        )->value('category_id');

        $netId = Category::where(
            'category_name',
            'Network'
        )->value('category_id');

        $knownIds = array_filter([
            $hwId,
            $swId,
            $netId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Chart arrays
        |--------------------------------------------------------------------------
        */

        $hardware = [];
        $software = [];
        $network = [];
        $others = [];

        /*
        |--------------------------------------------------------------------------
        | Generate data for each day
        |--------------------------------------------------------------------------
        */

        for ($i = 0; $i < 7; $i++) {

            $day = $start->copy()->addDays($i);

            $dayStart = $day->copy()->startOfDay();
            $dayEnd = $day->copy()->endOfDay();

            /*
            |--------------------------------------------------------------------------
            | Don't display days beyond the selected week's valid range.
            |--------------------------------------------------------------------------
            */

            if ($day->day > $weekEndDay) {

                $hardware[] = 0;
                $software[] = 0;
                $network[] = 0;
                $others[] = 0;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Don't display future days when viewing the current week.
            |--------------------------------------------------------------------------
            */

            if (
                $this->week === $currentWeek &&
                $day->isAfter($now)
            ) {

                $hardware[] = 0;
                $software[] = 0;
                $network[] = 0;
                $others[] = 0;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Hardware
            |--------------------------------------------------------------------------
            */

            $hardware[] = $hwId
                ? Ticket::whereHas(
                    'issue.category',
                    fn ($query) =>
                        $query->where(
                            'category_id',
                            $hwId
                        )
                )
                    ->whereBetween(
                        'created_at',
                        [$dayStart, $dayEnd]
                    )
                    ->count()
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Software
            |--------------------------------------------------------------------------
            */

            $software[] = $swId
                ? Ticket::whereHas(
                    'issue.category',
                    fn ($query) =>
                        $query->where(
                            'category_id',
                            $swId
                        )
                )
                    ->whereBetween(
                        'created_at',
                        [$dayStart, $dayEnd]
                    )
                    ->count()
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Network
            |--------------------------------------------------------------------------
            */

            $network[] = $netId
                ? Ticket::whereHas(
                    'issue.category',
                    fn ($query) =>
                        $query->where(
                            'category_id',
                            $netId
                        )
                )
                    ->whereBetween(
                        'created_at',
                        [$dayStart, $dayEnd]
                    )
                    ->count()
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Others
            |--------------------------------------------------------------------------
            */

            $others[] = $knownIds
                ? Ticket::whereHas(
                    'issue.category',
                    fn ($query) =>
                        $query->whereNotIn(
                            'category_id',
                            $knownIds
                        )
                )
                    ->whereBetween(
                        'created_at',
                        [$dayStart, $dayEnd]
                    )
                    ->count()
                : Ticket::whereBetween(
                    'created_at',
                    [$dayStart, $dayEnd]
                )->count();
        }

        return [
            'week' => $this->week,

            'labels' => $days,

            'hardware' => $hardware,

            'software' => $software,

            'network' => $network,

            'others' => $others,
        ];
    }
};

?>

<div class="h-full">

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    @endassets

    <div class="flex h-full flex-col gap-4 rounded-lg bg-white p-4 shadow-sm">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3">

            <p class="text-xs font-bold text-gray-800">
                WEEKLY TICKET REQUEST
            </p>

            <div
                wire:ignore
                class="flex items-center gap-2"
            >

                {{-- Reset week button --}}
                <button
                    type="button"
                    data-reset-range
                    title="Back to current week"
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

                {{-- Week dropdown --}}
                <x-dropdown
                    name="week"
                    :options="$weeks"
                    placeholder="Week"
                    :selected="$week"
                    size="sm"
                    data-default="{{ $currentWeek }}"
                    class="w-15"
                />

            </div>

        </div>

        {{-- Chart data bridge --}}
        <script type="application/json" data-chart-data>
            @json($chart)
        </script>

        {{-- Chart --}}
        <div class="relative flex flex-1 items-center justify-center">

            <div class="relative h-60 w-60">

                <canvas data-weekly-chart></canvas>

            </div>

        </div>

    </div>

    @script
        <script>

            /*
            |--------------------------------------------------------------------------
            | Component root
            |--------------------------------------------------------------------------
            */

            const root = document
                .querySelector('[data-weekly-chart]')
                .closest('[wire\\:id]');

            const canvas = root.querySelector(
                '[data-weekly-chart]'
            );

            const resetButton = root.querySelector(
                '[data-reset-range]'
            );

            let chart = null;


            /*
            |--------------------------------------------------------------------------
            | Get latest chart data
            |--------------------------------------------------------------------------
            */

            function chartPayload() {

                const element = root.querySelector(
                    '[data-chart-data]'
                );

                if (!element) {
                    return null;
                }

                try {

                    return JSON.parse(
                        element.textContent
                    );

                } catch (error) {

                    console.error(
                        'Unable to parse chart data:',
                        error
                    );

                    return null;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Draw / redraw chart
            |--------------------------------------------------------------------------
            */

            function drawChart() {

                const payload = chartPayload();

                if (
                    typeof Chart === 'undefined' ||
                    !payload ||
                    !canvas
                ) {
                    return;
                }

                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(
                    canvas.getContext('2d'),
                    {
                        type: 'doughnut',

                        data: {

                            labels: [
                                'Hardware',
                                'Software',
                                'Network',
                                'Others'
                            ],

                            datasets: [
                                {
                                    data: [

                                        (
                                            payload.hardware || []
                                        ).reduce(
                                            (a, b) => a + b,
                                            0
                                        ),

                                        (
                                            payload.software || []
                                        ).reduce(
                                            (a, b) => a + b,
                                            0
                                        ),

                                        (
                                            payload.network || []
                                        ).reduce(
                                            (a, b) => a + b,
                                            0
                                        ),

                                        (
                                            payload.others || []
                                        ).reduce(
                                            (a, b) => a + b,
                                            0
                                        ),

                                    ],

                                    backgroundColor: [
                                        '#fb923c',
                                        '#1E4079',
                                        '#22c55e',
                                        '#9ca3af',
                                    ],

                                    borderColor: '#ffffff',

                                    borderWidth: 2,
                                },
                            ],
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                legend: {

                                    position: 'bottom',

                                    labels: {

                                        usePointStyle: true,

                                        pointStyle: 'circle',

                                        boxWidth: 8,

                                        boxHeight: 8,

                                        padding: 16,

                                        font: {

                                            size: 12,

                                            weight: '600',

                                        },

                                    },

                                },

                                tooltip: {

                                    callbacks: {

                                        label: (item) => {

                                            const total =
                                                item.dataset.data.reduce(
                                                    (a, b) => a + b,
                                                    0
                                                );

                                            const value =
                                                item.parsed;

                                            const percentage =
                                                total > 0
                                                    ? (
                                                        (value / total) *
                                                        100
                                                    ).toFixed(1)
                                                    : 0;

                                            return `${item.label}: ${value} (${percentage}%)`;
                                        },

                                    },

                                    boxPadding: 3,

                                },

                            },

                        },

                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Get week dropdown input
            |--------------------------------------------------------------------------
            */

            function getWeekInput() {

                return root.querySelector(
                    '[data-dropdown-input][name="week"]'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Sync selected week with Livewire
            |--------------------------------------------------------------------------
            */

            function syncWeekToComponent() {

                const weekInput = getWeekInput();

                if (!weekInput) {
                    return;
                }

                const weekValue = parseInt(
                    weekInput.value,
                    10
                );

                if (
                    Number.isNaN(weekValue) ||
                    weekValue < 1 ||
                    weekValue > 4
                ) {
                    return;
                }

                $wire
                    .set('week', weekValue)
                    .then(() => {

                        updateResetVisibility();

                        drawChart();

                    });
            }


            /*
            |--------------------------------------------------------------------------
            | Update reset button visibility
            |--------------------------------------------------------------------------
            */

            function updateResetVisibility() {

                const weekDropdown = root.querySelector(
                    'details[data-dropdown][data-default]'
                );

                if (!weekDropdown) {
                    return;
                }

                const input = weekDropdown.querySelector(
                    '[data-dropdown-input]'
                );

                if (!input) {
                    return;
                }

                const defaultWeek =
                    weekDropdown.dataset.default;

                const changed =
                    input.value !== '' &&
                    input.value !== defaultWeek;

                resetButton.style.display =
                    changed ? '' : 'none';
            }


            /*
            |--------------------------------------------------------------------------
            | Detect dropdown changes
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'dropdown-change',
                (event) => {

                    if (!root.contains(event.target)) {
                        return;
                    }

                    syncWeekToComponent();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Reset to current week
            |--------------------------------------------------------------------------
            */

            resetButton.addEventListener(
                'click',
                () => {

                    const weekDropdown = root.querySelector(
                        'details[data-dropdown][data-default]'
                    );

                    if (!weekDropdown) {
                        return;
                    }

                    const input = weekDropdown.querySelector(
                        '[data-dropdown-input]'
                    );

                    if (!input) {
                        return;
                    }

                    const defaultWeek =
                        weekDropdown.dataset.default;

                    if (
                        input.value === defaultWeek
                    ) {
                        return;
                    }

                    window.xDropdownSelect(
                        weekDropdown,
                        defaultWeek
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Initial state
            |--------------------------------------------------------------------------
            */

            updateResetVisibility();

            drawChart();


            /*
            |--------------------------------------------------------------------------
            | Livewire update
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'livewire:navigated',
                () => {

                    updateResetVisibility();

                    drawChart();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Reverb real-time ticket update
            |--------------------------------------------------------------------------
            */

            if (window.Echo) {

                window.Echo
                    .channel('tickets')
                    .listen(
                        '.new-ticket',
                        (event) => {

                            $wire
                                .$refresh()
                                .then(() => {

                                    updateResetVisibility();

                                    drawChart();

                                });

                        }
                    );

            }

        </script>
    @endscript

</div>