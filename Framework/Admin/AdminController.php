<?php

declare(strict_types=1);

namespace Framework\Admin;

use Framework\BaseController;
use Framework\BaseUser\BaseUserRepositoryInterface;
use Framework\Container;
use Framework\Http\Request;
use Framework\Http\ResponseDTO;

class AdminController extends BaseController
{
    public function __construct(private AdminServiceInterface $adminService)
    {
        parent::__construct();
    }

    public function index(Request $request): ResponseDTO
    {
        $this->protectArea();
        $adminDTO = new AdminDTO();

        if ($request->isPost()) {
            $token = $request->pullString('TOKEN');
            $action = $request->pullString('action');

            switch ($action) {
                case 'init_db':
                    $adminDTO = $this->adminService->runDatabaseInit($token);

                    break;
                case 'clear_all':
                    $adminDTO = $this->adminService->clearAllData($token);

                    break;
                case 'log_info':
                    $adminDTO = $this->adminService->fetchSpecificLog($token, 'info.log');

                    break;
                case 'log_error':
                    $adminDTO = $this->adminService->fetchSpecificLog($token, 'error.log');

                    break;
                case 'log_sql':
                    $adminDTO = $this->adminService->fetchSpecificLog($token, 'sql.log');

                    break;
                case 'log_warning':
                    $adminDTO = $this->adminService->fetchSpecificLog($token, 'warning.log');

                    break;
                case 'sql_db':
                    $sqlQuery = $request->pullString('sql_query'); // Pega o texto do textarea
                    $adminDTO = $this->adminService->sqlExecute($token, $sqlQuery);

                    break;
                case 'reset_logs':
                    $adminDTO = $this->adminService->resetSystemLogs($token);

                    break;
                case 'test_redis':
                    $adminDTO = $this->adminService->testRedisConnection($token);

                    break;
                default:
                    $adminDTO->errorMessage = "Ação desconhecida!";
            }
        }

        return $this->render('admin', 'layout_logs', $adminDTO, true);
    }

    private function protectArea(): void
    {
        $user_id = $this->session->getUserId();

        if ($user_id === null) {
            throw new \Exception("Page not found!", 404);
        }

        $user = Container::resolve(BaseUserRepositoryInterface::class)->getUserById($user_id);
        $email = $user->email ?? '';

        if ($email !== Container::$config->adminEmail) {
            throw new \Exception("Page not found!", 404);
        }
    }


    public function toggleDebugMode(Request $request): ResponseDTO
    {
        $sessionDebugModeOn = $this->session->get('debugModeOn') ?? isDevEnvironment();
        $sessionDebugModeOn = ! $sessionDebugModeOn;
        $this->session->set('debugModeOn', $sessionDebugModeOn);
        $referer = $request->getHeader('Referer') ?? '/';
        return $this->redirect($referer);
    }



}
