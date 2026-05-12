@extends('layouts.app')

@section('title', 'Supplies')

@section('hero-text')
    Register your farm's supplies in the barn inventory 📦
@endsection

@section('hero-text-mobile')
    📦 Register farm supplies
@endsection

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-family: var(--font);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--gold-mid);
    }

    .btn-add {
        background: var(--green-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.1rem;
        font-family: var(--font);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
    }

    .btn-add:hover { 
        background: var(--green-dark); 
        transform: translateY(-1px); 
    }

    .table-card {
        background: var(--cream);
        border-radius: 12px;
        border: 1px solid var(--green-border);
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(20,50,8,0.08);
    }

    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 1.2rem;
        border-bottom: 1px solid var(--green-border);
        background: #ffffff;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .tbl-title {
        font-family: var(--font);
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--text-dark);
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .search-input {
        background: #fff;
        border: 1.5px solid var(--green-border);
        border-radius: 20px;
        padding: 0.35rem 0.9rem 0.35rem 2rem;
        font-family: var(--font);
        font-size: 0.82rem;
        color: var(--text-dark);
        outline: none;
        width: 200px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%234e9a30' stroke-width='2.5'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.6rem center;
    }

    .search-input:focus { border-color: var(--green-main); }

    .btn-filter {
        background: #fff;
        border: 1.5px solid var(--green-border);
        border-radius: 7px;
        padding: 0.35rem 0.8rem;
        font-family: var(--font);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--green-mid);
        cursor: pointer;
    }

    .filter-dropdown {
        display: none;
        position: absolute;
        background: #fff;
        border: 1px solid var(--green-border);
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 4px 15px rgba(20,50,8,0.15);
        z-index: 100;
        margin-top: 5px;
        width: 180px;
    }

    .filter-dropdown.show { display: block; }

    .inv-table {
        width: 100%;
        border-collapse: collapse;
        font-family: var(--font);
        font-size: 0.8rem;
        table-layout: auto;
    }

    .inv-table thead tr { background: var(--green-main); }
    .inv-table thead th {
        padding: 0.6rem 0.8rem;
        color: #fff;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .inv-table tbody tr {
        border-bottom: 1px solid #e8f0e0;
        transition: background 0.15s;
        cursor: pointer;
    }

    .inv-table tbody tr:hover { background: #f3faea; }

    .row-highlight {
        background-color: #e8f5e0 !important;
        transition: background-color 0.1s ease;
    }

    .inv-table tbody td {
        padding: 0.6rem 0.8rem;
        color: var(--text-dark);
        vertical-align: middle;
    }

    .inv-table th:nth-child(1), .inv-table td:nth-child(1) { width: 68px; text-align: center; }
    .inv-table th:nth-child(2), .inv-table td:nth-child(2) { width: 95px; text-align: center; }
    .inv-table th:nth-child(5), .inv-table td:nth-child(5) { text-align: right; width: 90px; }
    .inv-table th:nth-child(6), .inv-table td:nth-child(6) { text-align: right; width: 110px; }
    .inv-table th:nth-child(7), .inv-table td:nth-child(7) { text-align: center; }
    .inv-table th:nth-child(8), .inv-table td:nth-child(8) { text-align: center; width: 110px; }

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

    .empty-row td {
        text-align: center !important;
        color: #888;
        font-size: 0.96rem;
        border: none !important;
        background: #f8fbf4;
        padding: 4rem 2rem !important;
    }

    .supply-thumb {
        width: 44px; height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--green-border);
    }
    .supply-thumb-placeholder {
        width: 44px; height: 44px;
        border-radius: 6px;
        background: #e8f0e0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; color: #aaa;
        border: 1px solid var(--green-border);
    }
    .supply-id-tag {
        font-weight: 700;
        color: var(--green-mid);
        font-size: 0.72rem;
        letter-spacing: 0.04em;
    }
    .badge-status {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .badge-low      { background: #fde8e8; color: #856404; }
    .badge-ok       { background: var(--green-pale); color: var(--green-dark); }
    .badge-out      { background: #fff3cd; color: #c0392b; }
    .badge-inactive { background: #f0f0f0; color: #777; }
    .action-btns {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        justify-content: flex-end;
    }
    .btn-action {
        width: 28px; height: 28px;
        border: none; border-radius: 6px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        transition: transform 0.1s;
        background: #fff;
        border: 1px solid var(--green-border);
    }
    .btn-action:hover { transform: scale(1.15); background: var(--green-pale); }
    .fb-toast {
        position: fixed; bottom: 24px; right: 24px;
        background: #2e7d32; color: white;
        padding: 14px 20px; border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        font-family: var(--font); font-size: 0.95rem; font-weight: 600;
        display: none; align-items: center; gap: 8px;
        z-index: 10000; min-width: 280px; transition: all 0.3s ease;
    }
    .fb-toast.show { display: flex; }
    .fb-toast.error { background: #c62828; }

    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: stretch; }
        .table-toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-right { flex-direction: column; align-items: stretch; }
        .search-input, .btn-filter { width: 100%; text-align: center; }
        .filter-dropdown { left: 0; right: auto; width: 100%; }
        .inv-table thead { display: none; }
        .inv-table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid var(--green-border);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 0.5rem;
        }
        .inv-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0.8rem;
            border-bottom: 1px solid #e8f0e0;
        }
        .inv-table tbody td:last-child { border-bottom: none; }
        .inv-table tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--green-mid);
            width: 40%;
            flex-shrink: 0;
        }
        .inv-table td:nth-child(1), .inv-table td:nth-child(2),
        .inv-table td:nth-child(5), .inv-table td:nth-child(6),
        .inv-table td:nth-child(7), .inv-table td:nth-child(8) {
            width: auto;
            text-align: left;
        }
        .action-btns { justify-content: flex-end; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">Supplies</div>
    <button class="btn-add" onclick="openModal('addSupplyModal')">+ Add Supply</button>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <span class="tbl-title">All Supplies</span>
        <div class="toolbar-right" style="position:relative;">
            <input type="text" class="search-input" id="searchInput" placeholder="Search supplies..." oninput="filterTable()">
            <button class="btn-filter" onclick="toggleFilter()">▼ Filter</button>
            <div class="filter-dropdown" id="filterDropdown">
                <div class="mb-2">
                    <label style="font-size:0.75rem;font-weight:700;color:var(--text-mid);">Category</label>
                    <select class="form-select form-select-sm" id="filterCategory" onchange="filterTable()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ strtolower($cat->category_name) }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:700;color:var(--text-mid);">Status</label>
                    <select class="form-select form-select-sm" id="filterStatus" onchange="filterTable()">
                        <option value="">All Status</option>
                        <option value="low stock">Low Stock</option>
                        <option value="in stock">In Stock</option>
                        <option value="out of stock">Out of Stock</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="inv-table" id="invTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>ID</th>
                    <th>Supply</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplies as $supply)
                @php
                    $catName = $supply->category->category_name ?? 'N/A';
                    $supId   = strtoupper(substr($catName, 0, 3)) . str_pad($supply->id, 4, '0', STR_PAD_LEFT);
                    $isLow   = $supply->stock <= $supply->reorder_level;
                    $isOut   = $supply->stock == 0;
                    $badgeClass = $isOut ? 'badge-out' : ($isLow ? 'badge-low' : 'badge-ok');
                    $badgeLabel = $isOut ? 'Out of Stock' : ($isLow ? 'Low Stock' : 'In Stock');
                    $isActive = $supply->supply_status === 'active';
                @endphp
                <tr data-supply-id="{{ $supply->id }}"
                    data-name="{{ strtolower($supply->supply_name) }}"
                    data-category="{{ strtolower($catName) }}"
                    data-status="{{ strtolower($badgeLabel) }}"
                    class="{{ !$isActive ? 'table-secondary opacity-75' : '' }}">
                    <td data-label="Image">
                        @if($supply->supply_img_path)
                            <img src="{{ asset('storage/' . $supply->supply_img_path) }}" class="supply-thumb" alt="{{ $supply->supply_name }}">
                        @else
                            <div class="supply-thumb-placeholder">📦</div>
                        @endif
                    </td>
                    <td data-label="ID"><span class="supply-id-tag">{{ $supId }}</span></td>
                    <td data-label="Supply"><strong>{{ $supply->supply_name }}</strong></td>
                    <td data-label="Category">{{ $catName }}</td>
                    <td data-label="Stock">{{ $supply->stock }}</td>
                    <td data-label="Reorder Level">{{ $supply->reorder_level }}</td>
                    <td data-label="Status">
                        <span class="badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        @if(!$isActive)
                            <span class="badge-status badge-inactive ms-1">Inactive</span>
                        @endif
                    </td>
                    <td data-label="Actions">
                        <div class="action-btns">
                            <button class="btn-action btn-edit" title="Edit" onclick="openEditSupplyModal({{ $supply->id }})">✎</button>
                            <button class="btn-action" 
                                    title="{{ $isActive ? 'Deactivate Supply' : 'Reactivate Supply' }}"
                                    style="background: {{ $isActive ? '#c0392b' : '#28a745' }}; color:white;"
                                    onclick="toggleSupplyStatus({{ $supply->id }})">
                                {{ $isActive ? '🗑️' : '↺' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="8">
                            No supplies found. Click <strong>+ Add Supply</strong> to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($supplies, 'links'))
        <div class="pagination-wrapper">
            {{ $supplies->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

@include('barn_owner_inventory.modals.add_supply')
@include('barn_owner_inventory.modals.edit_supply')
@include('barn_owner_inventory.modals.view_supply')
@include('barn_owner_inventory.modals.delete_supply')

<div class="fb-toast" id="fbToast"></div>
@endsection

@push('scripts')
<script>
    const suppliesData = {!! json_encode($suppliesData ?? []) !!};

    function openModal(id)  { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    document.querySelectorAll('.fb-modal-backdrop').forEach(b => {
        b.addEventListener('click', e => { if (e.target === b) b.classList.remove('show'); });
    });

    function openViewSupplyModal(id) {
        const s = suppliesData.find(x => x.id === id);
        if (!s) return;

        document.getElementById('viewSupTitle').textContent = s.supply_name;
        const imgWrap = document.getElementById('viewSupImgWrap');
        imgWrap.innerHTML = s.img_url 
            ? `<img src="${s.img_url}" class="view-img" alt="${s.supply_name}">` 
            : `<div class="view-img-placeholder">📦</div>`;

        const statusColor = s.status_label === 'In Stock' 
            ? 'var(--green-mid)' 
            : (s.status_label === 'Out of Stock' ? '#c0392b' : '#856404');

        document.getElementById('viewSupGrid').innerHTML = `
            <div class="view-detail-item">
                <span class="detail-label">ID</span>
                <span class="detail-value" style="color:var(--green-mid)">${s.display_id}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Supply Name</span>
                <span class="detail-value">${s.supply_name}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Category</span>
                <span class="detail-value">${s.category_name}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Current Stock</span>
                <span class="detail-value" style="font-size:1.1rem;">${s.stock}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Reorder Level</span>
                <span class="detail-value">${s.reorder_level}</span>
            </div>
            <div class="view-detail-item" style="grid-column:1/-1">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="color:${statusColor}">${s.status_label}</span>
            </div>
        `;
        openModal('viewSupplyModal');
    }

    function openEditSupplyModal(id) {
        const s = suppliesData.find(x => x.id === id);
        if (!s) return;

        document.getElementById('editSupId').value           = s.display_id;
        document.getElementById('editSupName').value         = s.supply_name;
        document.getElementById('editSupCategoryId').value   = s.category_id;
        document.getElementById('editSupReorderLevel').value = s.reorder_level;

        const currentImgDiv = document.getElementById('editSupCurrentImg');
        const currentImgEl  = document.getElementById('editSupCurrentImgEl');
        if (s.img_url) {
            currentImgEl.src = s.img_url;
            currentImgDiv.style.display = 'block';
        } else {
            currentImgDiv.style.display = 'none';
        }
        document.getElementById('editSupPreview').style.display = 'none';
        document.getElementById('editSupplyForm').action = s.edit_url;
        openModal('editSupplyModal');
    }

    function toggleSupplyStatus(id) {
        const supply = suppliesData.find(s => s.id === id);
        if (!supply) return;

        const isActive = supply.supply_status === 'active';
        const title = isActive ? 'Deactivate Supply' : 'Reactivate Supply';
        const message = isActive 
            ? `You are about to deactivate <strong>${supply.supply_name}</strong>.<br>This supply will be marked as inactive.`
            : `You are about to reactivate <strong>${supply.supply_name}</strong>.`;

        document.getElementById('deleteSupplyTitle').textContent = title;
        document.getElementById('deleteSupplyMessage').innerHTML = message;
        document.getElementById('deleteSubmitBtn').textContent = isActive ? 'Yes, Deactivate' : 'Yes, Reactivate';

        const form = document.getElementById('deleteSupplyForm');
        form.action = supply.delete_url;
        openModal('deleteSupplyModal');
    }

    function previewFile(input, previewId) {
        const el = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            el.style.display = 'block';
            el.textContent = '✅ ' + input.files[0].name;
        }
    }

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const catF = document.getElementById('filterCategory').value.toLowerCase();
        const statF = document.getElementById('filterStatus').value.toLowerCase();

        document.querySelectorAll('#invTable tbody tr:not(.empty-row)').forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const cat = (row.dataset.category || '').toLowerCase();
            const status = (row.dataset.status || '').toLowerCase();
            const matchQ = !q || name.includes(q) || cat.includes(q);
            const matchCat = !catF || cat === catF;
            const matchStat = !statF || status === statF;
            row.style.display = (matchQ && matchCat && matchStat) ? '' : 'none';
        });
    }

    function toggleFilter() {
        document.getElementById('filterDropdown').classList.toggle('show');
    }

    document.addEventListener('click', e => {
        const wrap = document.querySelector('.toolbar-right');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('filterDropdown').classList.remove('show');
        }
    });

    // Double‑click row → view modal; single‑click → highlight
    document.querySelectorAll('#invTable tbody tr').forEach(row => {
        row.addEventListener('dblclick', function(e) {
            if (e.target.closest('.btn-action')) return;
            const supplyId = this.dataset.supplyId;
            if (supplyId) openViewSupplyModal(parseInt(supplyId));
        });
        row.addEventListener('click', function(e) {
            if (e.target.closest('.btn-action')) return;
            this.classList.add('row-highlight');
            setTimeout(() => this.classList.remove('row-highlight'), 300);
        });
    });

    @if($errors->any() && old('_token'))
        openModal('addSupplyModal');
    @endif
</script>
@endpush