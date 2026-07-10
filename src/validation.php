<?php

function validatePassword(string $password): ?string
{
    if(strlen($password) < 12) {
        return "Password must be at least 12 characters.";
    }

    if(!preg_match('/[A-Z]/', $password)) {
        return "Password must contain an uppercase letter.";
    }

    if(!preg_match('/[a-z]/', $password)) {
        return "Password must contain a lowercase letter.";
    }

    if(!preg_match('/[0-9]/', $password)) {
        return "Password must contain a number.";
    }

    if(!preg_match('/[^A-Za-z0-9]/', $password)) {
        return "Password must contain a special character.";
    }

    return null;
}