# Advance image accordion

## Add below code lines to berg > inint.php

```sh
if(file_exists(plugin_dir_path(__FILE__) . 'src/block/advance-accordion/init.php'))
require_once plugin_dir_path(__FILE__) . 'src/block/advance-accordion/init.php';
```

### mixing file need to add plugins\berg\src\mixins.scss
