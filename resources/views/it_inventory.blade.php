<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IT Asset Inventory | Northstar</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #172033;
            --muted: #788399;
            --line: #e7eaf0;
            --canvas: #f5f7fa;
            --white: #fff;
            --slate: #182334;
            --green: #15805d;
            --green-bg: #e8f7f0;
            --blue: #3264c8;
            --blue-bg: #edf3ff;
            --amber: #ad6a0a;
            --amber-bg: #fff4df;
            --red: #b83b45;
            --red-bg: #fff0f0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        * { box-sizing: border-box; }
        body { margin: 0; color: var(--ink); background: var(--canvas); font-size: 14px; }
        button, input, select, textarea { font: inherit; }
        button { cursor: pointer; }
        .app-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 238px; flex: 0 0 238px; padding: 26px 18px; color: #e8edf7; background: var(--slate); }
        .brand { display: flex; align-items: center; gap: 11px; padding: 0 10px 32px; font-weight: 750; font-size: 16px; letter-spacing: -.3px; }
        .brand-mark { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 10px; background: #6c8cef; color: #fff; font-size: 17px; }
        .nav-caption { margin: 7px 10px 10px; color: #8895aa; font-size: 10px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; }
        .nav-link { display: flex; align-items: center; gap: 12px; margin: 4px 0; padding: 11px 12px; border-radius: 8px; color: #c1cadd; text-decoration: none; font-weight: 550; }
        .nav-link.active { color: #fff; background: #2a3a52; }
        .nav-icon { width: 19px; color: #a6b4cb; text-align: center; }
        .side-note { margin-top: 34px; padding: 15px; border: 1px solid #35455c; border-radius: 10px; color: #b8c3d4; font-size: 12px; line-height: 1.6; }
        .side-note strong { display: block; margin-bottom: 3px; color: #f4f6fa; font-size: 12px; }
        .main { min-width: 0; flex: 1; }
        .topbar { display: flex; align-items: center; justify-content: space-between; min-height: 76px; padding: 0 38px; border-bottom: 1px solid var(--line); background: var(--white); }
        .breadcrumb { color: var(--muted); font-size: 13px; }
        .breadcrumb strong { color: var(--ink); font-weight: 650; }
        .top-user { display: flex; align-items: center; gap: 10px; color: #5d687b; font-size: 12px; }
        .avatar { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 50%; color: #3158a8; background: #e9efff; font-weight: 750; }
        .content { max-width: 1500px; margin: 0 auto; padding: 34px 38px 52px; }
        .page-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 25px; }
        h1 { margin: 0 0 7px; font-size: clamp(23px, 3vw, 29px); letter-spacing: -1px; }
        .subtitle { margin: 0; color: var(--muted); }
        .button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 40px; padding: 0 15px; border: 1px solid transparent; border-radius: 7px; color: #fff; background: #385fc5; font-weight: 650; text-decoration: none; transition: background .15s ease, transform .15s ease; }
        .button:hover { background: #294da9; }
        .button:active { transform: translateY(1px); }
        .button-secondary { border-color: #dce1ea; color: #354157; background: #fff; }
        .button-secondary:hover { background: #f6f8fb; }
        .button-small { min-height: 31px; padding: 0 10px; font-size: 12px; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 15px; margin-bottom: 18px; }
        .stat-card { position: relative; min-height: 132px; padding: 19px 20px; overflow: hidden; border: 1px solid var(--line); border-radius: 11px; background: var(--white); }
        .stat-label { color: var(--muted); font-size: 12px; font-weight: 600; }
        .stat-value { margin-top: 14px; font-size: 29px; font-weight: 750; letter-spacing: -.8px; }
        .stat-foot { margin-top: 5px; color: var(--muted); font-size: 11px; }
        .stat-icon { position: absolute; top: 17px; right: 18px; display: grid; width: 35px; height: 35px; place-items: center; border-radius: 9px; color: #4d69b7; background: #eff2fc; font-weight: 750; }
        .stat-card.assigned .stat-icon { color: var(--blue); background: var(--blue-bg); }
        .stat-card.available .stat-icon { color: var(--green); background: var(--green-bg); }
        .stat-card.repair .stat-icon { color: var(--amber); background: var(--amber-bg); }
        .valuation-strip { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 23px; padding: 15px 19px; border: 1px solid #e4e9f3; border-radius: 10px; background: #f0f4fb; }
        .valuation-label { color: #64718a; font-size: 12px; font-weight: 600; }
        .valuation-amount { margin-top: 3px; color: #23375f; font-size: 20px; font-weight: 750; letter-spacing: -.3px; }
        .valuation-note { color: #71809a; font-size: 11px; }
        .panel { overflow: hidden; border: 1px solid var(--line); border-radius: 11px; background: var(--white); box-shadow: 0 2px 7px rgb(20 35 60 / 3%); }
        .panel-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 19px 21px 0; }
        .panel-title { margin: 0; font-size: 16px; letter-spacing: -.2px; }
        .panel-caption { margin: 5px 0 0; color: var(--muted); font-size: 12px; }
        .tabs { display: flex; gap: 23px; padding: 15px 21px 0; border-bottom: 1px solid var(--line); }
        .tab { position: relative; padding: 0 0 12px; border: 0; color: var(--muted); background: transparent; font-weight: 600; }
        .tab.active { color: #3157ad; }
        .tab.active::after { position: absolute; right: 0; bottom: -1px; left: 0; height: 2px; background: #4267cf; content: ""; }
        .tab-panel[hidden] { display: none; }
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; color: #788399; background: #fafbfd; font-size: 10px; font-weight: 750; letter-spacing: .7px; text-transform: uppercase; white-space: nowrap; }
        td { padding: 14px 15px; border-top: 1px solid #edf0f4; vertical-align: middle; }
        th:first-child, td:first-child { padding-left: 21px; }
        th:last-child, td:last-child { padding-right: 21px; }
        .asset-tag { color: #3054a8; font-size: 12px; font-weight: 750; white-space: nowrap; }
        .cell-primary { color: #273247; font-weight: 650; }
        .cell-secondary { margin-top: 4px; color: #8993a4; font-size: 11px; }
        .serial { color: #5c687b; font-size: 12px; white-space: nowrap; }
        .employee-cell { min-width: 145px; }
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge::before { width: 6px; height: 6px; border-radius: 50%; background: currentColor; content: ""; }
        .badge-available { color: var(--green); background: var(--green-bg); }
        .badge-assigned { color: var(--blue); background: var(--blue-bg); }
        .badge-maintenance { color: var(--amber); background: var(--amber-bg); }
        .badge-decommissioned { color: #667085; background: #f0f1f4; }
        .actions { display: flex; align-items: center; gap: 7px; white-space: nowrap; }
        .icon-button { display: inline-flex; align-items: center; justify-content: center; min-height: 31px; padding: 0 9px; border: 1px solid #dce2ed; border-radius: 6px; color: #44516a; background: #fff; font-size: 11px; font-weight: 650; }
        .icon-button:hover { background: #f6f8fc; }
        .icon-button.danger { border-color: #f1dada; color: var(--red); }
        .icon-button.danger:hover { background: var(--red-bg); }
        .empty { padding: 46px 20px; color: var(--muted); text-align: center; }
        .history-action { color: #344361; font-weight: 650; }
        .history-asset { color: #3054a8; font-size: 12px; font-weight: 700; }
        .history-date { min-width: 125px; color: #596579; font-size: 12px; white-space: nowrap; }
        .flash { margin-bottom: 18px; padding: 12px 15px; border: 1px solid #c5ead9; border-radius: 8px; color: #176749; background: #edfaf4; }
        .validation-summary { margin-bottom: 18px; padding: 12px 15px; border: 1px solid #f1c8c8; border-radius: 8px; color: #9f2731; background: #fff2f2; }
        dialog { width: min(540px, calc(100% - 28px)); max-height: min(90vh, 760px); padding: 0; overflow: auto; border: 0; border-radius: 13px; color: var(--ink); box-shadow: 0 22px 80px rgb(18 27 44 / 25%); }
        dialog::backdrop { background: rgb(18 27 44 / 48%); backdrop-filter: blur(2px); }
        .modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 21px 23px 17px; border-bottom: 1px solid var(--line); }
        .modal-header h2 { margin: 0; font-size: 18px; }
        .modal-header p { margin: 5px 0 0; color: var(--muted); font-size: 12px; }
        .modal-close { width: 30px; height: 30px; border: 0; border-radius: 6px; color: #596579; background: #f2f4f8; font-size: 18px; }
        .modal-body { padding: 20px 23px 22px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field.full { grid-column: 1 / -1; }
        .field label { color: #39465c; font-size: 12px; font-weight: 650; }
        .field input, .field select, .field textarea { width: 100%; min-height: 40px; padding: 9px 11px; border: 1px solid #dce2eb; border-radius: 7px; outline: none; color: #29354a; background: #fff; }
        .field textarea { min-height: 78px; resize: vertical; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color: #6887de; box-shadow: 0 0 0 3px rgb(87 120 218 / 13%); }
        .field-error { color: #b4232c; font-size: 11px; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 9px; margin-top: 20px; }
        @media (max-width: 1050px) {
            .sidebar { width: 200px; flex-basis: 200px; }
            .content { padding-right: 24px; padding-left: 24px; }
            .topbar { padding-right: 24px; padding-left: 24px; }
            .stats { gap: 10px; }
        }
        @media (max-width: 760px) {
            .sidebar { width: 60px; flex-basis: 60px; padding: 20px 8px; }
            .brand { justify-content: center; padding: 0 0 28px; }
            .brand-name, .nav-caption, .nav-label, .side-note { display: none; }
            .nav-link { justify-content: center; padding: 12px 5px; }
            .nav-icon { width: auto; font-size: 16px; }
            .topbar { min-height: 62px; padding: 0 16px; }
            .content { padding: 24px 16px 38px; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .page-heading { align-items: flex-start; flex-direction: column; }
            .page-heading .button { align-self: stretch; }
            .panel-header { align-items: flex-start; flex-direction: column; }
            .panel-header > div:last-child { align-self: stretch; }
        }
        @media (max-width: 440px) {
            .stats { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card { min-height: 120px; padding: 15px; }
            .stat-value { font-size: 25px; }
            .stat-icon { top: 12px; right: 12px; width: 30px; height: 30px; }
            .valuation-strip { align-items: flex-start; flex-direction: column; }
            .form-grid { grid-template-columns: 1fr; }
            .field.full { grid-column: auto; }
            .top-user span { display: none; }
        }
    </style>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand"><span class="brand-mark">N</span><span class="brand-name">Northstar IT</span></div>
        <div class="nav-caption">Workspace</div>
        <a class="nav-link active" href="{{ route('it.dashboard') }}"><span class="nav-icon">▦</span><span class="nav-label">Asset inventory</span></a>
        <a class="nav-link" href="#history" data-tab-link="history"><span class="nav-icon">◷</span><span class="nav-label">Assignment history</span></a>
        <div class="side-note"><strong>Hardware operations</strong>One place to track company devices, assignments and service history.</div>
    </aside>
    <main class="main">
        <header class="topbar">
            <div class="breadcrumb">Workspace <span aria-hidden="true">/</span> <strong>IT Asset Inventory</strong></div>
            <div class="top-user"><div class="avatar">IT</div><span>IT Operations</span></div>
        </header>
        <div class="content">
            @if (session('status'))
                <div class="flash" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="validation-summary" role="alert">
                    <strong>Please check the form and try again.</strong>
                    <ul style="margin: 7px 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <section class="page-heading">
                <div>
                    <h1>IT asset inventory</h1>
                    <p class="subtitle">Manage devices, keep assignments current, and review hardware history.</p>
                </div>
                <button class="button" type="button" data-open-dialog="addAssetModal"><span aria-hidden="true">＋</span> Add hardware asset</button>
            </section>

            <section class="stats" aria-label="Inventory summary">
                <article class="stat-card">
                    <span class="stat-icon">▣</span><div class="stat-label">Total devices</div>
                    <div class="stat-value">{{ number_format($totalAssets) }}</div><div class="stat-foot">Tracked hardware assets</div>
                </article>
                <article class="stat-card assigned">
                    <span class="stat-icon">↗</span><div class="stat-label">Assigned devices</div>
                    <div class="stat-value">{{ number_format($assignedAssets) }}</div><div class="stat-foot">Currently with employees</div>
                </article>
                <article class="stat-card available">
                    <span class="stat-icon">✓</span><div class="stat-label">Available in stock</div>
                    <div class="stat-value">{{ number_format($availableAssets) }}</div><div class="stat-foot">Ready to be assigned</div>
                </article>
                <article class="stat-card repair">
                    <span class="stat-icon">⚒</span><div class="stat-label">Under repair</div>
                    <div class="stat-value">{{ number_format($maintenanceAssets) }}</div><div class="stat-foot">In maintenance or service</div>
                </article>
            </section>

            <section class="valuation-strip">
                <div><div class="valuation-label">Total hardware asset valuation</div><div class="valuation-amount">${{ number_format($totalValuation, 2) }}</div></div>
                <div class="valuation-note">Based on recorded purchase cost</div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div><h2 class="panel-title">Hardware assets</h2><p class="panel-caption">Browse devices and manage employee assignments.</p></div>
                    <div><button class="button button-secondary button-small" type="button" data-open-dialog="addAssetModal">＋ Add device</button></div>
                </div>
                <div class="tabs" role="tablist" aria-label="Inventory views">
                    <button class="tab active" id="inventoryTab" type="button" role="tab" aria-selected="true" aria-controls="inventoryPanel" data-tab="inventory">Asset inventory</button>
                    <button class="tab" id="historyTab" type="button" role="tab" aria-selected="false" aria-controls="historyPanel" data-tab="history">Assignment history <span style="color: #98a1b1;">({{ $recentLogs->count() }})</span></button>
                </div>
                <div class="tab-panel" id="inventoryPanel" role="tabpanel" aria-labelledby="inventoryTab">
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Asset tag</th><th>Category</th><th>Model &amp; specifications</th><th>Serial number</th><th>Assigned employee</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                            @forelse ($assets as $asset)
                                <tr>
                                    <td><span class="asset-tag">{{ $asset->asset_tag }}</span></td>
                                    <td><span class="cell-primary">{{ $asset->category->name }}</span></td>
                                    <td><div class="cell-primary">{{ $asset->model_name }}</div><div class="cell-secondary">{{ $asset->specifications }}</div></td>
                                    <td><span class="serial">{{ $asset->serial_number }}</span></td>
                                    <td class="employee-cell">
                                        @if ($asset->employee)
                                            <div class="cell-primary">{{ $asset->employee->name }}</div><div class="cell-secondary">{{ $asset->employee->designation }}</div>
                                        @else
                                            <span class="cell-secondary">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-{{ $asset->status }}">{{ ucfirst($asset->status) }}</span></td>
                                    <td>
                                        <div class="actions">
                                            @if ($asset->status === 'available')
                                                <button class="icon-button" type="button" data-action="assign" data-asset-id="{{ $asset->id }}" data-asset-tag="{{ $asset->asset_tag }}" data-url="{{ route('assets.assign', $asset->id) }}">Assign</button>
                                            @elseif ($asset->status === 'assigned')
                                                <button class="icon-button" type="button" data-action="return" data-asset-id="{{ $asset->id }}" data-asset-tag="{{ $asset->asset_tag }}" data-employee="{{ $asset->employee?->name }}" data-url="{{ route('assets.return', $asset->id) }}">Return</button>
                                            @else
                                                <span class="cell-secondary">No action</span>
                                            @endif
                                            <form method="POST" action="{{ route('assets.destroy', $asset->id) }}" onsubmit="return confirm('Delete this asset and its history? This cannot be undone.');">
                                                @csrf @method('DELETE')
                                                <button class="icon-button danger" type="submit" aria-label="Delete {{ $asset->asset_tag }}">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="empty" colspan="7">No hardware assets yet. Add your first device to start the inventory.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-panel" id="historyPanel" role="tabpanel" aria-labelledby="historyTab" hidden>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Activity</th><th>Asset</th><th>Employee</th><th>Remarks</th><th>Date &amp; time</th></tr></thead>
                            <tbody>
                            @forelse ($recentLogs as $log)
                                <tr>
                                    <td><span class="history-action">{{ $log->action }}</span></td>
                                    <td><span class="history-asset">{{ $log->asset?->asset_tag ?? 'Deleted asset' }}</span><div class="cell-secondary">{{ $log->asset?->model_name }}</div></td>
                                    <td>{{ $log->employee?->name ?? '—' }}</td>
                                    <td>{{ $log->remarks ?: '—' }}</td>
                                    <td class="history-date">{{ $log->created_at->format('M j, Y · g:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td class="empty" colspan="5">No assignment or maintenance history has been recorded.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

<dialog id="addAssetModal" aria-labelledby="addAssetTitle">
    <div class="modal-header"><div><h2 id="addAssetTitle">Add hardware asset</h2><p>Record a new device in the company inventory.</p></div><button class="modal-close" type="button" data-close-dialog aria-label="Close">×</button></div>
    <form class="modal-body" method="POST" action="{{ route('assets.store') }}">
        @csrf
        <div class="form-grid">
            <div class="field"><label for="category_id">Category</label><select id="category_id" name="category_id" required><option value="">Select category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="field"><label for="asset_tag">Asset tag</label><input id="asset_tag" name="asset_tag" value="{{ old('asset_tag') }}" placeholder="IT-LAP-001" required></div>
            <div class="field"><label for="model_name">Model name</label><input id="model_name" name="model_name" value="{{ old('model_name') }}" placeholder="MacBook Pro M3" required></div>
            <div class="field"><label for="serial_number">Serial number</label><input id="serial_number" name="serial_number" value="{{ old('serial_number') }}" required></div>
            <div class="field"><label for="purchase_date">Purchase date</label><input id="purchase_date" name="purchase_date" type="date" value="{{ old('purchase_date') }}" required></div>
            <div class="field"><label for="purchase_cost">Purchase cost (USD)</label><input id="purchase_cost" name="purchase_cost" type="number" min="0" step="0.01" value="{{ old('purchase_cost') }}" placeholder="0.00" required></div>
            <div class="field full"><label for="specifications">Specifications</label><textarea id="specifications" name="specifications" placeholder="16GB RAM, 512GB SSD, Core i7" required>{{ old('specifications') }}</textarea></div>
        </div>
        <div class="modal-footer"><button class="button button-secondary" type="button" data-close-dialog>Cancel</button><button class="button" type="submit">Add asset</button></div>
    </form>
</dialog>

<dialog id="assetActionModal" aria-labelledby="assetActionTitle">
    <div class="modal-header"><div><h2 id="assetActionTitle">Assign device</h2><p id="assetActionSubtitle">Choose an employee to receive this device.</p></div><button class="modal-close" type="button" data-close-dialog aria-label="Close">×</button></div>
    <form class="modal-body" id="assetActionForm" method="POST">
        @csrf
        <div id="employeeField" class="field">
            <label for="employee_id">Employee</label>
            <select id="employee_id" name="employee_id">
                <option value="">Select employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }} — {{ $employee->designation }}</option>
                @endforeach
            </select>
        </div>
        <div id="returnEmployee" class="field" hidden><label>Currently assigned to</label><div class="cell-primary" id="returnEmployeeName"></div></div>
        <div class="field" style="margin-top: 15px;"><label for="action_remarks">Remarks <span style="color: #8993a4; font-weight: 400;">(optional)</span></label><textarea id="action_remarks" name="remarks" maxlength="255" placeholder="Add assignment or return notes"></textarea></div>
        <div class="modal-footer"><button class="button button-secondary" type="button" data-close-dialog>Cancel</button><button class="button" id="assetActionSubmit" type="submit">Assign device</button></div>
    </form>
</dialog>

<script>
    (() => {
        const addDialog = document.getElementById('addAssetModal');
        const actionDialog = document.getElementById('assetActionModal');
        const actionForm = document.getElementById('assetActionForm');
        const employeeField = document.getElementById('employeeField');
        const employeeSelect = document.getElementById('employee_id');
        const returnEmployee = document.getElementById('returnEmployee');
        const actionTitle = document.getElementById('assetActionTitle');
        const actionSubtitle = document.getElementById('assetActionSubtitle');
        const actionSubmit = document.getElementById('assetActionSubmit');

        document.querySelectorAll('[data-open-dialog]').forEach((button) => {
            button.addEventListener('click', () => document.getElementById(button.dataset.openDialog).showModal());
        });
        document.querySelectorAll('[data-close-dialog]').forEach((button) => {
            button.addEventListener('click', () => button.closest('dialog').close());
        });
        document.querySelectorAll('[data-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const isAssign = button.dataset.action === 'assign';
                actionForm.action = button.dataset.url;
                actionTitle.textContent = isAssign ? 'Assign device' : 'Return device';
                actionSubtitle.textContent = `${button.dataset.assetTag} · ${isAssign ? 'Choose an employee to receive this device.' : 'Return this device to available stock.'}`;
                actionSubmit.textContent = isAssign ? 'Assign device' : 'Confirm return';
                employeeField.hidden = !isAssign;
                employeeSelect.required = isAssign;
                returnEmployee.hidden = isAssign;
                document.getElementById('returnEmployeeName').textContent = button.dataset.employee || 'Employee';
                document.getElementById('action_remarks').value = '';
                actionDialog.showModal();
            });
        });
        document.querySelectorAll('[data-tab]').forEach((tab) => {
            tab.addEventListener('click', () => activateTab(tab.dataset.tab));
        });
        document.querySelectorAll('[data-tab-link]').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                activateTab(link.dataset.tabLink);
                document.querySelector('.panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
        function activateTab(name) {
            const inventoryActive = name === 'inventory';
            document.getElementById('inventoryTab').classList.toggle('active', inventoryActive);
            document.getElementById('inventoryTab').setAttribute('aria-selected', String(inventoryActive));
            document.getElementById('historyTab').classList.toggle('active', !inventoryActive);
            document.getElementById('historyTab').setAttribute('aria-selected', String(!inventoryActive));
            document.getElementById('inventoryPanel').hidden = !inventoryActive;
            document.getElementById('historyPanel').hidden = inventoryActive;
        }

        @if ($errors->has('asset_tag') || $errors->has('category_id') || $errors->has('model_name') || $errors->has('serial_number') || $errors->has('specifications') || $errors->has('purchase_date') || $errors->has('purchase_cost'))
            addDialog.showModal();
        @endif
    })();
</script>
</body>
</html>
