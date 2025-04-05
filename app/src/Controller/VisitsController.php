<?php

declare(strict_types=1);

namespace App\Controller;

use App\Validation\VisitRequestValidator;
use App\Error\Exception\ApiValidationException;
use App\Service\VisitsService;
use Cake\ORM\TableRegistry;

/**
 * Visits Controller
 *
 * @property \App\Model\Table\VisitsTable $Visits
 */
class VisitsController extends ApiController
{
    protected VisitsService $VisitsService;

    public function initialize(): void
    {
        parent::initialize();
        $this->VisitsService = new VisitsService();
        $this->loadComponent('RequestHandler');
    }

    public function add()
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();

        try {
            // Validation
            $validator = VisitRequestValidator::create();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                throw new ApiValidationException('Erro na validação do body', $errors);
            }

            $visit = $this->VisitsService->createVisit($data);

            $this->set([
                'message' => 'Visita criada com sucesso.',
                'data' => $visit,
                '_serialize' => ['message', 'data'],
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
