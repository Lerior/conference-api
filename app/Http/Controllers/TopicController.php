<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Conference;
use App\Models\Topic;

class TopicController extends Controller
{

    public function addTopic (StoreTopicRequest $request) {

        $conference = Conference::findOrFail($request->conference_id);

        $this->authorize('create', [Topic::class, $conference]);
        
        $topic = Topic::create($request->validated());
        
        return response()->json($topic, 201);
    }

    public function getTopics (){
        
      return TopicResource::collection(
        Topic::with('user')->get()
      );
    }

    public function getTopicById($id){
        
        $topic=Topic::find($id);

        if (!$topic) {
            return response()->json(['message'=>'Topic not found'], 404);
        }

        return response()->json($topic, 200);
    }

    public function updateTopicById(UpdateTopicRequest $request, $id){

        $topic = Topic::find($id);

        if (!$topic) {
            return response()->json(['message' => 'Topic not found'],404);
        }

        $topic->update($request->validated());

        return response()->json(['message'=>'Topic updated successfully'],200);

    }

    public function deleteTopicById($id){
        
        $topic = Topic::find($id);

        if (!$topic) {
            return response()->json(['message'=>'Topic not found'],404);
        }

        $this->authorize('delete', $topic);

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
