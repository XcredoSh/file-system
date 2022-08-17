<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Files;
use App\Models\User;

class MainController extends Controller
{
    public $successStatus = 200;
    public $createdStatus = 201;
    public $acceptedStatus = 202;
    public $badRequestStatus = 400;
    public $unauthorized = 401;
    public $notFound = 404;


    function __construct()
    {
         $this->middleware('can:add file', ['only' => ['store']]);
         $this->middleware('can:show file', ['only' => ['index','show']]);
         $this->middleware('can:delete file', ['only' => ['destroy']]);
        //  $this->middleware('can:permission create', ['only' => ['create','store']]);
        //  $this->middleware('can:permission edit', ['only' => ['edit','update']]);
        //  $this->middleware('can:permission delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = null;
        $role_name = auth()->user()->getRoleNames()[0];
      
        if($role_name === 'moderator')
        {
            $files = Files::where('user_id', '!=', 1)->get();
        }else {
            $files = User::find(auth()->user()->id)->files;
        }

        return response()->json(
            [
                'files' => $files
            ],
            $this->successStatus); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:pdf,xlx,docx|max:2048',
        ]);


        $file = $_FILES['file'];
        $tmp = $_FILES['file']['tmp_name']; // It is the path to the file
        $hash = md5_file($tmp);
        $hash_name = md5($file['name']);
        $ext = $request->file->getClientOriginalExtension();
        $original_name = $request->file->getClientOriginalName();
        

        $hash_summa = Files::where('hash_summa', '=', $hash)->first();
        
        $parent_id = null;
     
        if($hash_summa)
        {
            $parent_id = $hash_summa->id;
            $hash_name = null;
            $ext = null;
        }else{
            $request->file->move(storage_path('app/public/uploads'), $hash_name . '.' . $request->file->getClientOriginalExtension());
        }

        $new_file = new Files();
        $new_file->user_id = auth()->user()->id;
        $new_file->path = storage_path('app/public/uploads');
        $new_file->path_url = 'http://127.0.0.1:8000';
        $new_file->type = $file['type'];
        $new_file->size = $file['size'];
        $new_file->hash_summa = $hash;
        $new_file->hash_name = $hash_name;
        $new_file->file_name = $original_name;
        $new_file->ext = $ext;
        $new_file->parent_id = $parent_id;
        if($new_file->save())
        {
            return response()->json(
                [
                    'success' => true,
                    'message' => 'File saved successfully!',
                    'file_name' => $file['name'],
                    'code' => '201'
                    ],
                 $this->createdStatus); 
        }

        // $base = base64_encode($_FILES['file']);
        // $s_files = User::find(1)->files;

        return ['hash' => $hash, 'files' => $file];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $file = Files::where('id', $id)->first();
        $role_name = auth()->user()->getRoleNames()[0];
        

        if($file->parent_id != null)
        {
            $parent_file = Files::where('id', $file->parent_id)->select('path', 'file_name as original_name', 'hash_name', 'ext')->get();
            $file['parent_file'] = $parent_file;
        }
        
        return response()->json(
            [
                'success' => true,
                'code' => '200',
                'file' => $file
                ],
             $this->successStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role_name = auth()->user()->getRoleNames()[0];
        if($role_name === 'moderator')
        {
            $file = Files::where('id', $id)->first();
            if($file->user_id === 1){
                return response()->json(
                    [
                        'message' => 'Files deleted error'
                    ]
                );
            }
        }
        Files::destroy($id);
        return response()->json(
            [
                'message' => 'Files deleted  successfully'
            ]
        );
    }
}
