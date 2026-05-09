<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelaporan;
use App\Models\Usaha;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PelaporanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role == 'umkm') {
            $laporans = Pelaporan::whereHas('usaha', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('usaha')->latest()->get();
        } elseif ($user->role == 'admin_wilayah') {
            $laporans = Pelaporan::whereHas('usaha', function($query) use ($user) {
                $query->where('wilayah_id', $user->wilayah_id);
            })->with('usaha')->latest()->get();
        } else {
            $laporans = Pelaporan::with('usaha')->latest()->get();
        }
        
        $layout = $user->role == 'umkm' ? 'layouts.umkm' : 'layouts.admin_premium';
        return view('admin.pelaporan.index-pelaporan', compact('laporans', 'layout'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role == 'umkm') {
            $usahas = Usaha::where('user_id', $user->id)->get();
        } elseif ($user->role == 'admin_wilayah') {
            $usahas = Usaha::where('wilayah_id', $user->wilayah_id)->get();
        } else {
            $usahas = Usaha::all();
        }
        
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $lastLaporan = Pelaporan::orderBy('id', 'desc')->first();
        $nextId = $lastLaporan ? $lastLaporan->id + 1 : 1;
        $autoKode = 'LAP' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $layout = $user->role == 'umkm' ? 'layouts.umkm' : 'layouts.admin_premium';
        return view('admin.pelaporan.create-pelaporan', compact('usahas', 'months', 'autoKode', 'layout'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'kode_laporan' => 'required|string|unique:pelaporan',
            'usaha_id' => 'required|exists:usaha,id',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'omset' => 'required|numeric',
            'deskripsi' => 'nullable|string',
        ]);

        if ($user->role == 'umkm') {
            $usaha = Usaha::where('id', $request->usaha_id)->where('user_id', $user->id)->first();
            if (!$usaha) {
                return redirect()->back()->with('error', 'Anda hanya dapat membuat laporan untuk usaha milik sendiri.');
            }
        } elseif ($user->role == 'admin_wilayah') {
            $usaha = Usaha::where('id', $request->usaha_id)->where('wilayah_id', $user->wilayah_id)->first();
            if (!$usaha) {
                return redirect()->back()->with('error', 'Anda hanya dapat membuat laporan untuk usaha di wilayah Anda.');
            }
        }

        Pelaporan::create($request->all());

        return redirect()->route('admin.pelaporan-index')
            ->with('success', 'Laporan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = auth()->user();
        $laporan = Pelaporan::findOrFail($id);
        
        if ($user->role == 'umkm') {
            if ($laporan->usaha->user_id != $user->id) {
                abort(403, 'Unauthorized action.');
            }
            $usahas = Usaha::where('user_id', $user->id)->get();
        } elseif ($user->role == 'admin_wilayah') {
            if ($laporan->usaha->wilayah_id != $user->wilayah_id) {
                abort(403, 'Unauthorized action.');
            }
            $usahas = Usaha::where('wilayah_id', $user->wilayah_id)->get();
        } else {
            $usahas = Usaha::all();
        }

        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $layout = $user->role == 'umkm' ? 'layouts.umkm' : 'layouts.admin_premium';
        return view('admin.pelaporan.edit-pelaporan', compact('laporan', 'usahas', 'months', 'layout'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $laporan = Pelaporan::findOrFail($id);

        if ($user->role == 'umkm' && $laporan->usaha->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'kode_laporan' => 'required|string|unique:pelaporan,kode_laporan,' . $id,
            'usaha_id' => 'required|exists:usaha,id',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'omset' => 'required|numeric',
            'deskripsi' => 'nullable|string',
        ]);

        if ($user->role == 'umkm') {
            $usaha = Usaha::where('id', $request->usaha_id)->where('user_id', $user->id)->first();
            if (!$usaha) {
                return redirect()->back()->with('error', 'Anda hanya dapat memperbarui laporan untuk usaha milik sendiri.');
            }
        } elseif ($user->role == 'admin_wilayah') {
            $usaha = Usaha::where('id', $request->usaha_id)->where('wilayah_id', $user->wilayah_id)->first();
            if (!$usaha) {
                return redirect()->back()->with('error', 'Anda hanya dapat memperbarui laporan untuk usaha di wilayah Anda.');
            }
        }

        $laporan->update($request->all());

        return redirect()->route('admin.pelaporan-index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $laporan = Pelaporan::findOrFail($id);

        if ($user->role == 'umkm' && $laporan->usaha->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $laporan->delete();

        return redirect()->route('admin.pelaporan-index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    public function chart(Request $request)
    {
        $user = auth()->user();
        $query = Pelaporan::query();

        // 1. Role-based filtering
        if ($user->role == 'umkm') {
            $query->whereHas('usaha', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->role == 'admin_wilayah') {
            $query->whereHas('usaha', function($q) use ($user) {
                $q->where('wilayah_id', $user->wilayah_id);
            });
        }

        // 2. Filter by Usaha (if admin/wilayah)
        if ($request->filled('usaha_id')) {
            $query->where('usaha_id', $request->usaha_id);
        }

        // 3. Filter by Year (Default to current)
        $selectedYear = $request->input('tahun', date('Y'));
        $query->where('tahun', $selectedYear);

        // 4. Time Range Filter (if provided)
        $range = $request->input('range', '1 tahun');
        if ($range == '6 bulan') {
            // Logic for last 6 months relative to today? 
            // Or just a subset of the year? Given the 12-month bar chart, subset is cleaner.
        }

        $data = $query->select('bulan', 'omset', 'tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        // Data for filters
        $availableYears = Pelaporan::distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty()) $availableYears = [date('Y')];

        if ($user->role == 'umkm') {
            $usahas = Usaha::where('user_id', $user->id)->get();
        } elseif ($user->role == 'admin_wilayah') {
            $usahas = Usaha::where('wilayah_id', $user->wilayah_id)->get();
        } else {
            $usahas = Usaha::all();
        }
            
        $layout = $user->role == 'umkm' ? 'layouts.umkm' : 'layouts.admin_premium';
        return view('admin.pelaporan.chart-pelaporan', compact('data', 'layout', 'availableYears', 'usahas', 'selectedYear', 'range'));
    }
}
