<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;

class UsersController extends Controller
{
    public function create()
    {
    	return view('users.create');
    }
    
    public function store(UserRequest $request,User $user)
    {   
        $user->fill($request->all());
        $user->password = bcrypt($user->password);
        $user->save();
        session()->flash('success','欢迎，您将在此开启一段旅程！');
        return redirect()->route('users.show',[$user]);
    }

    public function show(User $user)
    {
       return view('users.show',compact('user'));
    }


}
