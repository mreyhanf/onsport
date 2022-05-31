<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RemoveParticipantsController extends Controller
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

    /**
     * Show user information page
     * By Reyhan
     */
    public function deleteParticipant($idevent, $username) {
        DB::table('partisipan')->where('username', $username)->delete();
        DB::table('eoikt')->where('idevent', $idevent)->where('username', $username)->delete();

        $event = DB::table('eo')->where('idevent', $idevent)->get();
        $judulevent = $event[0]->judulevent;
        $usernamepg = $event[0]->usernamehost;

        RemoveParticipantsController::createParticipantRemovalNotification($idevent, $username, $judulevent, $usernamepg);
        RemoveParticipantsController::setStatusPenerimaan($idevent);

        return redirect()->back();
    }

    /**
     * Create participant removal notification for the removed partisipan
     * By Reyhan
     */
    public function createParticipantRemovalNotification($idevent, $usernamepn, $judulevent, $usernamepg) {

        $jenis = 2; //jenis notifikasi participant removal = 2
        DB::table('notifikasi')->insert([
            'usernamepn' => $usernamepn,
            'jenis' => $jenis,
            'idevent' => $idevent,
            'judulevent' => $judulevent,
            'usernamepg' => $usernamepg
        ]);
    }

    /**
     * Change status penerimaan of the event by comparing the event's quota with total participants
     * By Reyhan
     */
    public function setStatusPenerimaan($idevent) {
        $event = DB::table('eo')->where('idevent', $idevent)->first();
        $kuota = $event->kuotapartisipan;
        $totalpartisipan = DB::table('partisipan')->where('idevent', $idevent)->count();

        if ($kuota > $totalpartisipan) {
            DB::table('eo')->where('idevent', $idevent)->update(['statuspenerimaan' => 1]);
        } else {
            DB::table('eo')->where('idevent', $idevent)->update(['statuspenerimaan' => 0]);
        }
    }
}