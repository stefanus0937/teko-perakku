@extends($layout ?? 'layouts.admin_premium')

@section('title', 'Grafik Pelaporan')

@section('css')
<style>
    .chart-card {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .chart-title {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
    }
    .filter-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .filter-dropdown {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        outline: none;
        color: #4b5563;
        background: #fff;
        cursor: pointer;
    }
</style>
@stop

@section('content')
<div class="manage-header">
    <h2 class="manage-title">Pelaporan</h2>
</div>

<div class="chart-card">
    <form id="filter-form" action="{{ route('admin.pelaporan-chart') }}" method="GET">
        <div class="chart-header">
            <div class="chart-title">Omset Toko ({{ $selectedYear }})</div>
            <div class="filter-group">
                @if(auth()->user()->role !== 'umkm')
                    <select name="usaha_id" class="filter-dropdown" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Semua Toko</option>
                        @foreach($usahas as $u)
                            <option value="{{ $u->id }}" {{ request('usaha_id') == $u->id ? 'selected' : '' }}>{{ $u->nama_usaha }}</option>
                        @endforeach
                    </select>
                @endif

                <select name="tahun" class="filter-dropdown" onchange="document.getElementById('filter-form').submit()">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>

                <select name="range" class="filter-dropdown" onchange="document.getElementById('filter-form').submit()">
                    <option value="1 tahun" {{ $range == '1 tahun' ? 'selected' : '' }}>1 tahun</option>
                    <option value="6 bulan" {{ $range == '6 bulan' ? 'selected' : '' }}>6 bulan</option>
                    <option value="3 bulan" {{ $range == '3 bulan' ? 'selected' : '' }}>3 bulan</option>
                </select>
            </div>
        </div>
    </form>
    
    <div style="height: 400px;">
        <canvas id="omsetChart"></canvas>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('omsetChart').getContext('2d');
    
    const monthMap = {
        'Januari': 0, 'Februari': 1, 'Maret': 2, 'April': 3, 'Mei': 4, 'Juni': 5,
        'Juli': 6, 'Agustus': 7, 'September': 8, 'Oktober': 9, 'November': 10, 'Desember': 11
    };
    
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const values = new Array(12).fill(0);
    
    @foreach($data as $d)
        if (monthMap['{{ $d->bulan }}'] !== undefined) {
            values[monthMap['{{ $d->bulan }}']] += {{ (float)$d->omset }};
        }
    @endforeach
    
    // Filtering values based on range
    const range = '{{ $range }}';
    let filteredLabels = labels;
    let filteredValues = values;

    if (range === '6 bulan') {
        const currentMonth = new Date().getMonth();
        const start = Math.max(0, currentMonth - 5);
        filteredLabels = labels.slice(start, currentMonth + 1);
        filteredValues = values.slice(start, currentMonth + 1);
    } else if (range === '3 bulan') {
        const currentMonth = new Date().getMonth();
        const start = Math.max(0, currentMonth - 2);
        filteredLabels = labels.slice(start, currentMonth + 1);
        filteredValues = values.slice(start, currentMonth + 1);
    }
    
    const currentMonthIndex = new Date().getMonth();

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: filteredLabels,
            datasets: [{
                label: 'Omset',
                data: filteredValues,
                backgroundColor: (context) => {
                    // Check if this month is the current month in the labels
                    const label = filteredLabels[context.dataIndex];
                    const fullLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const realIndex = fullLabels.indexOf(label);
                    
                    if (realIndex === currentMonthIndex && '{{ $selectedYear }}' == new Date().getFullYear()) {
                        return '#991b1b'; // Dark Red for current month
                    }
                    return '#ef4444'; // Red for others
                },
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: '#f3f4f6'
                    },
                    ticks: {
                        color: '#9ca3af',
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af' }
                }
            }
        }
    });
</script>
@stop
