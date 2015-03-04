    </div>
</div>

<footer>
    <div class="container">
        <p>
            &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>.
            <?php _e('由 <a href="http://www.typecho.org" target="_blank">Typecho</a> 强力驱动'); ?>
        </p>
		<p>
			沪ICP备15008606
		</p>
    </div>
</footer>

<?php $this->footer(); ?>
<script src="<?php $this->options->adminUrl('js/jquery.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('js/script.js'); ?>"></script>
    </body>
</html>
