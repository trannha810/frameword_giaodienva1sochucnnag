<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //AdminDashboard
    public function AdminDashboard(){
        return view('admin.index');
    }//End Method
    //AdminLogin
    public function AdminLogin(){
        return view('admin.admin_login');
    }//End Method
    //AdminDestroy
    public function AdminDestroy(Request $request){
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }//End Method
    //AdminProfile
    public function AdminProfile(){
        $id = Auth::user()->id;
        $adminData = User::find($id);
        return view('admin.admin_profile_view',compact('adminData'));
    } //End Method
    //AdminUpdateProfile
    public function AdminUpdateProfile(Request $request){
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        //Photo process
        if($request->file('photo')){
            $file = $request->file('photo');
            @unlink('upload/admin_images/'.$data->photo);
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'),$filename);
            $data['photo'] = $filename;
        }
        $data->save();
        $notification = [
            'message' => 'Admin Update Profile Successfully.',
            'alert-type' => 'success',
        ];
        return redirect()->back()->with($notification);
    } //End Method
    //AdminChangePassword
    public function AdminChangePassword(){
        return view('admin.admin_change_password');
    } //End Method
    //AdminUpdatePassword
    public function AdminUpdatePassword(Request $request){
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        //Match old_password
        if(!Hash::check($request->old_password,auth::user()->password)){
            return back()->with("error","Old Password Doesn't Match!!!");
        }
        //Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password),
        ]);
        return back()->with("status","Password Change Successfully!");
    } //End Method
}
