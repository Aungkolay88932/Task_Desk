<?php
session_start();
session_destroy();
header("Location: /Task_Desk/frontend/register.html");
