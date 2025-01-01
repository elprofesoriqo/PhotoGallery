<?php
// Include our new database functions
require_once 'business.php';
require_once 'controller_utils.php';

// Initialize database connection at the start
$db = connectToDatabase();

function products(&$model) {
    global $db;
    // Get all images with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $result = getImages($db, $page, 20);

    $model['products'] = $result['images'];
    $model['pagination'] = $result['pagination'];

    return 'products_view';
}

function selected(&$model) {
    global $db;
    // Get images for selected view with specific filter if needed
    $filter = ['selected' => true]; // Example filter for selected images
    $result = getImages($db, 1, 20, $filter);

    $model['products'] = $result['images'];
    return 'selected_view';
}

function product(&$model) {
    global $db;
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        try {
            $objectId = new MongoDB\BSON\ObjectId($id);
            $product = $db->images->findOne(['_id' => $objectId]);

            if ($product) {
                $model['product'] = $product;
                return 'product_view';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Invalid product ID';
        }
    }

    http_response_code(404);
    exit;
}

function edit(&$model) {
    global $db;
    $product = [
        'name' => null,
        'author' => null,
        'type' => null,
        'extension' => null,
        '_id' => null
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['name']) && !empty($_POST['author']) && !empty($_POST['watermark'])) {
            try {
                // Handle file upload with watermark
                $id = handleImageUpload($_FILES['file'], $_POST['watermark']);

                $imageData = [
                    'name' => $_POST['name'],
                    'author' => $_POST['author'],
                    'type' => 1,
                    'extension' => getFileExtension($_FILES['file']['type'])
                ];

                // Save image metadata
                saveImage($db, $imageData);
                return 'redirect:products';

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $model['product'] = $product;
                return 'edit_view';
            }
        }
    } elseif (!empty($_GET['id'])) {
        try {
            $objectId = new MongoDB\BSON\ObjectId($_GET['id']);
            $product = $db->images->findOne(['_id' => $objectId]);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Invalid product ID';
        }
    }

    $model['product'] = $product;
    return 'edit_view';
}

function delete(&$model) {
    global $db;
    if (!empty($_REQUEST['id'])) {
        $id = $_REQUEST['id'];

        try {
            $objectId = new MongoDB\BSON\ObjectId($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Delete image files
                $product = $db->images->findOne(['_id' => $objectId]);
                if ($product) {
                    $basePath = UPLOAD_PATH . $id;
                    @unlink($basePath . $product['extension']);
                    @unlink($basePath . '_thumb' . $product['extension']);
                    @unlink($basePath . '_wm' . $product['extension']);

                    // Delete from database
                    $db->images->deleteOne(['_id' => $objectId]);
                    return 'redirect:products';
                }
            } else {
                $product = $db->images->findOne(['_id' => $objectId]);
                if ($product) {
                    $model['product'] = $product;
                    return 'delete_view';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Invalid product ID';
        }
    }

    http_response_code(404);
    exit;
}

function register(&$model) {
    global $db;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['login']) && isset($_POST['pass']) && isset($_POST['pass2']) && isset($_POST['email'])) {
            $login = trim($_POST['login']);
            $email = trim($_POST['email']);
            $pass = trim($_POST['pass']);
            $pass2 = trim($_POST['pass2']);

            if (validate_data($login, $email, $pass, $pass2)) {
                try {
                    $userData = [
                        'login' => $login,
                        'email' => $email,
                        'password' => $pass
                    ];

                    createUser($db, $userData);
                    return 'redirect:products';
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                    return 'register_view';
                }
            }
            return 'register_view';
        }
    }
    return 'register_view';
}

function login(&$model) {
    global $db;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['user']) && isset($_POST['pass'])) {
            $login = trim($_POST['user']);
            $pass = trim($_POST['pass']);

            try {
                $user = authenticateUser($db, $login, $pass);
                $_SESSION['user'] = $user;
                return 'redirect:products';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                return 'login_view';
            }
        }
    }
    return 'login_view';
}

function logout(&$model) {
    setcookie(session_id(), "", time() - 3600);
    session_destroy();
    session_write_close();
    return 'redirect:products';
}

// Cart functionality remains similar but uses new database structure
function cart(&$model) {
    $model['cart'] = get_cart();
    return 'partial/navbar_view';
}

function add_to_cart() {
    global $db;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id1'])) {
        $products = $db->images->find()->toArray();
        $num = count($products);

        for ($i = 1; $i <= $num; $i++) {
            $amount = isset($_POST["$i"]) ? 1 : 0;
            $id = $_POST["id$i"];

            try {
                $objectId = new MongoDB\BSON\ObjectId($id);
                $product = $db->images->findOne(['_id' => $objectId]);

                if ($product && $amount > 0) {
                    $cart = &get_cart();
                    $cart[$id] = ['name' => $product['name'], 'amount' => $amount];
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return 'redirect:' . $_SERVER['HTTP_REFERER'];
    }
}

function clear_cart() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['cart'] = [];
        return 'redirect:' . $_SERVER['HTTP_REFERER'];
    }
}