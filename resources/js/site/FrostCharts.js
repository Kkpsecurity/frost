(function ($) {
  // This ensures jQuery is passed in as $ and avoids conflicts
  var monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  var currentYear = new Date().getFullYear();
  var yearRegistrationChart;
  var monthRegistrationChart;

  function createChart(
    context,
    type,
    labels,
    data,
    backgroundColor,
    borderColor
  ) {
    return new Chart(context, {
      type: type,
      data: {
        labels: labels,
        datasets: [
          {
            data: data,
            backgroundColor: backgroundColor,
            borderColor: borderColor,
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  }

  function fetchChartData(url) {
    $.getJSON(url, function (data) {
      updateChartTitle("Frost Data Charts");

      // Check if the charts are initialized
      if (yearRegistrationChart && monthRegistrationChart) {
        updateYearlyChart(data.year);
        updateMonthlyChart(data.month);
      } else {
        // If not initialized, then do so with fetched data
        var yearCtx = $("#yearChart")[0].getContext("2d");
        var monthCtx = $("#monthChart")[0].getContext("2d");
        initializeCharts(
          yearCtx,
          monthCtx,
          Object.values(data.year),
          Object.values(data.month)
        );
      }
    }).fail(function () {
      console.error("There was an error fetching the data.");
    });
  }

  function getDaysOfMonth() {
    const date = new Date();
    const month = date.getMonth();
    const year = date.getFullYear();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const days = [];
    for (let i = 1; i <= daysInMonth; i++) {
      days.push(i);
    }

    return days;
  }

  function initializeCharts(yearCtx, monthCtx, yearData = [], monthData = []) {
      yearRegistrationChart = createChart(
        yearCtx,
        "line",
        monthNames,
        yearData,
        "rgba(95, 0, 0, 0.5)",
        "rgba(75, 192, 192, 1)",
        "Yearly Registrations" // <- Add this line for the title
      );
    
      monthRegistrationChart = createChart(
        monthCtx,
        "bar",
        getDaysOfMonth(),
        monthData,
        "rgba(95, 0, 0, 0.5)",
        "rgba(75, 192, 192, 1)",
        "Monthly Registrations" // <- Add this line for the title
      );
    }
    

  function updateChartTitle(title) {
    $("#chartTitle").text(title);
  }

  function updateYearlyChart(yearData) {
    yearRegistrationChart.data.datasets[0].data = Object.values(yearData);
    yearRegistrationChart.update();
  }

  function updateMonthlyChart(monthData) {
    monthRegistrationChart.data.datasets[0].data = Object.values(monthData);
    monthRegistrationChart.update();
  }

  $(document).ready(function () {
    var yearCtx = $("#yearChart")[0].getContext("2d");
    var monthCtx = $("#monthChart")[0].getContext("2d");
    initializeCharts(yearCtx, monthCtx);
    const baseUrl = window.location.origin;

    fetchChartData(baseUrl + "/admin/reports/services/charts/registration");

    $('[data-toggle="tab"]').click(function () {
      var view = $(this).attr("data-view");
      if (view === "year") {
        fetchChartData(baseUrl + "/admin/reports/services/charts/registration");
        updateChartTitle(currentYear + " Registrations");
      } else {
        fetchChartData(baseUrl + "/admin/reports/services/charts/registration");
        updateChartTitle(monthNames[new Date().getMonth()] + " Registrations");
      }
    });
  });

  // Attach the functions you want to expose to the window object for global access
  window.FrostCharts = {
    initializeCharts: initializeCharts,
    fetchChartData: fetchChartData,
  };
})(jQuery); // Pass in jQuery object
