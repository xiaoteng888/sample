<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Auth;
use Illuminate\Http\Request;
use Mail;

class UsersController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth',[
              'except' => ['show','create','store','index','confirmEmail']
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
        //Auth::login($user);
        $this->sendEmail($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect()->route('users.show',[$user]);
    }
    public function sendEmail($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'Sample888@gmail.com';
        $name = 'Sample';
        $to = $user->email;
        $subject = '感谢注册Sample,请确认邮箱！';
        Mail::send($view,$data,function($message)use($from,$name,$to,$subject){
            $message->from($from,$name)->to($to)->subject($subject);
        });
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
    public function confirmEmail($token)
    {
       $user = User::where('activation_token',$token)->firstOrFail();
       $user->activated = true;
       $user->activation_token = null;
       $user->save();
       Auth::login($user);
       session()->flash('success','恭喜你，激活成功!');
       return redirect()->route('users.show',[$user]);
    }
}
