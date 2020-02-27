<?php 

namespace Armincms\Tools\ToolbarAction;


use Laravel\Nova\Exceptions\MissingActionHandlerException;
use Laravel\Nova\Actions\ActionModelCollection;
use Laravel\Nova\Actions\Action as NovaAction;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Actions\ActionMethod; 
use Laravel\Nova\Actions\Transaction;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;
 
abstract class Action extends NovaAction
{ 
    /**
     * Indicates if this action is available on the resource index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Indicates if this action is available on the resource detail view.
     *
     * @var bool
     */
    public $showOnDetail = false;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    abstract public function handle(ActionFields $fields, Collection $models);

    /**
     * Execute the action for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @return mixed
     * @throws MissingActionHandlerException
     */
    public function handleRequest(ActionRequest $request)
    {
        if($request->toolbarAction) {
            $method = ActionMethod::determine($this, $request->targetModel()); 

            if (! method_exists($this, $method)) {
                throw MissingActionHandlerException::make($this, $method);
            }   

            return Transaction::run(function ($batchId) use ($request, $method) {  
                return $this->withBatchId($batchId)->{$method}(
                    $request->resolveFields(), new ActionModelCollection
                );
            }); 
        }

        return parent::handleRequest($request);
    } 

    /**
     * Prepare the action for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
    	return array_merge(parent::jsonSerialize(), [
    		'showOnToolbar' => true, 
    	]);
    }
}


