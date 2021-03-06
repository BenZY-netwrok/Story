<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdateFloorReplyRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\PostFloor;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $user = $request->user();
        return PostResource::collection(Post::where('owner_user_id', $user->id)->paginate(6));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        // check if image was given and save on local file system
        if(isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $post = Post::create($data);
        
        // create new reply
        foreach($data['floors'] as $floor) {
            $floor['post_id'] = $post->id;
            $this->createFloor($floor);
        }

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, Request $request)
    {
        $user = $request->user();
        // if($user->id !== $post->owner_user_id){
        //     return abort(403, "Unauthorized action.");
        // }
        return new PostResource($post);
    }





    public function storeReply(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();
         // Update post in the database
         $post->update($data);

         // Get ids as plain array of existing replies
             $existingIds = $post->floors()->pluck('id')->toArray();
         // Get ids as plain array of new replies
             $newIds = Arr::pluck($data['floors'], 'id');
         // Delete replies
             $toDelete = array_diff($existingIds, $newIds);
         // Find replies to add
             $toAdd = array_diff($newIds, $existingIds);
 
         // Delete replies by $toDelete array
             PostFloor::destroy($toDelete);
 
         // Create new replies
             foreach($data['floors'] as $floor) {
                 if(in_array($floor['id'], $toAdd)) {
                     $floor['post_id'] = $post->id;
                     $this->createFloor($floor);
                 }
             }
         
         // Update existing replies
             $replyMap = collect($data['floors'])->keyBy('id');
             foreach($post->floors as $floor) {
                 if(isset($replyMap[$floor->id])) {
                     $this->updateFloor($floor, $replyMap[$floor->id]);
                 }
             }
 
         return new PostResource($post);
    }





    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        //
        $data = $request->validated();
        
        // Check if image was given and save on local file system
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            // If there is an old image, delete it
            if ($post->image) {
                $absolutePath = public_path($post->image);
                File::delete($absolutePath);
            }
        }

        // Update post in the database
        $post->update($data);

        // Get ids as plain array of existing replies
            $existingIds = $post->floors()->pluck('id')->toArray();
        // Get ids as plain array of new replies
            $newIds = Arr::pluck($data['floors'], 'id');
        // Delete replies
            $toDelete = array_diff($existingIds, $newIds);
        // Find replies to add
            $toAdd = array_diff($newIds, $existingIds);

        // Delete replies by $toDelete array
            PostFloor::destroy($toDelete);

        // Create new replies
            foreach($data['floors'] as $floor) {
                if(in_array($floor['id'], $toAdd)) {
                    $floor['post_id'] = $post->id;
                    $this->createFloor($floor);
                }
            }
        
        // Update existing replies
            $replyMap = collect($data['floors'])->keyBy('id');
            foreach($post->floors as $floor) {
                if(isset($replyMap[$floor->id])) {
                    $this->updateFloor($floor, $replyMap[$floor->id]);
                }
            }

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, Request $request)
    {
        //
        $user = $request->user();
        if($user->id !== $post->owner_user_id) {

            return abort(403, "Unauthorized action.");
        }

        $post->delete();

        // If there is an old image, delete it
        if ($post->image) {
            $absolutePath = public_path($post->image);
            File::delete($absolutePath);
        }

        return response('', 204);
    }

    private function saveImage($image)
    {
        // Check if image is valid base64 string
        if(preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

             // Check if file is an image
             if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }

        }else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }


    private function createFloor($data)
    {
        // if(is_array($data['content'])) {
        //     $data['content'] = json_encode($data['content']);
            
        // }

        $validator = Validator::make($data, [
            
            'content' => 'required|string',
            'post_id' => 'exists:App\Models\Post,id',
            'floorNum' => 'nullable|int',
            'owner_user_id' => 'exists:App\Models\User,id'

        ]);

        return PostFloor::create($validator->validate());
    }


    private function updateFloor(PostFloor $floor, $data) {
        // if (is_array($data['data'])) {
        //     $data['data'] = json_encode($data['data']);
        // }
        $validator = Validator::make($data, [
            'id' => 'exists:App\Models\PostFloor,id',
            'content' => 'required|string',
            'floorNum' => 'nullable|int'

        ]);

        return $floor->update($validator->validated());
    }

}
