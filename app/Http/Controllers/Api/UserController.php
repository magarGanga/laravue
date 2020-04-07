<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Resources\User as UserResource;


class UserController extends Controller
{

/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page;


        return response()->json(['users' => UserResource::collection(User::paginate($per_page)), 'roles' => Role::pluck('name')->all()], 200);

        //return user with role data using ::with('role')
       // return response()->json(['users' => UserResource::collection(User::with('role')->paginate($per_page))], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $user = User::create([
           'name' => $request->name,
           'email' => $request->email,

       ]);
       return response()->json(['user' => $user], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::where('name', 'LIKE', "%$id%")->paginate();
        return response()->json(['users' => $users], 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $user = User::find($id);
        $user->name = $request->name;
        $user->save();
        return response()->json(['user' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

      $user = User::find($id)->delete();
      return response()->json(['user' => $user], 200);
    }

    public function deleteAll(Request $request){
        User::whereIn('id', $request->users)->delete();
        return response()->json(['Message', 'Records Deleted Successfully']);
    }


    public function login(Request $request){

        $credentials = $request->only('email', 'password');

        if(Auth::attempt($credentials)){
            $token = Str::random(80);
            Auth::user()->api_token = $token;
            Auth::user()->save();
            $admin = Auth::user()->isAdmin();
            return response()->json(['token' => $token, 'isAdmin' => $admin ], 200);
        }
        return response()->json(['status' => 'Email or Password is Wrong'], 403);


    }

    public function verify(Request $request)
    {
        return $request->user()->only('name', 'email');
    }
}
