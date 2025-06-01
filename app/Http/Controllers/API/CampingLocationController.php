<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\CampingLocation;
use App\Models\CampingSite;
use Exception;
use Illuminate\Http\Request;

class CampingLocationController extends Controller
{
    //
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
}
