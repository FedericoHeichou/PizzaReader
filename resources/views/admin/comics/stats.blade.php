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
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p>Select periods to compare:</p>
            <form method="dialog">
                <div class="form-row">
                    <div class="col-sm-2">
                        <label for="compare-with-from" class="sr-only">From</label>
                        <input required type="date" class="form-control" id="compare-with-from" value="{{ date('Y-m-d', strtotime('-14 days')) }}">
                    </div>
                    <div class="col-sm-2">
                        <label for="compare-with-to" class="sr-only">To</label>
                        <input required type="date" class="form-control" id="compare-with-to" value="{{ date('Y-m-d', strtotime('-8 days')) }}">
                    </div>
                    <div class="col-sm-4 text-center">
                        <input type="submit" class="btn btn-primary" id="compare" value="Compare">
                    </div>
                    <div class="col-sm-2">
                        <label for="compare-from" class="sr-only">From</label>
                        <input required type="date" class="form-control" id="compare-from" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                    </div>
                    <div class="col-sm-2">
                        <label for="compare-to" class="sr-only">To</label>
                        <input required type="date" class="form-control" id="compare-to" value="{{ date('Y-m-d', strtotime('-1 days')) }}">
                    </div>
                </div>
            </form>
            <div class="form-row mt-3">
                <div class="col-sm-4 text-center">
                    <p id="total-old"></p>
                </div>
                <div class="col-sm-4 text-center">
                    <p>Views</p>
                </div>
                <div class="col-sm-4 text-center">
                    <p id="total"></p>
                </div>
            </div>
            <p id="trend" class="text-center mt-4"></p>
            <canvas id="stats-chart"></canvas>
            <p class="mt-4"><strong>Tip:</strong> You can zoom pressing CTRL+scroll-up and CTRL+scroll-down or dragging the chart.</p>
            <script>
                const labels = {!! json_encode(array_keys($stats['views_per_day'])) !!};
                const counters = {!! json_encode(array_values($stats['views_per_day'])) !!};
                document.addEventListener("DOMContentLoaded", function(event) {
                    const data = {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Views',
                                backgroundColor: 'rgb(99, 255, 132)',
                                borderColor: 'rgb(99, 255, 132)',
                                data: counters,
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
                                            modifierKey: 'ctrl',
                                        },
                                        drag: {
                                            enabled: true,
                                        },
                                        pinch: {
                                            enabled: true,
                                        },
                                        mode: 'x',
                                        onZoomComplete: function({chart}) {
                                            const min_date = new Date(chart.scales.x.min).toISOString().split('T')[0];
                                            const max_date = new Date(chart.scales.x.max).toISOString().split('T')[0];
                                            const min_index = labels.indexOf(min_date);
                                            const max_index = labels.lastIndexOf(max_date);
                                            document.getElementById('time-range').value = 'custom';
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
                    document.getElementById('compare-from').min = labels[0];
                    document.getElementById('compare-with-from').min = labels[0];
                    document.getElementById('compare-to').max = labels[labels.length - 1];
                    document.getElementById('compare-with-to').max = labels[labels.length - 1];
                    chart.render();
                    setup_trend(chart.data.datasets[0].data, min, max);
                });
                document.getElementById('time-range').addEventListener('change', function(event) {
                    if (event.target.value === 'custom') {
                        return;
                    }
                    const dmin = parseInt(event.target.selectedOptions[0].dataset.min);
                    const dmax = parseInt(event.target.selectedOptions[0].dataset.max);
                    const chart = Chart.getChart('stats-chart');
                    const min_index = dmin > 0 && labels.length > dmin-1 ? labels.length - dmin : 0;
                    const max_index = dmax > 0 && labels.length > dmax-1 ? labels.length - dmax : labels.length - 1;
                    chart.options.scales.x.min = labels[min_index];
                    chart.options.scales.x.max = labels[max_index];
                    chart.update();
                    setup_trend(chart.data.datasets[0].data, min_index, max_index);
                });
                function setup_trend(values, min_index, max_index, old_min_index, old_max_index) {
                    document.getElementById('compare-from').value = labels[min_index];
                    document.getElementById('compare-to').value = labels[max_index];
                    const trend = document.getElementById('trend');
                    if (isNaN(old_min_index)) {
                        old_min_index = Math.max(min_index - (max_index - min_index + 1), 0);
                    }
                    if (isNaN(old_max_index)) {
                        old_max_index = max_index - (max_index - min_index + 1);
                        if (old_max_index < 0) {
                            trend.innerHTML = 'Not enough data to show a trend.';
                            return;
                        }
                    }
                    if (max_index < old_max_index) {
                        let tmp = old_max_index;
                        old_max_index = max_index;
                        max_index = tmp;
                        tmp = old_min_index;
                        old_min_index = min_index;
                        min_index = tmp;
                    }
                    let current_sum = 0;
                    for (let i = min_index; i <= max_index; i++) {
                        current_sum += values[i];
                    }
                    document.getElementById('total').innerHTML = current_sum;
                    let old_sum = 0;
                    for (let i = old_min_index; i <= old_max_index; i++) {
                        old_sum += values[i];
                    }
                    document.getElementById('total-old').innerHTML = old_sum;

                    const grow = current_sum - old_sum;
                    if (isNaN(grow)) {
                        trend.innerHTML = 'Not enough data to show a trend.';
                        return;
                    }

                    if (grow >= 0) {
                        trend.innerHTML = '<span class="text-success">+' + grow + ' views</span> compared to the previous period.';
                    } else {
                        trend.innerHTML = '<span class="text-danger">' + grow + ' views</span> compared to the previous period.';
                    }
                    document.getElementById('compare-with-from').value = labels[old_min_index];
                    document.getElementById('compare-with-to').value = labels[old_max_index];
                }
                document.getElementById('compare').addEventListener('click', function(event) {
                    const chart = Chart.getChart('stats-chart');
                    const compare_from_value = document.getElementById('compare-from').value;
                    const compare_to_value = document.getElementById('compare-to').value;
                    const compare_with_from_value = document.getElementById('compare-with-from').value;
                    const compare_with_to_value = document.getElementById('compare-with-to').value;
                    if (compare_from_value === '' || compare_to_value === '' || compare_with_from_value === '' || compare_with_to_value === '') {
                        return;
                    }
                    const min_date = new Date(compare_from_value).toISOString().split('T')[0];
                    const max_date = new Date(compare_to_value).toISOString().split('T')[0];
                    let min_index = labels.indexOf(min_date);
                    let max_index = labels.lastIndexOf(max_date);
                    const old_min_date = new Date(compare_with_from_value).toISOString().split('T')[0];
                    const old_max_date = new Date(compare_with_to_value).toISOString().split('T')[0];
                    let old_min_index = labels.indexOf(old_min_date);
                    let old_max_index = labels.lastIndexOf(old_max_date);
                    if (max_index < old_max_index) {
                        let tmp = old_max_index;
                        old_max_index = max_index;
                        max_index = tmp;
                        tmp = old_min_index;
                        old_min_index = min_index;
                        min_index = tmp;
                    }
                    setup_trend(chart.data.datasets[0].data, min_index, max_index, old_min_index, old_max_index);
                    chart.options.scales.x.min = labels[min_index];
                    chart.options.scales.x.max = labels[max_index];
                    chart.update();
                    document.getElementById('time-range').value = 'custom';
                });
            </script>
        </div>
    </div>
@endsection
