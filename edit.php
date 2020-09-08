<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['name'])) {
    $_SESSION['error'] = 'Kek edit';
    header('Location: index.php');
    return;
}

require_once 'util.php';
require_once 'model/edit-model.php';
flashMessages(); /* куда можно перенести? */

if (isset($_SESSION['confirm']) && $_SESSION['confirm'] == 'no') {
    $_SESSION['error'] = 'Confirm your email address.';
    header('Location: gallery.php?sort=all&page=1');
    return;
}

if (isset($_SESSION['name']) && isset($_GET['user']) && $_SESSION['name'] == $_GET['user']) {
    $row = getUserData($pdo, $_GET['user']);
    if ($row !== false) {
        if ($row['notification'] == 'yes')
            $checked = 'checked';

        if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {
            $page = 'edit.php?user=' . $row['name'];

            if (isset($_POST['notific']) && $_POST['notific'] == 'yes')
                $notific = 'yes';
            else
                $notific = 'no';
            
            changeNotific($pdo, $notific, $_SESSION['user_id']);
            
            if (strlen($_POST['username_up']) == 0 || strlen($_POST['email_up']) == 0) {
                $_SESSION['error'] = 'Username and email are required';
                header('Location: edit.php?user=' . $row['name']);
                return;
            }

            if ($_POST['username_up'] != $row['name']) {
                checkUserName($pdo, $page);
                $row['name'] = $_POST['username_up'];
            }
            if ($_POST['email_up'] != $row['email'])
                checkEmail($pdo, $page);
            checkLenInput('description', $page, 'Description');
            if (strlen($_POST['pass_up']) > 0 || strlen($_POST['repass_up']) > 0) {
                checkPassword($pdo, $page);
                if (!isset($_SESSION['error']))
                    changePass($pdo,hash('sha512', $salt . $_POST['repass_up']), $_SESSION['user_id']);
            }
            if (!isset($_SESSION['error'])) {
                updateAll($pdo, $_POST['username_up'], $_POST['email_up'], $_POST['description'], $_SESSION['user_id']);
                $_SESSION['name'] = $_POST['username_up'];

                $upload_dir = 'images/' . $row['user_id'];
                if (!file_exists($upload_dir))
                    mkdir($upload_dir, 0777, true);
                $upload_dir .= '/avatar';
                if (!file_exists($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $tmp_name = $_FILES['ava']['tmp_name'];
                $name = $upload_dir . '/' . date('HisdmY') . '_' . $row['user_id'] . '.png';
                $move = move_uploaded_file($tmp_name, $name);
                if ($move) {
                    updateAva($pdo, $name, $_SESSION['user_id']);
                    if (isset($row['avatar']) && $row['avatar'] && $row['avatar'] != 'img/icon/user.svg')
                        unlink($row['avatar']);
                }
                header('Location: profile.php?user=' . htmlentities($_SESSION['name']) . '&page=1&posts');
            }
        }
        if (isset($_POST['submit']) && $_POST['submit'] == 'Cancel')
            header('Location: profile.php?user=' . $row['name'] . '&page=1&posts');
    }
} else
    header('Location: index.php');

    
require_once 'components/header.php';
require_once 'components/edit-view.php';
require_once 'components/footer.php';




