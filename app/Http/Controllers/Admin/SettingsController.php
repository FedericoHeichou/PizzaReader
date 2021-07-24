<?php

namespace App\Http\Controllers\Admin;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
            if ($settings[$key] !== $fields[$key] || ($key === 'logo' && $value) || ($key === 'cover' && $value)) {
                if (($key === 'logo' || $key === 'cover') && $value) {
                    $path = "/public/img/$key";
                    Storage::makeDirectory($path);

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
        }
        Artisan::call('config:cache');
        // Sadly this session message is not showed because config:cache clears sessions too
        return back()->with('success', "Settings updated");
    }

    function convertAndStore($file, $path, $name, $old_name, $size) {
        $height = Image::make($file)->height();
        $width = Image::make($file)->width();
        $new_width = $size;
        $new_height = $size;
        if ($width > $height) $new_width = null;
        else $new_height = null;
        $file = Image::make($file)->resize($new_width, $new_height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $file->crop($size, $size);
        $file->encode('png');
        Storage::delete("$path/$old_name-$size.png");
        $file->save(storage_path("app$path/$name-$size.png"));
    }


}
