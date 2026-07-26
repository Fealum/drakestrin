<?php

namespace App\Http\Controllers;

use App\Http\Requests\Economy\UpdateCompanyInventoryRequest;
use App\Models\Economy\Company;
use App\Models\Economy\Inventory;
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
        Inventory $inventory,
    ): RedirectResponse {
        $this->inventory->classify(
            $company,
            $inventory,
            $request->targetWear(),
            $request->validated('quantity'),
        );

        return redirect()->route('company.view', ['company' => $company->id]);
    }
}
