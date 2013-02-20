<?php
	$body_content = ob_get_contents();
	ob_end_clean();
	ob_start();	// footer
?>
			<!-- </div> --> <!-- possible extra div -->
			<div class="clearer"></div>	
		</div>
		</div>
		<div class="clearer"></div>	
		<div id="dialog"></div>
		<footer>
		<?php include 'widget_navFoot.php'; ?>
		</footer>	
	</div>
	</body>
</html>

<?php
	$footer_content = ob_get_contents();
	ob_end_clean();
	$html = $header->spill() . $header_content . $body_content . $footer_content;
	echo $html;
	apache_note('PhpMemUsage', memory_get_peak_usage());
?>