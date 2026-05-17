@extends('layouts.app_staff')

@section('title', 'Supply List')

@section('hero-text')
    Welcome back to&nbsp;<strong>{{ $barn->barn_name }}</strong>, {{ Auth::user()->first_name }}. Let's get to work. 🌾
@endsection

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .page-title {
        font-family: var(--font);
        font-size: 1.48rem;
        font-weight: 800;
        color: var(--gold-mid);
    }

    .btn-history {
        background: var(--green-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.2rem;
        font-family: var(--font);
        font-size: 0.87rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .btn-history:hover { background: var(--green-dark); }

    .table-card {
        background: var(--cream);
        border-radius: 14px;
        border: 1px solid var(--green-border);
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(20,50,8,0.08);
    }

    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid var(--green-border);
        background: #f0f7e8;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .tbl-title {
        font-family: var(--font);
        font-size: 0.97rem;
        font-weight: 800;
        color: var(--text-dark);
    }

    .search-input {
        background: #fff;
        border: 1.6px solid var(--green-border);
        border-radius: 25px;
        padding: 0.45rem 1rem 0.45rem 2.3rem;
        font-family: var(--font);
        font-size: 0.84rem;
        width: 240px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%234e9a30' stroke-width='2.6'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.85rem center;
    }

    .txn-table {
        width: 100%;
        border-collapse: collapse;
        font-family: var(--font);
        font-size: 0.84rem;
    }

    .txn-table thead th {
        padding: 0.75rem 0.9rem;
        background: var(--green-main);
        color: #fff;
        font-weight: 700;
        font-size: 0.76rem;
        text-transform: uppercase;
        text-align: left;
    }

    .txn-table tbody tr {
        border-bottom: 1px solid #e8f0e0;
        transition: background 0.2s;
    }

    .txn-table tbody tr:hover { background: #f3faea; }

    .txn-table td {
        padding: 0.8rem 0.9rem;
        vertical-align: middle;
    }

    .supply-thumb, .supply-thumb-placeholder {
        width: 46px; height: 46px;
        border-radius: 8px;
        border: 1px solid var(--green-border);
    }

    .supply-thumb-placeholder {
        background: #e8f0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #aaa;
    }

    .supply-id-tag { 
        font-weight: 700; 
        color: var(--green-mid); 
        font-size: 0.74rem; 
        letter-spacing: 0.04em; 
    }

    .badge-status {
        padding: 0.28rem 0.7rem;
        border-radius: 20px;
        font-size: 0.73rem;
        font-weight: 700;
    }

    .action-btns { display: flex; gap: 0.5rem; }

    .btn-action {
        padding: 0.45rem 0.95rem;
        border: none;
        border-radius: 7px;
        font-family: var(--font);
        font-size: 0.81rem;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-stock-in  { background: var(--green-main); color: #fff; }
    .btn-stock-out { background: #c0392b; color: #fff; }

    .btn-action:hover { transform: translateY(-1px); filter: brightness(1.1); }

    /* Toast Notification */
    .fb-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #2e7d32;
        color: white;
        padding: 14px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        font-family: var(--font);
        font-size: 0.95rem;
        font-weight: 600;
        display: none;
        align-items: center;
        gap: 8px;
        z-index: 10000;
        min-width: 280px;
        transition: all 0.3s ease;
    }

    .fb-toast.show { display: flex; }
    .fb-toast.error { background: #c62828; }

     /* PAGINATION STYLES */
    .pagination-wrapper {
        padding: 1rem 1.2rem;
        border-top: 1px solid var(--green-border);
        background: #ffffff;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.3rem;
        flex-wrap: wrap;
        margin: 0;
    }
    .pagination .page-item .page-link {
        background: #fff;
        border: 1px solid var(--green-border);
        color: var(--green-dark);
        font-family: var(--font);
        font-size: 0.8rem;
        padding: 0.35rem 0.7rem;
        border-radius: 5px;
        transition: 0.1s;
    }
    .pagination .page-item.active .page-link {
        background: var(--green-main);
        border-color: var(--green-main);
        color: #fff;
    }
    .pagination .page-item .page-link:hover {
        background: var(--green-pale);
        transform: translateY(-1px);
    }


    /* Mobile Responsiveness Improvements */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .btn-history {
            width: 100%;
            justify-content: center;
            padding: 0.75rem 1.2rem;
            font-size: 0.9rem;
        }

        .table-toolbar {
            flex-direction: column;
            align-items: stretch;
            padding: 1rem 1.1rem;
            gap: 1rem;
        }

        .search-input {
            width: 100%;
            max-width: none;
            font-size: 0.87rem;
            padding: 0.55rem 1rem 0.55rem 2.4rem;
        }

        /* Stacked Mobile Table */
        .txn-table thead { display: none; }

        .txn-table tbody tr {
            display: block;
            background: #fff;
            margin: 0.9rem 0.8rem;
            padding: 1.2rem;
            border-radius: 14px;
            border: 1px solid var(--green-border);
            box-shadow: 0 3px 12px rgba(20,50,8,0.07);
        }

        .txn-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed #e0e9d4;
            align-items: center;
        }

        .txn-table td:last-child { 
            border-bottom: none; 
            padding-top: 1.2rem;
            justify-content: center;
        }

        .txn-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--green-mid);
            width: 135px;
            flex-shrink: 0;
        }

        .txn-table td[data-label="Image"]::before { display: none; }
        .txn-table td[data-label="Image"] { 
            justify-content: center; 
            border-bottom: 2px solid var(--green-pale);
            padding: 1rem 0;
        }

        .supply-thumb, .supply-thumb-placeholder {
            width: 54px; 
            height: 54px;
        }

        .action-btns { 
            width: 100%; 
            justify-content: center; 
            gap: 0.8rem; 
            flex-wrap: wrap;
        }

        .btn-action { 
            flex: 1; 
            max-width: 160px; 
            padding: 0.65rem 1rem; 
            font-size: 0.86rem; 
        }
    }

    @media (max-width: 576px) {
        .fb-modal {
            max-width: 100% !important;
            margin: 1rem;
        }
        
        .modal-form .form-select,
        .modal-form .form-control {
            font-size: 1rem; 
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">Supply List</div>
    <button onclick="openHistoryModal()" class="btn-history">Transaction History</button>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <span class="tbl-title">All Supplies</span>
        <input type="text" class="search-input" id="searchInput" placeholder="Search supplies..." oninput="filterTable()">
    </div>

    <div style="overflow-x:auto;">
        <table class="txn-table" id="txnTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>ID</th>
                    <th>Supply</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder</th>
                    <th>Status</th>
                    <th>Last Movement</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplies as $supply)
                @php
                    $catName = $supply->category->category_name ?? 'N/A';
                    $supId   = strtoupper(substr($catName, 0, 3)) . str_pad($supply->id, 4, '0', STR_PAD_LEFT);
                    $isOut   = $supply->stock == 0;
                    $isLow   = $supply->stock <= $supply->reorder_level && !$isOut;
                    $badge   = $isOut ? 'badge-out' : ($isLow ? 'badge-low' : 'badge-ok');
                    $label   = $isOut ? 'Out of Stock' : ($isLow ? 'Low Stock' : 'In Stock');
                    $lastTxn = $supply->transactions->first();
                    $lastMove = $lastTxn ? \Carbon\Carbon::parse($lastTxn->created_at)->format('M j, Y') : '—';
                @endphp
                <tr data-name="{{ strtolower($supply->supply_name) }}" 
                    data-category="{{ strtolower($catName) }}">
                    <td data-label="Image">
                        @if($supply->supply_img_path)
                            <img src="{{ $supply->supply_img_path }}" class="supply-thumb" alt="{{ $supply->supply_name }}" loading="lazy">
                        @else
                            <div class="supply-thumb-placeholder">📦</div>
                        @endif
                    </td>
                    <td data-label="ID"><span class="supply-id-tag">{{ $supId }}</span></td>
                    <td data-label="Supply"><strong>{{ $supply->supply_name }}</strong></td>
                    <td data-label="Category">{{ $catName }}</td>
                    <td data-label="Stock">{{ $supply->stock }}</td>
                    <td data-label="Reorder">{{ $supply->reorder_level }}</td>
                    <td data-label="Status"><span class="badge-status {{ $badge }}">{{ $label }}</span></td>
                    <td data-label="Last Movement"><span class="last-movement">{{ $lastMove }}</span></td>
                    <td data-label="Actions">
                        <div class="action-btns">
                            <button class="btn-action btn-stock-in" 
                                    onclick="openStockModal('in', {{ $supply->id }})">＋ Stock In</button>
                            <button class="btn-action btn-stock-out" 
                                    onclick="openStockModal('out', {{ $supply->id }})">− Stock Out</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="9">No supplies found in this barn yet.</td>
                </tr>
                @endforelse
            </tbody>
         </table>
    </div>
    @if(method_exists($supplies, 'links') && $supplies->lastPage() > 1)
        <div class="pagination-wrapper" style="padding: 1rem 1.2rem; border-top: 1px solid var(--green-border); background: #ffffff;">
            {{ $supplies->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

@include('barn_staff_transaction.modals.stock_in')
@include('barn_staff_transaction.modals.stock_out')
@include('barn_staff_transaction.modals.history')

<div class="fb-toast" id="fbToast"></div>

<script>
    const suppliesData = {!! json_encode($supplies->map(function($s) {
        $catName = $s->category->category_name ?? 'N/A';
        return [
            'id'           => $s->id,
            'display_id'   => strtoupper(substr($catName, 0, 3)) . str_pad($s->id, 4, '0', STR_PAD_LEFT),
            'supply_name'  => $s->supply_name,
            'category_id'  => $s->category_id,
            'stock'        => $s->stock,
            'img_url'      => $s->supply_img_path,
        ];
    })) !!};

    const suppliersByCategory = {!! json_encode($suppliersByCategory ?? []) !!};
</script>

@endsection

@push('scripts')
<script>
    function openStockModal(type, id) {
        const s = suppliesData.find(x => x.id === id);
        if (!s) return;

        const prefix = type === 'in' ? 'si' : 'so';
        const modalId = type === 'in' ? 'stockInModal' : 'stockOutModal';

        const imgWrap = document.getElementById(prefix + 'ImgWrap');
        imgWrap.innerHTML = s.img_url 
            ? `<img src="${s.img_url}" alt="${s.supply_name}" style="max-width:180px;max-height:160px;border-radius:10px;border:2px solid var(--green-border);object-fit:cover;">`
            : `<div style="width:120px;height:120px;background:#e8f0e0;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:2.6rem;color:#bbb;border:1px solid var(--green-border);">📦</div>`;

        document.getElementById(prefix + 'SupplyName').textContent = s.supply_name;
        document.getElementById(prefix + 'SupplyId').value = s.display_id;
        document.getElementById(prefix + 'HiddenId').value = s.id;
        document.getElementById(prefix + 'Quantity').value = '';

        if (type === 'in') {
            loadSuppliersForSupply(s);
        }

        openModal(modalId);
    }

    // Dynamic Supplier Dropdown
    function loadSuppliersForSupply(supply) {
        const select = document.getElementById('siSupplierId');
        if (!select) return;

        select.innerHTML = `<option value="">— Select Supplier —</option>`;

        if (!supply.category_id || !suppliersByCategory[supply.category_id]) {
            return;
        }

        const categorySuppliers = suppliersByCategory[supply.category_id];

        categorySuppliers.forEach(sup => {
            const option = document.createElement('option');
            option.value = sup.id;
            option.textContent = sup.supplier_name + (sup.contact_number ? ` (${sup.contact_number})` : '');
            select.appendChild(option);
        });
    }

    function openModal(id)  { 
        document.getElementById(id).classList.add('show'); 
    }
    
    function closeModal(id) { 
        document.getElementById(id).classList.remove('show'); 
    }

    document.querySelectorAll('.fb-modal-backdrop').forEach(b => {
        b.addEventListener('click', e => { 
            if (e.target === b) b.classList.remove('show'); 
        });
    });

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#txnTable tbody tr:not(.empty-row)').forEach(row => {
            const name = row.dataset.name || '';
            const cat  = row.dataset.category || '';
            row.style.display = (!q || name.includes(q) || cat.includes(q)) ? '' : 'none';
        });
    }

    function openHistoryModal() {
        openModal('historyModal');
        loadHistory();
    }

    function showToast(msg, type = 'success') {
        const t = document.getElementById('fbToast');
        t.textContent = (type === 'success' ? '✅ ' : '❌ ') + msg;
        t.className = 'fb-toast show' + (type === 'error' ? ' error' : '');
        setTimeout(() => t.classList.remove('show'), 3500);
    }
    
    @if(session('error')) 
    showToast({!! json_encode(session('error')) !!}, 'error'); 
    @endif
</script>
@endpush