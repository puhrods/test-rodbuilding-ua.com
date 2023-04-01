<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link rel="stylesheet" type="text/css" href="view/javascript/ompro/bootstrap/bootstrap.min.css" />
<script src="view/javascript/ompro/AdminLTE/jquery.min.js"></script>
<script src="view/javascript/ompro/bootstrap/bootstrap.min.js"></script>
<link rel="stylesheet" href="view/javascript/ompro/AdminLTE/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="view/javascript/ompro/ompro.css" />
<style media="print" type="text/css">* {-webkit-print-color-adjust: exact;} .noprint {display: none;}</style>
</head>
<body>
<div class="container"><span class="noprint"><a class="print-button btn btn-danger" href="javascript:window.print(); void 0;" title="Распечатать"> Печать </a></span><?php echo $html; ?></div>
</body>
</html>