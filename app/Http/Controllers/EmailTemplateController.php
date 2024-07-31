<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('admin.email_templates.index', compact('templates'));
    }

    public function create()
    {
        $stores = Store::all();
        return view('admin.email_templates.create', compact('stores'));
    }

    public function store(Request $request)
    {
        try {
            // Print the request data for debugging
            print('<pre>');
            print_r($request->all());

            // Validate the request data
            $request->validate([
                'name' => 'required',
                'type' => 'required',
                'subject' => 'required',
                'body' => 'required',
                'store' => 'required|exists:store,storeId'
            ]);

            // Generate the filename
            $filename = strtolower($request->input('name') . '_' . $request->input('type') . '_' . $request->input('store') . '.blade.php');
            $path = resource_path('views/vendor/notifications/' . $filename);

            // Check if the file already exists
            if (File::exists($path)) {
                return redirect()->back()->withErrors(['error' => 'Template file already exists.']);
            }

            // Create the template file with dynamic content wrapped in the base template
            $content = "@extends('vendor.notifications.base')\n@section('content')\n" . $request->input('body') . "\n@endsection";
            File::put($path, $content);

            // Save the template details in the database
            $template = new EmailTemplate();
            $template->name = $request->name;
            $template->subject = $request->subject;
            $template->type = $request->type;
            $template->body = $request->body;
            $template->storeId = $request->store;
            $template->file = $filename;
            $template->save();

            return redirect()->route('emailTemplates.index')->with('success', 'Email template created successfully');
        } catch (ValidationException $e) {
            Log::info($e->errors());
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $template = EmailTemplate::find($id);
            if ($template) {
                $path = resource_path('views/vendor/notifications/' . $template->file);
                if (File::exists($path)) {
                    File::delete($path);
                }
                $template->delete();
                return redirect()->route('emailTemplates.index')->with('success', 'Email template deleted successfully');
            }
            return redirect()->route('emailTemplates.index')->withErrors('Email template not found');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('emailTemplates.index')->withErrors($e->getMessage());
        }
    }
}
