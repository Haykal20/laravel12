<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function index()
    {
        // Tampilkan semua data mahasiswa, tidak hanya milik user yang login
        $mahasiswas = Mahasiswa::all();
        return view('mahasiswa.index', compact('mahasiswas'));
    }

    public function create()
    {
        return view('mahasiswa.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nim' => 'required',
            'nama' => 'required',
            'semester' => 'required|integer',
            'mata_kuliah' => 'required',
            'foto' => 'nullable|image|max:2048'
        ]);
        $data['user_id'] = Auth::id();

        $input = $data;

        if ($request->cropped_foto) {
            $base64 = $request->cropped_foto;
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
            $base64 = base64_decode($base64);
            $filename = 'mahasiswa/'.uniqid().'.jpg';
            \Storage::disk('public')->put($filename, $base64);
            $input['foto'] = $filename;
        } elseif ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('mahasiswa', 'public');
            $input['foto'] = $path;
        }

        Mahasiswa::create($input);
        return redirect()->route('mahasiswa.index');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $this->authorize('update', $mahasiswa);
        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        
        if ($mahasiswa->user_id !== auth()->id()) {
            return back()->with('error', 'Tidak bisa edit data mahasiswa lain');
        }

        $validated = $request->validate([
            'nim' => 'required',
            'nama' => 'required',
            'prodi' => 'required',
            'semester' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg,bmp,webp,heic,heif,tiff,raw|max:10240'
        ]);

        try {
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $extension = $foto->getClientOriginalExtension();
                $filename = uniqid() . '.' . $extension;

                // Hapus foto lama
                if ($mahasiswa->foto) {
                    Storage::disk('public')->delete($mahasiswa->foto);
                }

                // Simpan foto baru
                $fotoPath = $foto->storeAs('photos', $filename, 'public');
                $mahasiswa->foto = $fotoPath;
            }
            // Update mahasiswa
            $mahasiswa->update([
                'nim' => $validated['nim'],
                'nama' => $validated['nama'],
                'semester' => $validated['semester']
            ]);

            // Update related user
            $mahasiswa->user->update([
                'name' => $validated['nama'],
                'nim' => $validated['nim'],
                'prodi' => $validated['prodi']
            ]);

            return redirect()->route('mahasiswa.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            \Log::error('Error updating foto: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Cari mahasiswa dan user terkait
            $mahasiswa = Mahasiswa::with('user')->findOrFail($id);
            
            // Cek kepemilikan
            if ($mahasiswa->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Tidak bisa hapus data mahasiswa lain');
            }

            // Hapus foto
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }

            // Hapus mahasiswa
            $mahasiswa->delete();

            // Hapus user (akan men-trigger logout)
            $user = $mahasiswa->user;
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            $user->delete();

            DB::commit();

            return redirect()->route('login')
                ->with('success', 'Akun berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}