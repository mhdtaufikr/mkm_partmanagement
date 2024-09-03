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

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>

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
                                Bar Chart Example
                            </div>
                            <div class="card-body">
                                <div id="chartdiv" style="height: 400px;"></div>
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
                                <div id="chartdiv2" style="height: 400px;"></div>
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
                                <div id="chartdiv3" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Radar Chart -->
                    <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div style="color: white" class="card-header">
                                Radar Chart Example
                            </div>
                            <div class="card-body">
                                <div id="chartdiv4" style="height: 400px;"></div>
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
            '<b>' + greet + '</b> and welcome to MKM Part Management';
    </script>

<!-- Chart code -->
<script>
    am5.ready(function() {

    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartdiv");

    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: true,
      panY: false,
      wheelX: "panX",
      wheelY: "zoomX",
      paddingLeft: 0,
      layout: root.verticalLayout
    }));

    // Add scrollbar
    // https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
    chart.set("scrollbarX", am5.Scrollbar.new(root, {
      orientation: "horizontal"
    }));

    var data = [{
      "country": "USA",
      "year2004": 3.5,
      "year2005": 4.2
    }, {
      "country": "UK",
      "year2004": 1.7,
      "year2005": 3.1
    }, {
      "country": "Canada",
      "year2004": 2.8,
      "year2005": 2.9
    }, {
      "country": "Japan",
      "year2004": 2.6,
      "year2005": 2.3
    }, {
      "country": "France",
      "year2004": 1.4,
      "year2005": 2.1
    }, {
      "country": "Brazil",
      "year2004": 2.6,
      "year2005": 4.9
    }];

    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    var xRenderer = am5xy.AxisRendererX.new(root, {
      minGridDistance: 70,
      minorGridEnabled: true
    });

    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      categoryField: "country",
      renderer: xRenderer,
      tooltip: am5.Tooltip.new(root, {
        themeTags: ["axis"],
        animationDuration: 200
      })
    }));

    xRenderer.grid.template.setAll({
      location: 1
    })

    xAxis.data.setAll(data);

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      min: 0,
      renderer: am5xy.AxisRendererY.new(root, {
        strokeOpacity: 0.1
      })
    }));

    // Add series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/

    var series0 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2004",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "2004: {valueY}"
      })
    }));

    series0.columns.template.setAll({
      width: am5.percent(80),
      tooltipY: 0,
      strokeOpacity: 0
    });


    series0.data.setAll(data);


    var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2005",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "2005: {valueY}"
      })
    }));

    series1.columns.template.setAll({
      width: am5.percent(50),
      tooltipY: 0,
      strokeOpacity: 0
    });

    series1.data.setAll(data);

    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));


    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    chart.appear(1000, 100);
    series0.appear();
    series1.appear();

    }); // end am5.ready()
</script>

<script>
    am5.ready(function() {

    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartdiv3");

    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    // Create chart
    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
    var chart = root.container.children.push(am5percent.PieChart.new(root, {
      radius: am5.percent(90),
      innerRadius: am5.percent(50),
      layout: root.horizontalLayout
    }));

    // Create series
    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
    var series = chart.series.push(am5percent.PieSeries.new(root, {
      name: "Series",
      valueField: "sales",
      categoryField: "country"
    }));

    // Set data
    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
    series.data.setAll([{
      country: "Lithuania",
      sales: 501.9
    }, {
      country: "Czechia",
      sales: 301.9
    }, {
      country: "Ireland",
      sales: 201.1
    }, {
      country: "Germany",
      sales: 165.8
    }, {
      country: "Australia",
      sales: 139.9
    }, {
      country: "Austria",
      sales: 128.3
    }, {
      country: "UK",
      sales: 99
    }, {
      country: "Belgium",
      sales: 60
    }, {
      country: "The Netherlands",
      sales: 50
    }]);

    // Disabling labels and ticks
    series.labels.template.set("visible", false);
    series.ticks.template.set("visible", false);

    // Adding gradients
    series.slices.template.set("strokeOpacity", 0);
    series.slices.template.set("fillGradient", am5.RadialGradient.new(root, {
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

    // Create legend
    // https://www.amcharts.com/docs/v5/charts/percent-charts/legend-percent-series/
    var legend = chart.children.push(am5.Legend.new(root, {
      centerY: am5.percent(50),
      y: am5.percent(50),
      layout: root.verticalLayout
    }));
    // set value labels align to right
    legend.valueLabels.template.setAll({ textAlign: "right" })
    // set width and max width of labels
    legend.labels.template.setAll({
      maxWidth: 140,
      width: 140,
      oversizedBehavior: "wrap"
    });

    legend.data.setAll(series.dataItems);


    // Play initial series animation
    // https://www.amcharts.com/docs/v5/concepts/animations/#Animation_of_series
    series.appear(1000, 100);

    }); // end am5.ready()
    </script>

<script>
    am5.ready(function() {

    var data = [{
      "date": "2012-01-01",
      "distance": 227,
      "townName": "New York",
      "townSize": 12,
      "latitude": 40.71,
      "duration": 408
    }, {
      "date": "2012-01-02",
      "distance": 371,
      "townName": "Washington",
      "townSize": 7,
      "latitude": 38.89,
      "duration": 482
    }, {
      "date": "2012-01-03",
      "distance": 433,
      "townName": "Wilmington",
      "townSize": 3,
      "latitude": 34.22,
      "duration": 562
    }, {
      "date": "2012-01-04",
      "distance": 345,
      "townName": "Jacksonville",
      "townSize": 3.5,
      "latitude": 30.35,
      "duration": 379
    }, {
      "date": "2012-01-05",
      "distance": 480,
      "townName": "Miami",
      "townSize": 5,
      "latitude": 25.83,
      "duration": 501
    }, {
      "date": "2012-01-06",
      "distance": 386,
      "townName": "Tallahassee",
      "townSize": 3.5,
      "latitude": 30.46,
      "duration": 443
    }, {
      "date": "2012-01-07",
      "distance": 348,
      "townName": "New Orleans",
      "townSize": 5,
      "latitude": 29.94,
      "duration": 405
    }, {
      "date": "2012-01-08",
      "distance": 238,
      "townName": "Houston",
      "townSize": 8,
      "latitude": 29.76,
      "duration": 309
    }, {
      "date": "2012-01-09",
      "distance": 218,
      "townName": "Dalas",
      "townSize": 8,
      "latitude": 32.8,
      "duration": 287
    }, {
      "date": "2012-01-10",
      "distance": 349,
      "townName": "Oklahoma City",
      "townSize": 5,
      "latitude": 35.49,
      "duration": 485
    }, {
      "date": "2012-01-11",
      "distance": 603,
      "townName": "Kansas City",
      "townSize": 5,
      "latitude": 39.1,
      "duration": 890
    }, {
      "date": "2012-01-12",
      "distance": 534,
      "townName": "Denver",
      "townSize": 9,
      "latitude": 39.74,
      "duration": 810
    }, {
      "date": "2012-01-13",
      "townName": "Salt Lake City",
      "townSize": 6,
      "distance": 425,
      "duration": 670,
      "latitude": 40.75,
      "dashLength": 8,
      "alpha": 0.4
    }, {
      "date": "2012-01-14",
      "latitude": 36.1,
      "duration": 470,
      "townName": "Las Vegas"
    }, {
      "date": "2012-01-15"
    }, {
      "date": "2012-01-16"
    }, {
      "date": "2012-01-17"
    }];

    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartdiv2");

    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
      am5themes_Animated.new(root)
    ]);


    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: false,
      panY: false,
      wheelY: "none"
    }));

    chart.zoomOutButton.set("forceHidden", true);

    chart.get("colors").set("step", 2);

    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
      baseInterval: { timeUnit: "day", count: 1 },
      renderer: am5xy.AxisRendererX.new(root, {
        minGridDistance: 70,
        minorGridEnabled: true
      }),
      tooltip: am5.Tooltip.new(root, {})
    }));


    var distanceAxisRenderer = am5xy.AxisRendererY.new(root, {});
    distanceAxisRenderer.grid.template.set("forceHidden", true);
    var distanceAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      renderer: distanceAxisRenderer,
      tooltip: am5.Tooltip.new(root, {})
    }));

    var latitudeAxisRenderer = am5xy.AxisRendererY.new(root, {});
    latitudeAxisRenderer.grid.template.set("forceHidden", true);
    var latitudeAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      renderer: latitudeAxisRenderer,
      forceHidden: true
    }));

    var durationAxisRenderer = am5xy.AxisRendererY.new(root, {
      opposite: true
    });
    durationAxisRenderer.grid.template.set("forceHidden", true);
    var durationAxis = chart.yAxes.push(am5xy.DurationAxis.new(root, {
      baseUnit:"minute",
      renderer: durationAxisRenderer,
      extraMax:0.3
    }));

    // Create series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    var distanceSeries = chart.series.push(am5xy.ColumnSeries.new(root, {
      xAxis: xAxis,
      yAxis: distanceAxis,
      valueYField: "distance",
      valueXField: "date",
      tooltip:am5.Tooltip.new(root, {
        labelText:"{valueY} miles"
      })
    }));

    distanceSeries.data.processor = am5.DataProcessor.new(root, {
      dateFields: ["date"],
      dateFormat: "yyyy-MM-dd"
    });

    var latitudeSeries = chart.series.push(am5xy.LineSeries.new(root, {
      xAxis: xAxis,
      yAxis: latitudeAxis,
      valueYField: "latitude",
      valueXField: "date",
      tooltip:am5.Tooltip.new(root, {
        labelText:"latitude: {valueY} ({townName})"
      })
    }));

    latitudeSeries.strokes.template.setAll({ strokeWidth: 2 });

    // Add circle bullet
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/#Bullets
    latitudeSeries.bullets.push(function() {
      var graphics = am5.Circle.new(root, {
        strokeWidth: 2,
        radius: 5,
        stroke: latitudeSeries.get("stroke"),
        fill: root.interfaceColors.get("background"),
      });

      graphics.adapters.add("radius", function(radius, target) {
        return target.dataItem.dataContext.townSize;
      })

      return am5.Bullet.new(root, {
        sprite: graphics
      });
    });

    var durationSeries = chart.series.push(am5xy.LineSeries.new(root, {
      xAxis: xAxis,
      yAxis: durationAxis,
      valueYField: "duration",
      valueXField: "date",
      tooltip:am5.Tooltip.new(root, {
        labelText:"duration: {valueY.formatDuration()}"
      })
    }));

    durationSeries.strokes.template.setAll({ strokeWidth: 2 });

    // Add circle bullet
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/#Bullets
    durationSeries.bullets.push(function() {
      var graphics = am5.Rectangle.new(root, {
        width:10,
        height:10,
        centerX:am5.p50,
        centerY:am5.p50,
        fill: durationSeries.get("stroke")
      });

      return am5.Bullet.new(root, {
        sprite: graphics
      });
    });

    // Add cursor
    // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
    chart.set("cursor", am5xy.XYCursor.new(root, {
      xAxis: xAxis,
      yAxis: distanceAxis
    }));


    distanceSeries.data.setAll(data);
    latitudeSeries.data.setAll(data);
    durationSeries.data.setAll(data);
    xAxis.data.setAll(data);

    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    distanceSeries.appear(1000);
    chart.appear(1000, 100);

    }); // end am5.ready()
    </script>

    <!-- Chart code -->
<script>
    am5.ready(function() {

    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartdiv4");

    // Create custom theme
    // https://www.amcharts.com/docs/v5/concepts/themes/#Quick_custom_theme
    const myTheme = am5.Theme.new(root);
    myTheme.rule("Label").set("fontSize", 10);
    myTheme.rule("Grid").set("strokeOpacity", 0.06);

    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([am5themes_Animated.new(root), myTheme]);

    // tell that valueX should be formatted as a date (show week number)
    root.dateFormatter.setAll({
      dateFormat: "w",
      dateFields: ["valueX"]
    });

    root.locale.firstDayOfWeek = 0;

    // data
    var data = [
      {
        "Activity Date": "2019-04-07",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 16901.30078125,
        "Moving Time": 4731
      },
      {
        "Activity Date": "2019-04-08",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 10051.400390625,
        "Moving Time": 2123
      },
      {
        "Activity Date": "2019-04-25",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 31012,
        "Moving Time": 7902
      },
      {
        "Activity Date": "2019-04-30",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 8279,
        "Moving Time": 2401
      },
      {
        "Activity Date": "2019-05-01",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 65781,
        "Moving Time": 11690
      },
      {
        "Activity Date": "2019-05-09",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 18331.599609375,
        "Moving Time": 4706
      },
      {
        "Activity Date": "2019-05-05",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 23213,
        "Moving Time": 9471
      },
      {
        "Activity Date": "2019-05-10",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 55106,
        "Moving Time": 12755
      },
      {
        "Activity Date": "2019-05-11",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 67423,
        "Moving Time": 15667
      },
      {
        "Activity Date": "2019-05-12",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 31127,
        "Moving Time": 6157
      },
      {
        "Activity Date": "2019-05-12",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 16067,
        "Moving Time": 4087
      },
      {
        "Activity Date": "2019-05-14",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 38208,
        "Moving Time": 8931
      },
      {
        "Activity Date": "2019-05-15",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 115606,
        "Moving Time": 26471
      },
      {
        "Activity Date": "2019-05-16",
        "Activity Name": "Palma de Mallorca day 3",
        "Activity Type": "Ride",
        "Distance": 110470,
        "Moving Time": 22967
      },
      {
        "Activity Date": "2019-05-17",
        "Activity Name": "Sa Colabra epic ride",
        "Activity Type": "Ride",
        "Distance": 67143,
        "Moving Time": 18009
      },
      {
        "Activity Date": "2019-05-18",
        "Activity Name": "Mallorka last day",
        "Activity Type": "Ride",
        "Distance": 87590,
        "Moving Time": 18553
      },
      {
        "Activity Date": "2019-05-24",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 21088,
        "Moving Time": 2555
      },
      {
        "Activity Date": "2019-05-25",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 53361,
        "Moving Time": 8473
      },
      {
        "Activity Date": "2019-05-26",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 13463.7001953125,
        "Moving Time": 3768
      },
      {
        "Activity Date": "2019-05-26",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 14177.2001953125,
        "Moving Time": 3642
      },
      {
        "Activity Date": "2019-06-01",
        "Activity Name": "3.5 karto Juodkrantė - Klaipėda",
        "Activity Type": "Ride",
        "Distance": 75997,
        "Moving Time": 14452
      },
      {
        "Activity Date": "2019-06-27",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 44062,
        "Moving Time": 6016
      },
      {
        "Activity Date": "2019-06-30",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 8756,
        "Moving Time": 3242
      },
      {
        "Activity Date": "2019-07-01",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 27867,
        "Moving Time": 6479
      },
      {
        "Activity Date": "2019-07-02",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 21775,
        "Moving Time": 5256
      },
      {
        "Activity Date": "2019-07-02",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 7343,
        "Moving Time": 2064
      },
      {
        "Activity Date": "2019-07-03",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 26956,
        "Moving Time": 6879
      },
      {
        "Activity Date": "2019-07-04",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 14175,
        "Moving Time": 3617
      },
      {
        "Activity Date": "2019-07-07",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 45489,
        "Moving Time": 11656
      },
      {
        "Activity Date": "2019-07-09",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 10049,
        "Moving Time": 1767
      },
      {
        "Activity Date": "2019-07-10",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 10000,
        "Moving Time": 1805
      },
      {
        "Activity Date": "2019-07-13",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 11603,
        "Moving Time": 3127
      },
      {
        "Activity Date": "2019-07-14",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 8701,
        "Moving Time": 2369
      },
      {
        "Activity Date": "2019-07-15",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 13021,
        "Moving Time": 2728
      },
      {
        "Activity Date": "2019-07-16",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 10094,
        "Moving Time": 1823
      },
      {
        "Activity Date": "2019-07-17",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 10075,
        "Moving Time": 1783
      },
      {
        "Activity Date": "2019-07-18",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 10170,
        "Moving Time": 2006
      },
      {
        "Activity Date": "2019-07-19",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 13796,
        "Moving Time": 2487
      },
      {
        "Activity Date": "2019-07-21",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 9837,
        "Moving Time": 1761
      },
      {
        "Activity Date": "2019-07-23",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 20292,
        "Moving Time": 4581
      },
      {
        "Activity Date": "2019-07-24",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 43681,
        "Moving Time": 12542
      },
      {
        "Activity Date": "2019-07-27",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 21879,
        "Moving Time": 3556
      },
      {
        "Activity Date": "2019-07-26",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 42881,
        "Moving Time": 7302
      },
      {
        "Activity Date": "2019-08-13",
        "Activity Name": "Evening Ride",
        "Activity Type": "Ride",
        "Distance": 11756.5,
        "Moving Time": 2433
      },
      {
        "Activity Date": "2019-08-26",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 5596,
        "Moving Time": 1505
      },
      {
        "Activity Date": "2019-07-25",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 10639.2001953125,
        "Moving Time": 2615
      },
      {
        "Activity Date": "2019-07-26",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 41150.6015625,
        "Moving Time": 6795
      },
      {
        "Activity Date": "2019-07-27",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 43459.80078125,
        "Moving Time": 6986
      },
      {
        "Activity Date": "2019-08-26",
        "Activity Name": "Norvegija su Jurgiu!",
        "Activity Type": "Ride",
        "Distance": 83720,
        "Moving Time": 21811
      },
      {
        "Activity Date": "2019-08-27",
        "Activity Name": "Norvegija su Jurgiu! Day 2",
        "Activity Type": "Ride",
        "Distance": 27739.400390625,
        "Moving Time": 8280
      },
      {
        "Activity Date": "2019-08-28",
        "Activity Name": "Norvegija su Jurgiu! day 3",
        "Activity Type": "Ride",
        "Distance": 25866.599609375,
        "Moving Time": 6333
      },
      {
        "Activity Date": "2019-09-11",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 4512.2998046875,
        "Moving Time": 1250
      },
      {
        "Activity Date": "2019-09-12",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 8641.400390625,
        "Moving Time": 3404
      },
      {
        "Activity Date": "2019-09-15",
        "Activity Name": "Nuo Pisos iki Florencijos",
        "Activity Type": "Ride",
        "Distance": 103813.6015625,
        "Moving Time": 23376
      },
      {
        "Activity Date": "2019-09-16",
        "Activity Name": "Toskana, antra diena",
        "Activity Type": "Ride",
        "Distance": 55542.6015625,
        "Moving Time": 15264
      },
      {
        "Activity Date": "2019-09-17",
        "Activity Name": "Toskana, 3 diena",
        "Activity Type": "Ride",
        "Distance": 70001.3984375,
        "Moving Time": 15377
      },
      {
        "Activity Date": "2019-09-18",
        "Activity Name": "Toskana, 4 diena",
        "Activity Type": "Ride",
        "Distance": 82216.703125,
        "Moving Time": 18648
      },
      {
        "Activity Date": "2019-09-19",
        "Activity Name": "Toskana, 5 diena",
        "Activity Type": "Ride",
        "Distance": 82086.203125,
        "Moving Time": 20213
      },
      {
        "Activity Date": "2019-09-20",
        "Activity Name": "Toskana, 6 diena, važiuojam namo.",
        "Activity Type": "Ride",
        "Distance": 61489.8984375,
        "Moving Time": 11320
      },
      {
        "Activity Date": "2019-09-27",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 4236.2001953125,
        "Moving Time": 1030
      },
      {
        "Activity Date": "2019-09-27",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 4303.60009765625,
        "Moving Time": 1142
      },
      {
        "Activity Date": "2019-10-13",
        "Activity Name": "Lunch Ride",
        "Activity Type": "Ride",
        "Distance": 14578,
        "Moving Time": 3591
      },
      {
        "Activity Date": "2019-10-01",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 7996.2998046875,
        "Moving Time": 2219
      },
      {
        "Activity Date": "2019-10-02",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 4265.2998046875,
        "Moving Time": 1131
      },
      {
        "Activity Date": "2019-10-02",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 4353.10009765625,
        "Moving Time": 1219
      },
      {
        "Activity Date": "2019-10-03",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 17238.80078125,
        "Moving Time": 4641
      },
      {
        "Activity Date": "2019-10-04",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 4259.7998046875,
        "Moving Time": 1054
      },
      {
        "Activity Date": "2019-10-16",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 14651.5,
        "Moving Time": 3184
      },
      {
        "Activity Date": "2019-10-18",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 4194,
        "Moving Time": 1029
      },
      {
        "Activity Date": "2019-10-22",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 4102.7998046875,
        "Moving Time": 1063
      },
      {
        "Activity Date": "2019-11-04",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 8456.2998046875,
        "Moving Time": 2157
      },
      {
        "Activity Date": "2019-11-05",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 8816.400390625,
        "Moving Time": 2353
      },
      {
        "Activity Date": "2019-11-06",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 8090.7001953125,
        "Moving Time": 1911
      },
      {
        "Activity Date": "2019-11-07",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 1382.699951171875,
        "Moving Time": 336
      },
      {
        "Activity Date": "2019-11-08",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 4856.2001953125,
        "Moving Time": 1351
      },
      {
        "Activity Date": "2019-11-12",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 5141.10009765625,
        "Moving Time": 1526
      },
      {
        "Activity Date": "2019-11-13",
        "Activity Name": "Afternoon Ride",
        "Activity Type": "Ride",
        "Distance": 4582.60009765625,
        "Moving Time": 1237
      },
      {
        "Activity Date": "2019-11-14",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 15022,
        "Moving Time": 3742
      },
      {
        "Activity Date": "2019-09-16",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 57270.3984375,
        "Moving Time": 14393
      },
      {
        "Activity Date": "2019-09-20",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 66988.1015625,
        "Moving Time": 12096
      },
      {
        "Activity Date": "2019-09-15",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 103671.1015625,
        "Moving Time": 22042
      },
      {
        "Activity Date": "2019-09-19",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 81357.5,
        "Moving Time": 18880
      },
      {
        "Activity Date": "2019-09-17",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 74034.796875,
        "Moving Time": 16013
      },
      {
        "Activity Date": "2019-09-18",
        "Activity Name": "Morning Ride",
        "Activity Type": "Ride",
        "Distance": 82354.3984375,
        "Moving Time": 16583
      },
      {
        "Activity Date": "2019-11-20",
        "Activity Name": "Taiwan, day 1",
        "Activity Type": "Ride",
        "Distance": 94371.203125,
        "Moving Time": 18130
      },
      {
        "Activity Date": "2019-11-21",
        "Activity Name": "Taiwan, day 2, Sun Moon lake",
        "Activity Type": "Ride",
        "Distance": 115457.203125,
        "Moving Time": 21181
      },
      {
        "Activity Date": "2019-11-22",
        "Activity Name": "Taiwan day 3",
        "Activity Type": "Ride",
        "Distance": 80677.8984375,
        "Moving Time": 12403
      },
      {
        "Activity Date": "2019-11-23",
        "Activity Name": "Taiwan day 4",
        "Activity Type": "Ride",
        "Distance": 121866.796875,
        "Moving Time": 26665
      },
      {
        "Activity Date": "2019-11-24",
        "Activity Name": "Taiwan day 5",
        "Activity Type": "Ride",
        "Distance": 107690.703125,
        "Moving Time": 23386
      },
      {
        "Activity Date": "2019-11-25",
        "Activity Name": "Taiwan day 6",
        "Activity Type": "Ride",
        "Distance": 90308.203125,
        "Moving Time": 18331
      }
    ];


    var weeklyData = [];
    var dailyData = [];

    var firstDay = am5.time.round(new Date(data[0]["Activity Date"]), "year", 1);
    var total = 0;
    var dateFormatter = am5.DateFormatter.new(root, {});
    var weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    var weekAxisData = [
      { day: "Sun" },
      { day: "Mon" },
      { day: "Tue" },
      { day: "Wed" },
      { day: "Thu" },
      { day: "Fri" },
      { day: "Sat" }
    ];

    var colorSet = am5.ColorSet.new(root, {});

    // PREPARE DATA
    function prepareDistanceData(data) {
      for (var i = 0; i < 53; i++) {
        weeklyData[i] = {};
        weeklyData[i].distance = 0;
        var date = new Date(firstDay);
        date.setDate(i * 7);
        am5.time.round(date, "week", 1);
        var endDate = am5.time.round(new Date(date), "week", 1);

        weeklyData[i].date = date.getTime();
        weeklyData[i].endDate = endDate.getTime();
      }

      am5.array.each(data, function (di) {
        var date = new Date(di["Activity Date"]);
        var weekDay = date.getDay();
        var weekNumber = am5.utils.getWeek(date);

        if (weekNumber == 1 && date.getMonth() == 11) {
          weekNumber = 53;
        }

        var distance = am5.math.round(di["Distance"] / 1000, 1);

        weeklyData[weekNumber - 1].distance += distance;
        weeklyData[weekNumber - 1].distance = am5.math.round(
          weeklyData[weekNumber - 1].distance,
          1
        );
        total += distance;

        dailyData.push({
          date: date.getTime(),
          day: weekdays[weekDay],
          "Distance": distance,
          title: di["Activity Name"]
        });
      });
    }

    prepareDistanceData(data);

    var div = document.getElementById("chartdiv");

    // Create chart
    // https://www.amcharts.com/docs/v5/charts/radar-chart/
    var chart = root.container.children.push(
      am5radar.RadarChart.new(root, {
        panX: false,
        panY: false,
        wheelX: "panX",
        wheelY: "zoomX",
        innerRadius: am5.percent(20),
        radius: am5.percent(85),
        startAngle: 270 - 170,
        endAngle: 270 + 170
      })
    );

    // add label in the center
    chart.radarContainer.children.push(
      am5.Label.new(root, {
        text:
          "[fontSize:0.8em]In 2019 I cycled:[/]\n[fontSize:1.5em]" +
          Math.round(total) +
          " km[/]",
        textAlign: "center",
        centerX: am5.percent(50),
        centerY: am5.percent(50)
      })
    );

    // Add cursor
    // https://www.amcharts.com/docs/v5/charts/radar-chart/#Cursor
    var cursor = chart.set(
      "cursor",
      am5radar.RadarCursor.new(root, {
        behavior: "zoomX"
      })
    );
    cursor.lineY.set("visible", false);

    // Create axes and their renderers
    // https://www.amcharts.com/docs/v5/charts/radar-chart/#Adding_axes

    // date axis
    var dateAxisRenderer = am5radar.AxisRendererCircular.new(root, {
      minGridDistance: 20
    });

    dateAxisRenderer.labels.template.setAll({
      radius: 30,
      textType: "radial",
      centerY: am5.p50
    });

    var dateAxis = chart.xAxes.push(
      am5xy.DateAxis.new(root, {
        baseInterval: { timeUnit: "week", count: 1 },
        renderer: dateAxisRenderer,
        min: new Date(2019, 0, 1, 0, 0, 0).getTime(),
        max: new Date(2020, 0, 1, 0, 0, 0).getTime()
      })
    );

    // distance axis
    var distanceAxisRenderer = am5radar.AxisRendererRadial.new(root, {
      axisAngle: 90,
      radius: am5.percent(60),
      innerRadius: am5.percent(20),
      inversed: true,
      minGridDistance: 20
    });

    distanceAxisRenderer.labels.template.setAll({
      centerX: am5.p50,
      minPosition: 0.05,
      maxPosition: 0.95
    });

    var distanceAxis = chart.yAxes.push(
      am5xy.ValueAxis.new(root, {
        renderer: distanceAxisRenderer
      })
    );

    distanceAxis.set("numberFormat", "# ' km'");

    // week axis
    var weekAxisRenderer = am5radar.AxisRendererRadial.new(root, {
      axisAngle: 90,
      innerRadius: am5.percent(60),
      radius: am5.percent(100),
      minGridDistance: 20
    });

    weekAxisRenderer.labels.template.setAll({
      centerX: am5.p50
    });

    var weekAxis = chart.yAxes.push(
      am5xy.CategoryAxis.new(root, {
        categoryField: "day",
        renderer: weekAxisRenderer
      })
    );

    // Create series
    // https://www.amcharts.com/docs/v5/charts/radar-chart/#Adding_series
    var distanceSeries = chart.series.push(
      am5radar.RadarColumnSeries.new(root, {
        calculateAggregates: true,
        xAxis: dateAxis,
        yAxis: distanceAxis,
        valueYField: "distance",
        valueXField: "date",
        tooltip: am5.Tooltip.new(root, {
          labelText: "week {valueX}: {valueY}"
        })
      })
    );

    distanceSeries.columns.template.set("strokeOpacity", 0);

    // Set up heat rules
    // https://www.amcharts.com/docs/v5/concepts/settings/heat-rules/
    distanceSeries.set("heatRules", [{
      target: distanceSeries.columns.template,
      key: "fill",
      min: am5.color(0x673ab7),
      max: am5.color(0xf44336),
      dataField: "valueY"
    }]);

    // bubble series is a line series with stroeks hiddden
    // https://www.amcharts.com/docs/v5/charts/radar-chart/#Adding_series
    var bubbleSeries = chart.series.push(
      am5radar.RadarLineSeries.new(root, {
        calculateAggregates: true,
        xAxis: dateAxis,
        yAxis: weekAxis,
        baseAxis: dateAxis,
        categoryYField: "day",
        valueXField: "date",
        valueField: "Distance",
        maskBullets: false
      })
    );

    // only bullets are visible, hide stroke
    bubbleSeries.strokes.template.set("forceHidden", true);

    // add bullet
    var circleTemplate = am5.Template.new({});
    bubbleSeries.bullets.push(function () {
      var graphics = am5.Circle.new(root, {
        fill: distanceSeries.get("fill"),
        tooltipText: "{title}: {value} km"
      }, circleTemplate);
      return am5.Bullet.new(root, {
        sprite: graphics
      });
    });

    // Add heat rule (makes bubbles to be of a various size, depending on a value)
    // https://www.amcharts.com/docs/v5/concepts/settings/heat-rules/
    bubbleSeries.set("heatRules", [{
      target: circleTemplate,
      min: 3,
      max: 15,
      dataField: "value",
      key: "radius"
    }]);

    // set data
    // https://www.amcharts.com/docs/v5/charts/radar-chart/#Setting_data

    distanceSeries.data.setAll(weeklyData);
    weekAxis.data.setAll(weekAxisData);
    bubbleSeries.data.setAll(dailyData);

    bubbleSeries.appear(1000);
    distanceSeries.appear(1000);
    chart.appear(1000, 100);

    // create axis ranges
    var months = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec"
    ];
    for (var i = 0; i < 12; i++) {
      createRange(months[i], i);
    }

    function createRange(name, index) {
      var axisRange = dateAxis.createAxisRange(
        dateAxis.makeDataItem({ above: true })
      );
      axisRange.get("label").setAll({ text: name });

      var fromTime = new Date(firstDay.getFullYear(), i, 1, 0, 0, 0).getTime();
      var toTime = am5.time.add(new Date(fromTime), "month", 1).getTime();

      axisRange.set("value", fromTime);
      axisRange.set("endValue", toTime);

      // every 2nd color for a bigger contrast
      var fill = axisRange.get("axisFill");
      fill.setAll({
        toggleKey: "active",
        cursorOverStyle: "pointer",
        fill: colorSet.getIndex(index * 2),
        visible: true,
        dRadius: 25,
        innerRadius: -25
      });
      axisRange.get("grid").set("visible", false);

      var label = axisRange.get("label");
      label.setAll({
        fill: am5.color(0xffffff),
        textType: "circular",
        radius: 8,
        text: months[index]
      });

      // clicking on a range zooms in
      fill.events.on("click", function (event) {
        var dataItem = event.target.dataItem;
        if (event.target.get("active")) {
          dateAxis.zoom(0, 1);
        } else {
          dateAxis.zoomToValues(dataItem.get("value"), dataItem.get("endValue"));
        }
      });
    }

    }); // end am5.ready()
    </script>

</main>
@endsection
