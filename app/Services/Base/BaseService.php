<?php

namespace App\Services\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleData;
use Kwidoo\Lifecycle\Data\LifecycleOptionsData;
use Kwidoo\Mere\Contracts\BaseService as BaseServiceInterface;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Data\ShowQueryData;
use Kwidoo\Mere\Presenters\FormPresenter;
use Kwidoo\Mere\Presenters\ResourcePresenter;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Abstract base service providing common CRUD operations with lifecycle events.
 *
 * This class is an adaptation of Kwidoo\Mere\Services\BaseService that works with
 * the new kwidoo/lifecycle package instead of the old Lifecycle implementation.
 *
 * @package App\Services\Base
 */
abstract class BaseService implements BaseServiceInterface
{
    /**
     * Options for lifecycle operations.
     *
     * @var LifecycleOptionsData
     */
    protected LifecycleOptionsData $options;

    /**
     * Initialize a new BaseService instance.
     *
     * @param MenuService $menuService Service for retrieving menu configuration
     * @param RepositoryInterface $repository Repository for data access
     * @param Lifecycle $lifecycle Lifecycle handler for events and transactions
     */
    public function __construct(
        protected MenuService $menuService,
        protected RepositoryInterface $repository,
        protected Lifecycle $lifecycle,
    ) {
        $this->options = new LifecycleOptionsData();
    }

    /**
     * Retrieve a paginated or complete list of resources.
     *
     * This method applies appropriate presenters, includes related resources
     * if specified, and handles lifecycle events for resource listing.
     *
     * @param ListQueryData $query Query parameters for filtering and pagination
     * @return mixed Collection of resources or paginated results
     */
    public function list(ListQueryData $query)
    {
        $presenter = $query->presenter ?? $this->defaultListPresenter();
        $with = $query->with ?? [];

        $this->repository->setPresenter($presenter);

        // Create lifecycle data with query context
        $lifecycleData = new LifecycleData(
            action: 'viewAny',
            resource: $this->eventKey(),
            context: $query
        );

        // Use specific options for listing operations
        $listOptions = $this->options
            ->withoutEvents()
            ->withoutTrx();

        // Execute through lifecycle
        return $this->lifecycle->run(
            $lifecycleData,
            function () use ($query, $with) {
                return $query->perPage
                    ? $this->repository->with($with)->paginate($query->perPage, $query->columns)
                    : $this->repository->with($with)->all($query->columns);
            },
            $listOptions
        );
    }

    /**
     * Retrieve a single resource by its identifier.
     *
     * This method applies the appropriate presenter, includes related resources
     * if specified, and handles lifecycle events for single resource retrieval.
     *
     * @param ShowQueryData $query Query parameters for resource retrieval
     * @return Model|array The retrieved model instance or presented array
     * @throws ModelNotFoundException If the resource cannot be found
     */
    public function getById(ShowQueryData $query): Model|array
    {
        $presenter = $query->presenter ?? $this->defaultFormPresenter();
        $with = $query->with ?? [];

        $this->repository->setPresenter($presenter);

        // Create lifecycle data for viewing a resource
        $lifecycleData = new LifecycleData(
            action: 'view',
            resource: $this->eventKey(),
            context: $query
        );

        // Use specific options for view operations
        $viewOptions = $this->options
            ->withoutEvents()
            ->withoutTrx();

        // Execute through lifecycle
        $model = $this->lifecycle->run(
            $lifecycleData,
            function () use ($query, $with) {
                return $this->repository->with($with)->find($query->id);
            },
            $viewOptions
        );

        if (!$model) {
            throw (new ModelNotFoundException())->setModel(get_class($this->repository->model()));
        }

        return $model;
    }

    /**
     * Create a new resource with the provided data.
     *
     * This method validates the input data against menu-defined rules,
     * handles lifecycle events, and creates the resource in a transaction.
     *
     * @param  array $data The data for creating the new resource
     * @return mixed The newly created resource
     */
    public function create(array $data): mixed
    {
        // Create lifecycle data for creating a resource
        $lifecycleData = new LifecycleData(
            action: 'create',
            resource: $this->eventKey(),
            context: $data
        );

        // Execute through lifecycle with default options
        return $this->lifecycle->run(
            $lifecycleData,
            function () use ($data) {
                return $this->handleCreate($data);
            },
            $this->options
        );
    }

    /**
     * Update an existing resource with the provided data.
     *
     * This method validates the input data against menu-defined rules,
     * handles lifecycle events, and updates the resource in a transaction.
     *
     * @param  string $id The identifier of the resource to update
     * @param  array  $data The new data to apply to the resource
     * @return mixed The updated resource
     */
    public function update(string $id, array $data): mixed
    {
        // Create lifecycle data for updating a resource
        $lifecycleData = new LifecycleData(
            action: 'update',
            resource: $this->eventKey(),
            context: [
                'id' => $id,
                'data' => $data
            ]
        );

        // Execute through lifecycle with default options
        return $this->lifecycle->run(
            $lifecycleData,
            function () use ($id, $data) {
                return $this->handleUpdate($id, $data);
            },
            $this->options
        );
    }

    /**
     * Delete a resource by its identifier.
     *
     * This method handles lifecycle events and performs the deletion
     * in a transaction.
     *
     * @param  string $id The identifier of the resource to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool
    {
        // Create lifecycle data for deleting a resource
        $lifecycleData = new LifecycleData(
            action: 'delete',
            resource: $this->eventKey(),
            context: ['id' => $id]
        );

        // Execute through lifecycle with default options
        return $this->lifecycle->run(
            $lifecycleData,
            function () use ($id) {
                return $this->handleDelete($id);
            },
            $this->options
        );
    }

    /**
     * Handle the creation of a new resource.
     *
     * Delegates the creation to the repository after any necessary
     * validation or preparation.
     *
     * @param array $data The data for creating the new resource
     * @return mixed The newly created resource
     */
    protected function handleCreate(array $data): mixed
    {
        return $this->repository->create($data);
    }

    /**
     * Handle the update of an existing resource.
     *
     * Retrieves validation rules from the menu service and
     * delegates the update to the repository.
     *
     * @param  string $id The identifier of the resource to update
     * @param  array  $data The new data to apply to the resource
     * @return mixed The updated resource
     */
    protected function handleUpdate(string $id, array $data): mixed
    {
        $rules = $this->menuService->getRules(
            $this->studlyEvent('update')
        ) ?? [];

        $this->repository->setRules([
            'update' => $rules,
        ]);

        return $this->repository->update($data, $id);
    }

    /**
     * Handle the deletion of a resource.
     *
     * Delegates the deletion to the repository after any necessary
     * validation or preparation.
     *
     * @param  string $id The identifier of the resource to delete
     * @return bool True if deletion was successful, false otherwise
     */
    protected function handleDelete(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get the event key used for lifecycle events related to this service.
     *
     * @return string The unique event key for this service
     */
    abstract protected function eventKey(): string;

    /**
     * Generate a studly case event name with the given suffix.
     *
     * @param string $suffix The suffix to append to the event key
     * @return string The formatted event name
     */
    protected function studlyEvent(string $suffix): string
    {
        return Str::studly("{$this->eventKey()}-{$suffix}");
    }

    /**
     * Get the default presenter class for list operations.
     *
     * @return string The class name of the default list presenter
     */
    protected function defaultListPresenter(): string
    {
        return ResourcePresenter::class;
    }

    /**
     * Get the default presenter class for single resource operations.
     *
     * @return string The class name of the default form presenter
     */
    protected function defaultFormPresenter(): string
    {
        return FormPresenter::class;
    }
}
