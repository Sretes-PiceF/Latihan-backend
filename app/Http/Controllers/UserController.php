<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function registerPelanggan(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telpn' => 'required|string|max:15',
            'username' => 'required|string|unique:user,username',
            'password' => 'required|string|min:6',
            'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // 1. Create data pelanggan
            $pelanggan = Pelanggan::create([
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'no_telpn' => $validated['no_telpn'],
            ]);

            // 2. Create user untuk pelanggan
            $user = new User([
                'username' => $validated['username'],
                'password' => bcrypt($validated['password']),
                'role' => 'pelanggan',
            ]);

            $pelanggan->user()->save($user);

            // 3. Upload dan simpan file KTP
            $ktpData = null;
            if ($request->hasFile('ktp_file')) {
                $file = $request->file('ktp_file');

                // Validasi tambahan untuk memastikan file valid
                if (!$file->isValid()) {
                    throw new \Exception('File KTP tidak valid');
                }

                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('ktp_files', $fileName, 'public');

                // Generate full URL untuk file
                $fileUrl = asset('storage/' . $filePath);

                // Create data KTP di tabel pelanggan_data
                $ktpData = $pelanggan->pelangganData()->create([
                    'jenis' => 'KTP',
                    'file' => $filePath, // simpan path file
                ]);

                // Tambahkan URL ke response
                $ktpData->file_url = $fileUrl;
            }

            DB::commit();

            // Response data
            $responseData = [
                'message' => 'Registrasi pelanggan berhasil',
                'data' => [
                    'pelanggan' => $pelanggan,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                    ]
                ]
            ];

            // Tambahkan data KTP jika berhasil diupload
            if ($ktpData) {
                $responseData['data']['ktp'] = [
                    'id' => $ktpData->id,
                    'jenis' => $ktpData->jenis,
                    'file_path' => $ktpData->file,
                    'file_url' => $ktpData->file_url,
                    'upload_status' => 'success',
                    'message' => 'File KTP berhasil diupload'
                ];
            }

            return response()->json($responseData, 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage(),
                'file_validation' => [
                    'status' => 'error',
                    'message' => 'Validasi file gagal',
                    'required_format' => 'jpg, jpeg, png, pdf',
                    'max_size' => '2MB'
                ]
            ], 500);
        }
    }


    public function registerAdmin(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telpn' => 'required|string|max:15',
            'username' => 'required|string|unique:user,username',
            'password' => 'required|string|min:6',
        ]);

        try {
            // 1. Create data Admin
            $pelanggan = Admin::create([
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'no_telpn' => $validated['no_telpn'],
            ]);

            // 2. Create user untuk pelanggan
            $user = new User([
                'username' => $validated['username'],
                'password' => bcrypt($validated['password']),
                'role' => 'admin',
            ]);

            $pelanggan->user()->save($user);

            return response()->json([
                'message' => 'Registrasi admin berhasil',
                'data' => [
                    'pelanggan' => $pelanggan,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Cari user berdasarkan username
            $user = User::where('username', $validated['username'])->first();

            // Cek jika user exists dan password match
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'Username atau password salah'
                ], 401);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Get user details dengan relasi
            $userDetails = [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
            ];

            // Tambahkan data berdasarkan role
            if ($user->role === 'admin' && $user->userable) {
                $userDetails['admin'] = [
                    'id' => $user->userable->id,
                    'nama' => $user->userable->nama,
                    'alamat' => $user->userable->alamat,
                    'no_telpn' => $user->userable->no_telpn,
                ];
            } elseif ($user->role === 'pelanggan' && $user->userable) {
                $userDetails['pelanggan'] = [
                    'id' => $user->userable->id,
                    'nama' => $user->userable->nama,
                    'alamat' => $user->userable->alamat,
                    'no_telpn' => $user->userable->no_telpn,
                ];
            }

            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $userDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Hapus token yang sedang digunakan
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout berhasil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
