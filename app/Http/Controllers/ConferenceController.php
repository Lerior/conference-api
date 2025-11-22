<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class ConferenceController extends Controller
{
    public function addConference (Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:10|max:255',
            'description' => 'required|string|max:5000',
            'date' => 'required|date|after_or_equal:today',
        ]);

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

        return response()->json($conferences, 202);
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

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|min:10|max:255',
            'description' => 'sometimes|string|max:5000',
            'date' => 'sometimes|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()],422);
        }

        $validated = $validator->validated();
        $validated['user_id'] = Auth::id();

        $conference->update($validated);

        return response()->json(['message'=>'Conference updated successfully',200]);
    }

        public function deleteConferenceById($id){

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        $conference->delete();

        return response()->json(['message'=>'Conference deleted successfully',200]);
    }
}
