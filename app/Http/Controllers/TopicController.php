<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{

    private function validateTopic(Request $request){
        
        return Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:255',
            'description' => 'required|string|max:5000',
            'conference_id' => 'required|exists:conferences,id',
            'speaker_name' => 'required_without:user_id|string|max:120|nullable',
            'user_id' => 'required_without:speaker_name|exists:users,id|nullable',
        ]);


    }
    public function addTopic (Request $request) {

        $validator = $this->validateTopic($request);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()],422);
        }

        $validated = $validator->validated();
        
        Topic::create($validated);
        
        return response()->json(['message' => 'Topic created successfully'], 201);
    }

    public function getTopics (){
        
        $topics= Topic::all();

        if ($topics->isEmpty()) {
            return response()->json(['message'=>'Not topics found'], 404);
        }

        return response()->json($topics, 200);
    }

    public function getTopicById($id){
        
        $topic=Topic::find($id);

        if (!$topic) {
            return response()->json(['message'=>'Topic not found'], 404);
        }

        return response()->json($topic, 200);
    }

    public function updateTopicById(Request $request, $id){

        $topic=Topic::find($id);

        if (!$topic) {
            return response()->json(['message'=>'Topic not found'], 404);
        }

        $validator = $this->validateTopic($request);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()],422);
        }

        $validated = $validator->validated();

        $topic->update($validated);

        return response()->json(['message'=>'Topic updated successfully'],200);

    }

    public function deleteTopicById($id){
        
        $topic = Topic::find($id);

        if (!$topic) {
            return response()->json(['message'=>'Topic not found'],404);
        }

        $topic->delete();

        return response()->json(['message'=>'Topic deleted successfully'],200);
    }

    public function getConferenceTopic ($id) {

        $topic = Topic::with('conference')->find($id);

        if (!$topic) {
            return response()->json(['message'=>'No register found'],404);
        }

        return response()->json($topic->conference, 200);
    }
}
