<?php

use MongoDB\BSON\ObjectID;

define('THUMBNAIL_WIDTH', 200);
define('THUMBNAIL_HEIGHT', 150);
function get_db()
{
    $mongo = new MongoDB\Client(
        "mongodb://localhost:27017/wai",
        [
            'username' => 'wai_web',
            'password' => 'w@i_w3b',
        ]);

    $db = $mongo->wai;

    return $db;
}



function get_gallery()
{
    $db = get_db();
    return $db->gallery->find()->toArray();
}


function get_picture($id)
{
    $db = get_db();
    return $db->gallery->findOne(['_id' => new ObjectID($id)]);
}


function get_login_check($login)
{
    $db = get_db();
    $result = $db->users->find(['login' => $login])->toArray();
    return(!empty($result));
}

function save_user($user)
{
    $db = get_db();
    $db->users->insertOne($user);
}


function authenticate($login, $pass) {
    try {
        $db = get_db();
        $user = $db->users->findOne(['login' => $login]);

        if (!$user) {
            throw new Exception('Incorrect login details');
        }

        if (password_verify($pass, $user['hash'])) {
            $_SESSION['islogged'] = true;
            $_SESSION['loggedid'] = session_id();
            $_SESSION['loggeduser'] = $login;
        } else {
            throw new Exception('Incorrect login details');
        }

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        return false;
    }

    return true;
}




function save_picture($id, $picture, $watermark)
{
    $db = get_db();
    if($_FILES['file']['error']==0)
    {
        if ($id == null) {
            $insertResult = $db->gallery->insertOne($picture);
            $newDocID = $insertResult->getInsertedId();

            if(handleImageUpload($newDocID, $watermark)) {
                return true;
            }
        } else {
            $db->gallery->replaceOne(['_id' => new ObjectID($id)], $picture);
            if(handleImageUpload($id, $watermark)) {
                return true;
            }
        }
    }
    return false;
}
function handleImageUpload($id, $watermark) {
    try {
        if($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $imageType = $_FILES['file']['type'];
        if(!in_array($imageType, ['image/jpeg', 'image/png'])) {
            throw new Exception('Invalid image type');
        }

        $extension = ($imageType === 'image/jpeg') ? '.jpg' : '.png';
        $tempPath = $_FILES['file']['tmp_name'];

        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        $targetPath = rtrim(UPLOAD_PATH, '/') . '/' . $id . $extension;
        $thumbnailPath = rtrim(UPLOAD_PATH, '/') . '/' . $id . 'thu' . $extension;
        $watermarkPath = rtrim(UPLOAD_PATH, '/') . '/' . $id . 'wm' . $extension;

        if(!move_uploaded_file($tempPath, $targetPath)) {
            throw new Exception('Failed to save original image');
        }
        chmod($targetPath, 0644);

        $sourceImage = ($imageType === 'image/jpeg') ?
            imagecreatefromjpeg($targetPath) :
            imagecreatefrompng($targetPath);

        if(!$sourceImage) {
            throw new Exception('Failed to create image resource');
        }

        $thumbnail = resizeImage($sourceImage, THUMBNAIL_WIDTH, 125);
        if($imageType === 'image/jpeg') {
            imagejpeg($thumbnail, $thumbnailPath, 90);
        } else {
            imagepng($thumbnail, $thumbnailPath, 9);
        }
        chmod($thumbnailPath, 0644);

        $watermarked = addWatermark($sourceImage, $watermark);
        if($imageType === 'image/jpeg') {
            imagejpeg($watermarked, $watermarkPath, 90);
        } else {
            imagepng($watermarked, $watermarkPath, 9);
        }
        chmod($watermarkPath, 0644);

        imagedestroy($thumbnail);
        imagedestroy($watermarked);
        imagedestroy($sourceImage);

        return true;
    } catch(Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        return false;
    }
}

function getImgType($type)
{
    if($type =="image/png"){
        return ".png";
    }
    if($type =="image/jpeg"){
        return ".jpg";
    }
    throw new Exception('The uploaded file does not match accepted format');
}


function validate_data($login, $email, $pass, $pass2)
{
    try{
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception('Email is incorrect');
        }
        if($pass !==$pass2){
            throw new Exception('Passwords differ');
        }
        if(get_login_check($login)){
            throw new Exception('Login already taken');
        }
        

        return true;

    }catch(Exception $e){
        $_SESSION['error']=$e->getMessage();
        return false;
    }
}



define('UPLOAD_PATH', __dir__.'/web/images/');
define('MAX_FILE_SIZE', 1048576); // 1MB


function resizeImage($source, $width, $height) {
    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);

    $resized = imagecreatetruecolor($width, $height);
    imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
    return $resized;
}

function addWatermark($sourceImage, $watermarkText) {
    $width = imagesx($sourceImage);
    $height = imagesy($sourceImage);

    $watermarked = imagecreatetruecolor($width, $height);
    imagecopy($watermarked, $sourceImage, 0, 0, 0, 0, $width, $height);

    $white = imagecolorallocate($watermarked, 255, 255, 255);
    $black = imagecolorallocate($watermarked, 0, 0, 0);

    $spacing = 150;
    $fontSize = 5;

    for ($y = 0; $y < $height; $y += $spacing) {
        for ($x = 0; $x < $width; $x += strlen($watermarkText) * 15) {
            imagestring($watermarked, $fontSize, $x + 1, $y + 1, $watermarkText, $black);
            imagestring($watermarked, $fontSize, $x, $y, $watermarkText, $white);
        }
    }
    return $watermarked;
}

function getFileExtension($mimeType) {
    switch ($mimeType) {
        case 'image/jpeg':
            return '.jpg';
        case 'image/png':
            return '.png';
        default:
            throw new Exception('Unsupported image type. Only JPEG and PNG are allowed.');
    }
}

function getUploadErrorMessage($errorCode) {
    $messages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds PHP maximum file size limit',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form maximum file size limit',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    return $messages[$errorCode] ?? 'Unknown upload error occurred';
}
function checkUpload($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = getUploadErrorMessage($file['error']);
        return false;
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        $_SESSION['error'] = 'File size exceeds the maximum limit of 1MB';
        return false;
    }
    if (!in_array($file['type'], ['image/jpeg', 'image/png'])) {
        $_SESSION['error'] = 'Only JPEG and PNG files are allowed';
        return false;
    }

    return true;
}