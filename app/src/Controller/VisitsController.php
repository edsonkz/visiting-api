<?php

declare(strict_types=1);

namespace App\Controller;

use App\Validation\VisitRequestValidator;
use App\Error\Exception\ApiValidationException;
use App\Service\VisitsService;

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

            $visit = $this->VisitsService->create($data);

            $this->response = $this->response->withStatus(201);
            $this->set([
                'message' => 'Visita criada com sucesso.',
                'data' => $visit,
                '_serialize' => ['message', 'data'],
            ]);
        } catch (ApiValidationException  $e) {
            $this->response = $this->response->withStatus($e->getCode());
            $this->viewBuilder()->setClassName('Json');
            $this->set([
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                '_serialize' => ['message', 'errors'],
            ]);
        }
    }

    public function findByDate(string $date)
    {
        $this->request->allowMethod(['get']);

        try {
            // Validation
            $validator = VisitRequestValidator::findByDate();
            $errors = $validator->validate(['date' => $date]);

            if (!empty($errors)) {
                throw new ApiValidationException('Erro na validação da data', $errors);
            }

            $visits = $this->VisitsService->findByDate($date);

            $this->set([
                'visits' => $visits,
                '_serialize' => ['visits'],
            ]);

        } catch (ApiValidationException  $e) {
            $this->response = $this->response->withStatus($e->getCode());
            $this->viewBuilder()->setClassName('Json');
            $this->set([
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                '_serialize' => ['message', 'errors'],
            ]);
        }
    }

    public function update(int $id)
    {
        $this->request->allowMethod(['put']);

        $data = $this->request->getData();

        try {
            // Validation
            $validator = VisitRequestValidator::update();

            $errors = $validator->validate(array_merge(['id' => $id], $data));

            if (!empty($errors)) {
                throw new ApiValidationException('Erro na validação do id', $errors);
            }

            $visit = $this->VisitsService->update($data, $id);

            $this->set([
                'message' => 'Visita atualizada com sucesso.',
                'visit' => $visit,
                '_serialize' => ['message', 'visit'],
            ]);
        } catch (ApiValidationException  $e) {
            $this->response = $this->response->withStatus($e->getCode());
            $this->viewBuilder()->setClassName('Json');
            $this->set([
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
                '_serialize' => ['message', 'errors'],
            ]);
        }
    }
}
