<?php

declare(strict_types=1);

namespace App\Controller;

use App\Error\Exception\ApiValidationException;
use App\Service\WorkdaysService;
use App\Validation\WorkdayRequestValidator;

/**
 * Visits Controller
 *
 * @property \App\Model\Table\VisitsTable $Visits
 */
class WorkdaysController extends ApiController
{
    protected WorkdaysService $WorkdaysService;

    public function initialize(): void
    {
        parent::initialize();
        $this->WorkdaysService = new WorkdaysService();
        $this->loadComponent('RequestHandler');
    }

    public function findAll()
    {
        $this->request->allowMethod(['get']);

        try {
            $workdays = $this->WorkdaysService->findAll();

            $this->set([
                'workdays' => $workdays,
                '_serialize' => ['workdays'],
            ]);
        } catch (ApiValidationException  $e) {
            $this->response = $this->response->withStatus(422);
            $this->viewBuilder()->setClassName('Json');
            $this->set([
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                '_serialize' => ['message', 'errors'],
            ]);
        }
    }

    public function close(string $date) {
        $this->request->allowMethod(['put']);

        try {
            // Validation
            $validator = WorkdayRequestValidator::close();
            $errors = $validator->validate(['date' => $date]);

            if (!empty($errors)) {
                throw new ApiValidationException('Erro na validação da data', $errors);
            }

            $changedVisits = $this->WorkdaysService->close($date);

            $this->set([
                'changedVisits' => $changedVisits,
                '_serialize' => ['visits', 'changedVisits'],
            ]);

        } catch (ApiValidationException  $e) {
            $this->response = $this->response->withStatus(422);
            $this->viewBuilder()->setClassName('Json');
            $this->set([
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                '_serialize' => ['message', 'errors'],
            ]);
        }
    }
}
