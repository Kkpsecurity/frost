class FrostCharts {

    constructor() {
        this.monthNames = [
            "January", "February", "March",
            "April", "May", "June",
            "July", "August", "September",
            "October", "November", "December"
        ];
        this.currentYear = new Date().getFullYear();
    }

    // Method to initialize your charts
    initializeCharts(yearCtx, monthCtx) {
        this.yearRegistrationChart = this.createChart(
            yearCtx, 'line', this.monthNames, [],
            'rgba(95, 0, 0, 0.5)', 'rgba(75, 192, 192, 1)'
        );
        this.monthRegistrationChart = this.createChart(
            monthCtx, 'bar', this.getDaysOfMonth(), [],
            'some-background-color', 'some-border-color' // Replace with actual colors you need
        );
    }   

    fetchChartData(url) {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                this.updateChartTitle("TITLE");
                this.updateYearlyChart(data.year);
                this.updateMonthlyChart(data.month); // assuming you'd have similar logic for the monthly data.
            })
            .catch(error => {
                console.error('There was an error fetching the data: ', error);
            });
    }
    
    updateChartTitle(title) {
        const titleElement = document.getElementById('chartTitle');
        titleElement.innerText = title;
    }
    
    updateYearlyChart(yearData) {
        this.yearRegistrationChart.data.datasets[0].data = Object.values(yearData);
        this.yearRegistrationChart.update();
    }
    
    // Add a similar method for the monthly chart.
    updateMonthlyChart(monthData) {
        this.monthRegistrationChart.data.datasets[0].data = Object.values(monthData);
        this.monthRegistrationChart.update();
    }
    
}

// Exporting the module so you can import it in another file
export default FrostCharts;
