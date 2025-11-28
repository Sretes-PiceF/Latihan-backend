<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use App\Models\Penyewaan;
use App\Models\PenyewaanDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function Melihat()
    {
        $data = Product::all();

        return response()->json([
            "Message" => "Data yang ada",
            "Data" => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,product_id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Cek user -> pelanggan
            $user = auth()->user();
            if (!$user || $user->role !== 'pelanggan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pelanggan yang dapat melakukan penyewaan'
                ], 403);
            }

            // Debug: Cek data user dan pelanggan_id
            Log::info('User Data:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'pelanggan_id' => $user->pelanggan_id
            ]);

            // Ambil model pelanggan dari relasi hasOne
            $pelanggan = $user->pelanggan;

            // Debug: Cek apakah pelanggan ditemukan
            Log::info('Pelanggan Data:', [
                'pelanggan_found' => !!$pelanggan,
                'pelanggan_id_from_user' => $user->pelanggan_id,
                'pelanggan_details' => $pelanggan ? [
                    'id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'alamat' => $pelanggan->alamat
                ] : 'Not found'
            ]);

            if (!$pelanggan) {
                // Cek langsung di database apakah pelanggan dengan ID tersebut ada
                $pelangganExists = \App\Models\Pelanggan::where('id', $user->pelanggan_id)->exists();

                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan tidak ditemukan. Silakan lengkapi profil terlebih dahulu.',
                    'debug' => [
                        'user_id' => $user->id,
                        'pelanggan_id_in_user' => $user->pelanggan_id,
                        'pelanggan_exists_in_db' => $pelangganExists,
                        'suggestion' => $pelangganExists ?
                            'Relasi mungkin salah, periksa model relationships' :
                            'Data pelanggan dengan ID tersebut tidak ada di database'
                    ]
                ], 404);
            }

            // Ambil product
            $product = Product::where('product_id', $request->product_id)->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            // Validasi stok
            if ($product->product_stock < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi',
                    'stok_tersedia' => $product->product_stock,
                    'jumlah_diminta' => $request->jumlah
                ], 400);
            }

            // Hitung durasi dan subtotal
            $tanggalMulai = Carbon::parse($request->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
            $durasi = $tanggalMulai->diffInDays($tanggalSelesai);

            if ($durasi < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durasi sewa minimal 1 hari'
                ], 400);
            }

            $subharga = $product->product_price * $durasi * $request->jumlah;

            // 1. Simpan penyewaan
            $penyewaan = Penyewaan::create([
                'pelanggan_id' => $pelanggan->id,
                'tglsewa' => $request->tanggal_mulai,
                'tglkembali' => $request->tanggal_selesai,
                'status_pembayaran' => 'Belum bayar',
                'status_kembali' => 'Belum kembali',
                'total_harga' => $subharga,
            ]);

            // 2. Simpan detail
            $detail = PenyewaanDetail::create([
                'penyewaan_id' => $penyewaan->id,
                'product_id'   => $product->product_id,
                'jumlah'       => $request->jumlah,
                'subharga'     => $subharga
            ]);

            // 3. Kurangi stok produk
            $product->decrement('product_stock', $request->jumlah);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penyewaan berhasil dibuat',
                'data' => [
                    'penyewaan' => $penyewaan,
                    'detail' => $detail,
                    'rincian' => [
                        'durasi_hari' => $durasi,
                        'harga_per_hari' => $product->product_price,
                        'jumlah_barang' => $request->jumlah,
                        'total_harga' => $subharga
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat penyewaan',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePelangganRequest $request, Pelanggan $pelanggan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        //
    }
}
