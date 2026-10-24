document.addEventListener('DOMContentLoaded', function () {
    const reportTypeSelect = document.getElementById('reportType');
    const cards = document.querySelectorAll('[data-report-card]');
    const buttons = document.querySelectorAll('.generate-report');

    function showSelectedReport() {
        if (!reportTypeSelect) return;
        const selected = reportTypeSelect.value;

        cards.forEach(function (card) {
            card.style.display = card.dataset.reportCard === selected ? 'flex' : 'none';
        });
    }

    if (reportTypeSelect) {
        reportTypeSelect.addEventListener('change', showSelectedReport);
        showSelectedReport();
    }

    const reportConfig = {
        historical_inventory: {
            from: 'inventoryDateFrom',
            to: 'inventoryDateTo',
            recipient: 'inventoryRecipient',
            endpoint: 'reports/export_historical_inventory_pdf.php'
        },
        sales_profit: {
            from: 'salesDateFrom',
            to: 'salesDateTo',
            recipient: 'salesRecipient',
            endpoint: 'reports/export_sales_profit_pdf.php'
        },
        purchase_restocking: {
            from: 'purchaseDateFrom',
            to: 'purchaseDateTo',
            recipient: 'purchaseRecipient',
            endpoint: 'reports/export_purchase_restocking_pdf.php'
        }
    };

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const config = reportConfig[button.dataset.report];
            if (!config) return;

            const dateFrom = document.getElementById(config.from).value;
            const dateTo = document.getElementById(config.to).value;
            const recipient = document.getElementById(config.recipient).value.trim();

            if (!dateFrom || !dateTo) {
                alert('Please select both the start date and end date.');
                return;
            }

            if (dateFrom > dateTo) {
                alert('The start date cannot be later than the end date.');
                return;
            }

            const params = new URLSearchParams({
                date_from: dateFrom,
                date_to: dateTo,
                recipient: recipient
            });

            window.open(config.endpoint + '?' + params.toString(), '_blank');
        });
    });
});
