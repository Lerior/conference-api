<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Conference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    private function validateAttendance(Request $request){
        return Validator::make($request->all(),[
            'conference_id' => 'required|exists:conferences,id',
        ]);


    }
    public function addAttendance (StoreAttendanceRequest $request){

        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $exist = Attendance::where('user_id', $data['user_id'])
                           ->where('conference_id', $data['conference_id'])
                           ->first();

        if ($exist) {
            return response()->json(['message'=>'Attendance already exists!'], 409);
        }

        Attendance::create($data);

        return response()->json(['message'=>'Attendance created successfully'],200);
    }

    public function getAttendances(){
        
        $attendances = Attendance::all();

        if ($attendances->isEmpty()){
            return response()->json(['message'=>'No attendance found'],404);
        }

        return response()->json($attendances,200);
    }

    public function getAttendanceById($id){

        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message'=>'No attendance found'],404);
        }

        return response()->json($attendance,200);
    }

    public function getMyAttendances(){

        $attendances =  Attendance::where('user_id', Auth::id())->with('conference')->get();

        if ($attendances->isEmpty()) {
        return response()->json(['message'=>'No attendance found'],404);
    }

        return AttendanceResource::collection($attendances);
    }

    public function getConferenceAttendancesById($id){

        $conference = Conference::with('attendances.user')->find($id);

        if (!$conference) {
            return response()->json(['message' => 'Conference not found'], 404);
        }

        if ($conference->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

            return response()->json($conference->attendances, 200);
    }    


    public function deleteAttendanceById($id){

        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message'=>'No attendance found'],404);
        }

        $attendance->delete();

        return response()->json(['message'=>'Attendance deleted successfully'], 200);
    }
}
