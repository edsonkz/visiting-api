<?php

declare(strict_types=1);

namespace App\Controller;

use App\Error\Exception\ApiValidationException;
use Cake\Event\EventInterface;

/**
 * Error Handling Controller
 *
 * Controller used by ExceptionRenderer to render error responses.
 */
class ErrorController extends ApiController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler', [
            'viewClassMap' => ['json' => 'Json'],
        ]);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $exception = $this->getRequest()->getAttribute('cakephp.exception');

        if ($exception instanceof ApiValidationException) {
            $this->response = $this->response->withStatus(422);
            $this->viewBuilder()->setClassName('Json');
            $this->set([
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
                '_serialize' => ['message', 'errors'],
            ]);
        }
    }

    public function afterFilter(EventInterface $event)
    {
        parent::afterFilter($event);
    }
}
