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
        $request->validate([
            'settings.*' => 'nullable',
        ]);

        if ($request->hasFile('settings')) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'ico'];
            foreach ($request->file('settings') as $key => $image) {
                if ($image && $image->isValid()) {
                    $ext = strtolower($image->getClientOriginalExtension());
                    if (in_array($ext, $allowedExtensions)) {
                        $filename = "IMG-" . time() . (int) $key . '.' . $ext;
                        $image->move(public_path('/uploads/setting/'), $filename);
                        Setting::where("setting_id", (int) $key)->update(["setting_value" => $filename]);
                    }
                }
            }
        }

        $textSettings = $request->input('settings', []);
        if (is_array($textSettings)) {
            foreach ($textSettings as $key => $val) {
                if (!is_array($val) && !is_object($val)) {
                    Setting::where("setting_id", (int) $key)->update(["setting_value" => $val]);
                }
            }
        }

        Session::flash('successMsg', 'Setting updated successfully');
        return response()->json(['redirect_url' => route('setting')]);
    }
}
