<?php

namespace App\Http\Controllers;

use App\Data\Economy\InventoryMutationContext;
use App\Http\Requests\Economy\AssignLabourRequest;
use App\Models\Economy\Company;
use App\Models\Economy\CompanySite;
use App\Models\Economy\CompanyWorker;
use App\Models\Economy\Labour;
use App\Models\Economy\LabourActive;
use App\Repositories\Economy\TransferRepository;
use App\Repositories\Territory\LocationRepository;
use App\Services\Economy\ProductionService;
use App\Services\InventoryService;
use App\Services\PermissionService;
use App\Support\Currency;
use App\Support\InventoryMutationClock;
use App\Support\InventoryMutationKind;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        PermissionService $permissionService,
        private InventoryService $inventory,
        private ProductionService $production,
        private TransferRepository $transfers,
    ) {
        parent::__construct($permissionService);
    }

    public function index(): View
    {
        return $this->viewAll();
    }

    public function viewAll(): View
    {
        return view('company.viewall', [
            'canCreate' => auth()->check() && auth()->user()->can('create', Company::class),
            'companies' => Company::query()
                ->orderByRaw('LOWER(name)')
                ->get(),
        ]);
    }

    public function view(Company $company): View
    {
        Gate::authorize('view', $company);

        $company->load([
            'owners.character.user',
            'territory',
            'sites' => fn ($query) => $query->with([
                'inventory.item',
                'location',
                'productionRuns' => fn ($query) => $query->limit(10),
                'representatives.character.user',
                'workers.activeLabours.labour.components.item',
            ]),
            'representatives.site',
            'representatives.character.user',
        ]);

        $company->sites->each(function (CompanySite $site) {
            $site->setAttribute('inventory_groups', $site->inventory
                ->where('wear', '!=', InventoryStockState::COMMITTED_TOOL->value)
                ->groupBy(fn ($inventory) => $inventory->item?->stackable
                    ? 'stack:'.$inventory->id
                    : 'identity:'.hash('sha256', serialize([
                        $inventory->item_id,
                        $inventory->wear,
                        $inventory->timelastvalue,
                        $inventory->data,
                    ])))
                ->values());
        });

        $this->setLocation($company);

        return view('company.view', [
            'company' => $company,
            'canManage' => Gate::allows('manage', $company),
            'canEdit' => Gate::allows('update', $company),
            'canManageManagers' => Gate::allows('manageManagers', $company),
            'canManageOwners' => Gate::allows('manageOwners', $company),
            'canManageSiteRepresentatives' => Gate::allows('manageSiteRepresentatives', $company),
            'locations' => app(LocationRepository::class)->options(),
            'transfers' => $this->transfers->paginateForParticipants(
                PermissionEntityType::COMPANY_SITE,
                $company->sites->pluck('id')->map(fn ($id) => (int) $id)->all(),
            ),
        ]);
    }

    public function worker(CompanyWorker $worker): View
    {
        Gate::authorize('view', $worker);

        $worker->load([
            'activeLabours.labour.components.item',
            'activeLabours.currentRun',
            'company.owners.character.user',
            'site.location',
            'productionRuns' => fn ($query) => $query->limit(10),
        ]);

        $this->setLocation($worker);

        return view('company.worker', [
            'canAssignLabour' => Gate::allows('assignLabour', $worker),
            'canFire' => Gate::allows('fire', $worker) && $worker->activeLabours->isEmpty(),
            'labours' => Gate::allows('assignLabour', $worker) && $this->workload($worker) < 1 ? $this->possibleLabours($worker) : collect(),
            'worker' => $worker,
            'workload' => $this->workload($worker),
        ]);
    }

    public function hire(Company $company, CompanySite $site, int|string $type = 1): RedirectResponse
    {
        abort_unless((int) $site->company_id === (int) $company->id, 404);
        Gate::authorize('manageWorkers', $site);
        abort_unless($site->location_id, 422, 'Der Standort benötigt zuerst einen Ort.');

        $type = (int) $type;
        $type = $type > 0 && $type < 6 ? $type : 1;

        if ($type === 5 && $site->workers()->where('type', 5)->exists()) {
            $this->flashMessage('error', 'company.hire_already_clerk');

            return redirect()->route('company.view', ['company' => $company->id]);
        }

        $workerName = $this->randomWorkerName();

        CompanyWorker::create([
            'name' => $workerName,
            'type' => $type,
            'company_id' => $company->id,
            'company_site_id' => $site->id,
            'hired' => now()->timestamp,
            'paid' => now()->timestamp,
        ]);

        $this->flashMessage('success', 'company.hire', ['workername' => $workerName]);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function assignLabour(AssignLabourRequest $request, CompanyWorker $worker): RedirectResponse
    {
        Gate::authorize('assignLabour', $worker);
        $worker->loadMissing('activeLabours.labour', 'company');
        $company = $worker->company;
        abort_unless($company, 404);

        $labour = Labour::query()
            ->with('components.item')
            ->whereKey((int) $request->validated('labour'))
            ->firstOrFail();

        if ((int) $labour->type > (int) $worker->type || ! $this->labourFitsCapacity($worker, $labour)) {
            $this->flashMessage('error', 'company.assign_labour_not_possible');

            return redirect()->route('company.worker', ['worker' => $worker->id]);
        }

        $created = $this->production->start(
            $worker,
            $labour,
            $request->productionData(),
            $this->maxInstances($worker, $labour),
        );

        if (! $created) {
            $this->flashMessage('error', 'company.assign_labour_no_resources');

            return redirect()->route('company.worker', ['worker' => $worker->id]);
        }

        $this->flashMessage('success', 'company.assign_labour', ['labourname' => $labour->name]);

        return redirect()->route('company.worker', ['worker' => $worker->id]);
    }

    public function stopLabour(LabourActive $activeLabour): RedirectResponse
    {
        $activeLabour->loadMissing('companyWorker');
        $worker = $activeLabour->companyWorker;
        abort_unless($worker, 404);
        Gate::authorize('stopLabour', $worker);

        $status = $this->production->requestStop($activeLabour);
        $this->flashMessage('info', 'company.'.($status === 'stopping' ? 'labour_stopping' : 'labour_stopped'));

        return redirect()->route('company.worker', ['worker' => $worker->id]);
    }

    public function fire(CompanyWorker $worker): RedirectResponse
    {
        Gate::authorize('fire', $worker);
        $worker->loadMissing('company', 'site');

        $company = $worker->company;
        $site = $worker->site;
        abort_unless($company && $site, 404);

        if ($worker->activeLabours()->exists()) {
            $this->flashMessage('error', 'company.fire_busy');

            return redirect()->route('company.worker', ['worker' => $worker->id]);
        }

        $settlement = DB::transaction(function () use ($worker, $site) {
            $settlement = $this->settleWorkerSalary($worker, $site);

            $worker->delete();

            return $settlement;
        });

        $this->flashMessage('success', 'company.fire', $settlement);

        return redirect()->route('company.view', ['company' => $company->id]);
    }

    public function pay(Company $company, CompanySite $site): RedirectResponse
    {
        abort_unless((int) $site->company_id === (int) $company->id, 404);
        Gate::authorize('manageWorkers', $site);

        $paid = DB::transaction(function () use ($company, $site) {
            $company = Company::query()->whereKey($company->id)->lockForUpdate()->firstOrFail();
            $balance = $this->inventory->available(
                1,
                PermissionEntityType::COMPANY_SITE,
                $site->id,
                InventoryStockState::RESERVED->value,
            );

            if ($balance <= 0) {
                return null;
            }

            $paid = [
                'paid' => 0,
                'sumpaid' => 0,
                'unpaid' => 0,
                'months' => 0,
            ];

            $workers = $site->workers()
                ->orderBy('paid')
                ->lockForUpdate()
                ->get();

            $payroll = [];

            foreach ($workers as $worker) {
                ['months' => $months] = $this->owedSalaryDetails($worker);

                if ($months === 0) {
                    continue;
                }

                $payroll[$worker->id] = [
                    'worker' => $worker,
                    'paid_at' => $worker->paid?->timestamp ?? now()->timestamp,
                    'monthly_salary' => $this->monthlySalary($worker),
                    'months_remaining' => $months,
                    'months_paid' => 0,
                ];
            }

            $remainingBalance = $balance;

            while (true) {
                $nextWorkerId = null;

                foreach ($payroll as $workerId => $entry) {
                    if ($entry['months_remaining'] < 1 || $entry['monthly_salary'] > $remainingBalance) {
                        continue;
                    }

                    if ($nextWorkerId === null
                        || $entry['paid_at'] < $payroll[$nextWorkerId]['paid_at']
                        || ($entry['paid_at'] === $payroll[$nextWorkerId]['paid_at'] && $workerId < $nextWorkerId)
                    ) {
                        $nextWorkerId = $workerId;
                    }
                }

                if ($nextWorkerId === null) {
                    break;
                }

                $monthlySalary = $payroll[$nextWorkerId]['monthly_salary'];
                $payroll[$nextWorkerId]['paid_at'] += CompanyWorker::SALARY_PERIOD_SECONDS;
                $payroll[$nextWorkerId]['months_remaining']--;
                $payroll[$nextWorkerId]['months_paid']++;
                $remainingBalance -= $monthlySalary;
                $paid['sumpaid'] += $monthlySalary;
                $paid['months']++;
            }

            foreach ($payroll as $entry) {
                if ($entry['months_paid'] > 0) {
                    $entry['worker']->update(['paid' => $entry['paid_at']]);
                    $paid['paid']++;
                }

                if ($entry['months_remaining'] > 0) {
                    $paid['unpaid']++;
                }
            }

            if ($paid['sumpaid'] > 0) {
                $debited = $this->inventory->debitStack(
                    1,
                    $paid['sumpaid'],
                    PermissionEntityType::COMPANY_SITE,
                    $site->id,
                    InventoryStockState::RESERVED->value,
                    $this->simulationContext(InventoryMutationKind::CONSUMPTION, 'company_site', $site->id),
                );

                throw_unless($debited === $paid['sumpaid'], \RuntimeException::class, 'The payroll balance changed during payment.');
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
            $workload = (int) ($labour->labour?->workload ?? 0);

            return $workload > 0 ? (1 / $workload) * (int) $labour->instances : 0;
        });
    }

    private function possibleLabours(CompanyWorker $worker)
    {
        $worker->loadMissing('site', 'activeLabours.labour');

        return Labour::query()
            ->with('components.item')
            ->where('type', '<=', (int) $worker->type)
            ->orderByRaw('LOWER(name)')
            ->get()
            ->filter(fn (Labour $labour) => $this->labourFitsCapacity($worker, $labour)
                && $worker->site?->location_id
                && $this->production->maxInstancesByResources($worker->site, $labour) > 0)
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

    private function randomWorkerName(): string
    {
        $firstNames = ['Verion', 'Limnas', 'Kartrim', 'Parnyas', 'Hirion', 'Malnaetos', 'Wayront', 'Emmant', 'Piritugd', 'Lywin', 'Kamren'];
        $lastNames = ['Syrwantal', 'Karimtelmar', 'Vincis', 'Aralyis', 'Ewentem', 'Ionaer', 'Sayarmel'];

        return $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)];
    }

    /**
     * @return array{workername:string, owed:int, paid:int, unpaid:int}
     */
    private function settleWorkerSalary(CompanyWorker $worker, CompanySite $site): array
    {
        $owed = $this->owedSalary($worker);
        $paid = 0;

        if ($owed > 0) {
            $paid = $this->inventory->debitStack(
                1,
                $owed,
                PermissionEntityType::COMPANY_SITE,
                $site->id,
                InventoryStockState::RESERVED->value,
                $this->simulationContext(InventoryMutationKind::CONSUMPTION, 'company_worker', $worker->id),
            );
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

    private function simulationContext(InventoryMutationKind $kind, string $sourceType, int $sourceId): InventoryMutationContext
    {
        return new InventoryMutationContext(
            $kind,
            InventoryMutationClock::SIMULATION,
            now()->timestamp,
            $sourceType,
            $sourceId,
        );
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

        return ['months' => $months, 'owed' => $months * $this->monthlySalary($worker)];
    }

    private function monthlySalary(CompanyWorker $worker): int
    {
        $salary = (int) $worker->type === 1 ? 3 : (int) $worker->type + 1;

        return Currency::TEN_PER_TUK * $salary;
    }
}
