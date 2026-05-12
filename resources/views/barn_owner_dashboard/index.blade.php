@extends('layouts.app')

@section('title', 'Dashboard')

@section('hero-text')
    Welcome back to&nbsp<span class="barn-name-highlight">{{ $currentBarn->barn_name ?? ($currentUser?->full_name . "'s Barn") }}</span>.
    Here's what's happening in your barn today. 🌾
@endsection
@section('hero-text-mobile')
    Welcome back, {{ $currentUser?->first_name ?? 'Barn Owner' }}! 🌾
@endsection
@push('styles')
<style>
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .kpi-card {
        background: linear-gradient(135deg, #7a5c00 0%, #5c4200 100%);
        border-radius: 12px;
        padding: 1.4rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: 0 4px 16px rgba(90, 60, 0, 0.25);
        border: 1px solid rgba(200, 150, 10, 0.3);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        user-select: none;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(90, 60, 0, 0.35);
    }

    .kpi-card:active { transform: translateY(-1px); }

    .kpi-card::after {
        content: 'Click to view details';
        position: absolute;
        bottom: 0.45rem;
        right: 0.7rem;
        font-size: 0.62rem;
        color: rgba(255,255,255,0.45);
        font-family: var(--font);
        letter-spacing: 0.03em;
        pointer-events: none;
    }

    .kpi-left {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.45rem;
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        object-fit: contain;
        flex-shrink: 0;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }

    .kpi-label {
        font-size: 0.76rem;
        font-weight: 600;
        color: rgba(255,255,255,0.78);
        letter-spacing: 0.04em;
        text-transform: uppercase;
        line-height: 1.3;
    }

    .kpi-value {
        font-family: var(--font);
        font-size: 2.85rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
        text-shadow: 0 2px 6px rgba(0,0,0,0.25);
        text-align: right;
        flex-shrink: 0;
    }

    .bottom-row {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 1.25rem;
        min-height: 450px; /* fallback */
    }

    .chart-card, .alert-card {
        background: var(--cream);
        border-radius: 12px;
        padding: 1.3rem 1.5rem;
        border: 1px solid var(--green-border);
        box-shadow: 0 2px 10px rgba(20,50,8,0.08);
        display: flex;
        flex-direction: column;
    }

    .card-title {
        font-size: 0.97rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 1rem;
        padding-bottom: 0.6rem;
        border-bottom: 2px solid var(--green-pale);
    }

    .chart-container {
        flex: 1;
        position: relative;
        min-height: 320px;
    }

    /* Table wrapper for horizontal scroll if needed */
    .table-wrapper {
        overflow-x: auto;
        flex: 1;
    }

    .supply-alert-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
        table-layout: auto;
    }

    .supply-alert-table thead th {
        padding: 0.65rem 0.8rem;
        background: var(--green-main);
        color: #fff;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.76rem;
        text-align: left;
        white-space: nowrap;
    }

    .supply-alert-table tbody td {
        padding: 0.7rem 0.8rem;
        border-bottom: 1px solid #e8f0e0;
        vertical-align: top;
    }

    .supply-alert-table tbody tr:last-child td {
        border-bottom: none;
    }

    .supply-alert-table td:nth-child(4),
    .supply-alert-table td:nth-child(5) { 
        text-align: right;
    }

    .stock-value {
        color: #c0392b;
        font-weight: 700;
    }

    .supply-id {
        color: var(--green-mid);
        font-weight: 700;
        font-size: 0.76rem;
        letter-spacing: 0.04em;
    }

    .reorder-value {
        color: #7a6000;
        font-weight: 600;
    }

    .empty-alert {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
        font-size: 0.9rem;
    }

    /* ══════════════════════════════════════════════
       KPI DETAIL MODALS (unchanged)
    ══════════════════════════════════════════════ */
    .kpi-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 40, 5, 0.6);
        z-index: 9000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .kpi-modal-backdrop.show { display: flex; }

    .kpi-modal {
        background: var(--cream);
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15,40,5,0.35);
        border: 1px solid var(--green-border);
        animation: kpiModalPop 0.28s cubic-bezier(0.34,1.56,0.64,1) both;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
    }

    @keyframes kpiModalPop {
        from { transform: scale(0.88) translateY(20px); opacity: 0; }
        to   { transform: scale(1)    translateY(0);    opacity: 1; }
    }

    .kpi-modal-header {
        background: var(--green-main);
        color: #fff;
        padding: 1rem 1.5rem;
        font-family: var(--font);
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .kpi-modal-count {
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(255,255,255,0.2);
        padding: 0.15rem 0.6rem;
        border-radius: 20px;
    }

    .kpi-modal-list {
        overflow-y: auto;
        padding: 0.6rem 0.8rem;
        flex: 1;
    }

    .kpi-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.8rem;
        padding: 0.85rem 1rem;
        margin-bottom: 0.4rem;
        background: #fff;
        border-radius: 8px;
        border: 1px solid var(--green-border);
        transition: background 0.15s, border-color 0.15s;
    }

    .kpi-item:hover { background: #f8fbf4; border-color: var(--green-mid); }
    .kpi-item:last-child { margin-bottom: 0; }

    .kpi-item-info { flex: 1; min-width: 0; }

    .kpi-item-name {
        font-family: var(--font);
        font-weight: 700;
        color: var(--text-dark);
        font-size: 0.88rem;
        margin-bottom: 0.15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kpi-item-detail {
        font-size: 0.76rem;
        color: #777;
        line-height: 1.4;
    }

    .kpi-item-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.3rem;
        flex-shrink: 0;
    }

    .kpi-badge {
        font-family: var(--font);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.18rem 0.55rem;
        border-radius: 20px;
        white-space: nowrap;
    }

    .kpi-badge-in    { background: #e8f5e0; color: #2e7d32; }
    .kpi-badge-out   { background: #ffebee; color: #c62828; }
    .kpi-badge-ok    { background: var(--green-pale); color: var(--green-dark); }
    .kpi-badge-low   { background: #fde8e8; color: #c0392b; }
    .kpi-badge-empty { background: #fff3cd; color: #856404; }

    .kpi-item-value {
        font-family: var(--font);
        font-size: 1.1rem;
        font-weight: 800;
    }

    .kpi-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #aaa;
        font-size: 0.9rem;
        line-height: 1.7;
    }

    .kpi-modal-footer {
        padding: 0.9rem 1.5rem 1.2rem;
        border-top: 1px solid var(--green-border);
        flex-shrink: 0;
    }

    .kpi-close-btn {
        width: 100%;
        background: transparent;
        color: var(--green-dark);
        border: 1.5px solid var(--green-border);
        border-radius: 8px;
        padding: 0.6rem;
        font-family: var(--font);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }

    .kpi-close-btn:hover { background: var(--green-pale); }

    /* ========== MOBILE RESPONSIVENESS ========== */
    @media (max-width: 992px) {
        .bottom-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .chart-container {
            min-height: 280px;
        }
    }

    @media (max-width: 768px) {
        .kpi-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .kpi-card {
            padding: 1rem 1.2rem;
        }
        .kpi-icon {
            width: 44px;
            height: 44px;
        }
        .kpi-value {
            font-size: 2.2rem;
        }
        .kpi-label {
            font-size: 0.7rem;
        }
        .kpi-card::after {
            font-size: 0.55rem;
            bottom: 0.3rem;
            right: 0.5rem;
        }

        /* Table stacking */
        .table-wrapper {
            overflow-x: hidden;
        }
        .supply-alert-table thead {
            display: none;
        }
        .supply-alert-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid var(--green-border);
            border-radius: 10px;
            background: #fff;
            padding: 0.5rem;
        }
        .supply-alert-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0.8rem;
            border-bottom: 1px solid #e8f0e0;
            align-items: center;
        }
        .supply-alert-table td:last-child {
            border-bottom: none;
        }
        .supply-alert-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--green-mid);
            width: 45%;
            flex-shrink: 0;
        }
        /* Right align number columns */
        .supply-alert-table td:nth-child(4),
        .supply-alert-table td:nth-child(5) {
            text-align: left;
        }
        .supply-alert-table td:nth-child(4)::before,
        .supply-alert-table td:nth-child(5)::before {
            text-align: left;
        }

        .chart-container {
            min-height: 240px;
        }
    }

    @media (max-width: 576px) {
        .bottom-row {
            gap: 0.8rem;
        }
        .chart-card, .alert-card {
            padding: 1rem;
        }
        .kpi-icon {
            width: 38px;
            height: 38px;
        }
        .kpi-value {
            font-size: 1.8rem;
        }
    }
</style>
@endpush

@section('content')

<div class="kpi-row">
    <div class="kpi-card" onclick="openKpiModal('inventory', 'Inventory On Hand', '📦')">
        <div class="kpi-left">
            <img src="/images/icons/KPITotalSupplies.png" class="kpi-icon" alt="Total Supplies">
            <div class="kpi-label">Inventory On Hand</div>
        </div>
        <div class="kpi-value">{{ $totalSupplies }}</div>
    </div>

    <div class="kpi-card" onclick="openKpiModal('stock-in', 'Supplies Added Today', '📥')">
        <div class="kpi-left">
            <img src="/images/icons/KPIStockIn.png" class="kpi-icon" alt="Stock In">
            <div class="kpi-label">Supplies Added Today</div>
        </div>
        <div class="kpi-value">{{ $suppliesAddedToday }}</div>
    </div>

    <div class="kpi-card" onclick="openKpiModal('stock-out', 'Supplies Used Today', '📤')">
        <div class="kpi-left">
            <img src="/images/icons/KPIStockOut.png" class="kpi-icon" alt="Stock Out">
            <div class="kpi-label">Supplies Used Today</div>
        </div>
        <div class="kpi-value">{{ $suppliesUsedToday }}</div>
    </div>
</div>

<div class="bottom-row">
    <!-- Pie Chart -->
    <div class="chart-card">
        <div class="card-title">🟢 Supply by Category</div>
        <div class="chart-container">
            @if($categoryData->count() > 0)
                <canvas id="categoryChart"></canvas>
            @else
                <div class="empty-alert">No supply data available yet.</div>
            @endif
        </div>
    </div>

    <!-- Stock Alert Table -->
    <div class="alert-card">
        <div class="card-title">
            🔴 Supply Alert 
            <span style="font-size:0.75rem; font-weight:500; color:#777; margin-left:0.6rem;">
                Items at or below reorder level
            </span>
        </div>

        @if($lowStockSupplies->count() > 0)
            <div class="table-wrapper">
                <table class="supply-alert-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supply</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockSupplies as $supply)
                        <tr>
                            <td data-label="ID">
                                <a href="{{ route('inventory.show', $supply->id) }}" class="supply-id text-decoration-none">
                                    {{ strtoupper(substr($supply->category->category_name ?? 'XX', 0, 3)) }}{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td data-label="Supply">{{ $supply->supply_name }}</td>
                            <td data-label="Category">{{ $supply->category->category_name ?? '—' }}</td>
                            <td data-label="Stock">
                                <span class="stock-value">{{ $supply->stock }}</span>
                            </td>
                            <td data-label="Reorder Level">
                                <span class="reorder-value">{{ $supply->reorder_level }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-alert">
                ✅ All supplies are above reorder levels.
            </div>
        @endif
    </div>
</div>

@endsection

{{-- KPI MODAL (unchanged) --}}
<div class="kpi-modal-backdrop" id="kpiModal" onclick="handleKpiBackdropClick(event)">
    <div class="kpi-modal">
        <div class="kpi-modal-header">
            <span id="kpiModalTitle">Details</span>
            <span class="kpi-modal-count" id="kpiModalCount"></span>
        </div>
        <div class="kpi-modal-list" id="kpiModalList"></div>
        <div class="kpi-modal-footer">
            <button class="kpi-close-btn" onclick="closeKpiModal()">Close</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    @if($categoryData->count() > 0)
    const ctx = document.getElementById('categoryChart').getContext('2d');
    let categoryChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: @json($categoryData->pluck('category_name')),
            datasets: [{
                data: @json($categoryData->pluck('total')),
                backgroundColor: ['#c8960a', '#4e9a30', '#8a6200', '#6bbf42', '#3d7a25', '#e8b820', '#2d5a1b', '#b8dda0'],
                borderColor: '#f7f9f3',
                borderWidth: 3,
                hoverOffset: 12,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: window.innerWidth <= 768 ? 'bottom' : 'right',
                    labels: {
                        font: { family: 'Inconsolata', size: 12, weight: '600' },
                        color: '#1a2e10',
                        padding: 12,
                        boxWidth: 14
                    }
                }
            }
        }
    });

    // Update legend position on window resize
    function adjustChartLegend() {
        if (categoryChart) {
            const isMobile = window.innerWidth <= 768;
            categoryChart.options.plugins.legend.position = isMobile ? 'bottom' : 'right';
            categoryChart.update();
        }
    }
    window.addEventListener('resize', adjustChartLegend);
    @endif

    const kpiRoutes = {
        'inventory': '{{ route("dashboard.kpi.inventory") }}',
        'stock-in':  '{{ route("dashboard.kpi.stockIn") }}',
        'stock-out': '{{ route("dashboard.kpi.stockOut") }}',
    };

    function openKpiModal(type, title, icon) {
        const modal = document.getElementById('kpiModal');
        document.getElementById('kpiModalTitle').textContent = icon + ' ' + title;
        document.getElementById('kpiModalCount').textContent = '';
        document.getElementById('kpiModalList').innerHTML = `<div class="kpi-empty">⏳ Loading...</div>`;
        modal.classList.add('show');

        fetch(kpiRoutes[type], {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(data => renderKpiModal(data, type))
        .catch(() => {
            document.getElementById('kpiModalList').innerHTML = `<div class="kpi-empty">❌ Failed to load data.<br>Please try again.</div>`;
        });
    }

    function renderKpiModal(items, type) {
        const list = document.getElementById('kpiModalList');
        document.getElementById('kpiModalCount').textContent = items.length + ' record' + (items.length !== 1 ? 's' : '');

        if (!items || items.length === 0) {
            const messages = {
                'inventory': 'No active supplies in this barn yet.',
                'stock-in':  'No stock-in transactions recorded today.',
                'stock-out': 'No stock-out transactions recorded today.',
            };
            list.innerHTML = `<div class="kpi-empty">${messages[type] || 'No records found.'}</div>`;
            return;
        }

        list.innerHTML = items.map(item => {
            let badgeClass, badgeText;
            if (item.status === 'stock_in') {
                badgeClass = 'kpi-badge-in';  badgeText = 'STOCK IN';
            } else if (item.status === 'stock_out') {
                badgeClass = 'kpi-badge-out'; badgeText = 'STOCK OUT';
            } else if (item.status === 'In Stock') {
                badgeClass = 'kpi-badge-ok';  badgeText = 'IN STOCK';
            } else if (item.status === 'Low Stock') {
                badgeClass = 'kpi-badge-low'; badgeText = 'LOW STOCK';
            } else {
                badgeClass = 'kpi-badge-empty'; badgeText = 'OUT OF STOCK';
            }

            const valueColor = item.status === 'stock_in'  ? '#2e7d32'
                             : item.status === 'stock_out' ? '#c62828'
                             : item.status === 'Low Stock' ? '#c0392b'
                             : 'var(--green-dark)';

            return `
                <div class="kpi-item">
                    <div class="kpi-item-info">
                        <div class="kpi-item-name">${item.name}</div>
                        <div class="kpi-item-detail">${item.detail}</div>
                        ${item.unit_cost != null ? `<div class="kpi-item-detail">Unit Cost: <strong>₱${item.unit_cost}</strong></div>` : ''}
                        ${item.sub && item.sub !== 'No remarks' ? `<div class="kpi-item-detail" style="font-style:italic;">${item.sub}</div>` : ''}
                    </div>
                    <div class="kpi-item-right">
                        <span class="kpi-badge ${badgeClass}">${badgeText}</span>
                        <span class="kpi-item-value" style="color:${valueColor};">${item.value}</span>
                    </div>
                </div>`;
        }).join('');
    }

    function closeKpiModal() {
        document.getElementById('kpiModal').classList.remove('show');
    }

    function handleKpiBackdropClick(e) {
        if (e.target === document.getElementById('kpiModal')) closeKpiModal();
    }
</script>
@endpush