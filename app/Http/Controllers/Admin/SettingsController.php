<?php

namespace App\Http\Controllers\Admin;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller {

    public function edit() {
        $settings = Settings::all()->pluck('value', 'key')->toArray();
        if (!$settings) {
            abort(404);
        }
        return view('admin.settings.edit')->with(['settings' => $settings]);
    }

    public function update(Request $request) {
        $fields = Settings::getFieldsIfValid($request);
        $settings = Settings::all()->pluck('value', 'key')->toArray();
        foreach ($fields as $key => $value) {
            if($settings[$key] !== $fields[$key]){
                if($key === 'logo') {
                    $path = '/public/img/logo';
                    Storage::makeDirectory($path);
                    Storage::delete($path . '/' . $settings['logo']);
                    $request->file('logo')->storeAs($path, $value);
                }
                Settings::where('key', $key)->update(['value' => $value]);
            }
        }
        return back()->with('success', 'Settings updated');
    }


}
