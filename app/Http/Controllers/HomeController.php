<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        // TODO 3: Lengkapi logika index():
        // 1. Buat query pencarian berdasarkan judul, penulis, atau penerbit jika ada input 'search'
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('penulis', 'like', '%' . $request->search . '%')
                    ->orWhere('penerbit', 'like', '%' . $request->search . '%');
            });
        });

        // 2. Tambahkan filter penerbit dan filter ketersediaan stok jika ada input 'penerbit' / 'status'
        $query->when($request->penerbit, function ($q) use ($request) {
            $q->where('penerbit', $request->penerbit);
        });

        $query->when($request->status, function ($q) use ($request) {
            if ($request->status === 'tersedia') {
                $q->where('stok', '>', 0);
            } elseif ($request->status === 'habis') {
                $q->where('stok', '<=', 0);
            }
        });
        // 3. Tambahkan pengurutan data berdasarkan input 'sort' (judul, tahun, stok, default: latest)
        match ($request->sort) {
            'judul' => $query->orderBy('judul', 'asc'),
            'tahun' => $query->orderBy('tahun_terbit', 'desc'),
            'stok'  => $query->orderBy('stok', 'desc'),
            default => $query->latest(),
        };
        // 4. Lakukan paginasi 8 data dengan withQueryString()
        $buku = $query->paginate(8)->withQueryString();
        // 5. Ambil list penerbit unik untuk opsi dropdown filter
        $listPenerbit = Buku::select('penerbit')->distinct()->pluck('penerbit');
        // 6. Return view 'home' dengan data buku dan list penerbit

        $buku = Buku::paginate(8);
        $listPenerbit = collect();

        return view('home', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        // TODO 3: Kembalikan view 'home_show' membawa data $buku
        return view('home_show', compact('buku'));
    }
}
