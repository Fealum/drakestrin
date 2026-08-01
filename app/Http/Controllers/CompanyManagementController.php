<?php

namespace App\Http\Controllers;

use App\Http\Requests\Economy\StoreCompanyRepresentativeRequest;
use App\Http\Requests\Economy\StoreCompanyRequest;
use App\Http\Requests\Economy\UpdateCompanyRequest;
use App\Models\Economy\Company;
use App\Models\Economy\CompanyOwner;
use App\Models\Economy\CompanyRepresentative;
use App\Models\Territory\Location;
use App\Models\Territory\Territory;
use App\Repositories\Economy\CompanyRepository;
use App\Repositories\Territory\LocationRepository;
use App\Services\PermissionService;
use App\Support\CompanyRepresentativeRole;
use App\Support\CompanySector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyManagementController extends Controller
{
    public function __construct(
        PermissionService $permissionService,
        private CompanyRepository $companies,
        private LocationRepository $locations,
    ) {
        parent::__construct($permissionService);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Company::class);

        return view('company.create', [
            'characters' => $request->user()->characters()->orderByRaw('LOWER(name)')->get(),
            'locations' => $this->locations->options(),
            'fautheien' => Territory::query()
                ->where('type', 5)
                ->whereDoesntHave('children')
                ->orderByRaw('LOWER(name)')
                ->get()
                ->filter(fn (Territory $territory) => $request->user()->can('create', [Location::class, $territory])),
            'sectors' => CompanySector::cases(),
        ]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $company = $this->companies->create($request->toData(), $request->user());

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function edit(Company $company): View
    {
        $this->authorize('update', $company);
        $company->load('headquarters.location');

        return view('company.edit', [
            'canChangeSector' => $company->canChangeSector(),
            'company' => $company,
            'sectors' => CompanySector::cases(),
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        if ((int) $request->validated('sector') !== (int) $company->type
            && ! $company->canChangeSector()) {
            return back()
                ->withInput()
                ->withErrors(['sector' => 'Der Wirtschaftszweig kann nach Einstellung von Beschäftigten oder begonnener Produktion nicht mehr geändert werden.']);
        }

        $this->companies->update($company, $request->toData());

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function storeRepresentative(
        StoreCompanyRepresentativeRequest $request,
        Company $company,
    ): RedirectResponse {
        $role = CompanyRepresentativeRole::from($request->validated('role'));
        $representative = $company->representatives()->create([
            'company_site_id' => $role->isSiteSpecific() ? (int) $request->validated('company_site_id') : null,
            'character_id' => (int) $request->validated('character_id'),
            'role' => $role,
            'appointed_by_user_id' => $request->user()->id,
        ]);
        $this->recordRoleEvent($company, $representative->character_id, $role->value, 'appointed', $request, $representative->company_site_id);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function destroyRepresentative(
        Request $request,
        Company $company,
        CompanyRepresentative $representative,
    ): RedirectResponse {
        abort_unless((int) $representative->company_id === (int) $company->id, 404);
        $this->authorize(
            $representative->role === CompanyRepresentativeRole::MANAGER ? 'manageManagers' : 'manageSiteRepresentatives',
            $company,
        );
        $this->recordRoleEvent($company, $representative->character_id, $representative->role->value, 'dismissed', $request, $representative->company_site_id);
        $representative->delete();

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function storeOwner(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('manageOwners', $company);
        $data = $request->validate([
            'character_id' => [
                'required',
                'integer',
                'exists:characters,id',
                Rule::unique('company_owners', 'character_id')->where('company_id', $company->id),
                Rule::unique('company_representatives', 'character_id')->where('company_id', $company->id),
            ],
        ]);
        $owner = $company->owners()->create([
            'character_id' => (int) $data['character_id'],
            'added_by_user_id' => $request->user()->id,
        ]);
        $this->recordRoleEvent($company, $owner->character_id, 'owner', 'appointed', $request);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function transferOwner(Request $request, Company $company, CompanyOwner $owner): RedirectResponse
    {
        abort_unless((int) $owner->company_id === (int) $company->id, 404);
        $owner->loadMissing('character');
        abort_unless((int) $owner->character?->user_id === (int) $request->user()->id, 403);
        $data = $request->validate([
            'target_owner_id' => [
                'required',
                'integer',
                Rule::exists('company_owners', 'id')->where('company_id', $company->id),
                Rule::notIn([$owner->id]),
            ],
        ]);
        $target = $company->owners()->findOrFail((int) $data['target_owner_id']);

        DB::transaction(function () use ($company, $owner, $request) {
            $this->recordRoleEvent($company, $owner->character_id, 'owner', 'transferred', $request);

            $owner->delete();
        });

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    private function recordRoleEvent(
        Company $company,
        int $characterId,
        string $role,
        string $action,
        Request $request,
        ?int $siteId = null,
    ): void {
        DB::table('company_role_events')->insert([
            'company_id' => $company->id,
            'company_site_id' => $siteId,
            'character_id' => $characterId,
            'role' => $role,
            'action' => $action,
            'acted_by_user_id' => $request->user()->id,
            'created_at' => now(),
        ]);
    }
}
