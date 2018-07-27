<?php
session_start();
function GetPasswordHash($login,$password) {
    return hash("sha256",strtolower($login) . $password, false);
}
	