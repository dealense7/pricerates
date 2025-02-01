<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Model;
use App\Support\Resources\Contracts\TransformableContract;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    abstract protected static function getModel(): TransformableContract;

    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->initialize($app);

        return $app;
    }

    protected function initialize(Application $app): void
    {
        //$app['db']->statement('SET innodb_lock_wait_timeout = 300');
    }

    protected function getPermissions(array $permissions, ?Model $model = null): array
    {
        $model     ??= $this->getModel();
        $permsList = [];
        foreach ($permissions as $permission) {
            $permsList[] = $model->getPermission($permission);
        }

        return $permsList;
    }
}
