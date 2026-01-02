<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltersConferenceRequest;
use App\Http\Requests\StoreConferenceRequest;
use App\Http\Requests\UpdateConferenceRequest;
use App\Http\Resources\ConferenceListResource;
use App\Http\Resources\ConferenceResource;
use App\Http\Resources\TopicResource;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class ConferenceController extends Controller
{

    public function addConference (StoreConferenceRequest $request){

        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $conference = Conference::create($data);

        return new ConferenceListResource($conference->load('user'));
    }

    public function getConferences (FiltersConferenceRequest $request) {

        $data = $request->validated();

        $perPage = $data['per_page'] ?? 10;
        $orderBy = $data['order_by'] ?? 'date';
        $order = $data['order'] ?? 'desc';

        return ConferenceListResource::collection(
            Conference::with('user')
            ->when(isset($data['title']), 
            fn ($query) =>
                $query->title($data['title'])
                )
            ->when(isset($data['first_date']) && isset($data['last_date']), 
            fn ($query) =>
                $query->betweenDates($data['first_date'],$data['last_date'])
                )
            ->orderBy($orderBy, $order)
            ->paginate($perPage)
        );
    }

    public function getMyConferences(){

        return ConferenceListResource::collection(
            Conference::with('user')->where('user_id', Auth::id())->get()
        );
    }

    public function getConferenceById($id){

        $conference=Conference::find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        return response()->json($conference);
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
        
        $conference=Conference::with('topics.user')->find($id);

        if (!$conference) {
            return response()->json(['message'=>'No registers found'], 404);
        }

        return TopicResource::collection($conference->topics);
    }

    public function getConferenceFull($id) {

        $conference = Conference::with(['user', 'topics.user'])->find($id);

        if (!$conference) {
            return response()->json(['message'=>'Conference not found'],404);
        }

        return new ConferenceResource($conference);
    }
}
