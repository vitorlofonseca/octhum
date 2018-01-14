<?php

/** tbl_intelligence_category */
define('ID_INTELLIGENCE_CATEGORY_CLASSIFICATION', 1);
define('ID_INTELLIGENCE_CATEGORY_PREVISION', 2);
define('ID_INTELLIGENCE_CATEGORY_CLUSTERING', 3);

/** tbl_log_type */
define('ID_LOG_TYPE_CREATION', 1);
define('ID_LOG_TYPE_USE', 2);
define('ID_LOG_TYPE_MODIFICATION', 3);

/** tbl_intelligence_file_type */
define('ID_INTELLIGENCE_TYPE_FILE_SHEET', 1);
define('ID_INTELLIGENCE_TYPE_FILE_IMAGE', 2);
define('ID_INTELLIGENCE_TYPE_FILE_SOUND', 3);

/** file system paths */
define('FILES_FOLDER', '/var/www/html/octhum/files/');
define('MODULES_FOLDER', '/var/www/html/octhum/modules/');
define('SHEETS_TO_TRANING_FOLDER', FILES_FOLDER.'sheets/');
define('DATA_TO_TRANING_FOLDER', FILES_FOLDER.'data/');
define('NETS_FOLDER', FILES_FOLDER.'nets/');
define('EXEC_MLP', MODULES_FOLDER.'octhum.mlp/octhum.mlp');

/** return error codes of mlp 1+CODE_ERROR*/
define('MLP_SUCCESSFUL_CREATED', 1);
define('MLP_NON_EXISTENT_PATH_FILE', 1001);