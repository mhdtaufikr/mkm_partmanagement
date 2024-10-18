@extends('layouts.master')

@section('content')
<style>
    #lblGreetings {
        font-size: 1rem; /* Adjust the base font size as needed */
    }

    @media only screen and (max-width: 600px) {
        #lblGreetings {
            font-size: 1rem; /* Adjust the font size for smaller screens */
        }
    }

    .page-header .page-header-content {
        padding-top: 0rem;
        padding-bottom: 1rem;
    }

    .card {
        margin-bottom: 1rem;
    }
</style>



<style>
    #chartdiv {
      width: 100%;
      height: 500px;
    }
    </style>
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-2">
                        <h1 class="page-header-title">
                            <label id="lblGreetings"></label>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <section class="content">
        <div class="container-fluid">
            <div class="container-fluid px-4 mt-n10">
                <div class="row">
                    <!-- Card 1: Bar Chart -->
                   <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div style="color: white" class="card-header">
                                Daily Problem Summary
                            </div>
                            <div class="card-body">
                                <div id="dailyProblemSummary" style="height: 275px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Line Chart -->
                     <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div style="color: white"  class="card-header">
                                Line Chart Example
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                    </div>

                  <!-- Card 3: Pie Chart -->
<div class="col-xl-6 col-md-6">
    <div class="card">
        <div style="color: white" class="card-header">
            Pie Chart Example
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div id="downtimeByLineChart" style="width: 100%; height: 300px;"></div>
                </div>
                <div class="col-md-6">
                    <div id="problemCountByLineChart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    am5.ready(function() {

        // Adjust common settings
        var commonSettings = {
            fontSize: 18,  // Label font size
            fontWeight: "bold",  // Make the label bold
            y: -20  // Adjust the position of the label
        };

        // Create root element for Downtime by Line chart
        var downtimeRoot = am5.Root.new("downtimeByLineChart");

        // Set themes
        downtimeRoot.setThemes([am5themes_Animated.new(downtimeRoot)]);

        // Add title label for Downtime Total
        var downtimeTitle = downtimeRoot.container.children.push(am5.Label.new(downtimeRoot, {
            text: "Total Downtime",  // Title for the pie chart
            fontSize: commonSettings.fontSize,
            fontWeight: commonSettings.fontWeight,
            x: am5.p50,
            centerX: am5.p50,
            y: 0  // Adjust the position of the title label
        }));

        // Create pie chart for Downtime by Line
        var downtimeChart = downtimeRoot.container.children.push(am5percent.PieChart.new(downtimeRoot, {
            radius: am5.percent(70),  // Adjust radius for better spacing
            innerRadius: am5.percent(40),  // Thicker doughnut
            layout: downtimeRoot.verticalLayout  // Legend beneath the chart
        }));

        // Create series for Downtime by Line
        var downtimeSeries = downtimeChart.series.push(am5percent.PieSeries.new(downtimeRoot, {
            valueField: "total_downtime",
            categoryField: "line"
        }));

        // Set data for Downtime by Line chart
        downtimeSeries.data.setAll(@json($downtimeByLine));

        // Disabling labels and ticks
        downtimeSeries.labels.template.set("visible", false);
        downtimeSeries.ticks.template.set("visible", false);

        // Customizing the tooltip to show actual value instead of percentage
        downtimeSeries.slices.template.set("tooltipText", "{category}: {value}"+" Hour");

        // Adding gradients
        downtimeSeries.slices.template.set("strokeOpacity", 0);
        downtimeSeries.slices.template.set("fillGradient", am5.RadialGradient.new(downtimeRoot, {
            stops: [{
                brighten: -0.8
            }, {
                brighten: -0.8
            }, {
                brighten: -0.5
            }, {
                brighten: 0
            }, {
                brighten: -0.5
            }]
        }));

        // Create legend for Downtime by Line chart
        var downtimeLegend = downtimeChart.children.push(am5.Legend.new(downtimeRoot, {
            centerX: am5.p50,  // Center the legend
            x: am5.p50,
            layout: downtimeRoot.verticalLayout  // Horizontal layout for the legend below the chart
        }));

        // Set legend data
        downtimeLegend.data.setAll(downtimeSeries.dataItems);

        // Legend label settings
        downtimeLegend.valueLabels.template.setAll({ textAlign: "right" });
        downtimeLegend.labels.template.setAll({
            maxWidth: 140,
            width: 140,
            oversizedBehavior: "wrap"
        });

        // Play initial series animation
        downtimeSeries.appear(1000, 100);


        // Create root element for Problem Count by Line chart
        var problemRoot = am5.Root.new("problemCountByLineChart");

        // Set themes
        problemRoot.setThemes([am5themes_Animated.new(problemRoot)]);

        // Add title label for Problem Count
        var problemTitle = problemRoot.container.children.push(am5.Label.new(problemRoot, {
            text: "Total Problem",  // Title for the pie chart
            fontSize: commonSettings.fontSize,
            fontWeight: commonSettings.fontWeight,
            x: am5.p50,
            centerX: am5.p50,
            y: 0  // Adjust the position of the title label
        }));

        // Create pie chart for Problem Count by Line
        var problemChart = problemRoot.container.children.push(am5percent.PieChart.new(problemRoot, {
            radius: am5.percent(70),  // Adjust radius for better spacing
            innerRadius: am5.percent(40),  // Thicker doughnut
            layout: problemRoot.verticalLayout  // Legend beneath the chart
        }));

        // Create series for Problem Count by Line
        var problemSeries = problemChart.series.push(am5percent.PieSeries.new(problemRoot, {
            valueField: "total_problem_count",
            categoryField: "line"
        }));

        // Set data for Problem Count by Line chart
        problemSeries.data.setAll(@json($problemCountByLine));

        // Disabling labels and ticks
        problemSeries.labels.template.set("visible", false);
        problemSeries.ticks.template.set("visible", false);

        // Customizing the tooltip to show actual value instead of percentage
        problemSeries.slices.template.set("tooltipText", "{category}: {value}");

        // Adding gradients
        problemSeries.slices.template.set("strokeOpacity", 0);
        problemSeries.slices.template.set("fillGradient", am5.RadialGradient.new(problemRoot, {
            stops: [{
                brighten: -0.8
            }, {
                brighten: -0.8
            }, {
                brighten: -0.5
            }, {
                brighten: 0
            }, {
                brighten: -0.5
            }]
        }));

        // Create legend for Problem Count by Line chart
        var problemLegend = problemChart.children.push(am5.Legend.new(problemRoot, {
            centerX: am5.p50,  // Center the legend
            x: am5.p50,
            layout: problemRoot.verticalLayout  // Horizontal layout for the legend below the chart
        }));

        // Set legend data
        problemLegend.data.setAll(problemSeries.dataItems);

        // Legend label settings
        problemLegend.valueLabels.template.setAll({ textAlign: "right" });
        problemLegend.labels.template.setAll({
            maxWidth: 140,
            width: 140,
            oversizedBehavior: "wrap"
        });

        // Play initial series animation
        problemSeries.appear(1000, 100);

    }); // end am5.ready()
    </script>






                    <!-- Card 4: Radar Chart -->
                     <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div style="color: white" class="card-header">
                                Radar Chart Example
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <script>
        var myDate = new Date();
        var hrs = myDate.getHours();

        var greet;

        if (hrs < 12)
            greet = 'Good Morning';
        else if (hrs >= 12 && hrs <= 17)
            greet = 'Good Afternoon';
        else if (hrs >= 17 && hrs <= 24)
            greet = 'Good Evening';

        document.getElementById('lblGreetings').innerHTML =
            '<b>' + greet + '</b> and welcome to DigiMAMS';
    </script>


<script>
    am5.ready(function() {
        // Create root element
        var root = am5.Root.new("dailyProblemSummary");

        // Set themes
        root.setThemes([am5themes_Animated.new(root)]);

        // Get the current month
        var currentMonth = new Date().toLocaleString('default', { month: 'long' });

        // Add a label at the top of the chart to display the current month
        var monthLabel = root.container.children.push(am5.Label.new(root, {
            text: currentMonth,
            x: am5.p50,
            centerX: am5.p50,
            y: -10, // Move label higher up
            fontSize: 20,  // Adjust the font size
            fontWeight: "bold"  // Make the label bold
        }));

        // Create chart with padding for axis titles
        var chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: false,
            panY: false,
            pinchZoomX: false,
            paddingTop: 30, // Add some padding to avoid overlap with the label
            paddingBottom: 20 // Add some padding to avoid overlap with the label
        }));

        // Create X-axis (Days of the month)
        var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
        var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
            categoryField: "day", // Make sure the categoryField is set to "day"
            renderer: xRenderer,
            tooltip: am5.Tooltip.new(root, {
                labelText: "{category}" // Correctly display the day in the tooltip
            })
        }));

        // Predefine x-axis categories (1-31)
        var allDays = Array.from({ length: 31 }, (_, i) => (i + 1).toString());
        xAxis.data.setAll(allDays.map(day => ({ day })));


        // Create Y-axis for problem count (left)
        var yAxisProblemCount = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererY.new(root, {}),
            tooltip: am5.Tooltip.new(root, {}),
            min: 0
        }));

        // Add title for left Y-axis
        yAxisProblemCount.children.moveValue(am5.Label.new(root, {
            rotation: -90,
            text: "Problem Count", // Label for left Y-axis
            y: am5.p50,
            centerX: am5.p50
        }), 0);

        // Create Y-axis for downtime balance (right)
        var yAxisDowntime = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererY.new(root, { opposite: true }),
            tooltip: am5.Tooltip.new(root, {}),
            min: 0
        }));

        // Add title for right Y-axis
        yAxisDowntime.children.moveValue(am5.Label.new(root, {
            rotation: 90,
            text: "Total Downtime (Balance)", // Label for right Y-axis
            y: am5.p50,
            centerX: am5.p50
        }), 0);

        // Create ColumnSeries for Problem Count (left Y-axis)
        var problemCountSeries = chart.series.push(am5xy.ColumnSeries.new(root, {
            name: "Problem Count", // Legend label
            xAxis: xAxis,
            yAxis: yAxisProblemCount,
            valueYField: "total_problem_count",
            categoryXField: "day", // Update to use categoryXField instead of valueXField
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueY} problems"
            })
        }));

        problemCountSeries.columns.template.setAll({
            width: am5.percent(50),
            fill: am5.color("#00677F"), // Changed color to #00677F
            strokeOpacity: 0
        });

        // Create series for Total Downtime (right Y-axis)
        var downtimeSeries = chart.series.push(am5xy.LineSeries.new(root, {
            name: "Total Downtime (Balance)", // Legend label
            xAxis: xAxis,
            yAxis: yAxisDowntime,
            valueYField: "total_downtime",
            categoryXField: "day", // Update to use categoryXField instead of valueXField
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueY} hours"
            })
        }));

        downtimeSeries.strokes.template.setAll({
            strokeWidth: 2,
            stroke: am5.color(0xff0000) // Red color for downtime
        });

        // Data processing
        var data = {!! json_encode($dailyProblemSummary) !!};
        var chartData = [];

        // Generate data for days from 1 to 31
        for (var i = 1; i <= 31; i++) {
            let foundData = data.find(d => new Date(d.date).getDate() === i);
            chartData.push({
                day: i.toString(), // Make sure 'day' is a string to match x-axis categories
                total_downtime: foundData ? parseFloat(foundData.total_downtime) : 0, // Ensure downtime is a float
                total_problem_count: foundData ? foundData.total_problem_count : 0
            });
        }

        // Set data to series
        problemCountSeries.data.setAll(chartData);
        downtimeSeries.data.setAll(chartData);

        // Add legend
        var legend = chart.children.push(am5.Legend.new(root, {
            centerX: am5.p50,
            x: am5.p50,
            y: 250,
        }));

        legend.data.setAll([problemCountSeries, downtimeSeries]); // Add both series to legend

        // Add cursor to enable tooltips for the entire chart
        chart.set("cursor", am5xy.XYCursor.new(root, {
            xAxis: xAxis
        }));

        // Make chart animate on load
        chart.appear(1000, 100);
    }); // end am5.ready()
    </script>




</main>
@endsection
