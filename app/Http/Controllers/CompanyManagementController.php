<?php

namespace App\Http\Controllers;

use App\Http\Requests\Economy\StoreCompanyRepresentativeRequest;
use App\Http\Requests\Economy\StoreCompanyRequest;
use App\Http\Requests\Economy\UpdateCompanyRequest;
use App\Models\Economy\Company;
use App\Models\Economy\CompanyRepresentative;
use App\Repositories\Economy\CompanyRepository;
use App\Repositories\Territory\LocationRepository;
use App\Services\PermissionService;
use App\Support\CompanyRepresentativeRole;
use App\Support\CompanySector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'company' => $company,
            'locations' => $this->locations->options(),
            'sectors' => CompanySector::cases(),
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        if ((int) $request->validated('sector') !== (int) $company->type
            && ($company->workers()->exists() || $company->productionRuns()->exists())) {
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
        $company->representatives()->create([
            'character_id' => (int) $request->validated('character_id'),
            'role' => CompanyRepresentativeRole::from($request->validated('role')),
            'appointed_by_user_id' => $request->user()->id,
        ]);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function destroyRepresentative(
        Request $request,
        Company $company,
        CompanyRepresentative $representative,
    ): RedirectResponse {
        $this->authorize('manageRepresentatives', $company);
        abort_unless((int) $representative->company_id === (int) $company->id, 404);
        $representative->delete();

        return redirect()->route('company.view', ['company' => $company->id]);
    }
}
