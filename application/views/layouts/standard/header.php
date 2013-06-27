<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>title</title>
<? foreach ( $remoteCss as $style ) : ?>
       <link rel="stylesheet" type="text/css" href="<?= $style; ?>">
<? endforeach; ?>
<? foreach ( $css as $style ) : ?>
        <style type="text/css">
            <?= $style; ?>
	</style>
<? endforeach; ?>
<? foreach ( $remoteJsHeader as $script ) : ?>
        <script type="text/javascript" charset="utf-8" src="<?= $script; ?>"></script>
<? endforeach; ?>
<? foreach ( $jsHeader as $script ) : ?>
        <script type="text/javascript" charset="utf-8">
            <?= $script; ?>
        </script>
<? endforeach; ?>
</head>