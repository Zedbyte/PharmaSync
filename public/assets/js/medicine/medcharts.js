async function fetchTopStockByBatch() {
    const response = await fetch("/medicine-list/batch-stock");
    const data = await response.json();

    // Transform data for Highcharts or similar bar chart libraries
    const formattedData = {};

    // Group data by medicine name and prepare data for batches
    data.forEach((item) => {
        const { medicine_name, batch_id, stock_level } = item;

        if (!formattedData[medicine_name]) {
            formattedData[medicine_name] = [];
        }

        formattedData[medicine_name].push({
            x: batch_id, // Batch ID as x-axis
            y: stock_level // Stock level as y-axis
        });
    });

    // Prepare series for Highcharts
    const series = Object.keys(formattedData).map((medicine) => ({
        name: medicine,
        data: formattedData[medicine]
    }));
    // console.log(series);
    
    return series;
}


async function fetchMostSoldMedicine() {
    const response = await fetch("/medicine-list/most-sold");
    const data = await response.json();

    // Transform data for Highcharts or similar bar chart libraries
    const formattedData = [];

    // Prepare data for each medicine
    data.forEach((item) => {
        const { medicine_name, total_quantity_sold, unit_price, total_revenue } = item;

        formattedData.push({
            name: medicine_name, // Medicine name
            y: total_quantity_sold, // Total quantity sold as y-axis
            unitPrice: unit_price, // Additional data (optional for tooltips)
            totalRevenue: total_revenue // Additional data (optional for tooltips)
        });
    });

    // Prepare series for Highcharts (single series for bar chart)
    const series = [
        {
            name: "Most Sold Medicines",
            data: formattedData
        }
    ];

    // console.log(series);
    return series;
}


function getBarChartConfig(isDarkMode, stockData) {
    return {
        chart: {
            type: "bar",
            backgroundColor: isDarkMode ? null : null,
        },
        title: {
            text: "Top Stock Levels of Medicines",
            align: "left",
            style: {
                color: isDarkMode ? "#ffffff" : "#333333",
            },
        },
        subtitle: {
            text: "Top Stock Levels by Medicine Batch",
            align: "left",
            style: {
                color: isDarkMode ? "#aaaaaa" : "rgba(50, 50, 50, 1)",
            },
        },
        tooltip: {
            pointFormat: "<b>{point.name}</b>: {point.y} units (Batch: {point.batch_no})",
            style: {
                color: isDarkMode ? "#ffffff" : "#333333",
            },
            backgroundColor: isDarkMode ? "#333333" : "#ffffff",
            borderColor: isDarkMode ? "#555555" : "#dddddd",
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true,
                    format: "{point.name}: {point.y} units",
                    style: {
                        fontSize: "0.7em",
                        color: isDarkMode ? "#ffffff" : "#333333",
                        textOutline: "none",
                    },
                },
            },
        },
        xAxis: {
            categories: stockData.labels,
            title: {
                text: "Medicines",
                style: {
                    color: isDarkMode ? "#ffffff" : "#333333",
                },
            },
            labels: {
                overflow: "justify",
                style: {
                    color: isDarkMode ? "#ffffff" : "#333333",
                },
            },
        },
        yAxis: {
            min: 0,
            title: {
                text: "Stock Levels",
                style: {
                    color: isDarkMode ? "#ffffff" : "#333333",
                },
            },
            labels: {
                overflow: "justify",
                style: {
                    color: isDarkMode ? "#ffffff" : "#333333",
                },
            },
        },
        legend: {
            itemStyle: {
                color: isDarkMode ? "#ffffff" : "#333333",
            },
        },
        series: [
            {
                name: stockData.datasets[0].name, // The series name
                colorByPoint: true, // Enable unique colors
                data: stockData.datasets[0].data.map((item, index) => ({
                    name: stockData.labels[index], // Use the correct label for each data point
                    batch_no: item.x,  // Batch ID
                    y: item.y // Quantity in stock
                })),
            },
        ],
    };
}

function getPieChartConfig(isDarkMode, distribData) {
    return {
        chart: {
            type: "pie",
            backgroundColor: isDarkMode ? null : null,
        },
        title: {
            text: "Most Sold Medicines",
            align: "left",
            style: {
                color: isDarkMode ? "#ffffff" : "#333333",
            },
        },
        subtitle: {
            text: "Distribution of Top-Selling Medicines by Order Quantity",
            align: "left",
            style: {
                color: isDarkMode ? "#aaaaaa" : "rgba(50, 50, 50, 1)",
            },
        },
        tooltip: {
            pointFormat: "<b>{point.name}</b>: {point.y} units",
            style: {
                color: isDarkMode ? "#ffffff" : "#333333",
            },
            backgroundColor: isDarkMode ? "#333333" : "#ffffff",
            borderColor: isDarkMode ? "#555555" : "#dddddd",
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: "pointer",
                dataLabels: {
                    enabled: true,
                    format: "{point.name}: {point.percentage:.1f}%",
                    style: {
                        fontSize: "1.2em",
                        color: isDarkMode ? "#ffffff" : "#333333",
                        textOutline: "none",
                    },
                },
            },
        },
        series: [
            {
                name: distribData.datasets[0].name, // The series name
                colorByPoint: true, // Enable unique colors
                data: distribData.datasets[0].data, // Use preprocessed `data` directly
            },
        ],
    };
}



document.addEventListener("DOMContentLoaded", async () => {

    const pieMedChart = document.getElementById("pieMedChart");
    const pieData = await fetchMostSoldMedicine();
    
    const barMedChart = document.getElementById("barMedChart");
    const barData = await fetchTopStockByBatch();

    const distribData = {
        labels: pieData.map((item) => item.name),
        datasets: [
            {
                name: "Most Sold Medicines",
                data: pieData.flatMap((item) => 
                    item.data.map((medicine) => ({
                        name: medicine.name, // Medicine name
                        y: parseFloat(medicine.y), // Quantity sold (converted to number)
                        unitPrice: medicine.unitPrice, // Unit price
                        totalRevenue: medicine.totalRevenue, // Total revenue
                    }))
                ),
                colorByPoint: true,
            },
        ],
    };

    const stockData = {
        labels: barData.map((item) => item.name),
        datasets: [
            {
                name: "Stock Levels",
                data: barData.flatMap((item) => 
                    item.data.map((medicine) => ({
                        x: medicine.x, // Stock level
                        y: parseFloat(medicine.y) // Quantity in stock (converted to number)
                    }))
                ),
                colorByPoint: true,
            },
        ],
    };
    

    function renderPieChart() {
        const isPieDarkMode = document.documentElement.classList.contains("dark");
        const pieChartConfig = getPieChartConfig(isPieDarkMode, distribData);
        Highcharts.chart(pieMedChart, pieChartConfig);
    }

    // Render the chart initially
    renderPieChart();

    // Optional: Add a MutationObserver or an event listener for theme toggling
    const pieObserver = new MutationObserver(() => {
        renderPieChart();
    });

    pieObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"],
    });
    

    function renderBarChart() {
        const isBarDarkMode = document.documentElement.classList.contains("dark");
        const barChartConfig = getBarChartConfig(isBarDarkMode, stockData);
        Highcharts.chart(barMedChart, barChartConfig);
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

});
