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
            <div id="views-chart"></div>
            <style>.canvasjs-chart-container>canvas:first-of-type { position: relative !important; }</style>
            <script>
                document.addEventListener("DOMContentLoaded", function(event) {
                    var chart = new CanvasJS.Chart("views-chart", {
                        animationEnabled: true,
                        theme: "{{ isset($_COOKIE['dark']) &&  $_COOKIE['dark'] ? 'dark2' : 'light2' }}",
                        title:{
                            text: "Views per day"
                        },
                        axisY:{
                            includeZero: true
                        },
                        data: [{
                            type: "column",
                            yValueFormatString: "#,###",
                            dataPoints: [
                                @foreach($stats['views_per_day'] as $date => $views)
                                    { label: "{{ $date }}", y: {{ $views }} },
                                @endforeach
                            ]
                        }]
                    });
                    chart.render();
                });
            </script>
        </div>
    </div>
@endsection
