@extends('layouts.app')

@section('title', 'Reports')

@section('hero-text')
    Create the monthly reports here for your barn 📝
@endsection
@section('hero-text-mobile')
    📝 Create barn reports 
@endsection
@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .page-title {
        font-family: var(--font);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--gold-mid);
    }

    .btn-export {
        background: var(--green-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.1rem;
        font-family: var(--font);
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-export:hover { background: var(--green-dark); transform: translateY(-1px); }
    .btn-export:disabled { opacity: 0.4; cursor: not-allowed; }

    .report-type-bar {
        background: var(--cream);
        border: 1px solid var(--green-border);
        border-radius: 10px;
        padding: 0.9rem 1.2rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        box-shadow: 0 1px 6px rgba(20,50,8,0.06);
    }

    .report-type-label {
        font-family: var(--font);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-mid);
        white-space: nowrap;
    }

    .report-type-select {
        font-family: var(--font);
        font-size: 0.88rem;
        background: #fff;
        border: 1.5px solid var(--green-border);
        border-radius: 7px;
        padding: 0.45rem 0.75rem;
        color: var(--text-dark);
        min-width: 280px;
        outline: none;
    }

    .report-type-select:focus { border-color: var(--green-main); }

    .report-desc {
        font-family: var(--font);
        font-size: 0.78rem;
        color: #888;
        font-style: italic;
        margin-left: auto;
        max-width: 380px;
        text-align: right;
    }

    .report-card {
        background: var(--cream);
        border-radius: 12px;
        border: 1px solid var(--green-border);
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(20,50,8,0.08);
    }

    .filter-bar {
        background: #f0f7e8;
        border-bottom: 1px solid var(--green-border);
        padding: 0.8rem 1.2rem;
        display: flex;
        align-items: flex-end;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .filter-label {
        font-family: var(--font);
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-mid);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .filter-control {
        font-family: var(--font);
        font-size: 0.82rem;
        background: #fff;
        border: 1.5px solid var(--green-border);
        border-radius: 6px;
        padding: 0.38rem 0.65rem;
        color: var(--text-dark);
        outline: none;
        min-width: 130px;
    }

    .filter-control:focus { border-color: var(--green-main); }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        margin-left: auto;
        align-items: flex-end;
    }

    .btn-generate {
        background: var(--green-main);
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 0.42rem 1rem;
        font-family: var(--font);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-generate:hover { background: var(--green-dark); }

    .btn-summary {
        background: transparent;
        color: var(--green-dark);
        border: 1.5px solid var(--green-border);
        border-radius: 7px;
        padding: 0.42rem 0.9rem;
        font-family: var(--font);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-summary:hover { background: var(--green-pale); }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3.5rem;
        color: var(--gold-mid);
        margin-bottom: 0.8rem;
    }

    .rpt-table-wrap { overflow-x: auto; display: none; }
    .rpt-table-wrap.show { display: block; }

    .rpt-table {
        width: 100%;
        border-collapse: collapse;
        font-family: var(--font);
        font-size: 0.78rem;
    }

    .rpt-table thead tr { background: var(--green-main); }
    .rpt-table thead th {
        padding: 0.55rem 0.8rem;
        color: #fff;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .rpt-table tbody tr {
        border-bottom: 1px solid #e8f0e0;
        transition: background 0.12s;
    }

    .rpt-table tbody tr:hover { background: #f3faea; }

    .rpt-table tbody td {
        padding: 0.55rem 0.8rem;
        color: var(--text-dark);
        vertical-align: middle;
    }

    .rpt-table tfoot tr { background: #e8f5e0; font-weight: 700; }
    .rpt-table tfoot td { padding: 0.55rem 0.8rem; font-weight: 800; color: var(--green-dark); }

    .badge-in    { background:#d4edbc; color:#2d5a1b; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-out   { background:#fde8e8; color:#c0392b; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-low   { background:#fde8e8; color:#c0392b; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-ok    { background:#d4edbc; color:#2d5a1b; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-empty { background:#fff3cd; color:#856404; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }

    .no-results td {
        text-align: center;
        padding: 2.5rem;
        color: #aaa;
        font-size: 0.85rem;
    }

    .rpt-loading {
        padding: 3rem;
        text-align: center;
        color: var(--green-mid);
        font-size: 0.88rem;
        display: none;
    }

    .rpt-loading.show { display: block; }

    .summary-panel {
        display: none;
        background: #f0f7e8;
        border-top: 1px solid var(--green-border);
        padding: 1rem 1.4rem;
    }

    .summary-panel.show { display: block; }

    .summary-title {
        font-family: var(--font);
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--text-mid);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.7rem;
    }

    .summary-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .summary-item {
        background: var(--cream);
        border: 1px solid var(--green-border);
        border-radius: 8px;
        padding: 0.6rem 1rem;
        min-width: 130px;
        text-align: center;
    }

    .summary-value {
        font-family: var(--font);
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--green-dark);
        display: block;
    }

    .summary-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .fb-toast {
        position: fixed;
        bottom: 1.5rem; right: 1.5rem;
        z-index: 99999;
        background: var(--green-dark);
        color: #fff;
        border-radius: 10px;
        padding: 0.8rem 1.2rem;
        font-family: var(--font);
        font-size: 0.88rem;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(20,50,8,0.3);
        transform: translateY(20px);
        opacity: 0;
        transition: transform 0.3s, opacity 0.3s;
        max-width: 320px;
        pointer-events: none;
    }

    .fb-toast.show  { transform: translateY(0); opacity: 1; }
    .fb-toast.error { background: #922b21; }

    @media print {
        .fb-navbar, .fb-sidebar, .report-type-bar, .filter-bar, .page-header, .fb-hero { display: none !important; }
        .fb-main { position: static !important; overflow: visible !important; }
        .content-area { padding: 0 !important; }
        .report-card { border: none !important; box-shadow: none !important; }
        .summary-panel { display: block !important; }
        .rpt-table-wrap { display: block !important; }
        .empty-state { display: none !important; }
    }

    /* === Mobile Responsiveness Additions === */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }
    .btn-export {
        width: 100%;
        justify-content: center;
    }
    .report-type-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .report-type-select {
        width: 100%;
    }
    .report-desc {
        margin-left: 0;
        text-align: left;
        max-width: 100%;
    }
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-group {
        width: 100%;
    }
    .filter-control {
        width: 100%;
        min-width: unset;
    }
    .filter-actions {
        margin-left: 0;
        flex-direction: column;
        gap: 0.5rem;
    }
    .btn-generate, .btn-summary {
        width: 100%;
        text-align: center;
    }
    .summary-grid {
        flex-direction: column;
    }
    .summary-item {
        min-width: unset;
    }
    .rpt-table-wrap {
        overflow-x: auto;
    }
    .rpt-table {
        min-width: 600px;  /* ensures horizontal scroll on small screens */
    }
}
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">Reports</div>
    <button class="btn-export" id="btnExport" disabled onclick="exportPDF()">
        📄 Export Report
    </button>
</div>

<div class="report-type-bar">
    <span class="report-type-label">Report Type:</span>
    <select class="report-type-select" id="reportTypeSelect" onchange="onReportTypeChange()">
        <option value="">— Select a report type —</option>
        <option value="stock_movement">Stock Movement History</option>
        <option value="current_inventory">Current Stock Inventory</option>
        <option value="low_stock">Low Stock / Reorder Alert</option>
        <option value="valuation">Inventory Valuation</option>
        <option value="usage_analysis">Supply Usage / Consumption Analysis</option>
        <option value="barn_summary">Barn Inventory Summary</option>
    </select>
    <span class="report-desc" id="reportDesc"></span>
</div>

<div class="report-card">

    <div class="filter-bar" id="filterBar">
        <div id="filterControls" style="display:flex;gap:0.7rem;flex-wrap:wrap;align-items:flex-end;flex:1;">
            <div style="font-family:var(--font);font-size:0.82rem;color:#aaa;align-self:center;">
                Select a report type to see its filters.
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn-summary" id="btnSummary" onclick="toggleSummary()">📊 Summary</button>
            <button class="btn-generate" id="btnGenerate" onclick="generateReport()" disabled>Generate Report</button>
        </div>
    </div>

    <div class="rpt-loading" id="rptLoading">⏳ Generating report...</div>

    <div class="empty-state" id="emptyState">
        <span class="empty-icon">📄</span>
        <h3>Generate a Report</h3>
        <p>To create a Report, use the Filters above and click "Generate Report"</p>
    </div>

    <div class="rpt-table-wrap" id="rptTableWrap">
        <table class="rpt-table" id="rptTable">
            <thead id="rptThead"></thead>
            <tbody id="rptTbody"></tbody>
            <tfoot id="rptTfoot"></tfoot>
        </table>
    </div>

    <div class="summary-panel" id="summaryPanel">
        <div class="summary-title">📊 Report Summary</div>
        <div class="summary-grid" id="summaryGrid"></div>
    </div>

</div>

<div class="fb-toast" id="fbToast"></div>

<script>
    const barnName     = {!! json_encode($barn->barn_name) !!};
    const categories   = {!! json_encode($categories) !!};
    const supplies     = {!! json_encode($supplies->map(fn($s) => ['id' => $s->id, 'name' => $s->supply_name])) !!};
    const staffUsers   = {!! json_encode($staffUsers) !!};
    const reportRoute  = "{{ route('reports.generate') }}";
    const csrfToken    = "{{ csrf_token() }}";
</script>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
const REPORTS = {
    stock_movement: {
        desc: 'Complete history of all stock-in and stock-out transactions.',
        filters: ['date_from','date_to','txn_type','category','supply','user'],
        columns: ['Date','Supply','Category','Type','Qty','Unit Cost','Staff','Supplier','Remarks','Balance After'],
        keys:    ['date','supply','category','type','quantity','unit_cost','staff','supplier','remarks','balance'],
    },
    current_inventory: {
        desc: 'Real-time snapshot of all supplies with stock levels and status.',
        filters: ['snapshot_date','category','stock_status'],
        columns: ['Supply','Category','Stock','Reorder Level','Status'],
        keys:    ['supply','category','stock','reorder_level','status'],
    },
    low_stock: {
        desc: 'Supplies at or below reorder level — take timely action.',
        filters: ['snapshot_date','category','criticality'],
        columns: ['Supply','Category','Stock','Reorder Level','Shortage','Criticality'],
        keys:    ['supply','category','stock','reorder_level','shortage','criticality'],
    },
    valuation: {
        desc: 'Total monetary value of inventory broken down by supply and category.',
        filters: ['snapshot_date','category'],
        columns: ['Supply','Category','Stock','Avg Unit Cost','Total Value'],
        keys:    ['supply','category','stock','avg_unit_cost','total_value'],
    },
    usage_analysis: {
        desc: 'Consumption analysis: total used, avg daily usage, and trends.',
        filters: ['date_from','date_to','category','supply'],
        columns: ['Supply','Category','Total Used','Avg Daily','Days Active','Last Used'],
        keys:    ['supply','category','total_used','avg_daily','days_active','last_used'],
    },
    barn_summary: {
        desc: 'High-level overview: supply counts, stock value, low-stock items, staff.',
        filters: ['snapshot_date','category'],
        columns: ['Metric','Value'],
        keys:    ['metric','value'],
    },
};

const FILTER_TEMPLATES = {
    date_from: () => filterGroup('From Date', `<input type="date" class="filter-control" id="ff_date_from">`),
    date_to:   () => filterGroup('To Date',   `<input type="date" class="filter-control" id="ff_date_to">`),
    snapshot_date: () => filterGroup('Snapshot Date', `<input type="date" class="filter-control" id="ff_snapshot_date">`),
    txn_type:  () => filterGroup('Transaction Type', `
        <select class="filter-control" id="ff_txn_type">
            <option value="">All</option>
            <option value="stock_in">Stock In</option>
            <option value="stock_out">Stock Out</option>
        </select>`),
    category:  () => filterGroup('Category', `
        <select class="filter-control" id="ff_category">
            <option value="">All Categories</option>
            ${categories.map(c => `<option value="${c.id}">${c.category_name}</option>`).join('')}
        </select>`),
    supply:    () => filterGroup('Supply', `
        <select class="filter-control" id="ff_supply">
            <option value="">All Supplies</option>
            ${supplies.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}
        </select>`),
    user:      () => filterGroup('Staff', `
        <select class="filter-control" id="ff_user">
            <option value="">All Staff</option>
            ${staffUsers.map(u => `<option value="${u.id}">${u.name}</option>`).join('')}
        </select>`),
    stock_status: () => filterGroup('Stock Status', `
        <select class="filter-control" id="ff_stock_status">
            <option value="">All</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
        </select>`),
    criticality: () => filterGroup('Criticality', `
        <select class="filter-control" id="ff_criticality">
            <option value="">All</option>
            <option value="critical">Critical (&lt;50% reorder)</option>
            <option value="low">Low Stock</option>
        </select>`),
};

function filterGroup(label, html) {
    return `<div class="filter-group"><span class="filter-label">${label}</span>${html}</div>`;
}

function onReportTypeChange() {
    const type = document.getElementById('reportTypeSelect').value;
    const desc = document.getElementById('reportDesc');

    document.getElementById('emptyState').style.display = 'block';
    document.getElementById('rptTableWrap').classList.remove('show');
    document.getElementById('summaryPanel').classList.remove('show');
    document.getElementById('btnSummary').classList.remove('show');
    document.getElementById('btnExport').disabled = true;
    document.getElementById('btnGenerate').disabled = true;

    if (!type) {
        desc.classList.remove('show');
        document.getElementById('filterControls').innerHTML = 
            `<div style="font-family:var(--font);font-size:0.82rem;color:#aaa;align-self:center;">Select a report type to see its filters.</div>`;
        return;
    }

    const rpt = REPORTS[type];
    desc.textContent = rpt.desc;
    desc.classList.add('show');

    document.getElementById('filterControls').innerHTML = 
        rpt.filters.map(f => FILTER_TEMPLATES[f] ? FILTER_TEMPLATES[f]() : '').join('');

    document.getElementById('btnGenerate').disabled = false;
}

function generateReport() {
    const type = document.getElementById('reportTypeSelect').value;
    if (!type) return;

    const filters = { report_type: type };
    const ids = ['ff_date_from','ff_date_to','ff_snapshot_date','ff_txn_type',
                 'ff_category','ff_supply','ff_user','ff_stock_status','ff_criticality'];

    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) filters[id.replace('ff_', '')] = el.value;
    });

    document.getElementById('rptLoading').classList.add('show');
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('rptTableWrap').classList.remove('show');
    document.getElementById('summaryPanel').classList.remove('show');

    fetch(reportRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(filters),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('rptLoading').classList.remove('show');
        renderTable(data, type);
        renderSummary(data.summary, type);
        document.getElementById('btnSummary').classList.add('show');
        document.getElementById('btnExport').disabled = false;
    })
    .catch(() => {
        document.getElementById('rptLoading').classList.remove('show');
        showToast('Failed to generate report. Please try again.', 'error');
    });
}

function renderTable(data, type) {
    const rpt = REPORTS[type];
    const thead = document.getElementById('rptThead');
    const tbody = document.getElementById('rptTbody');
    const tfoot = document.getElementById('rptTfoot');

    thead.innerHTML = `<tr>${rpt.columns.map(c => `<th>${c}</th>`).join('')}</tr>`;

    if (!data.rows || data.rows.length === 0) {
        tbody.innerHTML = `<tr class="no-results"><td colspan="${rpt.columns.length}">No data found for the selected filters.</td></tr>`;
        tfoot.innerHTML = '';
    } else {
        tbody.innerHTML = data.rows.map(row => `
            <tr>${rpt.keys.map(k => `<td>${formatCell(k, row[k])}</td>`).join('')}</tr>
        `).join('');
        tfoot.innerHTML = data.totals ? `<tr>${rpt.keys.map((k,i) => `<td>${i === 0 ? 'TOTAL' : (data.totals[k] ?? '')}</td>`).join('')}</tr>` : '';
    }

    document.getElementById('rptTableWrap').classList.add('show');
}

function formatCell(key, val) {
    if (val === null || val === undefined || val === '') return '<span style="color:#ccc;">—</span>';
    if (key === 'type') return val === 'stock_in' ? '<span class="badge-in">Stock In</span>' : '<span class="badge-out">Stock Out</span>';
    if (key === 'status') {
        const cls = val === 'In Stock' ? 'badge-ok' : (val === 'Out of Stock' ? 'badge-empty' : 'badge-low');
        return `<span class="${cls}">${val}</span>`;
    }
    if (key === 'criticality') {
        return val === 'Critical' ? '<span class="badge-out">Critical</span>' : '<span class="badge-low">Low Stock</span>';
    }
    if (key === 'total_value' || key === 'avg_unit_cost' || key === 'unit_cost') { const n = parseFloat(val); return isNaN(n) ? val : '₱' + n.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}); }
    return val;
}

function renderSummary(summary) {
    if (!summary) return;
    const grid = document.getElementById('summaryGrid');
    grid.innerHTML = Object.entries(summary).map(([label, value]) => `
        <div class="summary-item">
            <span class="summary-value">${value}</span>
            <span class="summary-label">${label}</span>
        </div>
    `).join('');
}

function toggleSummary() {
    document.getElementById('summaryPanel').classList.toggle('show');
}

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const type = document.getElementById('reportTypeSelect').value;
    if (!type) return;

    const doc = new jsPDF({ orientation: 'landscape' });
    const rpt = REPORTS[type];
    const date = new Date().toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });

    doc.setFont('courier', 'bold');
    doc.setFontSize(16);
    doc.text('Farmbase — ' + barnName, 14, 16);
    doc.setFontSize(11);
    doc.text(rpt.desc.split(':')[0].trim(), 14, 23);
    doc.setFont('courier', 'normal');
    doc.setFontSize(9);
    doc.setTextColor(100);
    doc.text('Generated: ' + date, 14, 29);
    doc.setTextColor(0);

    const summaryItems = document.querySelectorAll('.summary-item');
    let sy = 35;
    if (summaryItems.length > 0) {
        doc.setFontSize(8);
        doc.setFont('courier', 'bold');
        doc.text('SUMMARY', 14, sy);
        sy += 4;
        summaryItems.forEach((item, i) => {
            const val = item.querySelector('.summary-value').textContent;
            const label = item.querySelector('.summary-label').textContent;
            doc.setFont('courier', 'normal');
            doc.setFontSize(8);
            doc.text(`${label}: ${val}`, 14 + (i % 4) * 70, sy + Math.floor(i / 4) * 6);
        });
        sy += Math.ceil(summaryItems.length / 4) * 6 + 8;
    }

    const thead = document.getElementById('rptThead');
    const tbody = document.getElementById('rptTbody');
    const tfoot = document.getElementById('rptTfoot');

    const headers = Array.from(thead.querySelectorAll('th')).map(th => th.textContent);
    const bodyRows = Array.from(tbody.querySelectorAll('tr')).map(tr =>
        Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim())
    );
    const footRows = Array.from(tfoot.querySelectorAll('tr')).map(tr =>
        Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim())
    );

    doc.autoTable({
        startY: sy,
        head: [headers],
        body: bodyRows,
        foot: footRows,
        styles: { font: 'courier', fontSize: 7.5, cellPadding: 2 },
        headStyles: { fillColor: [78, 154, 48], textColor: 255, fontStyle: 'bold' },
        footStyles: { fillColor: [232, 245, 224], textColor: [45, 90, 27], fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [247, 249, 243] },
        margin: { left: 14, right: 14 },
    });

    const reportLabel = document.getElementById('reportTypeSelect').selectedOptions[0].text;
    doc.save(`Farmbase_${reportLabel.replace(/\s+/g,'_')}_${date.replace(/\s+/g,'_')}.pdf`);

    showToast('Report exported as PDF.');
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('fbToast');
    t.textContent = (type === 'success' ? '✅ ' : '❌ ') + msg;
    t.className = 'fb-toast show' + (type === 'error' ? ' error' : '');
    setTimeout(() => t.classList.remove('show'), 3500);
}

@if(session('success'))
    showToast("{{ session('success') }}");
@endif
</script>
@endpush