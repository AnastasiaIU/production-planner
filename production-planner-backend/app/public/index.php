<?php

/**
 * Setup
 */
// require autoload file to autoload vendor libraries
require_once __DIR__ . '/../vendor/autoload.php';

// Set env variables
require_once(__DIR__ . "/../env.php");

// Set env variables
require_once(__DIR__ . "/../services/populate-db.php");

// require local classes
use App\Controllers\ItemController;
use App\Controllers\MachineController;
use App\Controllers\PlanController;
use App\Controllers\RecipeController;
use App\Controllers\UserController;
use App\Services\AuthHandler;
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
    Route::add('/auth/register', function () {
        $authController = new AuthController();
        $authController->register();
    }, ["post"]);

    Route::add('/auth/login', function () {
        $authController = new AuthController();
        $authController->login();
    }, ["post"]);

    Route::add('/auth/me', function () {
        $authController = new AuthController();
        $authController->me();
    });

    // update article by id
    Route::add('/auth/is-me/([0-9]*)', function ($id) {
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
    Route::add('/api/users/([0-9]+)/production-plans', function ($userId) {
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
     * Main page route
     */
    // Main page route
    Route::add('/', function () {
        $planError = $_SESSION['plan_error'] ?? null;
        $plan = $_SESSION['plan'] ?? null;
        unset($_SESSION['plan_error'], $_SESSION['plan']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize input
            $planId = null;
            $name = '';
            $items = [];
            foreach ($_POST as $key => $value) {
                $name = $key === 'planName' ? htmlspecialchars(trim($value)) : $name;
                $planId = $key === 'createPlanId' ? htmlspecialchars(trim($value)) : $planId;
                if ($key !== 'planName' && $key !== 'createPlanId') {
                    $items[$key] = htmlspecialchars(trim($value));
                }
            }

            $planController = new PlanController();

            if ($planId) {
                $planController->updateProductionPlan($planId, $name, $items);
            } else {
                $planController->createProductionPlan($_SESSION['user'], $name, $items);
            }

            if (http_response_code() === 500) {
                header('Location: /');
            }
        } else {
            require(__DIR__ . '/../views/pages/index.php');
        }
    }, ["get", "post"]);

    /**
     * Login routes
     */
    // Login page route
    Route::add('/login', function () {
        $loginError = $_SESSION['login_error'] ?? null;
        $loginFormData = $_SESSION['login_form_data'] ?? [];
        $loginUserCreated = $_SESSION['login_user_created'] ?? null;
        unset($_SESSION['login_error'], $_SESSION['login_form_data'], $_SESSION['login_user_created']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize input
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = htmlspecialchars(trim($_POST['password']));

            $userController = new UserController();
            $userController->attemptLogin($email, $password);

            if (http_response_code() === 400) {
                header('Location: /login');
            }
        } else {
            require_once(__DIR__ . '/../views/pages/login.php');
        }
    }, ["get", "post"]);

    // Logout route
    Route::add('/logout', function () {
        unset($_SESSION['user']);
        header('Location: /');
    });

    /**
     * Plane routes
     */
    // My plans page route
    Route::add('/plans', function () {
        AuthHandler::checkUserLoggedIn();

        $planError = $_SESSION['plan_error'] ?? null;
        unset($_SESSION['plan_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletePlanId'])) {
            $planId = filter_input(INPUT_POST, 'deletePlanId', FILTER_SANITIZE_NUMBER_INT);

            $planController = new PlanController();
            $planController->deleteProductionPlan($planId);

            header("Location: /plans");
        }

        require_once(__DIR__ . '/../views/pages/plans.php');
    }, ["get", "post"]);


    // Route for passing a plan to the main page
    Route::add('/plan/([a-zA-Z0-9_-]*)', function ($planId) {
        AuthHandler::checkUserLoggedIn();

        $planController = new PlanController();
        $plan = $planController->get($planId);

        if (!$plan || $plan->created_by !== $_SESSION['user']) {
            $_SESSION['plan_error'] = 'Plan not found.';
            header("Location: /");
            exit();
        }

        $_SESSION['plan'] = $plan;
        header("Location: /");
    });


    // API route for importing a production plan
    Route::add('/importPlan', function () {
        AuthHandler::checkUserLoggedIn();

        $plan = json_decode(file_get_contents('php://input'), true);

        if ($plan) {
            $planController = new PlanController();
            $planController->createProductionPlan($plan['created_by'], $plan['display_name'], $plan['items']);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        }
    }, ['post']);

    // API route for logging import errors
    Route::add('/logImportError', function () {
        AuthHandler::checkUserLoggedIn();

        $errorData = json_decode(file_get_contents('php://input'), true);

        if (isset($errorData['error'])) {
            $_SESSION['plan_error'] = $errorData['error'];
        }
    }, ['post']);

    /**
     * Registration routes
     */
    // Registration page route
    Route::add('/register', function () {
        $error = $_SESSION['error'] ?? null;
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['error'], $_SESSION['form_data']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize input
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = htmlspecialchars(trim($_POST['password']));

            $userController = new UserController();
            $userController->registerUser($email, $password);

            if (http_response_code() === 400) {
                header('Location: /register');
            }
        } else {
            require_once(__DIR__ . '/../views/pages/register.php');
        }
    }, ["get", "post"]);
} catch (Throwable $error) {
    if ($_ENV["environment" == "LOCAL"]) {
        var_dump($error);
    } else {
        error_log($error);
    }
    ResponseService::Error("A server error occurred");
}


Route::run();
