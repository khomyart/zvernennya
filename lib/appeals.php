<?php

/**
 * Returns list\array of contacts
 *
 * @param $filter
 * @return array
 */
function getListOfAppeals(&$number_of_results, $results_per_page, $filter = '')
{
    $query = 'SELECT * FROM `appeal` WHERE 1';

    if (is_string($filter) && (trim($filter) != '')) {
        $query .= ' AND ( (`full_name` LIKE :filter) OR (`address` LIKE :filter) OR (`social_category` LIKE :filter) OR (`phone` LIKE :filter) OR (`appeal_text` LIKE :filter) OR (`other` LIKE :filter) )';
        $params['filter'] = '%' . $filter . '%';
    }

    $appeals = getAllRows($query, $params);

    // define how many results you want per page

    // find out the number of results stored in database
    $number_of_results = count($appeals);

    // determine number of total pages available

    // determine which page number visitor is currently on
    if (!isset($_GET['page'])) {
        $_SESSION['number_of_page_to_replace_get_request'] = 1;
        $page = 1;
    } else {
        $page = $_GET['page'];
        $_SESSION['number_of_page_to_replace_get_request'] = $_GET['page'];
    }

    // determine the sql LIMIT starting number for the results on the displaying page
    $this_page_first_result = ($page-1)*$results_per_page;

    // retrieve selected results from database and display them on page
    $final_query = "SELECT * FROM `appeal` WHERE 1";

    if (is_string($filter) && (trim($filter) != '')) {
        $final_query .= ' AND ( (`full_name` LIKE :filter) OR (`address` LIKE :filter) OR (`social_category` LIKE :filter) OR (`phone` LIKE :filter) OR (`appeal_text` LIKE :filter) OR (`other` LIKE :filter) )';
        $params['filter'] = '%' . $filter . '%';
    }

    $final_query .= ' ORDER BY `id` DESC LIMIT ' . $this_page_first_result . ',' . $results_per_page ;

    return getAllRows($final_query, $params);
}

/**
 * Returns join list\array of contacts
 *
 * @param $filter
 * @return array
 */
function getJoinListOfContacts($filter = '')
{
    $query = 'SELECT c.id, c.phone, c.first_name, c.last_name, u.email, u.nickname FROM `contact` AS c LEFT JOIN `user` AS u ON c.user_id = u.id WHERE 1';

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
 * Creates a new contact
 *
 * @param $firstName
 * @param $lastName
 * @param $phone
 * @return bool
 */
function addAppeal($userId, $full_name, $address, $social_category, $phone, $appeal_text, $is_consultation_has_been_given, $is_package_needed, $other)
{
    $query = 'INSERT INTO `appeal`(`user_id`, `full_name`, `address`, `social_category`, `phone`, `appeal_text`, `is_consultation_has_been_given`, `is_package_needed`, `other`) 
                VALUES (:userId, :full_name, :address, :social_category, :phone, :appeal_text, :is_consultation_has_been_given, :is_package_needed, :other);';
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
function editAppeal($id, $full_name, $address, $social_category, $phone, $appeal_text, $is_consultation_has_been_given, $is_package_needed, $other)
{
    $query = 'UPDATE `appeal` SET `full_name` = :full_name, `address` = :address, `social_category` = :social_category, `phone` = :phone, `appeal_text` = :appeal_text, `is_consultation_has_been_given` = :is_consultation_has_been_given, `is_package_needed` = :is_package_needed, `other` = :other WHERE `id`= :id;';

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
