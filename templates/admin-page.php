<?php
if (!defined('ABSPATH')) exit;
?>

<div id="wcbn">
    <div id="wcbn-container">
      	<h3>WC Browser Notification</h3>
      	<form method="post">
      		<label>*Select any one option from below</label>
      		<br />
      		<select type="select" id="browser_cats" name="browser_cats[]">
      			<option value="">--None--</option>
      			<option value="close">On closing the browser tab </option>
      			<option value="open">On page load </option>
      			<option value="scroll">On scrolling through</option>
      		</select>

      		<select type="select" id="wc_cats" name="wc_cats[]">
      			<option value="">--None--</option>
      			<option value="woocommerce_simple_add_to_cart">woocommerce_simple_add_to_cart</option>
      		</select>
      		<br /><br />
      		<input type="submit" name="submit" class="button button-primary button-large"/>
      	</form>
    </div>
</div>