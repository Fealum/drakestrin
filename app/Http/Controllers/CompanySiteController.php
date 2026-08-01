<?php

namespace App\Http\Controllers;

use App\Models\Economy\Company;
use App\Models\Economy\CompanySite;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompanySiteController extends Controller
{
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct($permissionService);
    }

    public function store(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('manage', $company);
        $data = $request->validate($this->rules($company));

        $company->sites()->create([
            'name' => trim($data['name']),
            'location_id' => (int) $data['location_id'],
        ]);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function update(Request $request, Company $company, CompanySite $site): RedirectResponse
    {
        $this->assertCompany($company, $site);
        $this->authorize('update', $site);
        $data = $request->validate($this->rules($company, $site));
        $newLocationId = (int) $data['location_id'];

        if ($site->location_id !== null
            && $newLocationId !== (int) $site->location_id
            && ($site->inventory()->exists() || $site->workers()->exists())) {
            return back()->withErrors(['location_id' => 'Ein Standort mit Inventar oder Beschäftigten kann nicht verlegt werden.']);
        }

        $site->update(['name' => trim($data['name']), 'location_id' => $newLocationId]);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function headquarters(Company $company, CompanySite $site): RedirectResponse
    {
        $this->assertCompany($company, $site);
        $this->authorize('update', $site);
        abort_if($site->location_id === null, 422, 'Ein ungeklärter Standort kann nicht Hauptsitz werden.');
        $company->update(['headquarters_site_id' => $site->id]);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function destroy(Request $request, Company $company, CompanySite $site): RedirectResponse
    {
        $this->assertCompany($company, $site);
        $this->authorize('delete', $site);

        if ((int) $company->headquarters_site_id === (int) $site->id) {
            return back()->withErrors(['site' => 'Bestimme zuerst einen anderen Hauptsitz.']);
        }

        if ($company->sites()->count() <= 1 || $site->inventory()->exists() || $site->workers()->exists()) {
            return back()->withErrors(['site' => 'Der letzte, befüllte oder beschäftigte Standort kann nicht gelöscht werden.']);
        }

        DB::transaction(function () use ($request, $company, $site) {
            $site->representatives()->get()->each(function ($representative) use ($request, $company, $site) {
                DB::table('company_role_events')->insert([
                    'company_id' => $company->id,
                    'company_site_id' => $site->id,
                    'character_id' => $representative->character_id,
                    'role' => $representative->role->value,
                    'action' => 'dismissed',
                    'acted_by_user_id' => $request->user()->id,
                    'created_at' => now(),
                ]);
            });
            $site->representatives()->delete();
            $site->productionRuns()->update(['company_site_id' => null]);
            $site->delete();
        });

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    private function rules(Company $company, ?CompanySite $site = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location_id' => [
                'required',
                'integer',
                Rule::exists('locations', 'id'),
                Rule::unique('company_sites', 'location_id')->where('company_id', $company->id)->ignore($site?->id),
            ],
        ];
    }

    private function assertCompany(Company $company, CompanySite $site): void
    {
        abort_unless((int) $site->company_id === (int) $company->id, 404);
    }
}
