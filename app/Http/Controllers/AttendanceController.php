<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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
    public function addAttendance (Request $request){

        $validator = $this->validateAttendance($request);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()],422);
        }

        $validated = $validator->validated();
        $validated['user_id'] = Auth::id();

        $exist = Attendance::where('user_id', $validated['user_id'])
                           ->where('conference_id', $validated['conference_id'])
                           ->first();

        if ($exist) {
            return response()->json(['message'=>'Attendance already exists!'], 409);
        }

        Attendance::create($validated);

        return response()->json(['message'=>'Attendance created successfully'],200);
    }

    
}
