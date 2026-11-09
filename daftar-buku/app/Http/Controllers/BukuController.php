<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /* Display a listing of the resource */
    public function index(Request $request)
    {
        $cari = $request->query('cari');

        $daftarBuku = Buku::query()
            ->when($cari, function ($query, $cari) {
                $query->where('judul', 'like', "%{$cari}%")
                      -> orWhere('penulis', 'like', "%{$cari}%");
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('buku.index', compact('daftarBuku', 'cari'));
    }

    /* Show the form for creating a new resource */
    public function create()
    {
        return view('buku.create');
    }

    /* Store a newly created resource in storage */
    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());

        Buku::create($data);

        return redirect()->route('buku.index')
            ->with('sukses', 'Buku berhasil ditambahkan');
    }

    /* Display the specified resource */

    public function show(Buku $buku)
    {
        return view('buku.show', compact('buku'));
    }

    /* Show the form for editing the specified resource */

    public function edit(Buku $buku)
    {
        return view ('buku.edit', compact('buku'));
    }

    /* Update the specified resource in storage */

    public function update(Request $request, Buku $buku)
    {
        $data = $request->validates($this->aturan());

        $buku->update($data);

        return redirect()->route('buku.index')
            ->with('sukses', 'Buku berhasil diperbarui');
    }

    /* Remove the specified resource from storage. */
    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('sukses', 'Buku berhasil dihapus');
    }

    private function aturan(): array
    {
        return [
            'judul'         => 'required|string|max:255',
            'penulis'       => 'required|string|max:255',
            'tahun_terbit'  => 'required|integer|min:1900|max:' . date('Y'),
            'sinopsis'      => 'nullable|string'
        ];
    }
}
