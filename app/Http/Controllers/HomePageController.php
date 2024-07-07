<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomePageController extends Controller
{
    
    public function index(){
        try {
                $data['beritas'] = DB::table('berita')
                ->select('berita.id','berita.kategori_id', 'berita.judul', 'berita.slug','berita.created_at', 'berita.thumbnail', 'berita.gambar_dalam', 'berita.isi_berita', 'berita.user_id', 'users.name AS nama_user', 'kategori_berita.nama_kategori AS nama_kategori')
                ->leftJoin('users', 'berita.user_id', '=', 'users.id')
                ->leftJoin('kategori_berita', 'berita.kategori_id', '=', 'kategori_berita.id')
                ->orderBy('berita.created_at', 'desc')
                ->paginate(3);

                $data['agendas'] = DB::table('agenda')
                ->select('judul', 'isi', 'tanggal_mulai', DB::raw('
                    CASE 
                        WHEN MONTH(tanggal_mulai) = 1 THEN "Januari"
                        WHEN MONTH(tanggal_mulai) = 2 THEN "Februari"
                        WHEN MONTH(tanggal_mulai) = 3 THEN "Maret"
                        WHEN MONTH(tanggal_mulai) = 4 THEN "April"
                        WHEN MONTH(tanggal_mulai) = 5 THEN "Mei"
                        WHEN MONTH(tanggal_mulai) = 6 THEN "Juni"
                        WHEN MONTH(tanggal_mulai) = 7 THEN "Juli"
                        WHEN MONTH(tanggal_mulai) = 8 THEN "Agustus"
                        WHEN MONTH(tanggal_mulai) = 9 THEN "September"
                        WHEN MONTH(tanggal_mulai) = 10 THEN "Oktober"
                        WHEN MONTH(tanggal_mulai) = 11 THEN "November"
                        WHEN MONTH(tanggal_mulai) = 12 THEN "Desember"
                    END as bulan'))
                ->whereNull('deleted_at')
                ->where('tanggal_mulai', '>=', DB::raw('CURDATE()'))
                ->orderByRaw('ABS(DATEDIFF(NOW(), tanggal_mulai))')
                ->limit(5)
                ->get();


                $data['jumlahSiswaLaki'] = DB::table('santri_detail')->where([
                    'status' => 0,
                    'jenis_kelamin' => 'L'
                ])->count();
                
                $data['jumlahSiswaPerempuan'] = DB::table('santri_detail')->where([
                    'status' => 0,
                    'jenis_kelamin' => 'P'
                    ])->count();

                $data['jumlahMurroby'] = DB::table('tb_guru')
                ->where('jabatan_new','murobby')
                ->count();

                $data['jumlahGuru'] = DB::table('tb_guru')
                ->whereIn('jabatan_new',['murobby','ustadz','tahfidz'])
                ->count();

            return view('HomePage.index', $data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getSantriRandom(){
        $santri = null;
        $exists = false;
            
        while (!$exists) {

            $santri = DB::table('santri_detail')
                ->select(
                    'santri_detail.nama', 
                    'santri_detail.photo', 
                    'santri_detail.kelas', 
                )
                ->inRandomOrder()
                ->first();


            $url = 'https://manajemen.ppatq-rf.id/assets/img/upload/photo/' . $santri->photo;
            $headers = get_headers($url);
            $exists = strpos($headers[0], '200');
            if ($exists) {
                $exists = true;
            }
        }
            return response()->json($santri);
    }

    public function getPegawaiRandom(){
        // $pegawai = null;
        // $exists = false;

        // while(!$exists){

        $pegawai = DB::table('employee_new')
        ->select(
            'nama',
            'jabatan',
            'photo'
            )
        ->inRandomOrder()
        ->first();

        // $url = 'https://manajemen.ppatq-rf.id/assets/img/upload/photo/' . $pegawai->photo;
        // $headers = get_headers($url);
        // $exists = strpos($headers[0], '200');

        // if($exists){
        //     $exists = true;
        // }
        
        // }
        return response()->json($pegawai);
    }
    
    public function About(){
        return view('About.index');
    }
    public function visimisi(){
        return view('visimisi.index');
    }
    public function sekapursirih(){
        return view('sekapur_sirih.index');
    }
 
    
}
