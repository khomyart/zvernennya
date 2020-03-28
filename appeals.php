<?php

include './imports.php';

$searchValue = '';
$results_per_page = 10;
$feedbackContactError = [];

$full_name = ''; 
$address = ''; 
$social_category = ''; 
$phone = ''; 
$appeal_text = ''; 
$is_consultation_has_been_given = [1, 2]; 
$is_package_needed = [1, 2]; 
$other = ''; 

$filter_display_values = ["",'yes','no'];
$filter_sort_display_values = ['DESC', 'ASC'];

if (isset($_GET['do_the_filtration'])) {
    if (isset($_GET['filter'])) {
        $_SESSION['filter'] = $_GET['filter'];
        header('Location: appeals.php'.$current_page_for_insert_into_redirection_after_some_operations);
    }
}

if (isset($_GET['cancel_the_filtration'])) {
    unset($_SESSION['filter']);
    header('Location: appeals.php'.$current_page_for_insert_into_redirection_after_some_operations);
}

$searchValue = $_SESSION['filter'];
$appeals = getListOfAppeals($number_of_results,$results_per_page, $searchValue);
$current_page_for_insert_into_redirection_after_some_operations = '?page='. $_SESSION['number_of_page_to_replace_get_request'];
$number_of_pages = ceil($number_of_results/$results_per_page);

$page_to_go = intval($_GET['go_to_page_number_input']);

if (isset($_GET['go_to_page_number'])) {
    if (is_int($page_to_go) && (($page_to_go > 0) &&
    ($page_to_go <= $number_of_pages))) 
    {
        header('Location: appeals.php?page='.$page_to_go);
    } else {
        header('Location: appeals.php'.$current_page_for_insert_into_redirection_after_some_operations);
    }
}

if(isset($_POST['appeal_create'])) {
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

    if(editAppeal($id, $full_name,
        $address, $social_category,
        $phone, $appeal_text,
        $is_consultation_has_been_given,
        $is_package_needed, $other)) {
        header('Location: appeals.php'.$current_page_for_insert_into_redirection_after_some_operations);
    } else {
        //$feedbackContactError = contactErrorList($phone, $firstName, $lastName);
    }
}

if(isset($_POST['appeal_remove'])) {
    $id = $_POST['appeal_remove'];

    if(removeAppeal($id)) {
        header('Location: appeals.php'.$current_page_for_insert_into_redirection_after_some_operations);
    }
}

if(isset($_POST['appeal_mark_as_done'])) {
    $id = $_POST['appeal_mark_as_done'];

    if(markAppealAsDone($id)) {
        header('Location: appeals.php'.$current_page_for_insert_into_redirection_after_some_operations);
    }
}

?>

<?php include './templates/header.php'; ?>
<?php include './templates/navigation.php'; ?>

<style>
    .done_appeal {
        background: rgba(0, 117, 0, 0.79);
    }

    .pagenation_holder {
       //margin-top: 50px;
        //margin-bottom: 50px;
        width: 46%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: row;
    }

    .pagenation_holder button {
        margin:2px;
    }

    select, input {
        border-radius: 5px; 
        outline: none;
    }

</style>

<div class="container-fluid">
    <?php
    if($feedbackContactError) { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($feedbackContactError as $errMessage) { ?>
                <?= $errMessage.'<br>' ?>
            <?php } ?>
        </div>
    <?php } ?>
    <form action ="" method="get" id="form_for_cancel_filtration" hidden></form>
    <form action ="" method="get" id="form_for_go_to_page_number" hidden></form>

    <div class="row">
        <div class="col">
            <br />
            <p>
            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample"
                    aria-expanded="false" aria-controls="collapseExample">
                Фільтр
            </button>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <form class="form-inline d-flex justify-content-between" action="appeals.php" method="get">
                        <div class="d-flex justify-content-between w-100 pb-3 flex-wrap">
                            <div class="d-flex align-items-center p-3">
                                <input type="hidden" name="isSearch" value="Y">
                                <div class="form-group">
                                    <label for="search" class="sr-only">Текст для пошуку</label>
                                    <input
                                        type="text"
                                        name="filter[search]"
                                        class="form-control"
                                        id="search"
                                        placeholder="Ключове слово"
                                        value="<?= $searchValue['search'] ?>">
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-3">
                                <span>Консультація надана:&nbsp</span>
                                <select name='filter[consultation]'>
                                
                                    <?php
                                    foreach($filter_display_values as $result) {
                                        if ($result == $_SESSION['filter']['consultation']) {
                                    ?>
                                            <option selected
                                                value="<?= $result ?>">
                                                <?php if ($result == '') {echo 'Всі';} elseif ($result == 'yes')
                                                {echo 'Так';} elseif ($result == 'no') {echo 'Ні';}?>
                                            </option>
                                    <?php
                                        } else {
                                    ?>
                                        <option value="<?= $result ?>">
                                            <?php if ($result == '') {echo 'Всі';} elseif ($result == 'yes')
                                            {echo 'Так';} elseif ($result == 'no') {echo 'Ні';}?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="d-flex align-items-center p-3">
                                <span>Гуманітарна допомога потрібна:&nbsp</span>
                                <select name='filter[packages]'>

                                    <?php
                                    foreach($filter_display_values as $result) {
                                        if ($result == $_SESSION['filter']['packages']) {
                                    ?>
                                            <option selected
                                                value="<?= $result ?>">
                                                <?php if ($result == '') {echo 'Всі';} elseif ($result == 'yes')
                                                {echo 'Так';} elseif ($result == 'no') {echo 'Ні';}?>
                                            </option>
                                    <?php
                                        } else {
                                    ?>
                                        <option value="<?= $result ?>">
                                            <?php if ($result == '') {echo 'Всі';} elseif ($result == 'yes')
                                            {echo 'Так';} elseif ($result == 'no') {echo 'Ні';}?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="d-flex align-items-center p-3">
                                <span>Виконання підтверджене:&nbsp</span>
                                <select name='filter[is_done]'>

                                    <?php
                                    foreach($filter_display_values as $result) {
                                        if ($result == $_SESSION['filter']['is_done']) {
                                    ?>
                                            <option selected
                                                value="<?= $result ?>">
                                                <?php if ($result == '') {echo 'Всі';} elseif ($result == 'yes')
                                                {echo 'Так';} elseif ($result == 'no') {echo 'Ні';}?>
                                            </option>
                                    <?php
                                        } else {
                                    ?>
                                        <option value="<?= $result ?>">
                                            <?php if ($result == '') {echo 'Всі';} elseif ($result == 'yes')
                                            {echo 'Так';} elseif ($result == 'no') {echo 'Ні';}?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="d-flex align-items-center p-2">
                                    <span>Сортувати:&nbsp</span>
                                    <select name='filter[sort_by]'>
                                    <?php
                                    foreach($filter_sort_display_values as $result) {
                                        if ($result == $_SESSION['filter']['sort_by']) {
                                    ?>
                                            <option selected
                                                    value="<?= $result ?>">
                                                    <?php if ($result == 'ASC') {echo 'Від старішого до новішого';} else {
                                                        echo 'Від новішого до старішого';}?>
                                            </option>
                                    <?php
                                        } else {
                                    ?>
                                        <option value="<?= $result ?>">
                                            <?php if ($result == 'ASC') {echo 'Від старішого до новішого';} else {
                                                echo 'Від новішого до старішого';}?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <hr class="w-100 h-10">
                        <div class="d-flex justify-content-center w-100 p-2">
                            <button type="submit" class="btn btn-primary" name="do_the_filtration" value="find_appeals" style="margin-left: 10px;">Застосувати фільтр</button>
                            <button type="submit" class="btn btn-primary" form="form_for_cancel_filtration" name="cancel_the_filtration" value="find_appeals" style="margin-left: 10px;">Скинути фільтри</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Додати звернення
                </button>
                <form class="pagenation_holder" action="" method="get" style="padding: 0px; margin: 0px;">
                    <div>
                        <?php if(($_SESSION['number_of_page_to_replace_get_request']-1) != 0) { ?>
                            <button class="btn btn-primary" type="submit" name="page"
                                    value="<?= $_SESSION['number_of_page_to_replace_get_request']-1 ?>"><</button>
                        <?php } ?>
                        
                        <?php if(($_SESSION['number_of_page_to_replace_get_request']) != $number_of_pages) { ?>
                            <button class="btn btn-primary" type="submit" name="page"
                                    value="<?= $_SESSION['number_of_page_to_replace_get_request']+1 ?>">></button>
                        <?php } ?>
                    </div>
                    <div class='ml-2'>
                        Сторінка: <?= $_SESSION['number_of_page_to_replace_get_request'] ?> із <?= $number_of_pages ?>
                    </div>
                    <input
                        type="text"
                        name="go_to_page_number_input"
                        class="form-control ml-2"
                        style = "width: 50px;"
                        form="form_for_go_to_page_number"
                        placeholder="№"
                        value="">
                    <button class="btn btn-primary ml-2" type="submit" form="form_for_go_to_page_number"
                            name="go_to_page_number" value="go_to_page_number"> Перейти на сторінку </button>
                </form> 
            </div>
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
                                    <input id="full_name_field" type="text" class="form-control"
                                           name="full_name"
                                           value="<?= $full_name ?>"
                                    >
                                    <div id="check_full_name_result" ></div>
                                    <div id="check_full_name" style="margin-top: 10px;" class="btn btn-primary">
                                        Перевірити
                                    </div>
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
                    <?php $appealData['is_done'] === 'yes' ? $appeal_style = 'style = "background: rgba(0, 117, 0, 0.6);"' : $appeal_style = '' ?>
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
                                        <a class="dropdown-item"  target="_blank"
                                                href="view.php?id=<?= $appealData['id'] ?>"
                                        >
                                            Переглянути
                                        </a>
                                        <?php if ($appealData['is_done'] === 'no') { ?>
                                            <button class="dropdown-item"
                                                type="submit"
                                                name="appeal_mark_as_done"
                                                value="<?= $appealData['id'] ?>"
                                            >
                                                Відмітити виконаним
                                            </button>
                                        <?php } ?>
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

<script type="application/javascript" src="https://code.jquery.com/jquery-3.4.1.slim.js"
        integrity="sha256-BTlTdQO9/fascB1drekrDVkaKd9PkwBymMlHOiG+qLI="
        crossorigin="anonymous">
</script>
<script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="application/javascript" src="lib/js/check_full_name.js"></script>
