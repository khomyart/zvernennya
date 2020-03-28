<?php
include './imports.php';
include './templates/header.php';
?>

<?php
$show_error_div = false;

if (isset($_GET['id'])) {
    $query = 'SELECT a.id, u.first_name, u.last_name, a.full_name, a.address, a.social_category, a.appeal_text,
                        a.phone, a.is_consultation_has_been_given, a.is_package_needed, a.other, a.when_created 
                     FROM `appeal` AS a 
                     LEFT JOIN `users` AS u ON a.user_id = u.id WHERE a.id = :id';
   $appeal = getRow($query, ['id' => $_GET['id']]);

   if ($appeal === false) {
       $show_error_div = true;
   }
}

//arrays has been created to do easier card text forming
$array_with_param_names = [
        'Номер картки:', 'Ім\'я заявника:', 'Адреса:',
        'Соціальна категорія:', 'Телефон:', 'Текст звернення:', 'Чи надана консультація:',
        'Чи необхідна допомога:', 'Інше:', 'Реєстратор:', 'Картка створена:'
    ];
$array_with_param_values = [
        $appeal['id'], $appeal['full_name'], $appeal['address'],
        $appeal['social_category'],$appeal['phone'], $appeal['appeal_text'], $appeal['is_consultation_has_been_given'],
        $appeal['is_package_needed'], $appeal['other'], $appeal['first_name'] . ' ' . $appeal['last_name'], $appeal['when_created']
    ];

?>

<style>
    .info-holder {
        margin-top: 30px;
    }

    .custom_class_for_row {
        margin-bottom: 20px;
    }

    .param_name {
        font-weight: bolder;
    }

    .param_value {

    }
</style>

<div class="container">
    <div class="info-holder">
        <?php $i = 0; ?>
        <?php foreach ($array_with_param_names as $param_name) { ?>
            <div class="row custom_class_for_row">
                <div class="col param_name d-flex align-items-center justify-content-end">
                    <?= $param_name ?>
                </div>
                <div class="col param_value d-flex align-items-center justify-content-start">
                    <span>
                        <?php if (current($array_with_param_values) === 'yes') {echo 'Так';} ?>
                        <?php if (current($array_with_param_values) === 'no') {echo 'No';} ?>
                        <?php if ((current($array_with_param_values) !== 'yes') &&
                                  (current($array_with_param_values) !== 'no'))
                                {
                                    echo current($array_with_param_values);
                                } ?>
                    </span>
                </div>
            </div>
            <?php if ($param_name === 'Інше:') {echo '<hr>';} ?>
            <?php next($array_with_param_values) ?>
        <?php }?>
    </div>

    <div style="margin-top: 30px"></div>

    <a class="btn btn-primary" onclick="window.close()"
       href="http://koronavirus.zvernennya.khomyart.com/appeals.php?page=<?= $_SESSION['number_of_page_to_replace_get_request'] ?>">
        Назад
    </a>

</div>

<?php include './templates/footer.php';?>