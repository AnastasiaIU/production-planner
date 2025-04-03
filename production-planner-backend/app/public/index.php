<?php

/**
 * Setup
 */
// require autoload file to autoload vendor libraries
require_once __DIR__ . '/../vendor/autoload.php';

// Set env variables
require_once(__DIR__ . "/../env.php");

// Populates the database with data from the game developers' JSON file if database tables are empty
require_once(__DIR__ . "/../services/populate-db.php");

// require local classes
use App\Controllers\BaseController;
use App\Controllers\ItemController;
use App\Controllers\MachineController;
use App\Controllers\PlanController;
use App\Controllers\RecipeController;
use App\Services\ErrorReportingService;
use App\Services\ResponseService;
use App\Controllers\AuthController;

// require vendor libraries
use Steampixel\Route;

// initialize error reporting (on in local env)
ErrorReportingService::Init();

// set CORS headers
ResponseService::SetCorsHeaders();

/**
 * Main application routes
 */
// top level fail-safe try/catch
try {
    /**
     * Auth routes
     */
    Route::add('/api/auth/register', function () {
        $authController = new AuthController();
        $authController->register();
    }, 'post');

    Route::add('/api/auth/login', function () {
        $authController = new AuthController();
        $authController->login();
    }, 'post');

    Route::add('/api/auth/me', function () {
        $authController = new AuthController();
        $authController->me();
    });

    Route::add('/api/auth/is-me/([0-9]*)', function ($id) {
        $authController = new AuthController();
        $authController->isMe($id);
    });

    /**
     * 404 route handler
     */
    Route::pathNotFound(function () {
        ResponseService::Error("route is not defined", 404);
    });

    /**
     * GET API routes. All get APIs for collections support pagination with parameters page and limit, e.g. ?page=1&limit=10.
     */
    // API route for fetching producible items
    Route::add('/api/producible-items', function () {
        $itemController = new ItemController();
        $itemController->getAllProducible();
    });
    // API route for fetching an item by its ID
    Route::add('/api/items/([a-zA-Z0-9_-]*)', function ($id) {
        $itemController = new ItemController();
        $itemController->get($id);
    });
    // API route for fetching a machine by its ID
    Route::add('/api/machines/([a-zA-Z0-9_-]*)', function ($id) {
        $machineController = new MachineController();
        $machineController->get($id);
    });
    // API route for fetching production plans by user ID
    Route::add('/api/users/([0-9]+)/plans', function ($userId) {
        $planController = new PlanController();
        $planController->getAllByUser($userId);
    });
    // API route for fetching a production plan by its ID
    Route::add('/api/plans/([0-9]+)', function ($id) {
        $planController = new PlanController();
        $planController->get($id);
    });
    // API route for fetching a standard recipe for an item
    Route::add('/api/items/([a-zA-Z0-9_-]*)/standard-recipe', function ($id) {
        $recipeController = new RecipeController();
        $recipeController->getStandardByItem($id);
    });

    /**
     * POST API routes
     */
    // API route for creating a production plan
    Route::add('/api/plans', function () {
        $baseController = new BaseController();
        $baseController->getAuthenticatedUser();

        $planController = new PlanController();
        $planController->create();
    }, 'post');

    /**
     * PUT API routes
     */
    // API route for updating the production plan by its ID
    Route::add('/api/plans/([0-9]+)', function ($id) {
        $baseController = new BaseController();
        $baseController->getAuthenticatedUser();

        $planController = new PlanController();
        $planController->update($id);
    }, 'put');

    /**
     * DELETE API routes
     */
    // API route for deleting the production plan by its ID
    Route::add('/api/plans/([0-9]+)', function ($id) {
        $baseController = new BaseController();
        $baseController->getAuthenticatedUser();

        $planController = new PlanController();
        $planController->delete($id);
    }, 'delete');

} catch (Throwable $error) {
    error_log($error);
    ResponseService::Error("A server error occurred");
}

Route::run();
