@extends('layouts.admin')

@section('content')
    <style>
        #charts-container {
            position: relative;
            overflow: hidden;
        }

        .chart-slide {
            position: relative;
            top: 0;
            left: 0;
            transition: all 0.3s ease-in-out;
            width: 100%;
            display: none;
        }

        .chart-slide.active {
            display: block !important;
        }

        .chart-slide.out {
            left: -100%;
        }
    </style>
    @include('admin.partials.titlebar')

    <section class="content">
        <div class="container-fluid bg-light mb-5">
            <div class="row">
                <div class="col-md-4">
                    @include('admin/reports/menu')
                </div>
            </div>

            <div id="charts-container">
                <div id="registration-chart" class="chart-slide active" style="width: 100%; height: 100%;">
                    @include('admin/reports/charts/registration-chart')
                </div>

                <div id="registrations-vs-sales-chart" class="chart-slide">
                    <h1>Monthly Trend of Registrations vs Sales</h1>
                </div>

                <div id="ratio-chart" class="chart-slide">
                    <h1>Ratio of Registrations to Sales</h1>
                </div>
            </div>
        </div>
    </section>
@stop

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/admin/js/FrostCharts.js') }}"></script>

    <script>
        const registrationChartUrl = "{{ url('admin/reports/services/charts/registration') }}";

        $(document).ready(function() {
            // Check if FrostCharts is loaded and functional
            if (typeof FrostCharts === 'undefined' || !FrostCharts.initializeCharts || !FrostCharts.fetchChartData) {
                console.error('FrostCharts is not properly loaded or its methods are missing!');
                $('#charts-container').html(
                    '<div class="alert alert-danger">Error loading charts. Please refresh the page or contact support.</div>'
                );
                return;
            }

            // Handle chart tab switching
            $("ul.list-group a").click(function(e) {
                e.preventDefault();

                const target = $(this).data("target"); // Define target from the data attribute
                const targetElement = $("#" + target);

                if (!targetElement.length) {
                    console.error(`Target element #${target} not found.`);
                    return;
                }

                // Hide current active chart and show the target chart
                $(".chart-slide.active").removeClass("active").addClass("out");
                targetElement.removeClass("out").addClass("active");

                // Initialize and load chart data only if it hasn't been loaded yet
                if (!targetElement.data("loaded")) {
                    if (target === "registration-chart") {
                        const yearCtx = $('#yearChart')[0]?.getContext('2d');
                        const monthCtx = $('#monthChart')[0]?.getContext('2d');
                        if (yearCtx && monthCtx) {
                                registrationChartUrl
                            FrostCharts.fetchChartData(
                                "{{ url('admin/reports/services/charts/registration') }}"
                            );
                        } else {
                            console.error('Chart canvas elements are missing.');
                        }
                    }

                    targetElement.data("loaded", true); // Mark chart as loaded
                }
            });
        });
    </script>
@stop
