<?php
    if ($logged_user != null) {
        echo $logged_user->getPlayer()->credits;
    } else {
        echo "Va te connecter";
    }