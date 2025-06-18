<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\CampingLocation;
use App\Models\CampingSite;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Gunakan Validator facade
use Illuminate\Support\Facades\DB;

class CampingLocationController extends Controller
{
    //GET CAMPING LOCATIONS
    public function index(Request $request)
    {
        try {
            $filter = $request->query('filter');
            // SEPERTI SWITCH CASE
            $locations = match ($filter) {
                'nama' => CampingLocation::orderBy('name', 'asc')->get(),
                'terbanyak' => CampingLocation::orderBy('total_camps', 'desc')->get(),
                default => CampingLocation::all(),
            };

            return ResponseFormatter::success($locations, 'Data lokasi perkemahan berhasil diambil');
        } catch (Exception $e) {
            return ResponseFormatter::error('Data lokasi perkemahan gagal diambil', 500, $e->getMessage());
        }
    }

    public function getLocationWithSites(Request $request, $id)
    {
        $search = $request->query('search');
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);

        // Cek apakah lokasi valid
        $location = CampingLocation::find($id);
        if (!$location) {
            return ResponseFormatter::error('Location not found', 404);
        }

        // Query camping sites milik lokasi tersebut
        $query = CampingSite::where('location_id', $id);

        // Filter berdasarkan nama jika ada pencarian
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%$search%");
        }

        // Urutkan berdasarkan rating tertinggi
        $query->orderBy('rating', 'desc');

        // Ambil data dengan pagination
        $campingSites = $query->paginate($limit, ['*'], 'page', $page);

        return ResponseFormatter::success($campingSites, 'Filtered camping sites retrieved successfully');
    }

    public function getDetailSites(Request $request, $id, $camping_site_id)
    {
        $campingSite = CampingSite::where('id', $camping_site_id)
            ->where('location_id', $id)
            ->first();

        if (!$campingSite) {
            return ResponseFormatter::error('Camping site not found', 404);
        }

        return ResponseFormatter::success($campingSite, 'Camping site details retrieved successfully');
    }

    // INSERT CITY DATA

    public function insertCampingLocations(Request $request) // Nama fungsi diubah agar lebih deskriptif
    {
        // Ambil semua data dari request, diharapkan berupa array
        $locations = $request->all();

        // Pastikan data yang masuk adalah array
        if (!is_array($locations)) {
            return ResponseFormatter::error('Data yang dikirim harus berupa array.', 400);
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        // Lakukan looping untuk setiap item di dalam array JSON
        foreach ($locations as $locationData) {
            try {
                // Buat validator untuk setiap item di dalam loop
                $validator = Validator::make($locationData, [
                    // Sesuaikan aturan validasi dengan struktur JSON
                    'id' => 'required|integer',
                    'name' => 'required|string|max:255',
                    'image_url' => 'nullable|string',
                    'total_camps' => 'required|integer|min:0',
                ]);

                // Jika validasi untuk item ini gagal, catat error dan lanjutkan ke item berikutnya
                if ($validator->fails()) {
                    $gagal++;
                    $errors[] = [
                        'name' => $locationData['name'] ?? 'N/A',
                        'errors' => $validator->errors()->all()
                    ];
                    continue; // Lanjut ke item berikutnya
                }

                // Gunakan updateOrCreate untuk mencegah duplikasi data
                // Data akan di-update jika id sudah ada, atau dibuat jika belum ada.
                CampingLocation::updateOrCreate(
                    ['id' => $locationData['id']], // Kondisi pengecekan
                    [
                        'name' => $locationData['name'],
                        'image_url' => $locationData['image_url'],
                        'total_camps' => $locationData['total_camps']
                    ]
                );

                $berhasil++;
            } catch (Exception $e) {
                $gagal++;
                $errors[] = [
                    'name' => $locationData['name'] ?? 'N/A',
                    'error' => $e->getMessage()
                ];
            }
        }

        // Siapkan response
        $response = [
            'total_data' => count($locations),
            'berhasil_disimpan' => $berhasil,
            'gagal_disimpan' => $gagal,
            'detail_gagal' => $errors
        ];

        return ResponseFormatter::success($response, 'Proses import selesai');
    }

    // INSERT CAMPING SITE DATA
    public function insertCampingSites(Request $request)
    {
        // Ambil semua data dari request, diharapkan formatnya adalah array JSON
        $sites = $request->all();

        // Validasi awal untuk memastikan data yang dikirim adalah array
        if (!is_array($sites)) {
            return ResponseFormatter::error('Input data harus berupa array.', 400);
        }

        // Siapkan variabel untuk laporan hasil import
        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        // Mulai transaksi database untuk menjaga konsistensi data
        DB::beginTransaction();

        try {
            // Lakukan looping untuk setiap item (camping site) di dalam array
            foreach ($sites as $siteData) {
                // Definisikan aturan validasi sesuai dengan struktur JSON
                $validator = Validator::make($siteData, [
                    'id' => 'required|integer',
                    'name' => 'required|string|max:255',
                    'location_id' => 'required|integer',
                    'link' => 'nullable|url',
                    'total_reviews' => 'required|integer',
                    'rating' => 'required|numeric',
                    'image_url' => 'nullable|string',
                    'location' => 'nullable|string',
                    'text_reviews' => 'required|array',      // Pastikan ini adalah array
                    'total_sentimen' => 'required|array',   // Pastikan ini adalah array
                ]);

                // Jika validasi untuk item ini gagal, catat error dan lanjutkan ke item berikutnya
                if ($validator->fails()) {
                    $gagal++;
                    $errors[] = [
                        'name' => $siteData['name'] ?? 'N/A',
                        'errors' => $validator->errors()->all()
                    ];
                    continue; // Lanjut ke item berikutnya
                }

                // Ambil data yang sudah tervalidasi
                $validatedData = $validator->validated();

                // Gunakan updateOrCreate untuk mencegah duplikasi data
                // Data akan di-update jika id sudah ada, atau dibuat jika belum ada.
                CampingSite::updateOrCreate(
                    ['id' => $validatedData['id']], // Kondisi pengecekan
                    [
                        'name' => $validatedData['name'],
                        'location_id' => $validatedData['location_id'],
                        'link' => $validatedData['link'],
                        'total_reviews' => $validatedData['total_reviews'],
                        'rating' => $validatedData['rating'],
                        'image_url' => $validatedData['image_url'],
                        'location' => $validatedData['location'],
                        // Encode array menjadi string JSON sebelum disimpan ke DB
                        'text_reviews' => $validatedData['text_review'],
                        'total_sentimen' => $validatedData['total_sentimen'],
                    ]
                );

                $berhasil++;
            }

            DB::commit(); // Simpan semua perubahan jika loop berhasil tanpa error fatal

        } catch (Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika terjadi error di tengah jalan
            return ResponseFormatter::error('Proses import gagal total: ' . $e->getMessage(), 500);
        }

        // Siapkan response dengan ringkasan hasil import
        $response = [
            'total_data' => count($sites),
            'berhasil_disimpan' => $berhasil,
            'gagal_disimpan' => $gagal,
            'detail_gagal' => $errors
        ];

        return ResponseFormatter::success($response, 'Proses import selesai');
    }
}
