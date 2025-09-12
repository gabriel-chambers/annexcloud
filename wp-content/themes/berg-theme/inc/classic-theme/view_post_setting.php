<fieldset>
    <legend class="screen-reader-text"><span>Classic theme</span></legend>
    <label>
        <input name="berg_classic_theme_enabled_on_post" type="radio" value="0"  <?php echo !$enabled ? 'checked=""' : '' ?> />
        <span class="format-i18n">Disable Classic Mode</span>
    </label>
    <br>
    <label>
        <input name="berg_classic_theme_enabled_on_post" type="radio" value="1"  <?php echo $enabled ? 'checked=""' : '' ?> />
        <span class="format-i18n">Enable Classic Mode</span>
    </label>
</fieldset>
