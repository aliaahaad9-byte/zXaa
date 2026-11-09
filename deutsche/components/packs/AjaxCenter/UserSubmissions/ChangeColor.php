<?php
header("Content-Type: application/json");
global $current_user;
//
update_user_meta($current_user->ID, "color", $_POST['color']);
echo json_encode(GetAvatar($current_user->ID));
die();