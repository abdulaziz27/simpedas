import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
} from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend, ChartDataLabels);

document.addEventListener('DOMContentLoaded', () => {
    // Chart utama (bar/line/pie)
    const statistikChart = document.getElementById('statistikChart');
    if (statistikChart) {
        const chartData = JSON.parse(statistikChart.dataset.chartData || '{}');
        const chartOptions = JSON.parse(statistikChart.dataset.chartOptions || '{}');
        const chartType = statistikChart.dataset.chartType || 'bar';
        // Perkecil bar
        if (chartData.datasets && chartData.datasets.length > 0) {
            chartData.datasets.forEach(ds => {
                ds.barPercentage = 0.5;
                ds.categoryPercentage = 0.7;
            });
        }
        try {
            new Chart(statistikChart, {
                type: chartType,
                data: chartData,
                options: chartOptions
            });
        } catch (e) {
            console.error('Chart.js error statistikChart:', e);
        }
    }
    // Bar horizontal (progress style, stacked)
    const statistikBarChart = document.getElementById('statistikBarChart');
    if (statistikBarChart) {
        const barChartData = JSON.parse(statistikBarChart.dataset.chartData || '{}');
        const barChartOptions = JSON.parse(statistikBarChart.dataset.chartOptions || '{}');
        barChartOptions.indexAxis = 'y';
        // Figma style: stacked bar, datalabels as percentage only for green bar
        barChartOptions.plugins = barChartOptions.plugins || {};
        barChartOptions.plugins.datalabels = {
            align: 'end',
            anchor: 'end',
            color: '#0d524a',
            font: { weight: 'bold', size: 16 },
            formatter: (value, context) => {
                // Only show datalabels for green bar (datasetIndex 0)
                if (context.datasetIndex === 0) {
                    const percentages = context.dataset.percentages || [];
                    return percentages[context.dataIndex] !== undefined ? percentages[context.dataIndex] + '%' : '';
                }
                return '';
            },
            display: function(context) {
                // Only show datalabels for green bar (datasetIndex 0)
                return context.datasetIndex === 0;
            }
        };
        // Stacked config
        barChartOptions.scales = barChartOptions.scales || {};
        barChartOptions.scales.x = barChartOptions.scales.x || {};
        barChartOptions.scales.x.beginAtZero = true;
        barChartOptions.scales.x.max = 100;
        barChartOptions.scales.x.stacked = true;
        barChartOptions.scales.x.grid = { color: '#e5e7eb' };
        barChartOptions.scales.x.ticks = { color: '#22223b', callback: function(value) { return value + '%'; } };
        barChartOptions.scales.y = barChartOptions.scales.y || {};
        barChartOptions.scales.y.stacked = true;
        barChartOptions.scales.y.grid = { display: false };
        barChartOptions.scales.y.ticks = { color: '#22223b' };
        // Destroy previous chart if exists
        if (statistikBarChart._chartInstance) {
            statistikBarChart._chartInstance.destroy();
        }
        statistikBarChart._chartInstance = new Chart(statistikBarChart, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions,
            plugins: [ChartDataLabels]
        });
    }
}); 