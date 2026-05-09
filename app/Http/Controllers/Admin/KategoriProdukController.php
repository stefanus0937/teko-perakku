<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriProduk; // Pastikan model JenisUsaha sudah dibuat
use Illuminate\Support\Str;

class KategoriProdukController extends Controller
{
    public function index()
    {
        // Mengambil semua data kategori produk dari database
        $kategoriProduk = KategoriProduk::ordered()->get(); // atau bisa juga pakai paginate()
        // Mengirim data ke view
        return view('admin.kategori_produk.index-kategori_produk', [
            'kategoriProduks' => $kategoriProduk,
            'categoryTypeLabels' => KategoriProduk::TYPE_LABELS,
        ]);
    }

    public function create()
    {
        return view('admin.kategori_produk.create-kategori_produk', [
            'categoryTypeLabels' => KategoriProduk::TYPE_LABELS,
        ]);
    }

    public function edit($id)
    {
        // Mengambil data kategori produk berdasarkan ID
        $kategoriProduk = KategoriProduk::findOrFail($id);
        return view('admin.kategori_produk.edit-kategori_produk', [
            'kategoriProduk' => $kategoriProduk,
            'categoryTypeLabels' => KategoriProduk::TYPE_LABELS,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi dan simpan data kategori produk
        $request->validate([
            'kode_kategori_produk' => 'required|string|max:255',
            'nama_kategori_produk' => 'required|string|max:255',
            'category_type' => 'required|string|in:' . implode(',', array_keys(KategoriProduk::TYPE_LABELS)),
            'sort_order' => 'nullable|integer|min:0|max:65535',
        ]);

        // Simpan data ke database
        KategoriProduk::create($request->only([
            'kode_kategori_produk',
            'nama_kategori_produk',
            'category_type',
            'sort_order',
        ]));

        return redirect()->route('admin.kategori_produk-index')
            ->with('success', 'Kategori Produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // Validasi dan update data kategori produk
        $data = $request->validate([
            'kode_kategori_produk' => 'required|string|max:255',
            'nama_kategori_produk' => 'required|string|max:255',
            'category_type' => 'required|string|in:' . implode(',', array_keys(KategoriProduk::TYPE_LABELS)),
            'sort_order' => 'nullable|integer|min:0|max:65535',
        ]);

        // Generate slug dari nama kategori
        $data['slug'] = Str::slug($data['nama_kategori_produk']);

        // Update data ke database
        KategoriProduk::where('id', $id)->update($data);

        return redirect()->route('admin.kategori_produk-index')
            ->with('success', 'Data Kategori Produk berhasil diupdate.');
    }

    public function destroy($id)
    {
        $kategoriProduk = KategoriProduk::findOrFail($id);
        $kategoriProduk->delete();

        return redirect()->route('admin.kategori_produk-index')
            ->with('success', 'Data Kategori Produk berhasil dihapus.');
    }
}
