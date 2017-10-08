<div class="ffi-opacity"></div>
<div class="ffi-deactivate-popup">
    <div class="ffi-deactivate-popup-opacity">
        <img src="<?php echo $this->context['plugin_url'] . $this->context['slug'] . '/assets/spinner.gif'; ?>"
             class="ffi-img-loader">
    </div>
    <div class="ffi-deactivate-popup-header">
        <?php _e("Please kindly let us know why you are deactivating. Your answer will help us to serve you better", $this->context['slug']); ?>:
    </div>
    <div class="ffi-deactivate-popup-body">
        <?php foreach ($deactivate_reasons as $deactivate_reason_slug => $deactivate_reason) { ?>
            <div class="ffi-reasons">
                <input type="radio" value="<?php echo $deactivate_reason["id"]; ?>"
                       id="ffi-<?php echo $deactivate_reason["id"]; ?>" name="ffi_reasons">
                <label for="ffi-<?php echo $deactivate_reason["id"]; ?>"><?php echo $deactivate_reason["text"]; ?></label>
            </div>
        <?php } ?>
        <div class="ffi-additional-details-wrap"></div>
    </div>
    <div class="ffi-btns">
        <a href="<?php echo $deactivate_url; ?>" data-val="1"
           class="button button-secondary button-close ffi-deactivate"
           id="ffi-deactivate"><?php _e("Deactivate", $this->context['slug']); ?></a>
        <a href="<?php echo $deactivate_url; ?>" data-val="2"
           class="button button-secondary button-close ffi-deactivate" id="ffi-submit-and-deactivate"
           style="display:none;"><?php _e("Submit and deactivate", $this->context['slug']); ?></a>
        <a href="<?php echo admin_url('plugins.php'); ?>"
           class="button button-primary ffi-cancel"><?php _e("Cancel", $this->context['slug']); ?></a>
    </div>

</div>
