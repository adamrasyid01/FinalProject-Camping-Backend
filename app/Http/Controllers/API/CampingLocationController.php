<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\CampingLocation;
use Exception;
use Illuminate\Http\Request;

class CampingLocationController extends Controller
{
    //
    public function index()
    {
        try {
            //code...
            $locations = CampingLocation::all();
            return ResponseFormatter::success($locations, 'Data lokasi perkemahan berhasil diambil');
        } catch (Exception $e) {
            return ResponseFormatter::error('Data lokasi perkemahan gagal diambil', 500, $e->getMessage());
        }
    }
    public function getLocationWithSites(Request $request, $id)
    {
        // Ambil parameter search, jika tidak ada nilainya kosong
        $search = $request->query('search', '');

        // Ambil lokasi dengan campingSites yang sesuai ID
        $location = CampingLocation::with(['campingSites' => function ($query) use ($search) {
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%");
            }
        }])->find($id);

        if (!$location) {
            return ResponseFormatter::error('Location not found', 404);
        }

        return ResponseFormatter::success($location, 'Location retrieved successfully');
    }
}
