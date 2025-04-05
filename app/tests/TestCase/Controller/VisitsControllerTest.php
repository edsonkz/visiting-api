<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\VisitsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\VisitsController Test Case
 *
 * @uses \App\Controller\VisitsController
 */
class VisitsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Visits',
        'app.Addresses',
        'app.Workdays',
    ];
}
