async function fetchProductionRate() {
  const response = await fetch('/dashboard/production-rate');
  const data = await response.json();

  console.log(data);
  

  // Transform data for Chart.js
  const labels = [];
  const counts = [];

  data.forEach(item => {
      const monthYear = `${item.year}-${item.month.toString().padStart(2, '0')}`;
      labels.push(monthYear);
      counts.push(item.batch_count);
  });

  return { labels, counts };
}


document.addEventListener("DOMContentLoaded", async () => {
  const productionChart = document.getElementById("productionChart");
  const barChart = document.getElementById("barChart");

  // Fetch data
  const { labels, counts } = await fetchProductionRate();

  // Chart.js configuration
  const productionData = {
    labels: labels,
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
        borderColor:  [
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
    scales: {
      x: {
        grid: {
          color: "rgba(200, 200, 200, 1)",
        },
        ticks: {
          color: "rgba(0, 0, 0, 1)",
        },
      },
      y: {
        grid: {
          color: "rgba(200, 200, 200, 1)",
        },
        ticks: {
          beginAtZero: true,
          color: "rgba(0, 0, 0, 1)",
        },
      },
    },
    responsive: true,
    maintainAspectRatio: false,
  };

  var data = {
    labels: ["2013", "2014", "2014", "2015", "2016", "2017"],
    datasets: [
      {
        label: "Dataset 1",
        data: [10, 19, 3, 5, 2, 3],
        backgroundColor: [
          "rgba(255, 99, 132, 0.2)",
          "rgba(54, 162, 235, 0.2)",
          "rgba(255, 206, 86, 0.2)",
          "rgba(75, 192, 192, 0.2)",
          "rgba(153, 102, 255, 0.2)",
          "rgba(255, 159, 64, 0.2)",
        ],
        borderColor: [
          "rgba(255,99,132,1)",
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(75, 192, 192, 1)",
          "rgba(153, 102, 255, 1)",
          "rgba(255, 159, 64, 1)",
        ],
        borderWidth: 1,
        fill: false,
      },
      {
        label: "Dataset 2",
        data: [12, 15, 8, 7, 5, 4],
        backgroundColor: [
          "rgba(99, 132, 255, 0.2)",
          "rgba(162, 235, 54, 0.2)",
          "rgba(206, 86, 255, 0.2)",
          "rgba(192, 192, 75, 0.2)",
          "rgba(102, 255, 153, 0.2)",
          "rgba(159, 64, 255, 0.2)",
        ],
        borderColor: [
          "rgba(99, 132, 255, 1)",
          "rgba(162, 235, 54, 1)",
          "rgba(206, 86, 255, 1)",
          "rgba(192, 192, 75, 1)",
          "rgba(102, 255, 153, 1)",
          "rgba(159, 64, 255, 1)",
        ],
        borderWidth: 1,
        fill: false,
      },
    ],
  };

  var multiLineData = {
    labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
    datasets: [
      {
        label: "Dataset 1",
        data: [12, 19, 3, 5, 2, 3],
        borderColor: ["#587ce4"],
        borderWidth: 2,
        fill: false,
      },
      {
        label: "Dataset 2",
        data: [5, 23, 7, 12, 42, 23],
        borderColor: ["#ede190"],
        borderWidth: 2,
        fill: false,
      },
      {
        label: "Dataset 3",
        data: [15, 10, 21, 32, 12, 33],
        borderColor: ["#f44252"],
        borderWidth: 2,
        fill: false,
      },
    ],
  };

  var options = {
    scales: {
      y: {
        ticks: {
          beginAtZero: true,
        },
      },
    },
    legend: {
      display: false,
    },
    elements: {
      line: {
        tension: 0.5,
      },
      point: {
        radius: 0,
      },
    },
    responsive: true,
    maintainAspectRatio: false,
  };

  const multiAreaData = {
    labels: [
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
      "Dec",
    ],
    datasets: [
      {
        label: "Facebook",
        data: [8, 11, 13, 15, 12, 13, 16, 15, 13, 19, 11, 14],
        borderColor: ["rgba(255, 99, 132, 0.5)"],
        backgroundColor: ["rgba(255, 99, 132, 0.5)"],
        borderWidth: 1,
        fill: true,
      },
      {
        label: "Twitter",
        data: [7, 17, 12, 16, 14, 18, 16, 12, 15, 11, 13, 9],
        borderColor: ["rgba(54, 162, 235, 0.5)"],
        backgroundColor: ["rgba(54, 162, 235, 0.5)"],
        borderWidth: 1,
        fill: true,
      },
      {
        label: "Linkedin",
        data: [6, 14, 16, 20, 12, 18, 15, 12, 17, 19, 15, 11],
        borderColor: ["rgba(255, 206, 86, 0.5)"],
        backgroundColor: ["rgba(255, 206, 86, 0.5)"],
        borderWidth: 1,
        fill: true,
      },
    ],
  };

  var multiAreaOptions = {
    plugins: {
      filler: {
        propagate: true,
      },
    },
    elements: {
      line: {
        tension: 0.5,
      },
      point: {
        radius: 0,
      },
    },
    scales: {
      x: {
        grid: {
          color: "rgba(200, 200, 200, 0.3)", // Light gray for grid lines
          drawBorder: true, // Draw border line for the axis
        },
        ticks: {
          color: "rgba(75, 75, 75, 1)", // Dark gray for x-axis labels
        },
      },
      y: {
        grid: {
          color: "rgba(200, 200, 200, 0.3)", // Light gray for grid lines
          drawBorder: true, // Draw border line for the axis
        },
        ticks: {
          color: "rgba(75, 75, 75, 1)", // Dark gray for y-axis labels
          beginAtZero: true, // Start y-axis at zero
        },
      },
    },
    responsive: true,
    maintainAspectRatio: false,
  };

  new Chart(productionChart, {
    type: "line",
    data: productionData,
    options: productionOptions,
  });

  new Chart(barChart, {
    type: "bar",
    data: data,
    options: options,
  });
});