<?php

include './imports.php';

$searchValue = '';
$feedbackContactError = [];

$full_name = ''; 
$address = ''; 
$social_category = ''; 
$phone = ''; 
$appeal_text = ''; 
$is_consultation_has_been_given = [1, 2]; 
$is_package_needed = [1, 2]; 
$other = ''; 

if (isset($_REQUEST['isSearch']) && ($_REQUEST['isSearch'] == 'Y')) {
    $searchValue = $_REQUEST['search'];
}

$appeals = getListOfAppeals($searchValue);

if(isset($_POST['appeal_create'])) {
    //$phone = $_POST['phoneNumberNoCode'] ? $_POST['phoneCode'].$_POST['phoneNumberNoCode'] : '';
   // $firstName = $_POST['firstName'] ?: '';
    //$lastName = $_POST['lastName'] ?: '';

    $full_name = $_POST['full_name'] ?: '';
    $address = $_POST['address'] ?: '';
    $social_category = $_POST['social_category'] ?: '';
    $phone = $_POST['phone'] ?: '';
    $appeal_text = $_POST['appeal_text'] ?: '';
    $is_consultation_has_been_given = $_POST['is_consultation_has_been_given'] ?: '';
    $is_package_needed = $_POST['is_package_needed'] ?: '';
    $other = $_POST['other'] ?: '';

    if(addAppeal($_SESSION['auth']['id'], $full_name, $address, $social_category, $phone, $appeal_text, $is_consultation_has_been_given, $is_package_needed, $other)) {
        header('Location: appeals.php');
        die();
    } else {
        //$feedbackContactError = contactErrorList($phone, $firstName, $lastName);
    }
}

if(isset($_POST['appeal_edit_save'])) {
    $id = $_POST['appeal_edit_save'];
    $full_name = $_POST['full_name_edit'];
    $address = $_POST['address_edit'];
    $social_category = $_POST['social_category_edit'];
    $phone = $_POST['phone_edit'];
    $appeal_text = $_POST['appeal_text_edit'];
    $is_consultation_has_been_given = $_POST['is_consultation_has_been_given_edit'];
    $is_package_needed = $_POST['is_package_needed_edit'];
    $other = $_POST['other_edit'];

    if(editAppeal($id, $full_name, $address, $social_category, $phone, $appeal_text, $is_consultation_has_been_given, $is_package_needed, $other)) {
        header('Location: appeals.php');
        die();
    } else {
        $feedbackContactError = contactErrorList($phone, $firstName, $lastName);
    }
}

if(isset($_POST['appeal_remove'])) {
    $id = $_POST['appeal_remove'];

    if(removeAppeal($id)) {
        header('Location: appeals.php');
        die();
    }
}

if(isset($_POST['appeal_mark_as_done'])) {
    $id = $_POST['appeal_mark_as_done'];

    if(markAppealAsDone($id)) {
        header('Location: appeals.php');
        die();
    }
}

?>

<?php include './templates/header.php'; ?>
<?php include './templates/navigation.php'; ?>

<div class="container-fluid">
    <?php
    if($feedbackContactError) { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($feedbackContactError as $errMessage) { ?>
                <?= $errMessage.'<br>' ?>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col">
            <br />
            <form class="form-inline d-flex justify-content-between" action="appeals.php" method="get">
                <div>
                    <input type="hidden" name="isSearch" value="Y">
                    <div class="form-group">
                        <label for="search" class="sr-only">Текст для пошуку</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            id="search"
                            placeholder="Text to search"
                            value="<?= $searchValue ?>">
                        <button type="submit" class="btn btn-primary" style="margin-left: 5px;">Пошук</button>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Додати звернення
                    </button>
                </div>
            </form>
            <div class="modal fade"
                 id="exampleModal"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="exampleModalLabel"
                 aria-hidden="true"
            >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="" method="post">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Нове звернення</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Повне ім'я</label>
                                    <input type="text" class="form-control"
                                           name="full_name"
                                           value="<?= $full_name ?>"
                                    >
                                    
                                </div>
                                <div class="form-group">
                                    <label>Адреса</label>
                                    <input type="text" class="form-control"
                                           name="address"
                                           value="<?= $address ?>"
                                    >
                                </div>
                                <div class="form-group">
                                    <label>Соціальна категорія</label>
                                    <input type="text"
                                           class="form-control"
                                           name="social_category"
                                           value="<?= $social_category ?>"
                                    >
                                </div>
                                <div class="form-group">
                                    <label>Контактний телефон</label>
                                    <input type="text"
                                           class="form-control"
                                           name="phone"
                                           value="<?= $phone ?>"
                                    >
                                </div>
                                <div class="form-group">
                                    <label>Текст звернення</label>
                                    <textarea type="text"
                                           class="form-control"
                                           name="appeal_text"
                                    ><?= $appeal_text ?></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex flex-column">
                                        <label>Чи була надана консультація?</label>

                                        <select class="form-control w-25" name="is_consultation_has_been_given">

                                            <?php
                                            foreach($is_consultation_has_been_given as $result) {
                                                if ($result == $_POST['is_consultation_has_been_given']) {
                                            ?>
                                                    <option selected
                                                            value="<?= $result ?>">
                                                            <?php if ($result == 1) {echo 'Так';} else {echo 'Ні';} ?>
                                                    </option>
                                            <?php
                                                } else {
                                            ?>
                                                <option value="<?= $result ?>">
                                                    <?php if ($result == 1) {echo 'Так';} else {echo 'Ні';} ?>
                                                </option>
                                            <?php
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex flex-column">
                                        <label>Чи потрібна гуманітарна допомога?</label>
                                        <select class="form-control w-25" name="is_package_needed">

                                            <?php
                                            foreach($is_package_needed as $result) {
                                                if ($result == $_POST['is_package_needed']) {
                                            ?>
                                                    <option selected
                                                            value="<?= $result ?>">
                                                            <?php if ($result == 1) {echo 'Так';} else {echo 'Ні';} ?>
                                                    </option>
                                            <?php
                                                } else {
                                            ?>
                                                <option value="<?= $result ?>">
                                                    <?php if ($result == 1) {echo 'Так';} else {echo 'Ні';} ?>
                                                </option>
                                            <?php
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Інше</label>
                                    <input type="text"
                                           class="form-control"
                                           name="other"
                                           value="<?= $other ?>"
                                    >
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" name="appeal_create" value="submit">
                                    Створити звернення
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Закрити
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <br />

            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="15%">Повне ім'я</th>
                    <th width="15%">Адреса</th>
                    <th width="10%">Соціальна категорія</th>
                    <th width="10%">Телефон</th>
                    <th width="19%">Текст звернення</th>
                    <th width="8%">Консультація</th>
                    <th width="8%">Пакунок</th>
                    <th width="15%">Інше</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($appeals as $appealData) { ?>
                    <?php $appealData['is_done'] === 'yes' ? $appeal_style = 'style = "background: orange;"' : $appeal_style = '' ?>
                    <tr <?= $appeal_style ?> >
                        <form action="" method="post">
                            <?php if($_POST['appealEditModeEnable'] === $appealData['id']) {?>
                                <td>
                                    <input class="w-100 text-center" name="full_name_edit"
                                           value="<?= $appealData['full_name'] ?>"
                                    >
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="address_edit"
                                           value="<?= $appealData['address'] ?>"
                                    >
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="social_category_edit"
                                           value="<?= $appealData['social_category'] ?>"
                                    >
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="phone_edit"
                                           value="<?= $appealData['phone'] ?>"
                                    >
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="appeal_text_edit"
                                           value="<?= $appealData['appeal_text'] ?>"
                                    >
                                </td>
                                <td>
                                    <select class="w-100 text-center" name="is_consultation_has_been_given_edit">

                                    <?php
                                    foreach($is_consultation_has_been_given as $result) {
                                        if ($result === 1) {$result = 'yes';} else {$result = 'no';}
                                        if ($result == $appealData['is_consultation_has_been_given']) {
                                    ?>
                                            <option selected
                                                    value="<?= $result ?>">
                                                    <?php if ($result === 'yes') {echo 'Так';} else {echo 'Ні';} ?>
                                            </option>
                                    <?php
                                        } else {
                                    ?>
                                        <option value="<?= $result ?>">
                                            <?php if ($result === 'yes') {echo 'Так';} else {echo 'Ні';} ?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>

                                    </select>
                                </td>
                                <td>
                                <select class="w-100 text-center" name="is_package_needed_edit">

                                    <?php
                                    foreach($is_package_needed as $result) {
                                        if ($result === 1) {$result = 'yes';} else {$result = 'no';}
                                        if ($result == $appealData['is_package_needed']) {
                                    ?>
                                            <option selected
                                                    value="<?= $result ?>">
                                                    <?php if ($result === 'yes') {echo 'Так';} else {echo 'Ні';} ?>
                                            </option>
                                    <?php
                                        } else {
                                    ?>
                                        <option value="<?= $result ?>">
                                            <?php if ($result === 'yes') {echo 'Так';} else {echo 'Ні';} ?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>

                                    </select>
                                </td>
                                <td>
                                    <input class="w-100 text-center" name="other_edit"
                                           value="<?= $appealData['other'] ?>"
                                    >
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
                                                    name="appeal_edit_save"
                                                    value="<?= $appealData['id'] ?>"
                                            >
                                                Зберегти
                                            </button>
                                            <button class="btn btn-secondary dropdown-item"
                                                    type="submit"
                                                    name="appeal_edit_cancel"
                                                    value="cancel"
                                            >
                                                Відмінити
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            <?php } else {?>
                            <td><?= $appealData['full_name'] ?></td>
                            <td><?= $appealData['address'] ?></td>
                            <td><?= $appealData['social_category'] ?></td>
                            <td><?= $appealData['phone'] ?></td>
                            <td><?= $appealData['appeal_text'] ?></td>
                            <td><?php if ($appealData['is_consultation_has_been_given'] === 'yes') {echo '✓';} else {echo '🞪';} ?></td>
                            <td><?php if ($appealData['is_package_needed'] === 'yes') {echo '✓';} else {echo '🞪';} ?></td>
                            <td><?= $appealData['other'] ?></td>
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
                                    <button class="dropdown-item"
                                                type="submit"
                                                name="appeal_mark_as_done"
                                                value="<?= $appealData['id'] ?>"
                                        >
                                            Відмітити виконаним
                                        </button>
                                        <button class="dropdown-item"
                                                type="submit"
                                                name="appealEditModeEnable"
                                                value="<?= $appealData['id'] ?>"
                                        >
                                            Редагувати
                                        </button>
                                        <button class="dropdown-item"
                                                type="submit"
                                                name="appeal_remove"
                                                value="<?= $appealData['id'] ?>"
                                        >
                                            Видалити
                                        </button>
                                    </div>
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
