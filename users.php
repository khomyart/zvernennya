<?php
include './imports.php';

if(!isAuthAdmin()) {
    header('Location: index.php');
    die();
}

$searchValue = '';
$regEmail = '';
$regPassword = '';
$regNickname = '';
$userType = ['default_user','administrator'];
$feedbackUserError = [];

if (isset($_REQUEST['isSearch']) && ($_REQUEST['isSearch'] == 'Y')) {
    $searchValue = $_REQUEST['search'];
}

$users = getListOfUsers($searchValue);

if ($_POST['regButton']) {
    $regEmail = $_POST['regEmail'] ?: '';
    $regPassword = $_POST['regPassword'] ?: '';
    $regNickname = $_POST['regNickname'] ?: '';

    if (addUser($regEmail, $regPassword, $regNickname)) {
        //Preventing re-registration by pressing F5 (sending post request again and again)
        header('Location: users.php');
    } else {
        $feedbackUserError = userErrorList($regEmail, $regPassword, $regNickname);
    }
}

if (isset($_POST['userEditSave'])) {
    $query = 'SELECT * FROM `user` WHERE `id` = :id;';
    $param = ['id' => $_POST['userEditSave']];
    $result = getRow($query, $param);

    $id = $_POST['userEditSave'];
    $email = $_POST['emailEdit'];
    $type = $_POST['typeEdit'];
    $nickname = $_POST['nicknameEdit'];

    if (($result['email'] == $email) && ($result['nickname'] == $nickname)) {
        if (editUser($id, $email, $nickname, $type, 'none')) {
            //Preventing re-registration by pressing F5 (sending post request again and again)
            header('Location: users.php');
        } else {
            $feedbackUserError = altUserErrorList($email, $nickname);
        }
    } elseif (($result['email'] == $email) && ($result['nickname'] !== $nickname)) {
        if (editUser($id, $email, $nickname, $type, 'nickname')) {
            header('Location: users.php');
        } else {
            $feedbackUserError = userEditNicknameExistenceChecker($nickname);
        }
    } elseif (($result['email'] !== $email) && ($result['nickname'] == $nickname)) {
        if (editUser($id, $email, $nickname, $type, 'login')) {
            header('Location: users.php');
        } else {
            $feedbackUserError = userEditLoginExistenceChecker($email);
        }
    } else {
        if (editUser($id, $email, $nickname, $type, 'full')) {
            header('Location: users.php');
        } else {
            $feedbackUserError = userErrorList($email, '******', $nickname);
        }
    }
}

if(isset($_POST['userRemove'])) {
    $id = $_POST['userRemove'];

    if(removeUser($id)) {
        header('Location: users.php');
        die();
    }
}

if(isset($_POST['editPasswordConfirm'])) {
    $id = $_POST['editPasswordConfirm'];
    $oldPassword = $_POST['editOldPassword'];
    $newPassword = $_POST['editNewPassword'];
    $repeatNewPassword = $_POST['editRepeatNewPassword'];

    if (empty(userPasswordChange($id, $oldPassword, $newPassword, $repeatNewPassword))) {
        header('Location: users.php');
        die();
    } else {
        $feedbackUserError = userPasswordChange($id, $oldPassword, $newPassword, $repeatNewPassword);
    }
}

?>

<?php include './templates/header.php'; ?>
<?php include './templates/navigation.php'; ?>

<div class="container">

    <?php
    if($feedbackUserError) { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($feedbackUserError as $errMessage) { ?>
                <?= $errMessage.'<br>' ?>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col">

            <br />

            <form class="form-inline d-flex justify-content-between" action="users.php" method="get">
                <div>
                    <input type="hidden" name="isSearch" value="Y">
                    <div class="form-group">
                        <label for="search" class="sr-only">Text to search</label>
                        <input
                                type="text"
                                name="search"
                                class="form-control"
                                id="search"
                                placeholder="Text to search"
                                value="<?= $searchValue ?>">
                        <button type="submit" class="btn btn-primary" style="margin-left: 5px;">Search</button>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Add user
                    </button>
                </div>
            </form>
            <div class="modal fade"
                 id="exampleModal"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Registration</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email address</label>
                                    <input type="email"
                                           class="form-control"
                                           id="exampleInputEmail1"
                                           aria-describedby="emailHelp"
                                           name="regEmail"
                                           value="<?= $regEmail ?>"
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="exampleInputPassword1"
                                           name="regPassword"
                                           value="<?= $regPassword ?>"
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputNickname1">Nickname</label>
                                    <input type="text"
                                           class="form-control"
                                           id="exampleInputNickname1"
                                           name="regNickname"
                                           value="<?= $regNickname ?>"
                                    >
                                </div>
                                <div class="modal-footer">
                                    <button type="submit"
                                            class="btn btn-primary"
                                            name="regButton"
                                            value="sent"
                                    >
                                        Confirm
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        Close
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <br />

            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="20%">Email</th>
                    <th width="38%">Nickname</th>
                    <th width="38%">Type</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $userData) { ?>
                    <tr>
                        <form action="" method="post">
                            <?php if($_POST['userEditModeEnable'] === $userData['id']) {?>
                                <td>
                                    <input class="w-100 text-center" name="emailEdit"
                                           value="<?= $userData['email'] ?>"
                                    >
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="nicknameEdit"
                                           value="<?= $userData['nickname'] ?>"
                                    >
                                </td>
                                <td>
                                    <select class="w-50" name="typeEdit">
                                        <?php
                                        foreach($userType as $type) {
                                            if ($type == $userData['type']) {
                                        ?>
                                                <option value="<?= $type ?>" selected>
                                                    <?= $type ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="<?= $type ?>">
                                                    <?= $type ?>
                                                </option>
                                            <?php }
                                        }?>
                                    </select>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle"
                                                type="button"
                                                id="dropdownMenuButton"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu" style="margin-left:-114px" aria-labelledby="dropdownMenuButton">
                                            <button class="btn btn-secondary dropdown-item"
                                                    type="submit"
                                                    name="userEditSave"
                                                    value="<?= $userData['id'] ?>"
                                            >
                                                Save
                                            </button>
                                            <button class="btn btn-secondary dropdown-item"
                                                    type="submit"
                                                    name="user_edit_cancel"
                                                    value="cancel"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            <?php } else {?>
                                <td><?= $userData['email'] ?></td>
                                <td><?= $userData['nickname'] ?></td>
                                <td><?= $userData['type'] ?></td>
                                <td width="30">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle"
                                                type="button"
                                                id="dropdownMenuButton"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu" style="margin-left:-114px" aria-labelledby="dropdownMenuButton">
                                            <!-- Button trigger modal (user password changing) -->
                                            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#changeUserPasswordModal">
                                                Change user password
                                            </button>
                                            <!-- Button trigger modal (user password changing) end -->
                                            <button class="dropdown-item"
                                                    type="submit"
                                                    name="userEditModeEnable"
                                                    value="<?= $userData['id'] ?>"
                                            >
                                                Edit
                                            </button>
                                            <button class="dropdown-item"
                                                    type="submit"
                                                    name="userRemove"
                                                    value="<?= $userData['id'] ?>"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <!-- Modal (user password changing) -->
                                        <div class="modal fade"
                                             id="changeUserPasswordModal"
                                             tabindex="-1"
                                             role="dialog"
                                             aria-labelledby="changeUserPasswordModalLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="changeUserPasswordModalLabel">
                                                            Change user password
                                                        </h5>
                                                        <button type="button"
                                                                class="close"
                                                                data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="exampleInputOldPassword" class="float-left">
                                                                Old password
                                                            </label>
                                                            <input type="password"
                                                                   class="form-control"
                                                                   id="exampleInputOldPassword"
                                                                   name="editOldPassword"
                                                            >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="exampleInputNewPassword" class="float-left">
                                                                New password
                                                            </label>
                                                            <input type="password"
                                                                   class="form-control"
                                                                   id="exampleInputNewPassword"
                                                                   name="editNewPassword"
                                                            >
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="exampleInputRepeatNewPassword" class="float-left">
                                                                Repeat new password
                                                            </label>
                                                            <input type="password"
                                                                   class="form-control"
                                                                   id="exampleInputRepeatNewPassword"
                                                                   name="editRepeatNewPassword"
                                                            >
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit"
                                                                    class="btn btn-primary"
                                                                    name="editPasswordConfirm"
                                                                    value="<?= $userData['id'] ?>"
                                                            >
                                                                Confirm
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-secondary"
                                                                    data-dismiss="modal"
                                                            >
                                                                Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal (user password changing) end-->
                                    </div>
                                </td>
                            <?php } ?>
                        </form>
                    </tr>
                <?php }  ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include './templates/footer.php'; ?>
