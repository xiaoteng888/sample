<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Auth;
use Illuminate\Http\Request;

class UsersController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth',[
              'except' => ['show','create','store','index']
        ]);
        $this->middleware('guest',[
              'only' => ['create']
        ]);
    }
    
    public function index()
    {
       $users = User::paginate(10);
       return view('users.index',compact('users'));
    }

    public function create()
    {
    	return view('users.create');
    }
    
    public function store(UserRequest $request,User $user)
    {   
        $user->fill($request->all());
        $user->password = bcrypt($user->password);
        $user->save();
        Auth::login($user);
        session()->flash('success','欢迎，您将在此开启一段旅程！');
        return redirect()->route('users.show',[$user]);
    }

    public function show(User $user)
    {
       return view('users.show',compact('user'));
    }
 
    public function edit(User $user)
    {
       $this->authorize('update',$user);
       return view('users.edit',compact('user'));
    }

    public function update(Request $request)
    {
       $this->validate($request,[
               'name' => 'required|max:50',
               'password' => 'nullable|confirmed|min:6'
       ]);
       $user = $request->user();
       $this->authorize('update',$user);
       $data['name'] = $request->name;
       if($request->password){
          $data['password'] = $request->password;
       }
       $user->update($data);
       session()->flash('success','个人资料更新成功');
       return redirect()->route('users.show',$user->id);
    }

    public function destroy(User $user)
    {  
       $this->authorize('destroy',$user);
       $user->delete();
       session()->flash('success','用户删除成功!');
       return back();
    }
}
