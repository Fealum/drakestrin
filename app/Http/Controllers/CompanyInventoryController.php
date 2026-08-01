<?php

namespace App\Http\Controllers;

use App\Http\Requests\Economy\UpdateCompanyInventoryRequest;
use App\Models\Economy\Company;
use App\Models\Economy\CompanySite;
use App\Services\Economy\CompanyInventoryService;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;

class CompanyInventoryController extends Controller
{
    public function __construct(
        PermissionService $permissionService,
        private CompanyInventoryService $inventory,
    ) {
        parent::__construct($permissionService);
    }

    public function update(
        UpdateCompanyInventoryRequest $request,
        Company $company,
        CompanySite $site,
    ): RedirectResponse {
        abort_unless((int) $site->company_id === (int) $company->id, 404);
        $this->inventory->classifyMany($site, $request->changes());

        return redirect()->route('company.view', ['company' => $company->id]);
    }
}
