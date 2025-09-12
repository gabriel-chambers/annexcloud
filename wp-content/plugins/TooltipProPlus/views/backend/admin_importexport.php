<p class="clear"></p>
<br/>
<div>
	<h3>Backup Glossary Terms to File</h3>

	<?php if ( $showBackupDownloadLink ) : ?>
		<a href="<?php echo esc_url( $showBackupDownloadLink ); ?>" class="button">Download Backup</a>
		<p>
			<strong>URL to the backup file:</strong>
			<input type="text" readonly="readonly" size="90" value="<?php echo esc_url( $showBackupDownloadLink ); ?>"/>
		</p>
	<?php endif; ?>

	<form method="post">
		<?php wp_nonce_field( 'cmtt_do_backup' ); ?>
		<div>
			<label>
				<span>Items to process in one go. You can reduce it if you're experiencing errors.</span>
				<input type="number" min="1" max="9999" name="cmtt_process_chunk_size" value="1000"/>
			</label>
		</div>
		<div>
			<input type="submit" value="<?php echo $backup_button_label; ?>" name="cmtt_doBackup"
			       class="button button-primary"/>
		</div>
	</form>
</div>

<br/><br/>
<div>
	<h3>Export Glossary Terms</h3>
	<form method="post" id="cmtt-export-glossary">
		<?php wp_nonce_field( 'cmtt_export', 'cmtt_nonce' ); ?>
		<div>
			<label>
				<span>Items to process in one go. You can reduce it if you're experiencing errors.</span>
				<input type="number" min="1" max="9999" name="cmtt_process_chunk_size" value="1000"/>
			</label>
		</div>
		<input type="submit" value="Export to CSV" name="cmtt_doExport" class="button button-primary"/>
        <div class="export-loader-bar" style="border-radius:8px;background-color:#fff!important; display:none;  margin: 0 12px; min-width: 50%;">
            <div id="loaderBar" class="loader-bar-inner" style="width:0%;border-radius:8px; padding:0.4em ;color:#fff!important;background-color:#2196F3!important">0%</div>
        </div>
	</form>

</div>
<br/><br/>

<div>
	<h3>Import Glossary Terms from File</h3>
	<p>
		If the term already exists in the database, only content is updated. Otherwise, new term is added.
	</p>

	<div>
		<strong>Important!!</strong>
		<ul style="list-style: circle; margin-left: 2em">
			<li>File should be UTF-8 encoded</li>
			<li>If you use MS Excel, please remember that by default it can't save proper CSV format (comma-delimited) -
				see <a href="http://support.microsoft.com/kb/291296" target="_blank" rel="nofollow">Microsoft Knowledge
					Base Article</a></li>
			<li>All the fields which can contain commas, must be enclosed in quotes! (to be 100% safe enclose each field
				in quotes)
			</li>
			<li>The only two mandatory fields are Title and Description</li>
			<li>Minimal row: <code>"","Title","","Description"</code></li>
		</ul>
	</div>

    <form id="cmtt-import-glossary" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'cmtt_import', 'cmtt_nonce' ); ?>
        <label>
            <span>Items to process in one go. You can reduce it if you're experiencing errors.</span>
            <input type="number" min="1" max="9999" name="import_chunk_size" value="100" />
        </label><br>
        <input type="file" name="importCSV"/>
		<input type="submit" value="Import from CSV" name="cmtt_doImport" class="button button-primary"/>
        <div class="import-loader-bar" style="border-radius:8px;background-color:#fff!important; display:none;  margin: 0 12px; min-width: 50%;">
            <div id="loaderBar" class="loader-bar-inner" style="width:0%;border-radius:8px; padding:0.4em ;color:#fff!important;background-color:#2196F3!important">0%</div>
        </div>
	</form>
	<br/>
	Format example:<br/>
	<pre>
Id,Title,Excerpt,Description,Synonyms,Variations,Categories,Abbreviation,Tags,Image,Languages,_cmtt_exclude_tooltip,_cmtt_exclude_parsing
100,"Example Term","Example term excerpt text","Description, if multiline then uses&lt;br&gt;HTML element","synonym1,synonym2","variation1,variation2","categoryID1,categoryID2","abbreviationID1","tagID1,tagID2","url_of_image",Languages,0,0
101,"Another",,"Excerpt can be empty",,
    </pre>
	<p>
		Download the example file prepared by CreativeMinds: <a
			href="<?php echo CMTT_PLUGIN_URL ?>assets/cm_tooltip_glossary_import.csv">cm_tooltip_glossary_import.csv</a>
	</p>
	<p>
		More information about import/export you can find in our
		<a href="https://creativeminds.helpscoutdocs.com/article/157-cmtg-extras-importing-and-exporting-terms"
		   target="_blank">documentation</a>.
	</p>
</div>

<br/><br/>
<div>
	<h3>Export Glossary Settings</h3>
	<form method="post">
		<?php wp_nonce_field( 'cmtt_export_settings', 'cmtt_nonce' ); ?>
		<input type="submit" value="Export to CSV" name="cmtt_doExportSettings" class="button button-primary"/>
	</form>
</div>
<div>
	<h3>Import Glossary Settings</h3>
	<?php
	$settingsMsg      = filter_input( INPUT_GET, 'settingsMsg' );
	$settingstotal    = filter_input( INPUT_GET, 'settingstotal' );
	$settingsImported = filter_input( INPUT_GET, 'settingsImported' );
	$importres        = $settingstotal === $settingsImported ? 'succesfully imported' : 'import failed';
	$settings_error   = filter_input( INPUT_GET, 'settings_error' );
	$class            = empty( $settings_error ) ? 'updated' : 'error';
	if ( 'imported' == $settingsMsg ):
		?>
		<div id="message" class="<?php echo esc_attr( $class ); ?> below-h2">
			<?php
			echo sprintf( 'Imported %d/%d items read from file.', $settingsImported, $settingstotal );
			?>
		</div>
	<?php endif; ?>

	<?php
	if ( ! empty( $settings_error ) ):
		?>
		<div id="message" class="<?php echo esc_attr( $class ); ?> below-h2">
			<?php
			if ( $settings_error ) {
				echo 'Error during import.';
			}
			?>
		</div>
	<?php endif; ?>
	<form method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'cmtt_import_settings', 'cmtt_nonce' ); ?>
		<input type="file" name="importCSV"/>
		<input type="submit" value="Import settings" name="cmtt_doImportSettings" class="button button-primary"/>
	</form>
</div>
<br/><br/>