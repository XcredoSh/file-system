<?php

namespace App\Http\Controllers\Api\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Files;
use App\Models\User;

class ModeratorFileController extends Controller
{
    public $successStatus = 200;
    public $createdStatus = 201;
    public $acceptedStatus = 202;
    public $badRequestStatus = 400;
    public $unauthorized = 401;
    public $notFound = 404;

    public function __construct()
    {
        $this->middleware(['role:moderator','permission:delete file|show file']);
    }
    /**
     * Display the specified resource.
     *
     
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // auth()->user()->assignRole()
        $role_id = [];
        $user = User::role('simple-user')->select('id')->get();
        foreach ($user as $key => $id) {
            $role_id[$key] = $id->id; 
        }

        $files = Files::whereIn('user_id', $role_id)->get();

        return response()->json(
            [
                'success' => true,
                'code' => '200',
                'files' => $files
            ],
            200); 
    }

    /**
     * Display a listing of the resource.
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = Files::where('id', $id)->first();
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Files::destroy($id);
        return response()->json(
            [
                'message' => 'Files deleted  successfully'
            ]
        );
    }
}
