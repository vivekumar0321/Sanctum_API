<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\select;
use App\Http\Controllers\API\BaseController as BaseController;


class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts']= Post::all();
        // return response([
        //     'status'=> true,
        //     'message' => ' All Post Data',
        //     'data' => $data
        // ],200);
        return $this->sendResponse($data, 'All Post Data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateuser  = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image'=> 'required|mimes:png,jpg,jpeg,gif'
            ] 
        );
        if($validateuser->fails()){
            // return response([
            //     'status'=> false,
            //     'message' => "Validation Error",
            //     'errors' => $validateuser->errors()->all()
            // ],401);
            return $this->errResponse('Validation Error',$validateuser->errors()->all());
        }
        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imgName = time().".".$ext;  
        $img->move(public_path().'/uploads',$imgName);
        $post =  Post::Create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imgName,
        ]);
        if($post){
            // return response([
            //     'status'=> true,
            //     'message' => 'Post Created Successfully',
            //     'post' => $post 
            // ],200);
            return $this->sendResponse($post, 'Post Created Successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select('id','title','description','image')->where(['id' => $id])->get();
        // return response([
        //     'status' => true,
        //     'message'=> "Your Single Post",
        //     'post' => $data 
        // ],200);
        return $this->sendResponse($data, 'Your Single Post');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateuser  = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image'=> 'required|mimes:png,jpg,jpeg,gif'
            ] 
        );
        if($validateuser->fails()){
            // return response([
            //     'status'=> false,
            //     'message' => "Validation Error",
            //     'errors' => $validateuser->errors()->all()
            // ],401);
            return $this->errResponse('Validation Error',$validateuser->errors()->all());

        }
        $postImage = Post::select('id','image')->where(['id'=>$id])->get();
        if(!empty($request->image)){
            $path = public_path().'/uploads';
            if(!empty($postImage[0]->image)){
                $old_path = $path.$postImage[0]->image;
                if(file_exists($old_path)){
                    unlink($old_path);
                }
            }
            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $imgName = time().".".$ext;  
            $img->move(public_path().'/uploads',$imgName);
        }else{
            $imgName = $postImage[0]->image; 
        }
        
        $post =  Post::where(['id'=>$id])->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imgName,
        ]);
        if($post){
            // return response([
            //     'status'=> true,
            //     'message' => 'Post Updated Successfully',
            //     'post' => $post 
            // ],200);
            return $this->sendResponse($post, 'Post Updated Successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagepath = Post::select('image')->where('id',$id)->get();
        $filpath = public_path().'/uploads/'. $imagepath[0]['image'];
        unlink($filpath);
        $post = Post::where('id',$id)->delete();

        if($post){
            // return response([
            //     'status'=> true,
            //     'message' => 'Your Post has been remove.',
            //     'post' => $post 
            // ],200);
            return $this->sendResponse($post,'Your Post has been removed.');
        }
    }
}
