<?php

namespace App\Http\Controllers\Admin;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

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
            if (array_key_exists($key, $settings)) {
                if ($settings[$key] !== $fields[$key] || ($key === 'logo' && $value) || ($key === 'cover' && $value)) {
                    if (($key === 'logo' || $key === 'cover') && $value) {
                        $path = "/public/img/$key";
                        Storage::makeDirectory($path);
                        Storage::setVisibility($path, 'public');

                        // Original
                        Storage::delete($path . '/' . $settings[$key]);
                        $request->file($key)->storeAs($path, $value);

                        if ($key === 'logo') {
                            // Only png are supported
                            $name = substr($value, 0, -4);
                            $old_name = substr($settings['logo'], 0, -4);
                            $this->convertAndStore($request->file('logo'), $path, $name, $old_name, 72);
                            $this->convertAndStore($request->file('logo'), $path, $name, $old_name, 96);
                            $this->convertAndStore($request->file('logo'), $path, $name, $old_name, 128);
                            $this->convertAndStore($request->file('logo'), $path, $name, $old_name, 192);
                            $this->convertAndStore($request->file('logo'), $path, $name, $old_name, 256);
                        }
                    }
                    Settings::where('key', $key)->update(['value' => $value]);
                }
            } else {
                $s = new Settings();
                $s->key = $key;
                $s->value = $value;
                $s->save();
            }
        }
        Artisan::call('config:cache');
        // Sadly this session message is not showed because config:cache clears sessions too
        return back()->with('success', "Settings updated");
    }

    function convertAndStore($file, $path, $name, $old_name, $size) {
        $image = Image::decode($file)->cover($size, $size);
        Storage::delete("$path/$old_name-$size.png");
        $image->save(storage_path("app$path/$name-$size.png"));        
    }


}
