<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateEventsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showCreateEvents()
    {
        return view('createevents');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $date = date('Y-m-d', strtotime($request->tanggal));

        // menyimpan data file gambar yang diupload ke variabel $file
		$file = $request->file('gambar');

        // isi dengan nama folder tempat kemana file diupload
		$tujuan_upload = 'events_image';
        $namadanlokasi = '';
        if(!empty($file)) {
            $namadanlokasi = $tujuan_upload . "/" . $file->getClientOriginalName(); //teknik tanpa direname
        }


	// insert data ke table eo
	$id = DB::table('eo')->insertGetId([
        'usernamehost'=> $user->username,
		'judulevent' => $request->judulevent,
		'kategori' => $request->kategori,
        'tanggal' => $date,
        'jam' => $request->jam,
        'lokasi' => $request->lokasi,
        'kota' => $request->kota,
        'rangeub' => $request->rangeub,
        'rangeua' => $request->rangeua,
        'levelkeahlian' => $request->levelkeahlian,
        'kuotapartisipan' => $request->kuotapartisipan,
        'catatan' => $request->catatan,
        'gambar' => $namadanlokasi
	]);

    if(!empty($file)) {
        // upload file
        $file->move($tujuan_upload,$file->getClientOriginalName());
    }

	// alihkan halaman ke halaman event details_host
	return redirect('/event/details/' . $id);
    }

}
