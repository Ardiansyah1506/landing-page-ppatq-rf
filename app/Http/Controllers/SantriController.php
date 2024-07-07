<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SantriController extends Controller
{
    public function index(){
        $query = DB::table('santri_detail')
        ->select(
            'santri_detail.nama', 
            'santri_detail.photo', 
            'santri_detail.kelas', 
            'santri_detail.kecamatan', 
            'ref_kamar.employee_id AS kamar_employee_id',
            'ref_kelas.employee_id AS kelas_employee_id',
            'guru_murroby.nama AS guru_murroby',  
            'wali_kelas.nama AS wali_kelas'  
        )
        ->leftJoin('ref_kamar', 'santri_detail.kamar_id', '=', 'ref_kamar.id')
        ->leftJoin('ref_kelas', 'santri_detail.kelas', '=', 'ref_kelas.code')
        ->leftJoin('employee_new AS guru_murroby', 'ref_kamar.employee_id', '=', 'guru_murroby.id')
        ->leftJoin('employee_new AS wali_kelas', 'ref_kelas.employee_id', '=', 'wali_kelas.id')
        ->inRandomOrder();
    
    
    $data = $query->paginate(8);
    
        return view('santri.index', ['data' => $data]);
    }

    public function get_santri(Request $request)
    {
        try {
            // Mendapatkan kata kunci pencarian dari query string
            $search = $request->input('search');

            // Mengambil data dari tabel 'santri_detail' dengan pagination
            $query = DB::table('santri_detail')
        ->select(
            'santri_detail.nama', 
            'santri_detail.photo', 
            'santri_detail.kelas', 
            'santri_detail.kecamatan', 
            'ref_kamar.employee_id AS kamar_employee_id',
            'ref_kelas.employee_id AS kelas_employee_id',
            'guru_murroby.nama AS guru_murroby', 
            'wali_kelas.nama AS wali_kelas'
        )
        ->leftJoin('ref_kamar', 'santri_detail.kamar_id', '=', 'ref_kamar.id')
        ->leftJoin('ref_kelas', 'santri_detail.kelas', '=', 'ref_kelas.code')
        ->leftJoin('employee_new AS guru_murroby', 'ref_kamar.employee_id', '=', 'guru_murroby.id')
        ->leftJoin('employee_new AS wali_kelas', 'ref_kelas.employee_id', '=', 'wali_kelas.id')
        ->inRandomOrder();

    
            if (!empty($search)) {
                $query->where(function($subQuery) use ($search) {
                    $subQuery->where('santri_detail.nama', 'like', '%' . $search . '%')
                             ->orWhere('santri_detail.kelas', 'like', '%' . $search . '%')
                             ->orWhere('santri_detail.kecamatan', 'like', '%' . $search . '%')
                             ->orWhere('guru_murroby.nama', 'like', '%' . $search . '%')
                             ->orWhere('wali_kelas.nama', 'like', '%' . $search . '%');
                });
            }
    
            // Mendapatkan hasil dengan pagination (10 item per halaman)
            $data = $query->paginate(8);
            // Kembalikan data dalam format JSON
            return response()->json($data);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
