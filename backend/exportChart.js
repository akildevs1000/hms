const { createCanvas } = require('canvas');
const { Chart, registerables } = require('chart.js');
const fs = require('fs');
const path = require('path');

// Register all the necessary components
Chart.register(...registerables);

function generateDonutChart() {
    // Sample data
    const dataValues = [12, 19, 3, 5, 2, 3];
    // Calculate the total value of the dataset
    const totalValue = dataValues.reduce((acc, value) => acc + value, 0);
    console.log(`Total Value: ${totalValue}`);

    // Create a canvas
    const canvas = createCanvas(1100, 800);
    const ctx = canvas.getContext('2d');

    // Chart.js configuration for a donut chart
    const chartConfig = {
        type: 'doughnut', // Change to 'doughnut' for donut chart
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: 'Votes',
                data: dataValues,
                backgroundColor: [`#139c4a`, `#71de36`, `#ffc000`, `#dc3545`],
                borderColor: [`#139c4a`, `#71de36`, `#ffc000`, `#dc3545`],
                borderWidth: 1,
            }]
        },
        options: {
            cutout: "65%", // Increase the hole size (increase percentage to make it larger)
            responsive: false, // Set to false to maintain the canvas size
            plugins: {
                legend: {
                    display: false, // Hide the legend
                },
            }
        }, plugins: [
            {
                afterDraw: function (chart) {
                    const ctx = chart.ctx;
                    const total = 55;

                    // Set font properties
                    ctx.restore();
                    const fontSize = (chart.height / 100).toFixed(2);
                    ctx.font = `5em sans-serif`;
                    ctx.textBaseline = "middle";
                    // Set the position to draw the total
                    const text = total;
                    const textX = Math.round(
                        (chart.width - ctx.measureText(text).width) / 2
                    );
                    const textY = Math.round(chart.height / 2);

                    ctx.fillStyle = "#959595"; // Text color
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            },
        ],
    };

    // Generate the chart
    const chart = new Chart(ctx, chartConfig);

    // Specify the output directory and file path
    const outputDir = '../backend/public/charts';
    const outputFilePath = path.join(outputDir, 'donutChart.png');

    // Ensure the output directory exists
    if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true }); // Create the directory if it doesn't exist
    }

    // Save the chart as a PNG image
    const buffer = canvas.toBuffer('image/png');
    fs.writeFileSync(outputFilePath, buffer);
    console.log(`Donut chart generated and saved as ${outputFilePath}`);
}

// Call the function to generate the donut chart
generateDonutChart();
