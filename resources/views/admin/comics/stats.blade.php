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
                    const chart = new Chart(document.getElementById('stats-chart'), {
                        type: 'line',
                        data: data,
                        options: {
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
                                },
                            },
                        },
                    });
                    chart.render();
                });
            </script>
        </div>
    </div>
@endsection
