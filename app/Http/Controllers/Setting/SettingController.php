<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function edit()
    {
        $homepageSetting = Setting::where('setting_for', 'homepage')->orderBy('setting_order')->get();
        $contactSetting = Setting::where('setting_for', 'contact')->orderBy('setting_order')->get();
        $globalseoSetting = Setting::where('setting_for', 'globalseo')->orderBy('setting_order')->get();
        $socialSetting = Setting::where('setting_for', 'social')->orderBy('setting_order')->get();
        //echo '<pre>'; print_r($bannerDetail); exit;
        return view('admin.setting.edit')->with([
            'homepageSetting' => $homepageSetting,
            'contactSetting' => $contactSetting,
            'globalseoSetting' => $globalseoSetting,
            'socialSetting' => $socialSetting
        ]);
    }

    public function update(Request $request)
    {
        //echo '<pre>'; print_r($request->file('settings')); die;
        if(!empty($request->file('settings')) && count($request->file('settings')) > 0 ){
            foreach ($request->file('settings') as $key => $val){
                $image = $val;
                $filename = "IMG-" . time() . $key .  '.' . $image->getClientOriginalExtension();
                foreach ($_FILES['settings']['tmp_name'] as $tkey => $tval){
                    if($key == $tkey){
                        $image->move(public_path('/uploads/setting/'), $filename);
                    }
                }
                if($val!="") {
                    Setting::where("setting_id", $key)->update(["setting_value" => $filename]);
                }
            }
        }

        if (isset($_POST['settings']) && count($_POST['settings']) > 0 ){
            foreach ($_POST['settings'] as $key => $val){
                Setting::where("setting_id", $key)->update(["setting_value" => $val]);
            }
        }

        Session::flash('successMsg', 'Setting updated successfully');
        return response()->json(['redirect_url' => route('setting')]);
    }
}
