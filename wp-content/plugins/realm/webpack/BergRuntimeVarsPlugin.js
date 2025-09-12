const execSync = require('child_process').execSync;
const DefinePlugin = require('webpack').DefinePlugin;
const path = require('path');

class BergRuntimeVars {
	constructor({ configFilePath, prefix }) {
		this.configFilePath =
			configFilePath ||
			path.resolve(__dirname, '../../../../wp-config.php');
		this.prefix = prefix || 'BERG_RUNTIME_';
		this.getVarsFromFile = this.getVarsFromFile.bind(this);
	}

	apply(compiler) {
		const bergVariables = this.getVarsFromFile();
		new DefinePlugin(bergVariables).apply(compiler);
	}

	getVarsFromFile() {
		try {
			const stdout = execSync(`
                  php -r 'try {
                      $config_file = "${this.configFilePath}";
                      $prefix = "${this.prefix}";
                      if (!file_exists($config_file)) {
                        echo json_encode([]);
                        die;
                      }
                      include_once $config_file;
                      $vars = [];
                      $constants = get_defined_constants();
                      array_walk(
                          $constants,
                          function ($v, $k) use ($prefix, &$vars) {
                              if (substr($k, 0, strlen($prefix)) === $prefix) {
                                  $vars[$k] = $v;
                              }
                          }
                      );
                      echo json_encode($vars);
                  } catch (Exception $ex) {
                      //
                  }'
              `);
			const variablesObject = JSON.parse(stdout.toString());
			Object.keys(variablesObject).forEach((key) => {
				variablesObject[key] = JSON.stringify(variablesObject[key]);
			});
			return variablesObject;
		} catch (error) {
			console.error(error.output ? error.output.toString() : error);
			process.exit(1);
		}

		return {};
	}
}

module.exports = BergRuntimeVars;
