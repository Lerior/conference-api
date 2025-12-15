<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConferenceRequest;
use App\Http\Requests\UpdateConferenceRequest;
use App\Http\Resources\ConferenceResource;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class ConferenceController extends Controller
{

    public function addConference (StoreConferenceRequest $request){

        $data = $request->validated();
        $data['user_id'] = Auth::id();

        Conference::create($data);

        return response()->json(['message'=>'Conference created'],201);
    }

    public function getConferences () {

        $conferences = Conference::all();

        if ($conferences->isEmpty()) {
            return response()->json(['message'=>'No conferences found'],200);
        }

        return response()->json($conferences, 200);
    }

    public function getMyConferences(){

        $conferences = Conference::where('user_id', Auth::id())->get();

        if ($conferences->isEmpty()) {
            return response()->json(['message'=>'No conferences found'],200);
        }

        return response()->json($conferences,200);
    }

    public function getConferenceById($id){

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        return response()->json($conference,200);
    }

    public function updateConferenceById(UpdateConferenceRequest $request, $id) {

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }


        $conference->update($request->validated());

        return response()->json(['message'=>'Conference updated successfully'], 200);
    }

        public function deleteConferenceById($id){

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        $this->authorize('delete', $conference);

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

    public function getConferenceFull($id) {

        $conference = Conference::with(['user', 'topics.user'])->find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        return new ConferenceResource($conference);
    }
}
