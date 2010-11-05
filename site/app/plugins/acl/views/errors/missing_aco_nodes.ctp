<?php
echo '<div class="error_page_message">';

echo '	<span class="error">' . $message . '</span>';
echo '	<p>&nbsp;</p>';
echo '	<p>&nbsp;</p>';
echo '	<p>' . $html->link(___('go to homepage', true), '/') . '</p>';

echo '</div>';
?>