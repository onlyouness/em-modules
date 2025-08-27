<?php


if (!defined('_PS_VERSION_')) {

    exit;
}

class B2bUploadFiles extends Module

{

    public function __construct()

    {

        $this->name = 'b2buploadfiles';

        $this->tab = 'front_office_features';

        $this->version = '1.0.0';

        $this->author = 'Youness Major media';

        parent::__construct();

        $this->need_instance = 0;

        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];

        $this->bootstrap = true;

        $this->displayName = $this->l('B2B file upload', 'b2buploadfiles');

        $this->description = $this->l('Helps you save file of your new customer');
    }

    public function install()
    {
        return parent::install() && $this->installDb()  &&
            $this->registerHook('actionCustomerAccountAdd');
    }

    public function uninstall()
    {

        return $this->uninstallDb()  && parent::uninstall();
    }
    public function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'b2b_files` (
            `file_id` INT AUTO_INCREMENT PRIMARY KEY,
            `customer_id` INT ,
            `file_path` varchar(255),
            `label` varchar(255)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
        return Db::getInstance()->execute($sql);
    }
    public function uninstallDb()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'b2b_files`';
        return Db::getInstance()->execute($sql);
    }
    
    public function hookActionCustomerAccountAdd($params)
    {
        $this->writeToLogFile('$_POST: ' . print_r($_POST, true));
        $this->writeToLogFile('$_FILES: ' . print_r($_FILES, true));
        
        $this->writeToLogFile('hookActionCustomerAccountAdd triggered.');

        $customerId = $params['newCustomer']->id;
        if ($customerId) {
            $this->writeToLogFile('Customer created with ID: ' . $customerId);

            for ($i = 1; $i <= 3; $i++) {
                if (isset($_FILES['b2b_file_' . $i]) && !empty($_FILES['b2b_file_' . $i]['tmp_name'])) {
                    $file = $_FILES['b2b_file_' . $i];
                    $destination = _PS_UPLOAD_DIR_ . basename($file['name']);

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $this->writeToLogFile("File $i uploaded successfully: $destination");
                        $this->saveFileInfo($customerId, 'File ' . $i, $destination);
                    } else {
                        $this->writeToLogFile("File $i upload failed.");
                    }
                } else {
                    $this->writeToLogFile("File input custom_file_$i is missing or empty.");
                }
            }
        } else {
            $this->writeToLogFile('Customer ID is missing.');
        }
    }


    private function saveFileInfo($customerId, $label, $path)
    {
        $db = Db::getInstance();
        $db->insert('b2b_files', [
            'customer_id' => (int)$customerId,
            'label' => pSQL($label),
            'file_path' => pSQL($path),
        ]);
    }
    private function writeToLogFile($message)
    {
        // Define the log file path within your module directory
        $logFile = _PS_MODULE_DIR_ . $this->name . '/logs/debug.log';

        // Ensure the logs directory exists
        if (!file_exists(_PS_MODULE_DIR_ . $this->name . '/logs')) {
            mkdir(_PS_MODULE_DIR_ . $this->name . '/logs', 0755, true);
        }

        // Add a timestamp to each log entry
        $date = date('Y-m-d H:i:s');
        $logEntry = "[$date] $message" . PHP_EOL;

        // Append the log entry to the file
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
