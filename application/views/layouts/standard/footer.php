<footer class="footer">
    <? if ( "development" === ENVIRONMENT ) { ?>Page rendered in <strong>{elapsed_time}</strong> seconds, using <strong>{memory_usage}</strong> memory.<? } ?>
</footer>
<? foreach ( $remoteJsFooter as $script ) : ?>
        <script type="text/javascript" charset="utf-8">
            <?= $script; ?>
        </script>
<? endforeach; ?>
<? foreach ( $jsFooter as $script ) : ?>
        <script type="text/javascript" charset="utf-8">
            <?= $script; ?>
        </script>
<? endforeach; ?>
</html>