<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;

/**
 * Visits Controller
 *
 * @property \App\Model\Table\VisitsTable $Visits
 */
class VisitsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function add()
    {
        $this->request->allowMethod(['post']);


        $this->set([
            'success' => true,
            'data' => ['message' => 'Deu tudo certo'],
            '_serialize' => ['success', 'data'],
        ]);

        // $visit = $this->Visits->newEmptyEntity();

        // $data = $this->request->getData();
        // $visit = $this->Visits->patchEntity($visit, $data);

        // if ($visit->hasErrors()) {
        //     throw new BadRequestException(json_encode($visit->getErrors()));
        // }

        // if (!$this->Visits->save($visit)) {
        //     throw new InternalErrorException('Erro ao salvar a visita');
        // }

        // $this->set([
        //     'success' => true,
        //     'data' => $visit,
        //     '_serialize' => ['success', 'data'],
        // ]);
    }
}
