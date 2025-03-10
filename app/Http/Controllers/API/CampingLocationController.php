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
    public function getLocationWithSites($id)
    {
        $location = CampingLocation::with('campingSites')->find($id);

        if (!$location) {
            return ResponseFormatter::error('Location not found', 404);
        }

        return ResponseFormatter::success($location, 'Location retrieved successfully');
    }
}
