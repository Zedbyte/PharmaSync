async function fetchProductionRate() {
  const response = await fetch("/dashboard/production-rate");
  const data = await response.json();

  console.log(data);

  // Transform data for Chart.js
  const labelsProduction = [];
  const counts = [];

  data.forEach((item) => {
    const monthYear = `${item.year}-${item.month.toString().padStart(2, "0")}`;
    labelsProduction.push(monthYear);
    counts.push(item.batch_count);
  });

  return { labelsProduction, counts };
}

async function fetchInventoryDistribution() {
  const response = await fetch("/dashboard/inventory-distribution");
  const data = await response.json();

  console.log(data);

  // Extract unique labels (month-year combinations) from both medicine and material data
  const labelSet = new Set();

  data.medicine.forEach((item) => {
    labelSet.add(`${item.year}-${item.month.toString().padStart(2, "0")}`);
  });

  data.material.forEach((item) => {
    labelSet.add(`${item.year}-${item.month.toString().padStart(2, "0")}`);
  });

  // Sort labels chronologically
  const labelsInventory = Array.from(labelSet).sort();

  // Prepare datasets
  const medicineData = labelsInventory.map((label) => {
    const [year, month] = label.split("-");
    const entry = data.medicine.find(
      (item) => item.year === parseInt(year) && item.month === parseInt(month)
    );
    return entry ? parseInt(entry.total_stock_level) : 0; // Default to 0 if no data
  });

  const materialData = labelsInventory.map((label) => {
    const [year, month] = label.split("-");
    const entry = data.material.find(
      (item) => item.year === parseInt(year) && item.month === parseInt(month)
    );
    return entry ? parseInt(entry.total_stock_level) : 0; // Default to 0 if no data
  });

  return { labelsInventory, medicineData, materialData };
}

function getBarChartConfig(isDarkMode, distribData) {
  return {
    chart: {
      type: "column",
      backgroundColor: isDarkMode ? null : null,
    },
    title: {
      text: "Inventory Distribution",
      align: "left",
      style: {
        color: isDarkMode ? "#ffffff" : "rgba(50, 50, 50, 1)",
      },
    },
    subtitle: {
      text: "Comparison of Stock Levels of Material and Medicine by Month and Year",
      align: "left",
      style: {
        color: isDarkMode ? "#aaaaaa" : "rgba(50, 50, 50, 1)",
      },
    },
    xAxis: {
      categories: distribData.labels,
      crosshair: true,
      title: {
        text: "Year-Month",
        style: {
          color: isDarkMode ? "#ffffff" : "rgba(50, 50, 50, 1)",
        },
      },
      gridLineColor: isDarkMode
        ? "rgba(255, 255, 255, 0.1)"
        : "rgba(200, 200, 200, 0.3)",
      labels: {
        style: {
          color: isDarkMode ? "#ffffff" : "rgba(50, 50, 50, 1)",
        },
      },
    },
    yAxis: {
      min: 0,
      title: {
        text: "Stock Level",
        style: {
          color: isDarkMode ? "#ffffff" : "rgba(50, 50, 50, 1)",
        },
      },
      gridLineColor: isDarkMode
        ? "rgba(255, 255, 255, 0.1)"
        : "rgba(200, 200, 200, 0.3)",
      labels: {
        style: {
          color: isDarkMode ? "#ffffff" : "rgba(50, 50, 50, 1)",
        },
      },
    },
    legend: {
      itemStyle: {
        color: isDarkMode ? "#ffffff" : "rgba(50, 50, 50, 1)",
      },
    },
    tooltip: {
      shared: true,
      valueSuffix: " units",
      style: {
        color: isDarkMode ? "#ffffff" : "rgba(50, 50, 50, 1)",
      },
      backgroundColor: isDarkMode ? "#333333" : "rgba(255, 255, 255, 0.9)",
      borderColor: isDarkMode ? "#555555" : "rgba(200, 200, 200, 0.3)",
    },
    plotOptions: {
      column: {
        pointPadding: 0.2,
        borderWidth: 0,
      },
    },
    series: distribData.datasets.map((dataset) => ({
      name: dataset.label,
      data: dataset.data,
      color: dataset.backgroundColor,
      borderColor: dataset.borderColor,
    })),
  };
}

function getLineChartConfig(isDarkMode, productionData) {
  return {
      chart: {
          type: 'line',
          backgroundColor: isDarkMode ? null : null
      },
      title: {
          text: 'Production Statistics',
          align: 'left',
          style: {
              color: isDarkMode ? '#ffffff' : 'rgba(50, 50, 50, 1)'
          }
      },
      subtitle: {
          text: 'Monthly Batch Production Levels',
          align: 'left',
          style: {
              color: isDarkMode ? '#aaaaaa' : 'rgba(50, 50, 50, 1)'
          }
      },
      xAxis: {
          categories: productionData.labels,
          title: {
              text: 'Months',
              style: {
                  color: isDarkMode ? '#ffffff' : 'rgba(50, 50, 50, 1)'
              }
          },
          gridLineColor: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(200, 200, 200, 0.3)',
          labels: {
              style: {
                  color: isDarkMode ? '#ffffff' : 'rgba(50, 50, 50, 1)'
              }
          }
      },
      yAxis: {
          title: {
              text: 'Batches',
              style: {
                  color: isDarkMode ? '#ffffff' : 'rgba(50, 50, 50, 1)'
              }
          },
          gridLineColor: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(200, 200, 200, 0.3)',
          labels: {
              style: {
                  color: isDarkMode ? '#ffffff' : 'rgba(50, 50, 50, 1)'
              }
          }
      },
      legend: {
          itemStyle: {
              color: isDarkMode ? '#ffffff' : 'rgba(50, 50, 50, 1)'
          }
      },
      tooltip: {
          shared: true,
          style: {
              color: isDarkMode ? '#ffffff' : 'rgba(50, 50, 50, 1)'
          },
          backgroundColor: isDarkMode ? '#333333' : 'rgba(255, 255, 255, 0.9)',
          borderColor: isDarkMode ? '#555555' : 'rgba(200, 200, 200, 0.3)'
      },
      series: productionData.datasets.map(dataset => ({
          name: dataset.label,
          data: dataset.data,
          color: dataset.borderColor[0], // Use the first border color for the line
          lineWidth: dataset.borderWidth,
          fillOpacity: 0.5,
          fillColor: dataset.backgroundColor[0] // Use the first background color for the fill
      }))
  };
}


document.addEventListener("DOMContentLoaded", async () => {
  const productionChart = document.getElementById("productionChart");
  const barChart = document.getElementById("barChart");

  // Fetch data
  const { labelsProduction, counts } = await fetchProductionRate();
  const { labelsInventory, medicineData, materialData } =
    await fetchInventoryDistribution();

  // Chart.js configuration
  const productionData = {
    labels: labelsProduction,
    datasets: [
      {
        label: "Batches Produced",
        data: counts,
        backgroundColor: [
          "rgba(54, 162, 235, 0.2)",
          "rgba(255, 206, 86, 0.2)",
          "rgba(75, 192, 192, 0.2)",
          "rgba(153, 102, 255, 0.2)",
          "rgba(255, 159, 64, 0.2)",
        ],
        borderColor: [
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(75, 192, 192, 1)",
          "rgba(153, 102, 255, 1)",
          "rgba(255, 159, 64, 1)",
        ],
        borderWidth: 2,
        fill: true,
      },
    ],
  };

  const productionOptions = {
    plugins: {
      legend: {
        labels: {
          color: "rgba(135, 135, 135, 1)", // Set the legend text color to white
        },
      },
    },
    scales: {
      x: {
        grid: {
          color: "rgba(200, 200, 200, 0.3)",
        },
        ticks: {
          color: "rgba(135, 135, 135, 1)",
        },
      },
      y: {
        grid: {
          color: "rgba(200, 200, 200, 0.3)",
        },
        ticks: {
          beginAtZero: true,
          color: "rgba(135, 135, 135, 1)",
        },
      },
    },
    responsive: true,
    maintainAspectRatio: false,
  };

  const distribData = {
    labels: labelsInventory,
    datasets: [
      {
        label: "Medicine Stock Level",
        data: medicineData,
        backgroundColor: "rgba(54, 162, 235, 0.6)",
        borderColor: "rgba(54, 162, 235, 1)",
        borderWidth: 1,
      },
      {
        label: "Material Stock Level",
        data: materialData,
        backgroundColor: "rgba(165, 55, 253, 0.6)",
        borderColor: "rgba(255, 99, 132, 1)",
        borderWidth: 1,
      },
    ],
  };

  const distribOptions = {
    plugins: {
      legend: {
        labels: {
          color: "rgba(135, 135, 135, 1)", // Set the legend text color to white
        },
      },
    },
    scales: {
      x: {
        grid: {
          color: "rgba(200, 200, 200, 0.3)",
        },
        ticks: {
          color: "rgba(135, 135, 135, 1)",
        },
      },
      y: {
        grid: {
          color: "rgba(200, 200, 200, 0.3)",
        },
        ticks: {
          beginAtZero: true,
          color: "rgba(135, 135, 135, 1)",
        },
      },
    },
    responsive: true,
    maintainAspectRatio: false,
  };

  function renderBarChart() {
    const isBarDarkMode = document.documentElement.classList.contains("dark");
    const barChartConfig = getBarChartConfig(isBarDarkMode, distribData);
    Highcharts.chart(barChart, barChartConfig);
  }

  // Render the chart initially
  renderBarChart();

  // Optional: Add a MutationObserver or an event listener for theme toggling
  const barObserver = new MutationObserver(() => {
    renderBarChart();
  });

  barObserver.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ["class"],
  });


  function renderLineChart() {
    const isLineDarkMode = document.documentElement.classList.contains('dark');
    const chartConfig = getLineChartConfig(isLineDarkMode, productionData);
    Highcharts.chart(productionChart, chartConfig);
  }

  // Render the chart initially
  renderLineChart();

  // Optional: Add MutationObserver for theme toggling
  const lineChartObserver = new MutationObserver(() => {
      renderLineChart();
  });

  lineChartObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });



  Highcharts.chart('pieChart', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Beijing 2022 gold medals by country',
        align: 'left'
    },
    subtitle: {
        text: '3D donut in Highcharts',
        align: 'left'
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
    series: [{
        name: 'Medals',
        data: [
            ['Norway', 16],
            ['Germany', 12],
            ['USA', 8],
            ['Sweden', 8],
            ['Netherlands', 8],
            ['ROC', 6],
            ['Austria', 7],
            ['Canada', 4],
            ['Japan', 3]

        ]
    }]
});


});







// new Chart(productionChart, {
//   type: "line",
//   data: productionData,
//   options: productionOptions,
// });

// new Chart(barChart, {
//   type: "bar",
//   data: distribData,
//   options: distribOptions,
// });

// var data = {
//   labels: ["2013", "2014", "2014", "2015", "2016", "2017"],
//   datasets: [
//     {
//       label: "Dataset 1",
//       data: [10, 19, 3, 5, 2, 3],
//       backgroundColor: [
//         "rgba(255, 99, 132, 0.2)",
//         "rgba(54, 162, 235, 0.2)",
//         "rgba(255, 206, 86, 0.2)",
//         "rgba(75, 192, 192, 0.2)",
//         "rgba(153, 102, 255, 0.2)",
//         "rgba(255, 159, 64, 0.2)",
//       ],
//       borderColor: [
//         "rgba(255,99,132,1)",
//         "rgba(54, 162, 235, 1)",
//         "rgba(255, 206, 86, 1)",
//         "rgba(75, 192, 192, 1)",
//         "rgba(153, 102, 255, 1)",
//         "rgba(255, 159, 64, 1)",
//       ],
//       borderWidth: 1,
//       fill: false,
//     },
//     {
//       label: "Dataset 2",
//       data: [12, 15, 8, 7, 5, 4],
//       backgroundColor: [
//         "rgba(99, 132, 255, 0.2)",
//         "rgba(162, 235, 54, 0.2)",
//         "rgba(206, 86, 255, 0.2)",
//         "rgba(192, 192, 75, 0.2)",
//         "rgba(102, 255, 153, 0.2)",
//         "rgba(159, 64, 255, 0.2)",
//       ],
//       borderColor: [
//         "rgba(99, 132, 255, 1)",
//         "rgba(162, 235, 54, 1)",
//         "rgba(206, 86, 255, 1)",
//         "rgba(192, 192, 75, 1)",
//         "rgba(102, 255, 153, 1)",
//         "rgba(159, 64, 255, 1)",
//       ],
//       borderWidth: 1,
//       fill: false,
//     },
//   ],
// };

// var multiLineData = {
//   labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
//   datasets: [
//     {
//       label: "Dataset 1",
//       data: [12, 19, 3, 5, 2, 3],
//       borderColor: ["#587ce4"],
//       borderWidth: 2,
//       fill: false,
//     },
//     {
//       label: "Dataset 2",
//       data: [5, 23, 7, 12, 42, 23],
//       borderColor: ["#ede190"],
//       borderWidth: 2,
//       fill: false,
//     },
//     {
//       label: "Dataset 3",
//       data: [15, 10, 21, 32, 12, 33],
//       borderColor: ["#f44252"],
//       borderWidth: 2,
//       fill: false,
//     },
//   ],
// };

// var options = {
//   scales: {
//     y: {
//       ticks: {
//         beginAtZero: true,
//       },
//     },
//   },
//   legend: {
//     display: false,
//   },
//   elements: {
//     line: {
//       tension: 0.5,
//     },
//     point: {
//       radius: 0,
//     },
//   },
//   responsive: true,
//   maintainAspectRatio: false,
// };

// const multiAreaData = {
//   labels: [
//     "Jan",
//     "Feb",
//     "Mar",
//     "Apr",
//     "May",
//     "Jun",
//     "Jul",
//     "Aug",
//     "Sep",
//     "Oct",
//     "Nov",
//     "Dec",
//   ],
//   datasets: [
//     {
//       label: "Facebook",
//       data: [8, 11, 13, 15, 12, 13, 16, 15, 13, 19, 11, 14],
//       borderColor: ["rgba(255, 99, 132, 0.5)"],
//       backgroundColor: ["rgba(255, 99, 132, 0.5)"],
//       borderWidth: 1,
//       fill: true,
//     },
//     {
//       label: "Twitter",
//       data: [7, 17, 12, 16, 14, 18, 16, 12, 15, 11, 13, 9],
//       borderColor: ["rgba(54, 162, 235, 0.5)"],
//       backgroundColor: ["rgba(54, 162, 235, 0.5)"],
//       borderWidth: 1,
//       fill: true,
//     },
//     {
//       label: "Linkedin",
//       data: [6, 14, 16, 20, 12, 18, 15, 12, 17, 19, 15, 11],
//       borderColor: ["rgba(255, 206, 86, 0.5)"],
//       backgroundColor: ["rgba(255, 206, 86, 0.5)"],
//       borderWidth: 1,
//       fill: true,
//     },
//   ],
// };

// var multiAreaOptions = {
//   plugins: {
//     filler: {
//       propagate: true,
//     },
//   },
//   elements: {
//     line: {
//       tension: 0.5,
//     },
//     point: {
//       radius: 0,
//     },
//   },
//   scales: {
//     x: {
//       grid: {
//         color: "rgba(200, 200, 200, 0.3)", // Light gray for grid lines
//         drawBorder: true, // Draw border line for the axis
//       },
//       ticks: {
//         color: "rgba(75, 75, 75, 1)", // Dark gray for x-axis labels
//       },
//     },
//     y: {
//       grid: {
//         color: "rgba(200, 200, 200, 0.3)", // Light gray for grid lines
//         drawBorder: true, // Draw border line for the axis
//       },
//       ticks: {
//         color: "rgba(75, 75, 75, 1)", // Dark gray for y-axis labels
//         beginAtZero: true, // Start y-axis at zero
//       },
//     },
//   },
//   responsive: true,
//   maintainAspectRatio: false,
// };
