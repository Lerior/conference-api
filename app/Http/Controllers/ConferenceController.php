<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class ConferenceController extends Controller
{

    private function validateConference(Request $request){

        return Validator::make($request->all(), [
            'name' => 'required|string|min:10|max:255',
            'description' => 'required|string|max:5000',
            'date' => 'required|date|after_or_equal:today',
        ]);
    }
    public function addConference (Request $request){

        $validator = $this->validateConference($request);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()],422);
        }

        $validated = $validator->validated();
        $validated['user_id'] = Auth::id();

        Conference::create($validated);

        return response()->json(['message'=>'Conference created'],201);
    }

    public function getConferences () {

        $conferences = Conference::all();

        if ($conferences->isEmpty()) {
            return response()->json(['message'=>'No conferences found'],404);
        }

        return response()->json($conferences, 200);
    }

    public function getConferenceById($id){

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        return response()->json($conference,200);
    }

    public function updateConferenceById(Request $request, $id) {

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        $validator = $this->validateConference($request);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()],422);
        }

        $validated = $validator->validated();

        $conference->update($validated);

        return response()->json(['message'=>'Conference updated successfully'], 200);
    }

        public function deleteConferenceById($id){

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        $conference->delete();

        return response()->json(['message'=>'Conference deleted successfully'], 200);
    }

    public function getTopicsConference($id){
        
        $conference=Conference::with('topics')->find($id);

        if (!$conference) {
            return response()->json(['message'=>'No registers found'], 404);
        }

        return response()->json($conference->topics, 200);
    }
}
