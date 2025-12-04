<?php


//Function to bind the parameters
function bind_params($stmt, $types, $params)
{
    if (empty($params)) {
        return true;
    }
    $params_count = count($params);
    if ($params_count == 1) {
        mysqli_stmt_bind_param($stmt, $types, $params[0]);
    } else if ($params_count == 2) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1]);
    } else if ($params_count == 3) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2]);
    } else if ($params_count == 4) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2], $params[3]);
    } else if ($params_count == 5) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2], $params[3], $params[4]);
    } else if ($params_count == 6) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
    } else if ($params_count == 7) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
    } else if ($params_count == 8) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7]);
    } else if ($params_count == 9) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8]);
    } else if ($params_count == 10) {
        mysqli_stmt_bind_param($stmt, $types, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9]);
    }
    return true;
}

//Function to retrieve only one row from database
function get_one_row($db_server, $query, $param_value = array(), $param_type = "")
{
    $stmt = mysqli_prepare($db_server, $query);
    if (!$stmt) {
        return null;
    }
    if (!empty($param_value) && !empty($param_type)) {
        bind_params($stmt, $param_type, $param_value);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row;
}

//Function to retrieve all the rows from database
function get_all_rows($db_server, $query, $param_value = array(), $param_type = "")
{
    //If no parameters
    if (empty($param_type)) {
        $result = mysqli_query($db_server, $query);
        if (!$result) {
            return [];
        }
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    //With parameters
    $stmt = mysqli_prepare($db_server, $query);
    if (!$stmt) {
        return [];
    }
    bind_params($stmt, $param_type, $param_value);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);


    if (!$result) {
        mysqli_stmt_close($stmt);
        return [];
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}

//Funtion to insert/update/ delete
function execute_query($db_server, $query, $params, $types)
{
    $stmt = mysqli_prepare($db_server, $query);
    if (!$stmt) {
        return false;
    }

    if (!empty($params) && !empty($types)) {
        bind_params($stmt, $types, $params);
    }

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}
