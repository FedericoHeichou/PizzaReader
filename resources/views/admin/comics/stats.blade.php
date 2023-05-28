@extends('layouts.admin')
@section('content')
    @yield('information')
    <div class="card">
        <div class="card-header">
            <div class="form-row">
                <div class="col-sm-12">
                    <h3 class="mt-1 float-left">{{ $comic->name }} stats</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
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
                    const scales = {
                        x: {
                            type: 'time',
                            min: labels.length > 7 ? labels[labels.length - 7] : labels[0],
                            max: labels[labels.length - 1],
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
                });
            </script>
            <p class="mt-4"><strong>Tip:</strong> You can zoom scrolling up/down or draging the char.</p>
        </div>
    </div>
@endsection
