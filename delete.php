<?php
if(isset($_GET['file'])) unlink("uploads/" . basename($_GET['file']));
?>