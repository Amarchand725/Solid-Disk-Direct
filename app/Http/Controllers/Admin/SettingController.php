<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function create()
    {
        $this->authorize('setting-create');
        $settings = Setting::first();
        if(empty($settings)){
            $title = 'Setting';
            return view('admin.settings.create', compact('title'));
        }else{
            return redirect()->route('settings.edit', $settings->id);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'logo' => 'required|image|mimes:jpeg,png,gif|max:2048',
            'favicon' => 'required|image|mimes:jpeg,png'
        ]);

        DB::beginTransaction();

        try{
            $model = new Setting();

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('admin/assets/img/logo'), $logoName);

                $model->logo = $logoName;
            }

            if ($request->hasFile('favicon')) {
                $favicon = $request->file('favicon');
                $faviconName = time() . '.' . $favicon->getClientOriginalExtension();
                $favicon->move(public_path('admin/assets/img/favicon'), $faviconName);

                $model->favicon = $faviconName;
            }

            if ($request->hasFile('banner')) {
                $banner = $request->file('banner');
                $bannerName = time() . '.' . $banner->getClientOriginalExtension();
                $banner->move(public_path('admin/assets/img/banner'), $bannerName);

                $model->banner = $bannerName;
            }

            $model->name = $request->name;
            $model->max_leaves = $request->max_leaves;
            $model->max_discrepancies = $request->max_discrepancies;
            $model->currency_symbol = $request->currency_symbol;
            $model->country = $request->country;
            $model->area = $request->area;
            $model->city = $request->city;
            $model->state = $request->state;
            $model->zip_code = $request->zip_code;
            $model->address = $request->address;
            $model->save();

            DB::commit();

            return redirect()->route('settings.edit', $model->id)->with('message', 'Setting details added successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('setting-edit');
        $title = 'Setting Details';
        $model = Setting::where('id', $id)->first();
        $country = Country::where('name', $model->country)->first();
        $states = [];
        if(!empty($country)){
            $states = State::where('country_id', $country->id)->get();
        }

        $state = State::where('name', $model->state)->first();
        $cities = [];
        if(!empty($state)){
            $cities = City::where('state_id', $state->id)->get();
        }
        
        return view('admin.settings.edit', get_defined_vars());
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'phone_number' => 'required',
            'support_email' => 'required',
            'address' => 'required',
        ]);

        DB::beginTransaction();

        try{
            $model = Setting::where('id', $id)->first();

            if($request->hasFile('logo')){
                if ($model->logo) {
                    $oldImagePath = storage_path('app/uploads/company/'.$model->logo);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete the old image
                    }
                }

                // Step 3: Store the new image
                $logoName = $request->file('logo')->store('uploads/company', 'public');
                $model->logo = $logoName;
            }

            if($request->hasFile('favicon')){
                if ($model->favicon) {
                    $oldImagePath = storage_path('app/uploads/company/'.$model->favicon);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete the old image
                    }
                }

                // Step 3: Store the new image
                $faviconName = $request->file('favicon')->store('uploads/company', 'public');
                $model->favicon = $faviconName;
            }

            if($request->hasFile('black_logo')){
                if ($model->black_logo) {
                    $oldImagePath = storage_path('app/uploads/company/'.$model->black_logo);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete the old image
                    }
                }

                // Step 3: Store the new image
                $black_logoName = $request->file('black_logo')->store('uploads/company', 'public');
                $model->black_logo = $black_logoName;
            }

            if($request->hasFile('slip_stamp')){
                if ($model->slip_stamp) {
                    $oldImagePath = storage_path('app/uploads/company/'.$model->slip_stamp);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete the old image
                    }
                }

                // Step 3: Store the new image
                $slip_stampName = $request->file('slip_stamp')->store('uploads/company', 'public');
                $model->slip_stamp = $slip_stampName;
            }

            if($request->hasFile('admin_signature')){
                if ($model->admin_signature) {
                    $oldImagePath = storage_path('app/uploads/company/'.$model->admin_signature);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete the old image
                    }
                }

                // Step 3: Store the new image
                $admin_signatureName = $request->file('admin_signature')->store('uploads/company', 'public');
                $model->admin_signature = $admin_signatureName;
            }

            $countryModel = Country::where('name', $request->country)->first();
            $currency_symbol = '';
            if(!empty($countryModel)){
                $currency_symbol = $countryModel->currency_symbol;
            }

            $model->name = $request->name;
            $model->phone_number = $request->phone_number;
            $model->support_email = $request->support_email;
            $model->sale_email = $request->sale_email;
            $model->day_range = $request->day_range;
            $model->start_time = $request->start_time;
            $model->end_time = $request->end_time;
            $model->timezone = $request->timezone;
            $model->website_url = $request->website_url;
            $model->currency_symbol = $currency_symbol;
            $model->country = $request->country;
            $model->area = $request->area;
            $model->city = $request->city;
            $model->state = $request->state;
            $model->zip_code = $request->zip_code;
            $model->address = $request->address;
            $model->facebook_link = $request->facebook_link;
            $model->instagram_link = $request->instagram_link;
            $model->linked_in_link = $request->linked_in_link;
            $model->twitter_link = $request->twitter_link;
            $model->save();

            DB::commit();

            return redirect()->back()->with('message', 'Setting details updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
