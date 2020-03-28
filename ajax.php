<?php

include './imports.php';

$fullNameFromAppealsFile = '%'.$_POST['inputText'].'%';

if ($_POST['inputText'] === '') {
    echo ('<div id="fullNameValidationRsult" style="color: black"> Спочатку введіть фрагмент імені </div>');
} else {
    $appealsRelatedToThisName = getAllRows('SELECT * FROM `appeal` WHERE `full_name` LIKE :fullName ORDER BY `id` DESC;', [
        'fullName' => $fullNameFromAppealsFile
    ]);

    if($appealsRelatedToThisName === false) {
        echo ('<div id="fullNameValidationRsult" style="color: green"> Співпадінь не знайдено </div>');
    } else { ?>
            <div class="d-flex justify-content-between align-items-center" style="margin-top: 10px;">
                <div id="fullnameValidationResult" style="color: red"> Співпадінь знайдено: <?= count($appealsRelatedToThisName) ?></div>
                <button class="btn btn-primary" type="button"
                data-toggle="collapse" data-target="#full_name_match" aria-expanded="false" aria-controls="full_name_match">
                    Переглянути
                </button>
            </div>
            <div class="collapse" id="full_name_match" style="margin-top: 10px">
                <?php foreach ($appealsRelatedToThisName as $appeal) { ?>
                <div class="card card-body d-flex justify-content-between align-items-center flex-row mb-2 p-2"
                <?php if ($appeal['is_done'] === 'yes') { echo 'style="background:rgba(0, 117, 0, 0.6);"'; }?>>
                    <span><?= $appeal['when_created'] ?></span>
                    <div class="d-flex justify-content-start">
                        <span class="p-3" style="font-size: 20px; width: 60px;">
                            <?php if ($appeal['is_consultation_has_been_given'] === 'yes') { echo '📞'; }?>
                        </span>
                        <span class="p-3" style="font-size: 20px; width: 60px;">
                            <?php if ($appeal['is_package_needed'] === 'yes') { echo '🍱'; }?>
                        </span>
                    </div>
                    <a id="check_appeal_view_after_validation"
                    href="view.php?id=<?= $appeal['id'] ?>"
                    target="_blank" class="btn btn-secondary" >Картка </a>
                </div>
                <?php } ?>
            </div>
        <?php
        }
    }
?>