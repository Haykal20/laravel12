<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nim' => ['required', 'string', 'max:20', 'unique:users'],
            'prodi' => ['required', 'string', 'max:100'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,svg,bmp,webp,heic,heif,tiff,raw', 'max:10240'],
        ], [
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto tidak didukung. Gunakan: JPG, PNG, GIF, SVG, BMP, WEBP, HEIC, TIFF, atau RAW',
            'foto.max' => 'Ukuran foto maksimal 10MB'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        try {
            $fotoPath = null;
            if (request()->hasFile('foto')) {
                $foto = request()->file('foto');
                $extension = $foto->getClientOriginalExtension();
                $filename = uniqid() . '.' . $extension;
                
                // Convert HEIC/HEIF to JPG if needed
                if (in_array(strtolower($extension), ['heic', 'heif'])) {
                    // Konversi ke JPG menggunakan library image processing
                    // Jika menggunakan Intervention Image:
                    // $image = Image::make($foto)->encode('jpg', 90);
                    // Storage::disk('public')->put('photos/' . $filename, $image);
                    $fotoPath = $foto->storeAs('photos', $filename, 'public');
                } else {
                    $fotoPath = $foto->storeAs('photos', $filename, 'public');
                }
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['nim'] . '@student.com', // Generate email dari NIM
                'password' => Hash::make($data['password']),
                'nim' => $data['nim'],
                'prodi' => $data['prodi'],
                'foto' => $fotoPath,
            ]);

            // Buat data mahasiswa otomatis
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'nama' => $data['name'],
                'semester' => 1,
                'mata_kuliah' => 'Belum ada',
                'foto' => $fotoPath
            ]);

            // Setelah create user dan mahasiswa, logout
            Auth::logout();
            
            return $user;
        } catch (\Exception $e) {
            \Log::error('Error uploading foto: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function registered(Request $request, $user)
    {
        Auth::logout();
        return redirect()->route('login')
            ->with('success', 'Pendaftaran berhasil! Silakan login menggunakan NIM atau Username Anda.');
    }
}
