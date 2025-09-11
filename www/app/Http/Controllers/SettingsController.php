<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display company settings.
     */
    public function index()
    {
        $settings = CompanySetting::getCurrent();
        
        return $this->viewWithTitle('settings.index', compact('settings'), 'index');
    }

    /**
     * Show the form for creating company settings.
     */
    public function create()
    {
        return $this->viewWithTitle('settings.create', [], 'create');
    }

    /**
     * Store newly created company settings.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'brn' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'phone_primary' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email_primary' => 'nullable|email|max:255',
            'email_secondary' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'currency' => 'nullable|string|max:3',
            'timezone' => 'nullable|string|max:50',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['logo']);
        $data['created_by'] = Auth::id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        CompanySetting::create($data);

        return redirect()->route('settings.index')
            ->with('success', 'Company settings created successfully.');
    }

    /**
     * Display the specified company settings.
     */
    public function show(CompanySetting $setting)
    {
        return view('settings.show', compact('setting'));
    }

    /**
     * Show the form for editing company settings.
     */
    public function edit(CompanySetting $setting)
    {
        return $this->viewWithTitle('settings.edit', compact('setting'), 'edit');
    }

    /**
     * Update company settings.
     */
    public function update(Request $request, CompanySetting $setting)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'brn' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'phone_primary' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email_primary' => 'nullable|email|max:255',
            'email_secondary' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'currency' => 'nullable|string|max:3',
            'timezone' => 'nullable|string|max:50',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['logo']);
        $data['updated_by'] = Auth::id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        $setting->update($data);

        return redirect()->route('settings.index')
            ->with('success', 'Company settings updated successfully.');
    }

    /**
     * Remove company settings.
     */
    public function destroy(CompanySetting $setting)
    {
        // Delete logo if exists
        if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $setting->delete();

        return redirect()->route('settings.index')
            ->with('success', 'Company settings deleted successfully.');
    }

    /**
     * Get current company settings (API endpoint).
     */
    public function getCurrent()
    {
        $settings = CompanySetting::getCurrent();
        
        if (!$settings) {
            return response()->json(['message' => 'No company settings found'], 404);
        }

        return response()->json($settings);
    }

    /**
     * Update logo only.
     */
    public function updateLogo(Request $request, CompanySetting $setting)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete old logo if exists
        if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $logoPath = $request->file('logo')->store('company-logos', 'public');
        $setting->update([
            'logo_path' => $logoPath,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Logo updated successfully',
            'logo_url' => Storage::disk('public')->url($logoPath)
        ]);
    }
}
