<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyWorker;
use App\Models\Labour;
use App\Models\LabourActive;
use App\Services\InventoryService;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(PermissionService $permissionService, private InventoryService $inventory)
    {
        parent::__construct($permissionService);
    }

    public function index(): View
    {
        return $this->viewAll();
    }

    public function viewAll(): View
    {
        return view('company.viewall', [
            'companies' => Company::query()
                ->with('characterModel')
                ->orderByRaw('LOWER(name)')
                ->get(),
        ]);
    }

    public function view(Company $company): View
    {
        Gate::authorize('view', $company);

        $company->load([
            'characterModel.userModel',
            'inventory.itemModel',
            'territoryModel',
            'workers.activeLabours.labourModel.components.itemModel',
        ]);

        $this->setLocation($company);

        return view('company.view', [
            'canHire' => Gate::allows('hire', $company),
            'canPay' => Gate::allows('pay', $company),
            'company' => $company,
            'canManage' => Gate::allows('manage', $company),
        ]);
    }

    public function worker(CompanyWorker $worker): View
    {
        Gate::authorize('view', $worker);

        $worker->load([
            'activeLabours.labourModel.components.itemModel',
            'companyModel.characterModel.userModel',
        ]);

        $this->setLocation($worker);

        return view('company.worker', [
            'canAssignLabour' => Gate::allows('assignLabour', $worker),
            'canFire' => Gate::allows('fire', $worker),
            'labours' => Gate::allows('assignLabour', $worker) && $this->workload($worker) < 1 ? $this->possibleLabours($worker) : collect(),
            'worker' => $worker,
            'workload' => $this->workload($worker),
        ]);
    }

    public function hire(Company $company, int|string $type = 1): RedirectResponse
    {
        Gate::authorize('hire', $company);

        $type = (int) $type;
        $type = $type > 0 && $type < 6 ? $type : 1;

        if ($type === 5 && $company->workers()->where('type', 5)->exists()) {
            $this->flashMessage('error', 'company.hire_already_clerk');

            return redirect()->route('company.view', ['company' => $company->id]);
        }

        $workerName = $this->randomWorkerName();

        CompanyWorker::create([
            'name' => $workerName,
            'type' => $type,
            'company' => $company->id,
            'hired' => now()->timestamp,
            'paid' => now()->timestamp,
        ]);

        $this->flashMessage('success', 'company.hire', ['workername' => $workerName]);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function assignLabour(Request $request, CompanyWorker $worker): RedirectResponse
    {
        Gate::authorize('assignLabour', $worker);
        $worker->loadMissing('activeLabours.labourModel', 'companyModel');
        $company = $worker->companyModel;
        abort_unless($company, 404);

        $data = $request->validate([
            'labour' => ['required', 'integer', 'exists:dra_labour,id'],
            'quantity' => ['required', 'integer', 'in:-1,0'],
            'quantity_count' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'instances' => ['required', 'integer', 'min:1', 'max:9999'],
            'prodas' => ['required', 'integer'],
            'prodas_value' => ['nullable', 'integer', 'min:0'],
        ]);

        $labour = Labour::query()
            ->with('components.itemModel')
            ->whereKey((int) $data['labour'])
            ->firstOrFail();

        if ((int) $labour->type > (int) $worker->type || ! $this->labourFitsCapacity($worker, $labour)) {
            $this->flashMessage('error', 'company.assign_labour_not_possible');

            return redirect()->route('company.worker', ['worker' => $worker->id]);
        }

        $created = DB::transaction(function () use ($data, $worker, $company, $labour) {
            $worker->refresh()->load('activeLabours.labourModel');

            if (! $this->labourFitsCapacity($worker, $labour) || ! $this->componentsAvailable($labour, $company)) {
                return false;
            }

            foreach ($labour->components->where('type', '!=', 2) as $component) {
                if ((int) $component->type === 1) {
                    $this->inventory->take((int) $component->item, (int) $component->quantity, 2, $company->id, -2, -3);
                } else {
                    $this->inventory->take((int) $component->item, (int) $component->quantity, 2, $company->id, -2);
                }
            }

            $instances = min((int) $data['instances'], $this->maxInstances($worker, $labour));

            LabourActive::create([
                'company_worker' => $worker->id,
                'labour' => $labour->id,
                'since' => now()->timestamp,
                'until' => now()->timestamp + (int) $labour->duration,
                'prodas' => (int) $data['prodas'] === 0 ? (int) ($data['prodas_value'] ?? 0) : (int) $data['prodas'],
                'quantity' => (int) $data['quantity'] === 0 ? (int) ($data['quantity_count'] ?? 1) : -1,
                'instances' => max(1, $instances),
                'nextinsta' => 0,
            ]);

            return true;
        });

        if (! $created) {
            $this->flashMessage('error', 'company.assign_labour_no_resources');

            return redirect()->route('company.worker', ['worker' => $worker->id]);
        }

        $this->flashMessage('success', 'company.assign_labour', ['labourname' => $labour->name]);

        return redirect()->route('company.worker', ['worker' => $worker->id]);
    }

    public function fire(CompanyWorker $worker): RedirectResponse
    {
        Gate::authorize('fire', $worker);
        $worker->loadMissing('companyModel');

        $company = $worker->companyModel;
        abort_unless($company, 404);

        $settlement = DB::transaction(function () use ($worker, $company) {
            $settlement = $this->settleWorkerSalary($worker, $company);

            $worker->activeLabours()->delete();
            $worker->delete();

            return $settlement;
        });

        $this->flashMessage('success', 'company.fire', $settlement);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function pay(Company $company): RedirectResponse
    {
        Gate::authorize('pay', $company);

        $paid = DB::transaction(function () use ($company) {
            $balance = $this->inventory->available(1, 2, $company->id, -1);

            if ($balance <= 0) {
                return null;
            }

            $paid = [
                'paid' => 0,
                'sumpaid' => 0,
                'unpaid' => 0,
            ];

            $workers = $company->workers()
                ->orderBy('paid')
                ->lockForUpdate()
                ->get();

            foreach ($workers as $worker) {
                ['months' => $months, 'owed' => $owed] = $this->owedSalaryDetails($worker);

                if ($owed === 0) {
                    continue;
                }

                if ($balance - $paid['sumpaid'] >= $owed) {
                    $paid['sumpaid'] += $owed;
                    $paid['paid']++;
                    $worker->update(['paid' => ($worker->paid?->timestamp ?? now()->timestamp) + ($months * 2592000)]);
                } else {
                    $paid['unpaid']++;
                }
            }

            if ($paid['sumpaid'] > 0) {
                $this->inventory->debitStack(1, $paid['sumpaid'], 2, $company->id, -1);
            }

            return $paid;
        });

        if ($paid === null) {
            $this->flashMessage('error', 'company.pay_no_money');
        } else {
            $this->flashMessage('info', 'company.pay', $paid);
        }

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    private function workload(CompanyWorker $worker): float
    {
        return $worker->activeLabours->sum(function ($labour) {
            $workload = (int) ($labour->labourModel?->workload ?? 0);

            return $workload > 0 ? (1 / $workload) * (int) $labour->instances : 0;
        });
    }

    private function possibleLabours(CompanyWorker $worker)
    {
        $worker->loadMissing('companyModel', 'activeLabours.labourModel');

        return Labour::query()
            ->with('components.itemModel')
            ->where('type', '<=', (int) $worker->type)
            ->orderByRaw('LOWER(name)')
            ->get()
            ->filter(fn (Labour $labour) => $this->labourFitsCapacity($worker, $labour) && $this->componentsAvailable($labour, $worker->companyModel))
            ->values();
    }

    private function labourFitsCapacity(CompanyWorker $worker, Labour $labour): bool
    {
        if ((int) $labour->workload <= 0) {
            return false;
        }

        return $this->maxInstances($worker, $labour) >= 1;
    }

    private function maxInstances(CompanyWorker $worker, Labour $labour): int
    {
        return (int) floor(max(0, 1 - $this->workload($worker)) * (int) $labour->workload);
    }

    private function componentsAvailable(Labour $labour, ?Company $company): bool
    {
        if (! $company) {
            return false;
        }

        foreach ($labour->components->where('type', '!=', 2) as $component) {
            if ($this->inventory->available((int) $component->item, 2, $company->id, -2) < (int) $component->quantity) {
                return false;
            }
        }

        return true;
    }

    private function randomWorkerName(): string
    {
        $firstNames = ['Verion', 'Limnas', 'Kartrim', 'Parnyas', 'Hirion', 'Malnaetos', 'Wayront', 'Emmant', 'Piritugd', 'Lywin', 'Kamren'];
        $lastNames = ['Syrwantal', 'Karimtelmar', 'Vincis', 'Aralyis', 'Ewentem', 'Ionaer', 'Sayarmel'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * @return array{workername:string, owed:int, paid:int, unpaid:int}
     */
    private function settleWorkerSalary(CompanyWorker $worker, Company $company): array
    {
        $owed = $this->owedSalary($worker);
        $paid = 0;

        if ($owed > 0) {
            $paid = $this->inventory->debitStack(1, $owed, 2, $company->id, -1);
        }

        return [
            'workername' => $worker->name,
            'owed' => $owed,
            'paid' => $paid,
            'unpaid' => max(0, $owed - $paid),
        ];
    }

    private function owedSalary(CompanyWorker $worker): int
    {
        return $this->owedSalaryDetails($worker)['owed'];
    }

    /**
     * @return array{months:int,owed:int}
     */
    private function owedSalaryDetails(CompanyWorker $worker): array
    {
        $paidAt = $worker->paid?->timestamp ?? now()->timestamp;
        $months = (int) floor((now()->timestamp - $paidAt) / 2592000);

        if ($months <= 0) {
            return ['months' => 0, 'owed' => 0];
        }

        $monthlySalary = (int) $worker->type === 1 ? 3 : (int) $worker->type + 1;

        return ['months' => $months, 'owed' => 10000 * $months * $monthlySalary];
    }
}
