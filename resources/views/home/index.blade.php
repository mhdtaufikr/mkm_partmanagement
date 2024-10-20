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

<!-- Card 2: Preventive Maintenance Chart -->
<div class="col-xl-6 col-md-6">
    <div class="card">
        <div style="color: white" class="card-header">
            Preventive Maintenance Yearly
        </div>
        <div class="card-body">
            <div id="pmChartDiv" style="width: 100%; height: 275px;"></div>
        </div>
    </div>
</div>

<script>
    am5.ready(function() {
        function createPMChart(plannedData, actualData, trendData, endDate, year) {
            var root = am5.Root.new("pmChartDiv");

            root.setThemes([am5themes_Animated.new(root)]);

            // Add Year Label at the top of the chart
            var yearLabel = root.container.children.push(am5.Label.new(root, {
                text: year, // Display the year
                fontSize: 20,
                fontWeight: "bold",
                x: am5.p50,
                centerX: am5.p50,
                y: -10 // Adjust position of the year label
            }));

            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: "none",
                wheelY: "none",
                layout: root.verticalLayout
            }));

            // Define month names for X-axis tooltip
            var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                              "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

            // X-axis - Months
            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "month",
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{category}", // Show month name in the tooltip
                }),
                renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 30 })
            }));

            // Set X-axis categories (Months)
            xAxis.data.setAll(Array.from({ length: endDate }, (_, i) => ({ month: monthNames[i] })));

            // Y-axis - Quantities
            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                min: 0,
                renderer: am5xy.AxisRendererY.new(root, { strokeOpacity: 0.1 })
            }));

            var yAxisRight = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                min: 0,
                max: 120,  // For percentage accuracy
                strictMinMax: true,
                renderer: am5xy.AxisRendererY.new(root, { opposite: true, strokeOpacity: 0.1 })
            }));

            // Y-axis labels
            yAxis.children.moveValue(am5.Label.new(root, {
                rotation: -90,
                text: "Quantity",
                y: am5.p50,
                centerX: am5.p50
            }), 0);

            yAxisRight.children.moveValue(am5.Label.new(root, {
                rotation: -90,
                text: "Percentage (%)",
                y: am5.p50,
                centerX: am5.p50
            }), 0);

            // Planned series (Bars)
            var plannedSeries = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: "Planned PM",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "planned",
                categoryXField: "month",
                clustered: true,
                tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}" })
            }));

            plannedSeries.columns.template.setAll({ fill: am5.color("#36A2EB"), width: am5.percent(80) });
            plannedSeries.data.setAll(plannedData.map((value, i) => ({ month: monthNames[i], planned: value || 0 })));

            // Actual series (Bars)
            var actualSeries = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: "Actual PM",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "actual",
                categoryXField: "month",
                clustered: true,
                tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}" })
            }));

            actualSeries.columns.template.setAll({ fill: am5.color("#FF9F40"), width: am5.percent(80) });
            actualSeries.data.setAll(actualData.map((value, i) => ({ month: monthNames[i], actual: value || 0 })));

            // Percentage Trend (Line)
            var percentageSeries = chart.series.push(am5xy.LineSeries.new(root, {
                name: "Percentage Accuracy",
                xAxis: xAxis,
                yAxis: yAxisRight,
                valueYField: "percentage",
                categoryXField: "month",
                tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}%" }),
                stroke: am5.color(0x000000),
                fill: am5.color(0x000000)
            }));

            percentageSeries.strokes.template.setAll({ strokeWidth: 3 });
            percentageSeries.data.setAll(trendData.map((value, i) => ({ month: monthNames[i], percentage: value || 0 })));

            percentageSeries.bullets.push(function(root, series, dataItem) {
                var value = dataItem.dataContext.percentage;
                var bulletColor = value < 100 ? am5.color(0xff0000) : am5.color(0x00ff00);
                return am5.Bullet.new(root, {
                    sprite: am5.Circle.new(root, {
                        strokeWidth: 3,
                        stroke: series.get("stroke"),
                        radius: 5,
                        fill: bulletColor
                    })
                });
            });

            // Standard Line (Dashed 100% Line)
            var standardLine = chart.series.push(am5xy.LineSeries.new(root, {
                name: "Standard (100%)",
                xAxis: xAxis,
                yAxis: yAxisRight,
                valueYField: "standard",
                categoryXField: "month",
                stroke: am5.color(0x00FF00),
                tooltip: am5.Tooltip.new(root, { labelText: "Standard: 100%" })
            }));

            standardLine.strokes.template.setAll({
                strokeWidth: 2,
                strokeDasharray: [5, 5],  // Dashed line
                stroke: am5.color(0x00FF00)  // Green color
            });

            // Set 100% line data
            var standardData = Array.from({ length: endDate }, (_, i) => ({ month: monthNames[i], standard: 100 }));
            standardLine.data.setAll(standardData);

            // Trend Line (Actual + Planned / 2) with Dotted Style
            var trendSeries = chart.series.push(am5xy.LineSeries.new(root, {
                name: "Trend Line",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "trend",
                categoryXField: "month",
                tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}" }),
                stroke: am5.color(0xFFA500)  // Orange color for the trend line
            }));

            // Make the trend line dotted
            trendSeries.strokes.template.setAll({
                strokeWidth: 3,
                strokeDasharray: [4, 4]  // Dotted line
            });

            var trendData = plannedData.map((planned, i) => {
                var actual = actualData[i] || 0;
                return { month: monthNames[i], trend: (planned + actual) / 2 };
            });
            trendSeries.data.setAll(trendData);

            // Add Legend
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }));

            legend.data.setAll(chart.series.values);

            // Add Cursor
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none",
                xAxis: xAxis
            }));

            cursor.lineY.set("visible", false);

            chart.appear(1000, 100);
            actualSeries.appear();
            plannedSeries.appear();
            percentageSeries.appear();
            trendSeries.appear();  // Add this line to show the trend series animation
            standardLine.appear();  // Add this line to show the standard line animation
        }

        // Assuming you have these data arrays available from the backend
        var plannedData = {!! json_encode($plannedData) !!};
        var actualData = {!! json_encode($actualData) !!};
        var trendData = {!! json_encode($trendData) !!};
        var endDate = 12;  // Assuming 12 months
        var currentYear = new Date().getFullYear();  // Get the current year

        // Create the chart
        createPMChart(plannedData, actualData, trendData, endDate, currentYear);
    });
</script>





                  <!-- Card 3: Pie Chart -->
                <div class="col-xl-6 col-md-6">
                    <div class="card">
                        <div style="color: white" class="card-header">
                        Daily Problem Summary Count
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
                                Preventive Maintenance Monthly
                            </div>
                            <div class="card-body">
                                <div id="pmRadarChartDiv" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    <script>
                        am5.ready(function() {
                            // Create the PM Chart using XY chart layout
                            function createPMChart(plannedData, actualData, percentageAccuracy, endDate) {
                                var root = am5.Root.new("pmRadarChartDiv");

                                root.setThemes([am5themes_Animated.new(root)]);

                                // Add month Label at the top of the chart
                                var currentMonthName = new Date().toLocaleString('default', { month: 'long' }); // Get current month name
                                var monthLabel = root.container.children.push(am5.Label.new(root, {
                                    text:   currentMonthName,
                                    fontSize: 20,
                                    fontWeight: "bold",
                                    x: am5.p50,
                                    centerX: am5.p50,
                                    y: -10
                                }));

                                var chart = root.container.children.push(am5xy.XYChart.new(root, {
                                    panX: false,
                                    panY: false,
                                    wheelX: "none",
                                    wheelY: "none",
                                    layout: root.verticalLayout
                                }));

                                var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                                    categoryField: "date",
                                    tooltip: am5.Tooltip.new(root, {}),
                                    renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 30 })
                                }));

                                xAxis.data.setAll(Array.from({ length: endDate }, (_, i) => ({ date: (i + 1).toString() })));

                                var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                                    min: 0,
                                    renderer: am5xy.AxisRendererY.new(root, { strokeOpacity: 0.1 })
                                }));

                                var yAxisRight = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                                    min: 0,
                                    max: 120,
                                    strictMinMax: true,
                                    renderer: am5xy.AxisRendererY.new(root, { opposite: true, strokeOpacity: 0.1 })
                                }));

                                // Add Y-axis label
                                yAxis.children.moveValue(am5.Label.new(root, {
                                    rotation: -90,
                                    text: "Quantity",
                                    y: am5.p50,
                                    centerX: am5.p50
                                }), 0);

                                // Add Y-axis label
                                yAxisRight.children.moveValue(am5.Label.new(root, {
                                    rotation: -90,
                                    text: "Percentage (%)",
                                    y: am5.p50,
                                    centerX: am5.p50
                                }), 0);

                                var planSeries = chart.series.push(am5xy.ColumnSeries.new(root, {
                                    name: "Planned Qty",
                                    xAxis: xAxis,
                                    yAxis: yAxis,
                                    valueYField: "plan",
                                    categoryXField: "date",
                                    clustered: true,
                                    tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}" })
                                }));

                                planSeries.columns.template.setAll({ fill: am5.color("#36A2EB"), width: am5.percent(80) });
                                planSeries.data.setAll(plannedData.slice(0, endDate).map((value, i) => ({ date: (i + 1).toString(), plan: value || 0 })));

                                var actualSeries = chart.series.push(am5xy.ColumnSeries.new(root, {
                                    name: "Actual Qty",
                                    xAxis: xAxis,
                                    yAxis: yAxis,
                                    valueYField: "actual",
                                    categoryXField: "date",
                                    clustered: true,
                                    tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}" })
                                }));

                                actualSeries.columns.template.setAll({ fill: am5.color("#FF9F40"), width: am5.percent(80) });
                                actualSeries.data.setAll(actualData.slice(0, endDate).map((value, i) => ({ date: (i + 1).toString(), actual: value || 0 })));

                                var percentageSeries = chart.series.push(am5xy.LineSeries.new(root, {
                                    name: "Percentage Accuracy",
                                    xAxis: xAxis,
                                    yAxis: yAxisRight,
                                    valueYField: "percentage",
                                    categoryXField: "date",
                                    tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}%" }),
                                    stroke: am5.color(0x000000),
                                    fill: am5.color(0x000000)
                                }));

                                percentageSeries.strokes.template.setAll({ strokeWidth: 3 });
                                percentageSeries.data.setAll(percentageAccuracy.slice(0, endDate).map((value, i) => ({ date: (i + 1).toString(), percentage: value || 0 })));

                                percentageSeries.bullets.push(function(root, series, dataItem) {
                                    var value = dataItem.dataContext.percentage;
                                    var bulletColor = value < 100 ? am5.color(0xff0000) : am5.color(0x00ff00);
                                    return am5.Bullet.new(root, {
                                        sprite: am5.Circle.new(root, {
                                            strokeWidth: 3,
                                            stroke: series.get("stroke"),
                                            radius: 5,
                                            fill: bulletColor
                                        })
                                    });
                                });

                                // Add the standard 100% line (dotted)
                                var standardLine = chart.series.push(am5xy.LineSeries.new(root, {
                                    name: "Standard (100%)",
                                    xAxis: xAxis,
                                    yAxis: yAxisRight,
                                    valueYField: "standard",
                                    categoryXField: "date",
                                    stroke: am5.color(0x00FF00),  // Green color for the standard line
                                    tooltip: am5.Tooltip.new(root, { labelText: "Standard: 100%" })
                                }));

                                // Make the standard line dotted
                                standardLine.strokes.template.setAll({
                                    strokeWidth: 2,
                                    strokeDasharray: [5, 5]  // Dotted line
                                });

                                // Set 100% line data
                                var standardData = Array.from({ length: endDate }, (_, i) => ({ date: (i + 1).toString(), standard: 100 }));
                                standardLine.data.setAll(standardData);

                                // Add the trend line: (Actual + Planned) / 2
                                var trendSeries = chart.series.push(am5xy.LineSeries.new(root, {
                                    name: "Trend Line",
                                    xAxis: xAxis,
                                    yAxis: yAxis,
                                    valueYField: "trend",
                                    categoryXField: "date",
                                    stroke: am5.color(0xFFA500),  // Orange color for the trendline
                                    tooltip: am5.Tooltip.new(root, { labelText: "{name}: {valueY}" })
                                }));

                                // Make the trendline dotted
                                trendSeries.strokes.template.setAll({
                                    strokeWidth: 3,
                                    strokeDasharray: [4, 4]  // Dotted line
                                });

                                // Calculate and set trend data (average of actual and planned)
                                var trendData = plannedData.map((planned, i) => {
                                    var actual = actualData[i] || 0;
                                    return { date: (i + 1).toString(), trend: (planned + actual) / 2 };
                                });
                                trendSeries.data.setAll(trendData);

                                var legend = chart.children.push(am5.Legend.new(root, {
                                    centerX: am5.p50,
                                    x: am5.p50
                                }));

                                legend.data.setAll(chart.series.values);

                                var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                                    behavior: "none",
                                    xAxis: xAxis
                                }));

                                cursor.lineY.set("visible", false);

                                chart.appear(1000, 100);
                                actualSeries.appear();
                                planSeries.appear();
                                percentageSeries.appear();
                                trendSeries.appear();  // Add trend series animation
                                standardLine.appear();  // Ensure the standard line is visible with animation
                            }

                            // Fetch the data passed from the controller
                            const plannedDataByDay = @json($plannedDataByDay);
                            const actualDataByDay = @json($actualDataByDay);
                            const trendDataByDay = @json($trendDataByDay);

                            // Create the chart
                            createPMChart(plannedDataByDay, actualDataByDay, trendDataByDay, 31);
                        });
                    </script>


                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <div id="lblGreetings"></div>

    <script>
        // Get current date and time
        var myDate = new Date();
        var hrs = myDate.getHours();

        var greet;

        if (hrs < 12)
            greet = 'Good Morning';
        else if (hrs >= 12 && hrs <= 17)
            greet = 'Good Afternoon';
        else if (hrs >= 17 && hrs < 24)
            greet = 'Good Evening';

        // Access the variables passed from the controller
        var plant = @json($plant).toUpperCase(); // Convert plant name to uppercase
        var shopTypes = @json($shopTypesCurrentMonth); // This will be an array of shop types

        // Create a string to display the shop types and convert to uppercase
        var shopTypesString = shopTypes.join(', ').toUpperCase(); // Join shop types into a string and convert to uppercase

        // Update the greeting message
        document.getElementById('lblGreetings').innerHTML =
            '<b>' + greet + '</b>, welcome to DigiMAMS || Plant : <b>' + plant + '</b>, Type:  <b>' + shopTypesString + '</b>';
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
