@extends('layouts.user')

@section('title', 'Reports')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Financial Reports</h2>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%);">
                    <h5 class="mb-0">Generate Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label class="form-label small mb-0 text-dark">From</label>
                            <input id="globalStart" type="date" class="form-control form-control-sm" value="{{ now()->startOfMonth()->toDateString() }}">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-0 text-dark">To</label>
                            <input id="globalEnd" type="date" class="form-control form-control-sm" value="{{ now()->endOfMonth()->toDateString() }}">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-0 text-dark">Category (Optional)</label>
                            <select id="globalCategory" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button id="generateAll" class="btn btn-primary btn-sm mt-4 px-4 shadow-sm">
                                <i class="fas fa-sync-alt me-1"></i> Update Reports
                            </button>
                        </div>
                        <div class="col-auto ms-auto">
                            <div class="btn-group mt-4">
                                <button class="btn btn-outline-dark btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#reportSheetModal">
                                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="row mb-4">
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Income</div>
                            <div id="summaryIncome" class="h4 mb-0 text-success">—</div>
                        </div>
                        <div><i class="fas fa-hand-holding-usd fa-2x text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Expenses</div>
                            <div id="summaryExpenses" class="h4 mb-0 text-danger">—</div>
                        </div>
                        <div><i class="fas fa-wallet fa-2x text-danger"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Net Balance</div>
                            <div id="summaryNet" class="h4 mb-0">—</div>
                        </div>
                        <div><i class="fas fa-balance-scale fa-2x text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Transactions</div>
                            <div id="summaryCount" class="h4 mb-0">—</div>
                        </div>
                        <div><i class="fas fa-list fa-2x text-info"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Monthly Financial Trend (Income vs Expense)</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Spending by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Yearly Summary</h6>
                </div>
                <div class="card-body">
                    <canvas id="yearlyChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Report Sheet Modal -->
<div class="modal fade" id="reportSheetModal" tabindex="-1" aria-labelledby="reportSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="   transform: translateY(45px);">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportSheetModalLabel">Generate Report Sheet (PDF)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('user.reports.reportsheet.download') }}" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bs-start" class="form-label">Start date</label>
                        <input type="date" class="form-control" id="bs-start" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label for="bs-end" class="form-label">End date</label>
                        <input type="date" class="form-control" id="bs-end" name="end_date" value="{{ now()->endOfMonth()->toDateString() }}">
                    </div>
                    <input type="hidden" name="format" value="pdf">
                    <p class="text-muted">The PDF will open in a new tab.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('reportSheetModal')).hide(), 1000)">Download PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Helper to fetch report JSON from web endpoint (session-authenticated)
    async function fetchReport(params = {}) {
        const url = new URL('/reports/spending/data', window.location.origin);
        Object.keys(params).forEach(k => url.searchParams.set(k, params[k]));
        const resp = await fetch(url.toString(), { credentials: 'same-origin' });
        if (!resp.ok) throw new Error('Failed to fetch report: ' + resp.status);
        return resp.json();
    }

    // Create or update Chart.js chart
    let monthlyChart = null, categoryChart = null, yearlyChart = null;

    function createBarChart(ctx, labels, datasets) {
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function createPieChart(ctx, labels, data) {
        return new Chart(ctx, {
            type: 'pie',
            data: { labels: labels, datasets: [{ data: data, backgroundColor: labels.map((_,i)=>`hsl(${i*60 % 360} 70% 50%)`) }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    async function loadMonthly(startDate, endDate, categoryId = '', updateSum = true) {
        try {
            const json = await fetchReport({ group_by: 'month', start_date: startDate, end_date: endDate, category_id: categoryId });
            const report = json.data.report;
            if (updateSum) updateSummary(json.data.summary || {});
            
            const labels = report.map(r => `${r.year}-${String(r.month).padStart(2,'0')}`);
            const incomeData = report.map(r => r.income);
            const expenseData = report.map(r => r.expense);
            
            const datasets = [
                { 
                    label: 'Income', 
                    data: incomeData, 
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: '#198754',
                    borderWidth: 1
                },
                { 
                    label: 'Expense', 
                    data: expenseData, 
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }
            ];

            const ctx = document.getElementById('monthlyChart').getContext('2d');
            if (monthlyChart) monthlyChart.destroy();
            monthlyChart = createBarChart(ctx, labels, datasets);
        } catch (err) {
            console.error(err);
        }
    }

    async function loadCategory(startDate, endDate, categoryId = '', updateSum = false) {
        try {
            const json = await fetchReport({ group_by: 'category', start_date: startDate, end_date: endDate, category_id: categoryId });
            const report = json.data.report;
            if (updateSum) updateSummary(json.data.summary || {});
            const labels = report.map(r => r.category ? r.category.name : 'Uncategorized');
            const data = report.map(r => parseFloat(r.total));
            const ctx = document.getElementById('categoryChart').getContext('2d');
            if (categoryChart) categoryChart.destroy();
            categoryChart = createPieChart(ctx, labels, data);
        } catch (err) { console.error(err); }
    }

    async function loadYearly(year, categoryId = '', updateSum = false) {
        try {
            const start = year + '-01-01';
            const end = year + '-12-31';
            const json = await fetchReport({ group_by: 'month', start_date: start, end_date: end, category_id: categoryId });
            const report = json.data.report;
            if (updateSum) updateSummary(json.data.summary || {});
            
            const labels = report.map(r => `${r.year}-${String(r.month).padStart(2,'0')}`);
            const incomeData = report.map(r => r.income);
            const expenseData = report.map(r => r.expense);
            
            const datasets = [
                { 
                    label: 'Income', 
                    data: incomeData, 
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: '#198754',
                    borderWidth: 1
                },
                { 
                    label: 'Expense', 
                    data: expenseData, 
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }
            ];

            const ctx = document.getElementById('yearlyChart').getContext('2d');
            if (yearlyChart) yearlyChart.destroy();
            yearlyChart = createBarChart(ctx, labels, datasets);
        } catch (err) { console.error(err); }
    }

function updateSummary(summary) {
    const fmt = (v) =>
        typeof v === 'number'
            ? v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : (v || '0.00');

    const totalExpenses = Number(summary.total_expenses ?? 0);
    const totalIncome   = Number(summary.total_income ?? 0);
    const totalNet      = Number(summary.net_total ?? 0);

    document.getElementById('summaryIncome').textContent   = fmt(totalIncome);
    document.getElementById('summaryExpenses').textContent = fmt(totalExpenses);
    document.getElementById('summaryCount').textContent    = summary.transaction_count ?? '0';

    const netEl = document.getElementById('summaryNet');
    const sign  = totalNet >= 0 ? '+' : '−';
    netEl.textContent = sign + '$' + fmt(Math.abs(totalNet));
    netEl.className = 'h4 mb-0 ' + (totalNet >= 0 ? 'text-success' : 'text-danger');
}

    function downloadChartAsImage(chart, filename) {
        try {
            const url = chart.toBase64Image();
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
        } catch (err) { console.error('Export failed', err); }
    }

    // Wire export buttons
    function wireExportButtons() {
        document.querySelectorAll('.export-chart-btn').forEach(btn=>{
            btn.addEventListener('click', (e)=>{
                const target = e.currentTarget.getAttribute('data-target');
                const chart = ({monthlyChart,categoryChart,yearlyChart})[target];
                if (!chart) return alert('Chart not loaded yet');
                downloadChartAsImage(chart, target + '_' + new Date().toISOString().slice(0,10) + '.png');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('generateAll').addEventListener('click', () => {
            const start = document.getElementById('globalStart').value;
            const end = document.getElementById('globalEnd').value;
            const categoryId = document.getElementById('globalCategory').value;
            
            // Main monthly call updates summary cards
            loadMonthly(start, end, categoryId, true);
            loadCategory(start, end, categoryId, false);
            
            // Yearly chart uses the year from the end date, but won't overwrite summary cards
            if (end) {
                const year = new Date(end).getFullYear();
                loadYearly(year, categoryId, false);
            }
        });

        // Initial load
        const today = new Date();
        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
        const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
        
        document.getElementById('globalStart').value = startOfMonth;
        document.getElementById('globalEnd').value = endOfMonth;
        
        loadMonthly(startOfMonth, endOfMonth, '', true);
        loadCategory(startOfMonth, endOfMonth, '', false);
        loadYearly(today.getFullYear(), '', false);

        wireExportButtons();
    });
</script>
@endpush
