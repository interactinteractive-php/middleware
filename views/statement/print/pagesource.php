<html>
<head>
<meta charset="utf-8" />
<title><?php echo $this->title; ?></title>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<base href="<?php echo URL; ?>">
<style type="text/css">
    <?php echo $this->style; ?>
    /*body {
        padding-top: <?php echo checkDefaultVal($this->paddingTop, '20px'); ?>;
        padding-left: <?php echo checkDefaultVal($this->paddingLeft, '20px'); ?>;
        padding-right: <?php echo checkDefaultVal($this->paddingRight, '20px'); ?>;
        padding-bottom: <?php echo checkDefaultVal($this->paddingBottom, '20px'); ?>;
    }*/
    body {
        padding-top: 20px!important;
        padding-left: 20px!important;
        padding-right: 20px!important;
        padding-bottom: 20px!important;
    }
</style>
</head>
<body>
    <?php echo $this->contentHtml; ?>
</body>
</html>