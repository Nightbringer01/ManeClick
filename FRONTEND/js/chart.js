var chart1, chart2, chart3, chart4, chart5;

function updateReport() {
    // Get the start and end dates from the inputs
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // Update the date range display
    const dateRangeText = document.getElementById('dateRangeText');
    dateRangeText.innerText = `Date Range: ${startDate} - ${endDate}`;

    // Fetch new data and update the charts
    fetchChartData(startDate, endDate).then(data => {
        updateCharts(data);
    });
}

function fetchChartData(startDate, endDate) {
    // Example of how you might fetch data from a server or API based on the date range
    return new Promise((resolve, reject) => {
        // Mock data fetching logic
        const data = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'], // Adjust this based on your data structure
            datasets: [{
                label: `Data from ${startDate} to ${endDate}`,
                data: [Math.random() * 20, Math.random() * 20, Math.random() * 20, Math.random() * 20, Math.random() * 20, Math.random() * 20],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };
        resolve(data);
    });
}

function updateCharts(data) {
    // Destroy existing charts if they exist
    if (chart1) chart1.destroy();
    if (chart2) chart2.destroy();
    if (chart3) chart3.destroy();
    if (chart4) chart4.destroy();
    if (chart5) chart5.destroy();

    // Generate new charts with updated data
    generateCharts(data);
}

function generateCharts(data) {
    // Get context for each chart
    var ctx1 = document.getElementById('chart1').getContext('2d');
    var ctx2 = document.getElementById('chart2').getContext('2d');
    var ctx3 = document.getElementById('chart3').getContext('2d');
    var ctx4 = document.getElementById('chart4').getContext('2d');
    var ctx5 = document.getElementById('chart5').getContext('2d');

    // Create each chart with the provided data
    chart1 = new Chart(ctx1, { type: 'bar', data: data });
    chart2 = new Chart(ctx2, { type: 'line', data: data });
    chart3 = new Chart(ctx3, { type: 'pie', data: data });
    chart4 = new Chart(ctx4, { type: 'doughnut', data: data });
    chart5 = new Chart(ctx5, { type: 'radar', data: data });
}

// Initial chart generation with default data
document.addEventListener("DOMContentLoaded", function() {
    const initialData = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
        datasets: [{
            label: 'Dummy Data',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    };
    generateCharts(initialData);
});