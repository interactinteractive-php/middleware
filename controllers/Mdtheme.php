<?php

if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

/**
 * Mdtheme Class 
 * 
 * @package     IA PHPframework
 * @subpackage	Middleware
 * @category	Theme
 * @author	S.Satjan <satjan@interactive.mn>
 * @link	http://www.interactive.mn/PHPframework/Middleware/Mdtheme
 */
class Mdtheme extends Controller {

    public static $viewPath = "middleware/views/theme/";

    public function __construct() {
        parent::__construct();
        Auth::handleLogin();
    }

    public function getMetaThemeHtml() {
        $this->view->metaThemeId = Input::post('metaThemeId');

        $this->view->metaThemeList = $this->model->getMetaThemeListModel($this->view->metaThemeId);

        $params = array();

        if ($this->view->metaThemeList) {
            $themePath = BASEPATH . '/assets/core/frontend/layout/theme/' . $this->view->metaThemeList[0]['FILE_NAME'];
            if (file_exists($themePath)) {
                $params['htmlContent'] = file_get_contents($themePath);
                $params['htmlContent'] = str_replace('[uniqId]', getUID(), $params['htmlContent']);
            }
        }

        echo json_encode($params);
    }

    public function getThemeStyleContent() {
        $this->view->fileName = Input::post('fileName');

        $params = array();

        $themePath = BASEPATH . '/middleware/assets/theme/layout/process/style/' . $this->view->fileName;
        if (file_exists($themePath)) {
            $params['htmlContent'] = file_get_contents($themePath);
        }

        echo json_encode($params);
    }

    public function checkFilePrevExist() {
        $fileprev = Input::post('fileprev');
        $themePath = BASEPATH . $fileprev;
        if (file_exists($themePath)) {
            echo json_encode(array('true'));
        } else {
            echo json_encode(array('false'));
        }
    }

}
