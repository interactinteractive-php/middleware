<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

class Mdtheme_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getMetaThemeListModel($id = null) {
        $qr = "
            SELECT 
                ID,
                NAME,
                FILE_NAME
            FROM META_THEME MT ";

        if (!is_null($id)) {
            $qr.= "WHERE MT.ID = " . $id;
        }

        $list = $this->db->GetAll($qr);

        return $list;
    }

    public function getInputParamList($processMetaDataId, $parentId = '') {
        
        $html = '';
        
        if ($parentId == '') {
            $where = 'AND PAL.PARENT_ID IS NULL';
        } else {
            $where = 'AND PAL.PARENT_ID = '.$parentId;
        }

        $data = $this->db->GetAll("
            SELECT 
                PAL.ID,  
                PAL.LABEL_NAME, 
                PAL.PARAM_REAL_PATH,
                PAL.PARAM_NAME,
                PAL.DATA_TYPE, 
                PAL.RECORD_TYPE 
            FROM META_PROCESS_PARAM_ATTR_LINK PAL                    
            WHERE PAL.PROCESS_META_DATA_ID = $processMetaDataId 
                AND PAL.IS_SHOW = 1 
                AND PAL.IS_INPUT = 1 
                $where 
            ORDER BY PAL.ORDER_NUMBER ASC");

        if ($data) {
            
            foreach ($data as $value) {
                
                if ($value['DATA_TYPE'] == 'group') {
                    
                    $html .= '<li data-metacode="' . $value['PARAM_NAME'] . '" data-groupname="' . $this->lang->line($value['LABEL_NAME']) . '" '
                            . 'class="pl10 dropdown draggableMeta isStyle isGroup"><span data-toggle="dropdown"><i class="fa fa-caret-right"></i>'
                            . '<span rel="tooltip" data-placement="top" data-original-title="' . $value['PARAM_NAME'] . '">' . $this->lang->line($value['LABEL_NAME']) . '</span></span>';
                    
                    $html .= '<ul id="metas" class="dropdown-menu" style="width: 100%;">';

                    $html .= self::getInputParamList($processMetaDataId, $value['ID']);
                    $html .= '</ul>';
                    
                } else {
                    
                    $html .= '<li data-realpath="' . $value['PARAM_REAL_PATH'] . '" data-metacode="' . $value['PARAM_NAME'] . '" '
                            . 'class="pl10 draggableMeta isMeta' . (($value['RECORD_TYPE'] == 'rows') ? ' isGroupChild' : '') . '" style="' . ($value['PARAM_NAME'] != '' ? 'padding-left: 40px !important;' : '') . '">'
                            . '<span rel="tooltip" data-placement="top" data-original-title="' . $value['PARAM_NAME'] . '">' . $this->lang->line($value['LABEL_NAME']) . '</span>';
                }

                $html .= '</li>';
            }
        }

        return $html;
    }

    public function generateStyleListModel() {
        $html = '';

        $data = $this->db->GetAll("
            SELECT
                ID,
                CODE,
                NAME,
                FILE_NAME,
                DESCRIPTION
            FROM META_THEME_STYLE 
            ORDER BY CODE ASC");

        if ($data) {
            foreach ($data as $value) {
                $html.='<div class="col-md-6 col-sm-12 col-xs-12 style-draggable-parent">';
                $html.='<div class="single-style draggableMeta isStyle" data-styleid="' . $value['ID'] . '" data-filename="' . $value['FILE_NAME'] . '">';
                $html.='<span class="style-title" title="' . $value['NAME'] . '">' . $value['NAME'] . '</span>';
                $styleThumbPath = 'middleware/assets/theme/layout/process/style/thumb/';
                $html.='<img class="img-fluid style-thumb' . (file_exists(BASEPATH . '/' . $styleThumbPath . $value['CODE'] . '.PNG')
                                    ? ' isLoadImg' : '') . '" src="' . $styleThumbPath . (file_exists(BASEPATH . $styleThumbPath . $value['CODE'] . '.PNG')
                                    ? $value['CODE'] : 'default') . '.PNG"/>';
                $html.='<span class="style-description" title="' . $value['DESCRIPTION'] . '">' . $value['DESCRIPTION'] . '</span>';
                $html.='</div>';
                $html.='</div>';
            }
        }

        return $html;
    }

    public function getThemeLinkDataModel($metaDataId) {
        $qr = "
            SELECT
                mtl.ID,
                mt.FILE_NAME,
                mt.ID AS THEME_ID,
                mtl.IS_MULTI_LANG
            FROM META_THEME_LINK mtl
                INNER JOIN META_DATA md ON mtl.META_DATA_ID = md.META_DATA_ID
                INNER JOIN META_THEME mt ON mtl.THEME_ID = mt.ID
            WHERE mtl.META_DATA_ID = ".$this->db->Param(0)." 
                AND mtl.IS_ACTIVE = 1";

        $result = $this->db->getRow($qr, array($metaDataId));
        
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function getThemeSectionDetailDataModel($themeLinkId) {
        $data = $this->db->GetAll("
            SELECT
                MTLS.ID, 
                MTLSD.POSITION_NAME,
                MTLSD.PARAM_PATH,
                MTLSD.PARAM_REAL_PATH
            FROM META_THEME_LINK_SECTION_DETAIL MTLSD 
                INNER JOIN META_THEME_LINK_SECTION MTLS ON MTLSD.THEME_SECTION_ID = MTLS.ID 
                INNER JOIN META_THEME_LINK MTL ON MTLS.META_THEME_LINK_ID = MTL.ID 
            WHERE MTL.ID = " . $this->db->Param(0), 
            array($themeLinkId) 
        );

        return $data;
    }

}
