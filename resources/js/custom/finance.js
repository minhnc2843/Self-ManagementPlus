// Import thư viện chỉ cho file này
import ApexCharts from 'apexcharts';

// Gán các hàm vào window để HTML (onclick) có thể gọi được
window.currentRemaining = 0;

window.openPaymentModal = function(id, name, remaining) {
    window.currentRemaining = remaining;
    document.getElementById('paymentLoanName').innerText = 'Khoản vay: ' + name;
    document.getElementById('paymentRemaining').innerText = new Intl.NumberFormat('vi-VN').format(remaining);
    document.getElementById('paymentInput').value = '';
    document.getElementById('paymentInput').max = remaining;
    
    // Set action URL dynamically
    // Lưu ý: route() của Laravel không chạy trong file JS, ta sẽ lấy URL từ data-attribute trong HTML sau
    let form = document.getElementById('paymentForm');
    form.action = `/finance/loans/${id}/pay`; // Hoặc xử lý URL gốc từ HTML truyền vào
    
    document.getElementById('paymentModal').showModal();
}

window.fillFullAmount = function() {
    document.getElementById('paymentInput').value = window.currentRemaining;
}

// Logic vẽ biểu đồ
document.addEventListener('DOMContentLoaded', function () {
    const chartElement = document.querySelector("#revenue-barchart");
    
    if(chartElement) {
        // Lấy dữ liệu từ attribute data (cách truyền dữ liệu chuẩn từ Blade sang JS)
        const incomeData = JSON.parse(chartElement.dataset.income || '[]');
        const expenseData = JSON.parse(chartElement.dataset.expense || '[]');
        const labelsData = JSON.parse(chartElement.dataset.labels || '[]');

        const chartConfig = {
            series: [{
                name: 'Thu nhập',
                data: incomeData
            }, {
                name: 'Chi tiêu',
                data: expenseData
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4
                },
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: labelsData,
            },
            fill: { opacity: 1 },
            colors: ['#4669fa', '#f1595c'], 
            tooltip: {
                y: {
                    formatter: function (val) {
                        return new Intl.NumberFormat('vi-VN').format(val) + " đ"
                    }
                }
            }
        };

        const chart = new ApexCharts(chartElement, chartConfig);
        chart.render();
    }
});