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
            $model['gallery'] = $picture;

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


function register(&$model)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return 'register';
    }

    $login = isset($_POST['login']) ? trim($_POST['login']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $pass = isset($_POST['pass']) ? trim($_POST['pass']) : null;
    $pass2 = isset($_POST['pass2']) ? trim($_POST['pass2']) : null;

    if ($login && $email && $pass && $pass2) {
        if (validate_data($login, $email, $pass, $pass2)) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $user = [
                'login' => $login,
                'email' => $email,
                'hash' => $hash,
            ];
            save_user($user);

            return 'redirect:gallery';
        } else {
            return 'register';
        }
    }

    return 'register';
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
    setcookie(session_name(), "", time() - 3600, "/");
    session_destroy();
    session_write_close();

    return 'redirect:gallery';
}


function selected(&$model)
{
    $model['gallery'] = get_gallery();
    $model['selected'] = &get_selected();
    return 'selected';
}

function remove_from_selected()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['picture_id'])) {
        $id = $_POST['picture_id'];

        $selected = &get_selected();

        if (isset($selected[$id])) {
            unset($selected[$id]);
        }

        return 'redirect:' . $_SERVER['HTTP_REFERER'];
    }
    return 'redirect:gallery';
}



function add_to_selected()
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
            $picture = get_picture($id);
            $selected = &get_selected();
            $selected[$id] = ['name' => $picture['name'], 'amount' => $amount];
        }

        return 'redirect:' . $_SERVER['HTTP_REFERER'];
    }
    return 'redirect:gallery';
}


