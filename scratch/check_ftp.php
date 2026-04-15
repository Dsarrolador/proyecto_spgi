<?php
if (function_exists('ftp_connect')) {
    echo "FTP enabled\n";
} else {
    echo "FTP NOT enabled\n";
}
