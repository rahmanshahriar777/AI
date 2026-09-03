<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\MailTemplate;
use Illuminate\Http\Request;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = MailTemplate::latest()->paginate(5);
        return view('mailtemplates.index', compact('data'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mailtemplates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'mailbody' => 'required',
        ]);

        $mailTemplate = new MailTemplate();
        $mailTemplate->name = $request->name;
        $mailTemplate->subject = $request->subject;
        $mailTemplate->body = $request->mailbody;
        $mailTemplate->status = $request->status;
        $mailTemplate->save();

        return redirect()->route('mailtemplates.index')
            ->with('success', 'Mail Template created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($mailid)
    {
        $mailTemplate = MailTemplate::find($mailid);
        if (!$mailTemplate) {
            return redirect()->route('mailtemplates.show')
                ->with('error', 'Mail Template not found.');
        }
        return view('mailtemplates.show', compact('mailTemplate'));
    }


    public function showmailtemplate($id, $rel_type='', $rel_id = 0)
    {
        $template = MailTemplate::findOrFail($id);

        $template = $template->body;
        if($rel_type=='enquiry'){
            $variables = [];
            $enquiry = Enquiry::find($rel_id);
            if($enquiry){
                $variables['{t}'] = ''; //TODO title not in use
                $variables['{fn}'] = $enquiry->customer->contact_firstname;
                $variables['{ln}'] = $enquiry->customer->contact_lastname;
            }
        }
        $replacedContent = str_replace(array_keys($variables), array_values($variables), $template);

        return response()->json([
            'message' => $replacedContent, // adjust according to your DB field
        ]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($mailid)
    {
        $mailTemplate = MailTemplate::find($mailid);
        if (!$mailTemplate) {
            return redirect()->route('mailtemplates.index')
                ->with('error', 'Mail Template not found.');
        }
        // dd($mailTemplate);
        return view('mailtemplates.edit', compact('mailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $mailid)
    {
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'mailbody' => 'required',
        ]);
        $mailTemplate = MailTemplate::find($mailid);
        if (!$mailTemplate) {
            return redirect()->route('mailtemplates.index')
                ->with('error', 'Mail Template not found.');
        }

        $mailTemplate->name = $request->name;
        $mailTemplate->subject = $request->subject;
        $mailTemplate->body = $request->mailbody;
        $mailTemplate->status = $request->status;
        $mailTemplate->save();

        return redirect()->route('mailtemplates.edit', $mailid)
            ->with('success', 'Mail Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($mailid)
    {
        $mailTemplate = MailTemplate::find($mailid);
        if (!$mailTemplate) {
            return redirect()->route('mailtemplates.index')
                ->with('error', 'Mail Template not found.');
        }
        // Delete the mail template
        // $mailTemplate->attachments()->delete(); // Assuming you have a relationship for attachments
        $mailTemplate->delete();

        return redirect()->route('mailtemplates.index')
            ->with('success', 'Mail Template deleted successfully.');
    }
}
