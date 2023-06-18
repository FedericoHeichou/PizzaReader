@extends('layouts.admin')
@section('content')
    @yield('information')
    <div class="card">
        <div class="card-header">
            <div class="form-row">
                <div class="col-sm-9">
                    <h3 class="mt-1 float-left">{{ $comic->name }} stats</h3>
                </div>
                <div class="col-sm-3">
                    <select class="form-control float-right" id="time-range">
                        <option value="today" data-min="2" data-max="1">Today</option>
                        <option value="yesterday" data-min="3" data-max="2">Yesterday</option>
                        <option value="last-7-days" data-min="8" data-max="2" selected="selected">Last 7 days</option>
                        <option value="last-28-days" data-min="29" data-max="2">Last 28 days</option>
                        <option value="last-90-days" data-min="91" data-max="2">Last 90 days</option>
                        <option value="last-year" data-min="366" data-max="2">Last year</option>
                        <option value="all-time" data-min="0" data-max="1">All time</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p id="trend" class="text-center"></p>
            <canvas id="stats-chart"></canvas>
            <script>
                document.addEventListener("DOMContentLoaded", function(event) {
                    const labels = {!! json_encode(array_keys($stats['views_per_day'])) !!};
                    const data = {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Views',
                                backgroundColor: 'rgb(99, 255, 132)',
                                borderColor: 'rgb(99, 255, 132)',
                                data: {!! json_encode(array_values($stats['views_per_day'])) !!},
                            },
                        ]
                    };
                    const min = labels.length > 7 ? labels.length - 8 : 0;
                    const max = labels.length > 1 ? labels.length - 2 : 0;
                    const scales = {
                        x: {
                            type: 'time',
                            min: labels[min],
                            max: labels[max],
                            time: {
                                unit: 'day',
                            },
                            position: 'bottom',
                        },
                        y: {
                            type: 'linear',
                            beginAtZero: true,
                            position: 'left',
                        },
                    };
                    const chart = new Chart(document.getElementById('stats-chart'), {
                        type: 'line',
                        data: data,
                        options: {
                            scales: scales,
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Stats per day'
                                },
                                zoom: {
                                    zoom: {
                                        wheel: {
                                            enabled: true,
                                        },
                                        drag: {
                                            enabled: true,
                                        },
                                        pinch: {
                                            enabled: true,
                                        },
                                        mode: 'x',
                                        onZoomComplete: function({chart}) {
                                            console.log(chart)
                                            const labels = chart.data.labels;
                                            const min_date = new Date(chart.scales.x.min).toISOString().split('T')[0];
                                            const max_date = new Date(chart.scales.x.max).toISOString().split('T')[0];
                                            const min_index = labels.indexOf(min_date);
                                            const max_index = labels.lastIndexOf(max_date);
                                            setup_trend(chart.data.datasets[0].data, min_index, max_index);
                                        },
                                    },
                                    limits: {
                                        y: {
                                            min: 0,
                                        },
                                        x: {
                                            min: new Date(labels[0]).valueOf(),
                                            max: new Date(labels[labels.length - 1]).valueOf(),
                                        },
                                    },
                                },
                            },
                        },
                    });
                    chart.render();
                    setup_trend(chart.data.datasets[0].data, min, max);
                });
                document.getElementById('time-range').addEventListener('change', function(event) {
                    const dmin = parseInt(event.target.selectedOptions[0].dataset.min);
                    const dmax = parseInt(event.target.selectedOptions[0].dataset.max);
                    const chart = Chart.getChart('stats-chart');
                    const labels = chart.data.labels;
                    const min = labels.length > dmin-1 ? labels.length - dmin : 0;
                    const max = labels.length > dmax-1 ? labels.length - dmax : 0;
                    chart.options.scales.x.min = labels[min];
                    chart.options.scales.x.max = labels[max];
                    chart.update();
                    setup_trend(chart.data.datasets[0].data, min, max);
                });
                function setup_trend(values, min, max) {
                    const diff = max - min + 1;
                    let current_sum = 0;
                    for (let i = min; i <= max; i++) {
                        current_sum += values[i];
                    }
                    let old_sum = 0;
                    for (let i = Math.max(min - diff, 0); i <= Math.max(max - diff, 0); i++) {
                        old_sum += values[i];
                    }
                    const grow = current_sum - old_sum;
                    if (isNaN(grow)) {
                        const trend = document.getElementById('trend');
                        trend.innerHTML = 'Not enough data to show a trend.';
                        return;
                    }

                    const trend = document.getElementById('trend');
                    if (grow >= 0) {
                        trend.innerHTML = '<span class="text-success">+' + grow + ' views</span> compared to the previous period.';
                    } else {
                        trend.innerHTML = '<span class="text-danger">' + grow + ' views</span> compared to the previous period.';
                    }
                }
            </script>
            <p class="mt-4"><strong>Tip:</strong> You can zoom scrolling up/down or draging the char.</p>
        </div>
    </div>
@endsection
