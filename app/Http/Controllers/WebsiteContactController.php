<?php

namespace App\Http\Controllers;

use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmLeadStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebsiteContactController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // Quietly discard basic bot submissions while keeping a normal response.
        if (filled($request->input('website'))) {
            return redirect()->to(route('home') . '#contact');
        }

        $validator = Validator::make($request->all(), [
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'product_interested' => ['nullable', 'string', 'max:255'],
            'requirements' => ['required', 'string', 'max:5000'],
        ]);
        $validator->after(function ($validator) use ($request): void {
            if (blank($request->input('email')) && blank($request->input('mobile'))) {
                $validator->errors()->add('contact_method', 'Add an email address or phone number so our team can reply.');
            }
        });
        $data = $validator->validate();

        $data['source_id'] = CrmLeadSource::query()
            ->where('is_active', true)
            ->where('slug', 'website')
            ->value('id');
        $data['status_id'] = CrmLeadStatus::query()
            ->where('is_active', true)
            ->where('slug', 'new')
            ->value('id');
        $data['priority'] = 'normal';
        $data['lead_date'] = today();
        $data['country'] = 'India';

        CrmLead::query()->create($data);

        return redirect()
            ->to(route('home') . '#contact')
            ->with('contact_success', 'Thanks for reaching out. Your enquiry has been sent to our team.');
    }
}
