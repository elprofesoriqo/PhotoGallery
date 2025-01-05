<?php
require_once 'business.php';
require_once 'controller_utils.php';


function gallery(&$model)
{
    $gallery = get_gallery();

    $model['gallery'] = $gallery;

    return 'gallery';
}




function picture(&$model)
{
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];

        if ($picture = get_picture($id)) {
            $model['gallery'] = $picture;  // Changed from 'picture' to 'gallery' to match view

            return 'picture';
        }
    }

    http_response_code(404);
    exit;
}


function edit(&$model)
{
    $picture = [
        'name' => null,
        'author' => null,
        'type' => null,
        'extension' => null,
        '_id' => null
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['name']) &&
            !empty($_POST['author']) &&
            !empty($_POST['watermark']) &&
            isset($_FILES['file'])
        ) {
            $id = isset($_POST['id']) ? $_POST['id'] : null;

            try {
                // Validate file upload
                if ($_FILES['file']['error'] === UPLOAD_ERR_INI_SIZE) {
                    throw new Exception('File is too big (max 1MB allowed)');
                }

                if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('File upload error: ' . $_FILES['file']['error']);
                }

                // Create picture record
                $picture = [
                    'name' => $_POST['name'],
                    'author' => $_POST['author'],
                    'type' => $_FILES['file']['type'],
                    'extension' => getFileExtension($_FILES['file']['type'])
                ];

                // Save picture and process image
                if (save_picture($id, $picture, $_POST['watermark'])) {
                    return 'redirect:gallery';
                }

                throw new Exception('Failed to save picture');

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $model['picture'] = $picture;
                return 'upload';
            }
        } else {
            $_SESSION['error'] = 'Please fill all required fields';
            $model['picture'] = $picture;
            return 'upload';
        }
    } elseif (!empty($_GET['id'])) {
        $picture = get_picture($_GET['id']);
    }

    $model['picture'] = $picture;
    return 'upload';
}




function delete(&$model)
{
    if (!empty($_REQUEST['id'])) {
        $id = $_REQUEST['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            delete_picture($id);
            return 'redirect:gallery';

        } else {
            if ($picture = get_picture($id)) {
                $model['gallery'] = $picture;
                return 'delete';
            }
        }
    }
    http_response_code(404);
    exit;
}



function register(&$model)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['login']) && isset($_POST['pass']) && isset($_POST['pass2']) && isset($_POST['email']))
        {
            $login=trim($_POST['login']);
            $email=trim($_POST['email']);
            $pass=trim($_POST['pass']);
            $pass2=trim($_POST['pass2']);

            if(validate_data($login,$email,$pass,$pass2)){
                
                $hash=password_hash($pass, PASSWORD_DEFAULT);

                $user = [
                    'login' => $login,
                    'email' => $email,
                    'hash' => $hash=password_hash($pass, PASSWORD_DEFAULT)

                ];
                save_user($user);


                return 'redirect:gallery';

            }else {
                return 'register';
            }
        }
    } else {
            return 'register';
    }

    return 'redirect:gallery';
}


function login(&$model)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['user']) && isset($_POST['pass']))
        {
            $login=trim($_POST['user']);
            $pass=trim($_POST['pass']);

            if(!authenticate($login,$pass)){
                return 'login';
            }
        }

    } else {
        return 'login';
        
    }

    return 'redirect:gallery';
}


function logout(&$model)
{
    setcookie (session_id(), "", time() - 3600);
    session_destroy();
    session_write_close();

    return 'redirect:gallery';
}


function selected(&$model)
{
    $model['gallery'] = get_gallery();
    $model['selected'] = get_selected();
    return 'components/selected';
}




function add_to_selected(&$model)  // Note: must accept $model parameter
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id1'])) {
        $db = get_db();
        $num = count($db->gallery->find()->toArray());

        for($i=1; $i<=$num; $i++) {
            $amount = 0;
            if(isset($_POST["$i"])) {
                $amount = 1;
            }
            $id = $_POST["id$i"];
            $product = get_picture($id);
            $cart = &get_selected();
            $cart[$id] = ['name' => $product['name'], 'amount' => $amount];
        }

        return 'redirect:' . $_SERVER['HTTP_REFERER'];
    }
    return 'redirect:gallery';  // Fallback redirect
}


function clear_selected()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['selected'] = [];
        return 'redirect:' . $_SERVER['HTTP_REFERER'];
    }
}

