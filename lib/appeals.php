<?php

/**
 * Returns list\array of contacts
 *
 * @param $filter
 * @return array
 */
function getListOfAppeals(&$number_of_results, $results_per_page, $filter = '')
{
    $query = "SELECT COUNT(*) FROM `appeal` WHERE 1";

    if (is_string($filter['search'])) {
        $query .= ' AND ( (`full_name` LIKE :filter) OR (`address` LIKE :filter) OR (`social_category` LIKE :filter) OR (`phone` LIKE :filter) OR (`appeal_text` LIKE :filter) OR (`other` LIKE :filter) ) AND (`is_consultation_has_been_given` LIKE :consultation) AND (`is_package_needed` LIKE :package) AND (`is_done` LIKE :is_done)';
        $params = [
            'filter' => '%' . $filter['search'] . '%',
            'consultation' => '%' .$filter['consultation']. '%',
            'package' => '%' .$filter['packages']. '%',
            'is_done' => '%' .$filter['is_done']. '%',
            'sort_buy' => '%' .$filter['sort_by']. '%',
        ];
    }

    $appeal = getRow($query, $params);

    $number_of_results = $appeal['COUNT(*)'];
     
    if (!isset($_GET['page'])) {
        $_SESSION['number_of_page_to_replace_get_request'] = 1;
        $page = 1;
    } else {
        $page = $_GET['page'];
        $_SESSION['number_of_page_to_replace_get_request'] = $_GET['page'];
    }

    $this_page_first_result = ($page-1)*$results_per_page;

    $final_query = "SELECT * FROM `appeal` WHERE 1";

    if (is_string($filter['search'])) {
        $final_query .= ' AND ( (`full_name` LIKE :filter) OR (`address` LIKE :filter) OR (`social_category` LIKE :filter) OR (`phone` LIKE :filter) OR (`appeal_text` LIKE :filter) OR (`other` LIKE :filter) ) AND (`is_consultation_has_been_given` LIKE :consultation) AND (`is_package_needed` LIKE :package) AND (`is_done` LIKE :is_done)';
        $params = [
            'filter' => '%' . $filter['search'] . '%',
            'consultation' => '%' .$filter['consultation']. '%',
            'package' => '%' .$filter['packages']. '%',
            'is_done' => '%' .$filter['is_done']. '%',
            'sort_buy' => '%' .$filter['sort_by']. '%',
        ];
    }

    if ($filter['sort_by'] == '') {
        $filter['sort_by'] = 'DESC';
    }

    $final_query .= ' ORDER BY `id` '.$filter['sort_by'].' LIMIT ' . $this_page_first_result . ',' . $results_per_page ;
    
    return getAllRows($final_query, $params);
}

/**
 * Returns join list\array of contacts
 *
 * @param $filter
 * @return array
 */
function getJoinListOfAppeals($filter = '')
{
    $query = 'SELECT a.user_id, a.full_name, a.address, a.social_category, a.phone, a.appeal_text, 
                                    a.is_consultation_has_been_given, a.is_package_needed, a.other, a.when_created FROM 
`appeal` AS a LEFT JOIN `users` AS u ON a.user_id = u.id WHERE 1';

    if (is_string($filter) && (trim($filter) != '')) {
        $query .= ' AND ( (c.first_name LIKE :filter) OR (c.last_name LIKE :filter) OR (c.phone LIKE :filter) OR (c.id LIKE :filter) OR (u.email LIKE :filter) OR (u.nickname LIKE :filter) )';
        $params['filter'] = '%' . $filter . '%';
    }

    return getAllRows($query, $params);
}

/**
 * Returns array with error messages
 *
 * @param string $phone
 * @param string $firstName
 * @param string $lastName
 * @return array
 */
function contactErrorList($phone, $firstName, $lastName)
{
    $phone = trim($phone);
    $firstName = trim($firstName);
    $lastName = trim($lastName);

    if (strlen($phone) !== 0) {
        if(strlen($phone) > 20) {
            $feedbackContactError['phone'] = 'Phone max. length is 20 symbols';
        }
    } else {
        $feedbackContactError['phone'] = 'Phone field be empty';
    }

    if (strlen($firstName) !== 0) {
        if(strlen($firstName) > 30) {
            $feedbackContactError['firstName'] = 'First name max. length is 30 symbols';
        }
    } else {
        $feedbackContactError['firstName'] = 'First name cannot be empty';
    }

    if (strlen($lastName) !== 0) {
        if(strlen($lastName) > 30) {
            $feedbackContactError['lastName'] = 'Last name max. length is 30 symbols';
        }
    } else {
        $feedbackContactError['lastName'] = 'Last name cannot be empty';
    }

    return $feedbackContactError;
}

/**
 * Creates a new appeal
 *
 * @param $userId
 * @param $full_name
 * @param $address
 * @param $social_category
 * @param $phone
 * @param $appeal_text
 * @param $is_consultation_has_been_given
 * @param $is_package_needed
 * @param $other
 * @return bool
 */
function addAppeal($userId, $full_name, $address, $social_category, $phone, $appeal_text,
                   $is_consultation_has_been_given, $is_package_needed, $other)
{
    $query = 'INSERT INTO `appeal`(`user_id`, `full_name`, `address`, `social_category`, `phone`, `appeal_text`, 
                                    `is_consultation_has_been_given`, `is_package_needed`, `other`, `when_created`) 
                VALUES (:userId, :full_name, :address, :social_category, :phone, :appeal_text, 
                                    :is_consultation_has_been_given, :is_package_needed, :other, :when_created);';
    $params = [
        'userId' => $userId,
        'full_name' => $full_name,
        'address' => $address,
        'social_category' => $social_category,
        'phone' => $phone,
        'appeal_text' => $appeal_text,
        'is_consultation_has_been_given' => $is_consultation_has_been_given,
        'is_package_needed' => $is_package_needed,
        'other' => $other,
        'when_created' => date('Y-m-d H:i:s'),
        //'is_done' => $is_done,
    ];

    if(performQuery($query, $params)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Edits contact with given id
 *
 * @param $id
 * @param $phone
 * @param $firstName
 * @param $lastName
 * @return bool
 */
function editAppeal($id, $full_name, $address, $social_category, $phone, $appeal_text,
                    $is_consultation_has_been_given, $is_package_needed, $other)
{
    $query = 'UPDATE `appeal` SET `full_name` = :full_name, `address` = :address, `social_category` = :social_category, 
                `phone` = :phone, `appeal_text` = :appeal_text, `is_consultation_has_been_given` = :is_consultation_has_been_given, 
                    `is_package_needed` = :is_package_needed, `other` = :other WHERE `id`= :id;';

    $params = [
        'id' => $id,
        'full_name' => $full_name,
        'address' => $address,
        'social_category' => $social_category,
        'phone' => $phone,
        'appeal_text' => $appeal_text,
        'is_consultation_has_been_given' => $is_consultation_has_been_given,
        'is_package_needed' => $is_package_needed,
        'other' => $other,
    ];

    //if(empty(contactErrorList($full_name))) {
        if(performQuery($query, $params)) {
            return true;
        } else {
            return false;
        }
    //}
}

/**
 * Removes contact with given id
 *
 * @param $id
 * @return bool
 */
function removeAppeal($id)
{
    $query = 'DELETE FROM `appeal` WHERE `id` = :id;';
    $params = ['id' => $id];

    if(performQuery($query, $params)) {
        return true;
    } else {
        return false;
    }
}

function markAppealAsDone($id) {
    $query = 'UPDATE `appeal` SET `is_done` = 1 WHERE `id` = :id;';
    $params = ['id' => $id];

    if(performQuery($query, $params)) {
        return true;
    } else {
        return false;
    }
}
