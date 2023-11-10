<?php if (!defined('_VALID_PHP')) exit('Direct access to this location is not allowed.');

class Mdupgrade_Model extends Model {
    
    private static $exportIgnoreColumns = array('CREATED_USER_ID', 'MODIFIED_USER_ID', 'EXPORT_SCRIPT', 'COPY_COUNT');
    private static $exportIgnoreTableColumns = array('META_PROCESS_RULE' => array('IS_ACTIVE' => 1));
    private static $ignoreDeleteScriptTables = array('UM_SYSTEM', 'META_PROCESS_RULE');
    private static $executedTables = array();
    private static $executedTablesPrimaryColumn = array();
    private static $exportedMetaIds = array();
    private static $executedFolderIds = array();
    private static $scriptFolderIds = array();
    private static $deleteIds = array();
    private static $clobColumns = array();
    private static $blobColumns = array();
    private static $fileColumn = array();
    private static $fileColumns = array();
    private static $previousTranslateList = array();
    private static $replaceIds = array();
    private static $updateMetaIds = array();
    private static $exportedRecordIds = array();
    private static $childCreateTable = array();
    private static $exportCreateTables = array();
    private static $metaFolderId = null;
    private static $insertDataFilter = null;
    private static $isIgnoreMetaFolder = false;
    private static $isIgnoreTranslate = false;
    private static $isIdReplace = false;
    private static $isPreviewUpdateMeta = false;
    private static $ignoreDeleteScript = false;
    private static $ignoreDbCommitTrans = false;
    private static $isMetaImportCopy = false;
    private static $isCreateTable = false;
    private static $isInsertData = true;
    private static $isCreateRollback = false;

    public function __construct() {
        parent::__construct();
    }
    
    private function metaTableRelation() {
        
        return array(
            
            Mdmetadata::$reportTemplateMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_REPORT_TEMPLATE_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'file' => array(
                            'HTML_FILE_PATH' => 'contentFile'
                        )
                    ), 
                    array(
                        'table' => 'META_SRC_TRG_PARAM', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'TRG_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_REPORT_TEMPLATE_GROUP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_REPORT_TEMPLATE_QRCODE', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'RT_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_TEMPLATE', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'TEMPLATE_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DM_TEMPLATE_DTL', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'TEMPLATE_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$menuMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_MENU_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'file' => array(
                            'PHOTO_NAME' => 'imageFile'
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$statementMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_STATEMENT_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_STATEMENT_LINK_GROUP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_STATEMENT_TEMPLATE', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DM_DRILLDOWN_DTL', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'STATEMENT_META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_DM_DRILLDOWN_PARAM', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'DM_DRILLDOWN_DTL_ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_TAG_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_TAG', 
                                'link' => array(
                                    array(
                                        'src' => 'TAG_ID', 
                                        'trg' => 'ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'RP_REPORT_LAYOUT', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'REPORT_LAYOUT_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$metaGroupMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_GROUP_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_GROUP_SUB_QUERY', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'META_GROUP_LINK_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_DM_STATEMENT_DTL', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'META_GROUP_LINK_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_DM_DRILLDOWN_DTL', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'MAIN_GROUP_LINK_ID'
                                    )
                                ), 
                                'child' => array(
                                    array(
                                        'table' => 'META_DM_DRILLDOWN_PARAM', 
                                        'link' => array(
                                            array(
                                                'src' => 'ID', 
                                                'trg' => 'DM_DRILLDOWN_DTL_ID'
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_GROUP_CONFIG', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_GROUP_GRID_OPTIONS', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_GROUP_GRID_LAYOUT', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_GROUP_GRID_LAYOUT_DTL', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'HEADER_ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_GROUP_RELATION', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_GROUP_PARAM_CONFIG', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'GROUP_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PARAM_VALUES', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DM_PROCESS_DTL', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DM_TRANSFER_PROCESS', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DM_PROCESS_BATCH', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_LOOKUP_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DM_ROW_PROCESS_PARAM', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DM_DM_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID' 
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$businessProcessMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_BUSINESS_PROCESS_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_BP_EXPRESSION_DTL', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'BP_LINK_ID'
                                    )
                                ), 
                                'child' => array(
                                    array(
                                        'table' => 'META_BP_EXP_CACHE_VERSION', 
                                        'link' => array(
                                            array(
                                                'src' => 'ID', 
                                                'trg' => 'VERSION_ID'
                                            )
                                        )
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_BP_EXP_CACHE', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'BP_LINK_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_PROCESS_TEMPLATE', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'PROCESS_LINK_ID'
                                    )
                                )
                            ), 
                            /*array(
                                'table' => 'META_PROCESS_NTF', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'BUSINESS_PROCESS_LINK_ID'
                                    )
                                ),
                                'child' => array(
                                    array(
                                        'table' => 'META_PROCESS_NTF_PARAM', 
                                        'link' => array(
                                            array(
                                                'src' => 'ID', 
                                                'trg' => 'PROCESS_NTF_ID'
                                            )
                                        )
                                    )
                                )
                            )*/
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_DEFAULT_GET', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'PROCESS_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_BP_EXPRESSION_PROCESS', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'PROCESS_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PARAM_VALUES', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_GROUP_PARAM_CONFIG', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_PROCESS_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_LOOKUP_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'PROCESS_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_PARAM_ATTR_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'PROCESS_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_BUSINESS_PROCESS_TEMPLATE', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'file' => array(
                            'HTML_FILE_PATH' => 'contentFile'
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_WORKFLOW', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_BP_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_WF_BEHAVIOUR', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_BP_ID'
                            )
                        )
                    ),
                    array(
                        'table' => 'META_PROCESS_PARAM_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_BP_ID'
                            )
                        )
                    ),
                    array(
                        'table' => 'META_PROCESS_RULE', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_PROCESS_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_PROCESS_PARAM_LINK', 
                                'link' => array(
                                    array(
                                        'src' => 'MAIN_PROCESS_ID', 
                                        'trg' => 'DONE_BP_ID'
                                    ), 
                                    array(
                                        'src' => 'RULE_PROCESS_ID', 
                                        'trg' => 'DO_BP_ID'
                                    )
                                )
                            )
                        )
                    ),
                    array(
                        'table' => 'META_BP_LAYOUT_HDR', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_BP_LAYOUT_SECTION', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'HEADER_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_BP_LAYOUT_PARAM', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'HEADER_ID'
                                    )
                                )
                            )
                        )
                    ),
                    array(
                        'table' => 'META_DATA_SEQUENCE_CONFIG', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$taskFlowMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_BUSINESS_PROCESS_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_PARAM_ATTR_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'PROCESS_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_WORKFLOW', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_BP_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_PROCESS_WF_BEHAVIOUR', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_BP_ID'
                            )
                        )
                    ),
                    array(
                        'table' => 'META_PROCESS_PARAM_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_BP_ID'
                            )
                        )
                    ),
                    array(
                        'table' => 'META_PROCESS_RULE', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_PROCESS_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_PROCESS_PARAM_LINK', 
                                'link' => array(
                                    array(
                                        'src' => 'MAIN_PROCESS_ID', 
                                        'trg' => 'DONE_BP_ID'
                                    ), 
                                    array(
                                        'src' => 'RULE_PROCESS_ID', 
                                        'trg' => 'DO_BP_ID'
                                    )
                                )
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$bookmarkMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_BOOKMARK_LINKS', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$dashboardMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_DASHBOARD_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$contentMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_CONTENT_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_CONTENT_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$googleMapMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_GOOGLE_MAP_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_GOOGLE_MAP_PARAM', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'GOOGLE_MAP_LINK_ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$calendarMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_CALENDAR_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$donutMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_DONUT', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$cardMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_CARD', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$diagramMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_DASHBOARD_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$packageMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_PACKAGE_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$workSpaceMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_WORKSPACE_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_WORKSPACE_MENU_CRITERIA', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'META_WORKSPACE_LINK_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_WORKSPACE_PARAM_MAP', 
                                'link' => array(
                                    array(
                                        'src' => 'META_DATA_ID', 
                                        'trg' => 'WORKSPACE_META_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_WORKSPACE_WIDGET_MAP', 
                                'link' => array(
                                    array(
                                        'src' => 'META_DATA_ID', 
                                        'trg' => 'WORKSPACE_META_ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$layoutMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_LAYOUT_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_LAYOUT_PARAM_MAP', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'META_LAYOUT_LINK_ID'
                                    )
                                ), 
                                'child' => array(
                                    array(
                                        'table' => 'META_LAYOUT_PARAM_CONFIG', 
                                        'link' => array(
                                            array(
                                                'src' => 'ID', 
                                                'trg' => 'LAYOUT_PARAM_MAP_ID'
                                            )
                                        )
                                    ) 
                                )
                            ) 
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$widgetMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_WIDGET_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_WIDGET_PARAM', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'WIDGET_LINK_ID'
                                    )
                                )
                            ) 
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$proxyMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_PROXY_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            
            Mdmetadata::$fieldMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_FIELD_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    )
                )
            ),

            Mdmetadata::$dmMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_DATAMART_LINK', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_DATAMART_COLUMN', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'META_DATAMART_LINK_ID'
                                    )
                                ),
                                'child' => array(
                                    array(
                                        'table' => 'META_DATAMART_COLUMN_CRITERIA', 
                                        'link' => array(
                                            array(
                                                'src' => 'ID', 
                                                'trg' => 'META_DATAMART_COLUMN_ID'
                                            )
                                        )
                                    )
                                )                                
                            ) 
                        )
                    ), 
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ),
                    array(
                        'table' => 'META_GROUP_RELATION', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'MAIN_META_DATA_ID'
                            )
                        )
                    )
                )
            ), 
            Mdmetadata::$pageMetaTypeId => array(
                'META_DATA' => array(
                    array(
                        'table' => 'META_DATA_FOLDER_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_META_MAP', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'SRC_META_DATA_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_BP_LAYOUT_HDR', 
                        'link' => array(
                            array(
                                'src' => 'META_DATA_ID', 
                                'trg' => 'META_DATA_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_BP_LAYOUT_SECTION', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'HEADER_ID'
                                    )
                                ), 
                                'child' => array(
                                    array(
                                        'table' => 'META_BP_LAYOUT_SECTION_DTL', 
                                        'link' => array(
                                            array(
                                                'src' => 'ID', 
                                                'trg' => 'SECTION_ID'
                                            )
                                        )
                                    )
                                )
                            ), 
                            array(
                                'table' => 'META_BP_LAYOUT_PARAM', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'HEADER_ID'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }
    
    private function objectTableRelation() {
        
        return array(

            'kpi' => array(
                'KPI_TEMPLATE' => array(
                    array(
                        'table' => 'KPI_TEMPLATE_DTL', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'TEMPLATE_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'KPI_TEMPLATE_DTL_FACT', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'TEMPLATE_DTL_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'KPI_INDICATOR', 
                                'link' => array(
                                    array(
                                        'src' => 'INDICATOR_ID', 
                                        'trg' => 'ID'
                                    )
                                ), 
                                'child' => array(
                                    array(
                                        'table' => 'KPI_DIMENSION', 
                                        'link' => array(
                                            array(
                                                'src' => 'DIMENSION_ID', 
                                                'trg' => 'ID'
                                            )
                                        )
                                    )
                                )
                            ) 
                        )
                    ), 
                    array(
                        'table' => 'KPI_TEMPLATE_FACT', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'TEMPLATE_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_TEMPLATE_MAP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'SRC_TEMPLATE_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_TEMPLATE_CRITERIA', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'TEMPLATE_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_TEMPLATE_DIMENSION', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'TEMPLATE_ID'
                            )
                        )
                    )
                )
            ), 
            
            'ntf' => array(
                'NTF_NOTIFICATION' => array(
                    array(
                        'table' => 'NTF_NOTIFICATION_ACTION', 
                        'link' => array(
                            array(
                                'src' => 'NOTIFICATION_ID', 
                                'trg' => 'NOTIFICATION_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'NTF_NOTIFICATION_TEMPLATE_MAP', 
                        'link' => array(
                            array(
                                'src' => 'NOTIFICATION_ID', 
                                'trg' => 'NOTIFICATION_ID'
                            )
                        )
                    )
                )
            ), 
            
            'testcase' => array(
                'TEST_CASE' => array(
                    array(
                        'table' => 'TEST_CASE_QUALITY', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'TEST_CASE_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'TEST_CASE_SCENARIO_MAP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'TEST_CASE_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'TEST_CASE_SCENARIO', 
                                'link' => array(
                                    array(
                                        'src' => 'SCENARIO_ID', 
                                        'trg' => 'ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'TEST_CASE_SYSTEM_MAP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'TEST_CASE_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'UM_SYSTEM', 
                                'link' => array(
                                    array(
                                        'src' => 'SYSTEM_ID', 
                                        'trg' => 'SYSTEM_ID'
                                    )
                                ), 
                                'child' => array(
                                    array(
                                        'table' => 'TEST_CASE_SYSTEM_USER_MAP', 
                                        'link' => array(
                                            array(
                                                'src' => 'SYSTEM_ID', 
                                                'trg' => 'SYSTEM_ID'
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            ), 
            
            'umobject' => array(
                'UM_OBJECT' => array(
                    array(
                        'table' => 'UM_OBJECT_CODE', 
                        'link' => array(
                            array(
                                'src' => 'OBJECT_ID', 
                                'trg' => 'OBJECT_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'UM_OBJECT_CODE_PARAM', 
                        'link' => array(
                            array(
                                'src' => 'OBJECT_ID', 
                                'trg' => 'OBJECT_ID'
                            )
                        )
                    )
                )
            ), 
            
            'bugfix' => array(
                'META_BUG_FIXING' => array(
                    array(
                        'table' => 'META_BUG_FIXING_DTL', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'META_BUG_FIXING_ID'
                            )
                        )
                    )
                )
            ), 
            
            'wfmfield' => array(
                'META_WFM_FIELD' => array(
                    array(
                        'table' => 'META_WFM_FIELD_DTL', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'WFM_FIELD_ID'
                            )
                        )
                    )
                )
            ), 
            
            'config' => array(
                'CONFIG' => array(
                    array(
                        'table' => 'CONFIG_VALUE', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'CONFIG_ID'
                            )
                        )
                    )
                )
            ), 
            
            'booktype' => array(
                'BOOK_TYPE' => array()
            ), 
            
            'kpiindicator' => array(
                'KPI_INDICATOR' => array(
                    array(
                        'table' => 'KPI_INDICATOR_CATEGORY', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'INDICATOR_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_INDICATOR_INDICATOR_MAP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID'
                            )
                        ), 
                        'childCreateTable' => array(
                            array(
                                'isCreateTable'   => true, 
                                'isInsertData'    => self::$isInsertData, 
                                'tableColumnName' => 'TEMPLATE_TABLE_NAME'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'KPI_INDICATOR_MAP_CRITERIA', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'SRC_INDICATOR_MAP_ID'
                                    )
                                )
                            ), 
                            array(
                                'table' => 'KPI_INDICATOR_MAP_CRITERIA', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'INDICATOR_MAP_ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_INDICATOR_INDICATOR_MAP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'SRC_INDICATOR_ID'
                            )
                        ),
                        'child' => array(
                            array(
                                'table' => 'KPI_INDICATOR_INDICATOR_MAP', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'SRC_INDICATOR_MAP_ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_INDICATOR_TYPE_MAP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'INDICATOR_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_DATAMODEL_MAP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'SRC_INDICATOR_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_DATAMODEL_MAP_KEY', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'KPI_DATAMODEL_MAP_KEY_DTL', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'DATAMODEL_MAP_KEY_ID'
                                    )
                                )
                            )
                        )
                    ), 
                    array(
                        'table' => 'KPI_DATAMODEL_CRITERIA', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID'
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_STATEMENT_LINK', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID' 
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_STATEMENT_LINK_GROUP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID' 
                            )
                        )
                    ),
                    array(
                        'table' => 'META_REPORT_TEMPLATE_LINK', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID' 
                            )
                        )
                    ), 
                    array(
                        'table' => 'META_REPORT_TEMPLATE_GROUP', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID' 
                            )
                        )
                    ),
                    array(
                        'table' => 'META_DM_DRILLDOWN_DTL', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'MAIN_INDICATOR_ID'
                            )
                        ), 
                        'child' => array(
                            array(
                                'table' => 'META_DM_DRILLDOWN_PARAM', 
                                'link' => array(
                                    array(
                                        'src' => 'ID', 
                                        'trg' => 'DM_DRILLDOWN_DTL_ID'
                                    )
                                )
                            )
                        )
                    )
                )
            ), 
            
            'kpitype' => array(
                'KPI_TYPE' => array()
            ), 
            
            'impexcel' => array(
                'IMP_EXCEL_TEMPLATE' => array(
                    array(
                        'table' => 'IMP_EXCEL_TEMPLATE_OPTIONS', 
                        'link' => array(
                            array(
                                'src' => 'ID', 
                                'trg' => 'EXCEL_TEMPLATE_ID'
                            )
                        )
                    )
                )
            ), 
            
            'metawidget' => array(
                'META_WIDGET' => array()
            ),
            
            'processrule' => array(
                'META_PROCESS_RULE' => array()
            ),
            
            'globedictionary' => array(
                'GLOBE_DICTIONARY' => array()
            ),
            
        );
    }
    
    public function tablePrimaryField($tableName) {
        
        $arr = array(
            'NTF_NOTIFICATION' => 'NOTIFICATION_ID', 
            'UM_OBJECT_CODE' => 'OBJECT_CODE_ID', 
            'UM_OBJECT' => 'OBJECT_ID', 
            'BOOK_TYPE' => 'BOOK_TYPE_ID'
        );
        
        return isset($arr[$tableName]) ? $arr[$tableName] : 'ID';
    }
    
    public function tableFileColumns($tableName) {
        
        $arr = array(
            'KPI_INDICATOR' => array(
                'PROFILE_PICTURE' => 'imageFile'
            ), 
            'KPI_INDICATOR_INDICATOR_MAP' => array(
                'DEFAULT_FILE' => 'contentFile'
            )
        );
        return issetParamArray($arr[$tableName]);
    }
    
    public function dynamicCreateTables($tableName) {
        
        $arr = array(
            'KPI_INDICATOR' => array(
                array(
                    'isCreateTable'   => self::$isCreateTable, 
                    'tableColumnName' => 'TABLE_NAME'
                )
            )
        );
        
        return issetParamArray($arr[$tableName]);
    }
    
    public function bugfixDatagridModel() {
        
        $page = Input::post('page', 1);
        $rows = Input::post('rows', 10);
        
        $param = array(
            'systemMetaGroupId' => '1498128719613',
            'showQuery' => 0,
            'paging' => array(
                'offset' => $page,
                'pageSize' => $rows
            )
        );
        
        if (Input::postCheck('sort') && Input::postCheck('order')) {
            
            $sortField = Input::post('sort');
            $sortOrder = Input::post('order');

            if (strpos($sortField, ',') === false) {
                
                $param['paging']['sortColumnNames'] = array(
                    $sortField => array(
                        'sortType' => $sortOrder
                    )
                );
                
            } else {
                
                $sortFieldArr = explode(',', $sortField);
                $sortOrderArr = explode(',', $sortOrder);
                
                foreach ($sortFieldArr as $sortK => $sortF) {
                    $sortColumnNames[$sortF] = array('sortType' => $sortOrderArr[$sortK]);
                }
                
                $param['paging']['sortColumnNames'] = $sortColumnNames;
            }
        }
        
        if (Input::postCheck('sortFields')) {
            
            parse_str(Input::post('sortFields'), $sortFields);
            
            if (count($sortFields) > 0) {
                
                foreach ($sortFields as $sortKey => $sortType) {
                    
                    $param['paging']['sortColumnNames'] = array(
                        $sortKey => array(
                            'sortType' => $sortType
                        )
                    );
                }
            }
        }
        
        if (Input::postCheck('filterRules')) {
            
            $filterRules = json_decode(Str::cp1251_utf8($_POST['filterRules']), true);

            if (count($filterRules) > 0) {

                foreach ($filterRules as $rule) {

                    $field = $rule['field'];
                    $value = Input::param($rule['value']);

                    $operatorFilter = 'LIKE';

                    $paramFilter[$field][] = array(
                        'operator' => $operatorFilter,
                        'operand'  => $operatorFilter === 'LIKE' ? '%'.$value.'%' : $value
                    );
                }

                if (isset($param['criteria'])) {
                    $param['criteria'] = array_merge($param['criteria'], $paramFilter);
                } else {
                    $param['criteria'] = $paramFilter;
                }
            }
        }
        
        if (Input::postCheck('defaultCriteriaData')) {

            parse_str(Input::post('defaultCriteriaData'), $defaultCriteriaData);
            
            $defaultCriteriaCondition = $defaultCriteriaData['criteriaCondition'];
            $defaultCriteriaParam     = $defaultCriteriaData['param'];
            
            foreach ($defaultCriteriaParam as $defParam => $defParamVal) {
                
                $defParamVal = Input::param($defParamVal);
                
                if ($defParamVal) {
                    
                    if (isset($defaultCriteriaCondition[$defParam])) {
                        $operator = $defaultCriteriaCondition[$defParam];
                    } else {
                        $operator = 'between';
                    }

                    if ($defParam == 'createddate' && !empty($defParamVal)) {
                        $paramDefaultCriteria['createddate'][] = array(
                            'operator' => $operator,
                            'operand'  => $defParamVal[0].' AND '.$defParamVal[1]
                        );
                    } else {
                        $paramDefaultCriteria[$defParam][] = array(
                            'operator' => $operator,
                            'operand'  => ($operator == 'like') ? '%'.$defParamVal.'%' : $defParamVal 
                        );
                    }
                }
            }
            
            if (isset($param['criteria'])) {
                $param['criteria'] = array_merge($param['criteria'], $paramDefaultCriteria);
            } else {
                $param['criteria'] = $paramDefaultCriteria;
            }
        }
        
        ini_set('max_execution_time', 15);
        ini_set('default_socket_timeout', 15);
        
        $data = Mdupgrade::getBugfixDataByCommand('list', $param);
        
        $result = array();
        
        if ($data['status'] == 'success' && isset($data['result'])) {
            
            $result['total'] = (isset($data['result']['paging']) ? $data['result']['paging']['totalcount'] : 0);

            unset($data['result']['paging']);

            if (isset($data['result']['aggregatecolumns']) && $data['result']['aggregatecolumns']) {
                $result['footer'] = array($data['result']['aggregatecolumns']);
            }
            unset($data['result']['aggregatecolumns']);
            
            $customerBugFixed = self::getCustomerBugFixed();
            
            array_walk($data['result'], function(&$value) use ($customerBugFixed) {          
                $value['fixed'] = isset($customerBugFixed[$value['id']]) ? '1' : '0';
            }); 
                            
            $result['rows'] = $data['result'];
            $result['status'] = 'success';
            
        } else {
            
            $message = isset($data['message']) ? $data['message'] : $this->ws->getResponseMessage($data);
            
            $result = array('status' => 'error', 'message' => $message, 'rows' => array(), 'total' => 0);
        }
        
        return $result;
    }
    
    public function getCustomerBugFixed() {
        
        $data = $this->db->GetAll("SELECT META_BUG_FIXING_ID FROM CUSTOMER_BUG_FIXED GROUP BY META_BUG_FIXING_ID");
        
        if ($data) {
            foreach ($data as $row) {
                $data[$row['META_BUG_FIXING_ID']] = 1;
            }
        }
        
        return $data;
    }
    
    public function updatingBugFixingModel() {
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('default_socket_timeout', 30000);
        
        $bugFixId = Input::numeric('bugFixId');
        
        if ($bugFixId) {
            
            $exportData = Mdupgrade::getBugfixDataByCommand('download', array('ids' => $bugFixId));

            if ($exportData['status'] == 'success' && isset($exportData['result'])) {
                
                includeLib('Compress/Compression');
                
                $fileContent = Compression::gzinflate($exportData['result']);
                
                if ($fileContent && strpos($fileContent, '<meta id="') === false) {
                    return array('status' => 'error', 'message' => 'PHP export хийсэн файл уншуулна уу!', 'logs' => '');
                } 
                
                $response = self::executeUpgradeScript(array($fileContent));
                
                if ($response['status'] == 'success') {
                    
                    $currentDate = Date::currentDate('Y-m-d H:i:s');

                    $fixedData = array(
                        'ID'                 => getUID(), 
                        'META_BUG_FIXING_ID' => $bugFixId, 
                        'CREATED_USER_ID'    => Ue::sessionUserKeyId(), 
                        'CREATED_DATE'       => $currentDate, 
                        'STATUS_ID'          => 1, 
                        'MODIFIED_DATE'      => $currentDate
                    );

                    $this->db->AutoExecute('CUSTOMER_BUG_FIXED', $fixedData);
                }

                return array('status' => 'success', 'logs' => issetParam($response['logs']));
            }
            
        } else {
            return array('status' => 'error', 'message' => 'Invalid ids!');
        }
    }
    
    public function getMetaTypeByMultiIds($ids) {
        
        $ids = str_replace(',,', ',', $ids);
        $ids = rtrim($ids, ',');
        
        if ($ids) {
            
            $data = $this->db->GetAll("
                SELECT 
                    META_TYPE_ID, 
                    META_DATA_ID, 
                    META_DATA_CODE 
                FROM META_DATA 
                WHERE META_TYPE_ID IN (".Mdmetadata::$businessProcessMetaTypeId.", ".Mdmetadata::$metaGroupMetaTypeId.") 
                    AND META_DATA_ID IN ($ids)"); 
            
        } else {
            $data = null;
        }
        
        return $data;
    }
    
    public function downloadBugFixingModel($ids) {
        
        if ($ids) {
            
            includeLib('Compress/Compression');
            
            $scripts = $this->db->GetAll(" 
                SELECT 
                    SCRIPT 
                FROM META_BUG_FIXING 
                WHERE ID IN ($ids) 
                    AND SCRIPT IS NOT NULL 
                ORDER BY CREATED_DATE ASC");

            $metas = $this->db->GetAll("
                SELECT 
                    TMP.*
                FROM ( 
                
                    SELECT 
                        TO_CHAR(MD.META_TYPE_ID) AS META_TYPE_ID, 
                        MD.META_DATA_ID, 
                        MD.META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID 
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = DTL.META_DATA_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    WHERE HDR.ID IN ($ids) 
                    
                    UNION 

                    SELECT 
                        'wfmfield' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        TO_CHAR(MD.ID) AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN META_WFM_FIELD MD ON MD.REF_STRUCTURE_ID = DTL.REF_STRUCTURE_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID  
                    WHERE HDR.ID IN ($ids)  

                    UNION 

                    SELECT 
                        'booktype' AS META_TYPE_ID, 
                        MD.BOOK_TYPE_ID AS META_DATA_ID, 
                        MD.BOOK_TYPE_CODE AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID 
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN BOOK_TYPE MD ON MD.BOOK_TYPE_ID = DTL.BOOK_TYPE_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID  
                    WHERE HDR.ID IN ($ids)  
                        
                    UNION 

                    SELECT 
                        'config' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        MD.CODE AS META_DATA_CODE, 
                        NULL AS USER_NAME, 
                        NULL AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN CONFIG MD ON MD.ID = DTL.CONFIG_ID 
                    WHERE HDR.ID IN ($ids) 
                    
                    UNION 

                    SELECT 
                        'ntf' AS META_TYPE_ID, 
                        MD.NOTIFICATION_ID AS META_DATA_ID, 
                        TO_CHAR(MD.NOTIFICATION_ID) AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN NTF_NOTIFICATION MD ON MD.NOTIFICATION_ID = DTL.NOTIFICATION_ID   
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID  
                    WHERE HDR.ID IN ($ids)  
                    
                    UNION 

                    SELECT 
                        'umobject' AS META_TYPE_ID, 
                        MD.OBJECT_ID AS META_DATA_ID, 
                        MD.CODE AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN UM_OBJECT MD ON MD.OBJECT_ID = DTL.OBJECT_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    WHERE HDR.ID IN ($ids)  
                        
                    UNION 

                    SELECT 
                        'impexcel' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        MD.CODE AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN IMP_EXCEL_TEMPLATE MD ON MD.ID = DTL.EXCEL_TEMPLATE_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    WHERE HDR.ID IN ($ids) 
                        
                    UNION 

                    SELECT 
                        'kpi' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        MD.CODE AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN KPI_TEMPLATE MD ON MD.ID = DTL.KPI_TEMPLATE_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    WHERE HDR.ID IN ($ids)  
                        
                    UNION 

                    SELECT 
                        'kpi' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        MD.CODE AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        MD.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID
                    FROM 
                        (
                            SELECT 
                                TRG_TEMPLATE_ID 
                            FROM KPI_TEMPLATE_MAP 
                            START WITH SRC_TEMPLATE_ID IN (
                                SELECT 
                                    KPI_TEMPLATE_ID 
                                FROM META_BUG_FIXING_DTL 
                                WHERE META_BUG_FIXING_ID IN ($ids) 
                                    AND KPI_TEMPLATE_ID IS NOT NULL 
                            ) 
                            CONNECT BY NOCYCLE SRC_TEMPLATE_ID = PRIOR TRG_TEMPLATE_ID 
                        ) KM 
                        INNER JOIN KPI_TEMPLATE MD ON MD.ID = KM.TRG_TEMPLATE_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                        
                    UNION 
                    
                    SELECT 
                        
                        TMP.META_TYPE_ID, 
                        TMP.META_DATA_ID, 
                        TMP.META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        MD.CREATED_DATE, 
                        TMP.SRC_RECORD_ID 
                    FROM (
                            SELECT
                                ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                CODE AS META_DATA_CODE, 
                                NULL AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR
                            START WITH 
                                ID IN (
                                    SELECT 
                                        INDICATOR_ID 
                                    FROM META_BUG_FIXING_DTL 
                                    WHERE META_BUG_FIXING_ID IN ($ids) 
                                        AND INDICATOR_ID IS NOT NULL
                                ) 
                            CONNECT BY NOCYCLE PRIOR PARENT_ID = ID 

                            UNION 

                            SELECT
                                ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                CODE AS META_DATA_CODE, 
                                NULL AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR
                                START WITH ID IN (
                                    SELECT 
                                        CATEGORY_ID 
                                    FROM KPI_INDICATOR_CATEGORY
                                    WHERE INDICATOR_ID IN (
                                        SELECT 
                                            INDICATOR_ID 
                                        FROM META_BUG_FIXING_DTL 
                                        WHERE META_BUG_FIXING_ID IN ($ids) 
                                            AND INDICATOR_ID IS NOT NULL
                                    )  
                                )
                                CONNECT BY NOCYCLE PRIOR PARENT_ID = ID 

                            UNION 

                            SELECT 
                                KI.ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                KI.CODE AS META_DATA_CODE, 
                                NULL AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR_INDICATOR_MAP M 
                                INNER JOIN KPI_INDICATOR KI ON M.TRG_INDICATOR_ID = KI.ID 
                            WHERE M.SEMANTIC_TYPE_ID = 10000009 
                                AND M.SRC_INDICATOR_ID IN ( 
                                    SELECT 
                                        INDICATOR_ID 
                                    FROM META_BUG_FIXING_DTL 
                                    WHERE META_BUG_FIXING_ID IN ($ids) 
                                        AND INDICATOR_ID IS NOT NULL
                                )  
                            
                            UNION 
                            
                            SELECT 
                                T2.ID AS META_DATA_ID, 
                                'kpiindicatorbydata' AS META_TYPE_ID, 
                                T2.CODE AS META_DATA_CODE, 
                                ".$this->db->listAgg('T0.ID', ',', 'T0.ID')." AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR T0 
                                INNER JOIN KPI_TYPE T1 ON T1.ID = T0.KPI_TYPE_ID 
                                INNER JOIN KPI_INDICATOR T2 ON T2.ID = T1.RELATED_INDICATOR_ID 
                            WHERE T0.ID IN ( 
                                SELECT 
                                    INDICATOR_ID 
                                FROM META_BUG_FIXING_DTL 
                                WHERE META_BUG_FIXING_ID IN ($ids) 
                                    AND INDICATOR_ID IS NOT NULL
                            )   
                            GROUP BY 
                                T2.ID, 
                                T2.CODE
                                
                        ) TMP 
                        INNER JOIN KPI_INDICATOR MD ON MD.ID = TMP.META_DATA_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    
                    UNION 
                    
                    SELECT 
                        
                        TMP.META_TYPE_ID, 
                        TMP.META_DATA_ID, 
                        TMP.META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        MD.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID 
                        
                    FROM (
                            SELECT 
                                KT.ID AS META_DATA_ID, 
                                'kpitype' AS META_TYPE_ID, 
                                KT.CODE AS META_DATA_CODE 
                            FROM 
                                (
                                    SELECT 
                                        ID 
                                    FROM KPI_TYPE 
                                    START WITH ID IN ( 
                                        SELECT 
                                            KPI_TYPE_ID 
                                        FROM META_BUG_FIXING_DTL 
                                        WHERE META_BUG_FIXING_ID IN ($ids) 
                                            AND KPI_TYPE_ID IS NOT NULL 
                                    )   
                                    CONNECT BY NOCYCLE ID = PRIOR PARENT_ID
                                ) KM 
                                INNER JOIN KPI_TYPE KT ON KT.ID = KM.ID 

                            UNION 

                            SELECT 
                                KI.ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                KI.CODE AS META_DATA_CODE 
                            FROM (
                                SELECT 
                                    RELATED_INDICATOR_ID 
                                FROM KPI_TYPE 
                                START WITH ID IN ( 
                                    SELECT 
                                        KPI_TYPE_ID 
                                    FROM META_BUG_FIXING_DTL 
                                    WHERE META_BUG_FIXING_ID IN ($ids) 
                                        AND KPI_TYPE_ID IS NOT NULL 
                                )    
                                CONNECT BY NOCYCLE ID = PRIOR PARENT_ID 
                            ) KT 
                            INNER JOIN KPI_INDICATOR KI ON KI.ID = KT.RELATED_INDICATOR_ID 
                        ) TMP 
                        LEFT JOIN KPI_INDICATOR MD ON MD.ID = TMP.META_DATA_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    
                    UNION 
                    
                    SELECT 
                        'metawidget' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        MD.CODE AS META_DATA_CODE, 
                        NULL AS USER_NAME, 
                        NULL AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID 
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN META_WIDGET MD ON MD.ID = DTL.WIDGET_ID 
                    WHERE HDR.ID IN ($ids) 
                    
                    UNION 
                    
                    SELECT 
                        'processrule' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        TO_CHAR(MD.ID) AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID 
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN META_PROCESS_RULE MD ON MD.ID = DTL.PROCESS_RULE_ID  
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    WHERE HDR.ID IN ($ids)  
                    
                    UNION 
                    
                    SELECT 
                        'globedictionary' AS META_TYPE_ID, 
                        MD.ID AS META_DATA_ID, 
                        MD.CODE AS META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE, 
                        HDR.CREATED_DATE, 
                        NULL AS SRC_RECORD_ID 
                    FROM META_BUG_FIXING HDR 
                        INNER JOIN META_BUG_FIXING_DTL DTL ON DTL.META_BUG_FIXING_ID = HDR.ID 
                        INNER JOIN GLOBE_DICTIONARY MD ON MD.ID = DTL.GLOBE_ID   
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                    WHERE HDR.ID IN ($ids)  
                        
                ) TMP 
                GROUP BY 
                    TMP.META_TYPE_ID, 
                    TMP.META_DATA_ID, 
                    TMP.META_DATA_CODE, 
                    TMP.USER_NAME, 
                    TMP.MODIFIED_DATE, 
                    TMP.CREATED_DATE, 
                    TMP.SRC_RECORD_ID 
                ORDER BY 
                    TMP.META_TYPE_ID DESC, 
                    TMP.CREATED_DATE ASC");
            
            $scriptXml = $metaXml = null;
            
            if ($scripts) {

                foreach ($scripts as $scrpt) {

                    $scriptXml .= $scrpt['SCRIPT'] . Mdcommon::$separator . "\n";
                }
            }
            
            if ($metas) {
                
                $isUseMetaUserId = Config::getFromCache('IS_USE_META_CREATED_MODIFIED_USER_ID');
            
                if ($isUseMetaUserId == '1') {
                    self::$exportIgnoreColumns = array('EXPORT_SCRIPT', 'COPY_COUNT');
                }

                foreach ($metas as $meta) {
                    
                    if (is_numeric($meta['META_TYPE_ID'])) {
                        
                        self::$ignoreDeleteScript = false;
                        $metaResult = self::oneMetaModel($meta);

                        if ($metaResult['status'] == 'success') {
                            $metaXml .= $metaResult['result'];
                        }
                    
                    } else {
                        
                        if ($meta['META_TYPE_ID'] == 'impexcel' 
                                || $meta['META_TYPE_ID'] == 'kpi' 
                                || $meta['META_TYPE_ID'] == 'kpiindicator' 
                                || $meta['META_TYPE_ID'] == 'kpitype' 
                                || $meta['META_TYPE_ID'] == 'metawidget' 
                                || $meta['META_TYPE_ID'] == 'processrule' 
                                || $meta['META_TYPE_ID'] == 'globedictionary') {
                            
                            self::$ignoreDeleteScript = false;
                        } else {
                            self::$ignoreDeleteScript = true;
                        }
                        
                        self::$isCreateTable = false;
                        self::$isInsertData = true;
                        self::$insertDataFilter = null;

                        if ($meta['META_TYPE_ID'] == 'kpiindicatorbydata') { 

                            self::$isCreateTable = true;
                            self::$isInsertData = false;
                            self::$insertDataFilter = 'SRC_RECORD_ID='.$meta['SRC_RECORD_ID'];

                            $meta['META_TYPE_ID'] = 'kpiindicator';
                        } 
                        
                        $objectResult = self::oneObjectModel($meta['META_DATA_ID'], $meta['META_TYPE_ID'], $meta['META_DATA_CODE']);

                        if ($objectResult['status'] == 'success') {
                            
                            $metaXml .= $objectResult['result'];
                        }
                    }
                }
            }
            
            if ($metaXml || $scriptXml) {

                $xml = self::upgradeXmlHeader();
                
                if ($scriptXml) {
                    $xml .= '<scripts>' . "\n";
                        $xml .= '<![CDATA[' . "\n";
                            $xml .= $scriptXml;
                        $xml .= ']]>' . "\n";
                    $xml .= '</scripts>' . "\n";
                }
                
                if ($metaXml) {
                    $xml .= '<metas>' . "\n";
                        $xml .= $metaXml;
                    $xml .= '</metas>' . "\n";
                }
                
                $xml .= self::upgradeXmlFooter();

                $script = Compression::gzdeflate($xml);

                $result = array('status' => 'success', 'result' => $script);
                
            } else {
                $result = array('status' => 'error', 'message' => 'Татах өгөгдөл олдсонгүй!');
            }
            
        } else {
            $result = array('status' => 'error', 'message' => 'Invalid ids!');
        }
        
        return $result;
    }
    
    public function downloadObjectModel($objectCode, $ids) {
        
        $selectedRows = array();
        $idsArr = explode(',', $ids);
        
        foreach ($idsArr as $id) {
            $selectedRows[] = array('id' => $id);
        }
        
        $_POST['id'] = 'id';
        $_POST['objectCode'] = $objectCode;
        $_POST['selectedRows'] = $selectedRows;
        
        self::$ignoreDeleteScript = false;
        $export = self::exportObjectModel();

        if ($export['status'] == 'success') {
            $result = array('status' => 'success', 'result' => $export['result']);
        } else {
            $result = array('status' => 'error', 'message' => $export['message']);
        }
        
        return $result;
    }
    
    public function exportMetaModel() {
        
        includeLib('Compress/Compression');
        $this->load->model('mdmetadata', 'middleware/models/');
        
        $metaId = Input::post('metaId');
        
        if (is_array($metaId)) { /* Олон үзүүлэлт */
            
            $metaIds = Arr::implode_r(',', $metaId, true);
            
            if (count($metaId) == 1) {
                $isOneMeta = true;
                $metaId = $metaId[0];
            }
            
        } else { /* Нэг үзүүлэлт */
            
            $metaIds = $metaId;
            $isOneMeta = true;
        }
        
        $metas = $this->db->GetAll("
            SELECT 
                TMP.*
            FROM (    
                SELECT 
                    TT.META_TYPE_ID, 
                    TT.META_DATA_ID, 
                    TT.META_DATA_CODE, 
                    ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                    ".$this->db->IfNull('TT.MODIFIED_DATE', 'TT.CREATED_DATE')." AS MODIFIED_DATE 
                FROM (
                        SELECT 
                            META_DATA_ID, 
                            META_TYPE_ID, 
                            META_DATA_CODE, 
                            CREATED_USER_ID, 
                            MODIFIED_USER_ID, 
                            CREATED_DATE, 
                            MODIFIED_DATE 
                        FROM META_DATA 
                        WHERE META_DATA_ID IN ($metaIds) 

                        UNION  

                        SELECT 
                            MD.META_DATA_ID, 
                            MD.META_TYPE_ID, 
                            MD.META_DATA_CODE, 
                            MD.CREATED_USER_ID, 
                            MD.MODIFIED_USER_ID, 
                            MD.CREATED_DATE, 
                            MD.MODIFIED_DATE 
                        FROM META_PROCESS_RULE PR 
                            INNER JOIN META_BUSINESS_PROCESS_LINK BP ON BP.META_DATA_ID = PR.RULE_PROCESS_ID 
                            INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.META_DATA_ID 
                        WHERE PR.MAIN_PROCESS_ID IN ($metaIds) 
                    ) TT 
                    LEFT JOIN UM_USER US ON US.USER_ID = TT.CREATED_USER_ID 
                    LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                    LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = TT.MODIFIED_USER_ID 
            ) TMP 
            GROUP BY 
                TMP.META_TYPE_ID, 
                TMP.META_DATA_ID, 
                TMP.META_DATA_CODE, 
                TMP.USER_NAME, 
                TMP.MODIFIED_DATE" 
        );
        
        if ($metas) {
            
            $isUseMetaUserId = Config::getFromCache('IS_USE_META_CREATED_MODIFIED_USER_ID');
            
            if ($isUseMetaUserId == '1') {
                self::$exportIgnoreColumns = array('EXPORT_SCRIPT', 'COPY_COUNT');
            }
            
            $metaXml = null;
            
            foreach ($metas as $meta) {

                $metaResult = self::oneMetaModel($meta);

                if ($metaResult['status'] == 'success') {
                    $metaXml .= $metaResult['result'];
                }
            }

            if ($metaXml) {

                $xml = self::upgradeXmlHeader();

                $xml .= '<metas>' . "\n";
                    $xml .= $metaXml;
                $xml .= '</metas>' . "\n";

                $xml .= self::upgradeXmlFooter();

                $script = Compression::gzdeflate($xml);

                $result = array('status' => 'success', 'result' => $script);
                
                if (isset($isOneMeta)) {
                    $result['metaId'] = $metaId;
                }
            }
        }
        
        return isset($result) ? $result : array('status' => 'error', 'message' => 'Татах өгөгдөл олдсонгүй!');
    }
    
    public function upgradeXmlHeader() {
        
        $domainUrl    = URL;
        $unitName     = Session::unitName();
        $dbUserName   = $unitName ? $unitName : DB_USER;
        $currentDate  = Date::currentDate();
        $userName     = Ue::getSessionUserName();
            
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<documents domainUrl="'.$domainUrl.'" dbDriver="'.DB_DRIVER.'" dbUserName="'.$dbUserName.'" exportedDate="'.$currentDate.'" sessionUserName="'.$userName.'">' . "\n";
        
        return $xml;
    }
    
    public function upgradeXmlFooter() {
        
        $xml = '</documents>';
        
        return $xml;
    }
    
    public function oneMetaModel($metaRow) {
        
        $metaTypeId = $metaRow['META_TYPE_ID']; 
        $metaId     = $metaRow['META_DATA_ID']; 
        
        if (!isset(self::$exportedMetaIds[$metaTypeId][$metaId])) {
            
            self::$clobColumns = array();
            self::$blobColumns = array();
            self::$fileColumns = array();
            self::$deleteIds = array();
            self::$exportedRecordIds = array();
            self::$exportCreateTables = array();
            self::$childCreateTable = array();
            self::$exportedMetaIds[$metaTypeId][$metaId] = 1;
            
            $metaCode     = $metaRow['META_DATA_CODE'];
            $userName     = $metaRow['USER_NAME'];
            $modifiedDate = $metaRow['MODIFIED_DATE'];
            
            $separator         = Mdcommon::$separator;
            $metaTableRelation = self::metaTableRelation();

            try {
                
                $xmlScript = '';
                
                if (isset($metaTableRelation[$metaTypeId])) {
                    
                    $script    = '';
                    $relations = $metaTableRelation[$metaTypeId];

                    foreach ($relations as $tblName => $rel) {

                        $script .= self::generateDeleteInsertQuery($tblName, 'META_DATA_ID='.$metaId, $separator);

                        foreach ($rel as $relRow) {

                            $relTableName = $relRow['table'];
                            $relTrg       = $relRow['link'][0]['trg'];
                            $child        = issetParamArray($relRow['child']);

                            self::$fileColumn = issetParamArray($relRow['file']);

                            $script .= self::generateDeleteInsertQuery($relTableName, $relTrg.'='.$metaId, $separator, $child);
                        } 
                    } 

                    $deleteScript = self::generateDeleteScript($separator);

                    $script = $deleteScript . $script;
                    
                    $xmlScript = '<meta id="'.$metaId.'" typeId="'.$metaTypeId.'" code="'.$metaCode.'" userName="'.$userName.'" modifiedDate="'.$modifiedDate.'">' . "\n";
                    
                    $xmlScript .= '<scripts>' . "\n";
                            $xmlScript .= '<![CDATA[' . "\n";
                                $xmlScript .= $script;
                            $xmlScript .= ']]>' . "\n";
                        $xmlScript .= '</scripts>' . "\n";
                        
                    $xmlScript .= self::clobBlobAppendXml();
                    $xmlScript .= self::fileAppendXml();
                    $xmlScript .= self::translateAppendXml($metaId, $metaTypeId);
                    
                    $xmlScript .= '</meta>' . "\n";
                }

                $result = array('status' => 'success', 'result' => $xmlScript);

            } catch (Exception $ex) {
                $result = array('status' => 'error', 'message' => $ex->getMessage());
            }
            
        } else {
            $result = array('status' => 'success', 'result' => '');
        }
        
        return $result;
    }
    
    public function oneObjectModel($metaId, $metaTypeId, $metaCode) {
        
        if (!isset(self::$exportedMetaIds[$metaTypeId][$metaId])) {
            
            self::$clobColumns = array();
            self::$blobColumns = array();
            self::$fileColumns = array();
            self::$deleteIds = array();
            self::$exportedRecordIds = array();
            self::$exportCreateTables = array();
            self::$childCreateTable = array();
            self::$exportedMetaIds[$metaTypeId][$metaId] = 1;
            
            $separator           = Mdcommon::$separator;
            $objectTableRelation = self::objectTableRelation();

            try {
                
                $xmlScript = '';
                
                if (isset($objectTableRelation[$metaTypeId])) {
                    
                    $script    = '';
                    $relations = $objectTableRelation[$metaTypeId];
                    
                    if (Input::isEmpty('dataTableName') == false) {
                        $dataTableNames = Input::post('dataTableName');
                    }

                    foreach ($relations as $tblName => $rel) {
                        
                        self::$fileColumn = self::tableFileColumns($tblName);
                        
                        if (isset($dataTableNames) && is_array($dataTableNames)) {
                                
                            foreach ($dataTableNames as $dataTableName) {

                                if ($dataTableName == $tblName && $tblName == 'KPI_INDICATOR') {

                                    self::$childCreateTable = array(
                                        array(
                                            'isCreateTable'   => true, 
                                            'isInsertData'    => true, 
                                            'tableColumnName' => 'TABLE_NAME'
                                        )
                                    );
                                }
                            }
                            
                        } elseif ($dynamicCreateTable = self::dynamicCreateTables($tblName)) {
                            
                            self::$childCreateTable = $dynamicCreateTable;
                        }
                        
                        $script .= self::generateDeleteInsertQuery($tblName, self::tablePrimaryField($tblName).'='.$metaId, $separator);
                    
                        foreach ($rel as $relRow) {

                            $relTableName = $relRow['table'];
                            $relTrg       = $relRow['link'][0]['trg'];
                            $child        = issetParamArray($relRow['child']);

                            self::$fileColumn = issetParamArray($relRow['file']);
                            self::$childCreateTable = issetParamArray($relRow['childCreateTable']);
                            
                            $script .= self::generateDeleteInsertQuery($relTableName, $relTrg.'='.$metaId, $separator, $child);
                        } 
                    } 

                    $deleteScript      = self::generateObjectDeleteScript($separator); 
                    $createTableScript = self::exportCreateTableAppendXml();
                    $clobBlobScript    = self::clobBlobAppendXml();
                    
                    if (self::$exportCreateTables) {
                            
                        $kpiDbSchemaName   = Config::getFromCache('kpiDbSchemaName');
                        
                        $script            = str_replace(", '$kpiDbSchemaName.", ", '[kpiDbSchemaName].", $script);
                        $createTableScript = str_replace('tblName="'.$kpiDbSchemaName.'.', 'tblName="[kpiDbSchemaName].', $createTableScript);
                        $clobBlobScript    = str_replace('tblName="'.$kpiDbSchemaName.'.', 'tblName="[kpiDbSchemaName].', $clobBlobScript);
                    }
                        
                    $script = $deleteScript . $script;
                    
                    $xmlScript = '<meta id="'.$metaId.'" typeId="'.$metaTypeId.'" code="'.$metaCode.'" skipError="'.(self::$ignoreDeleteScript ? 1 : 0).'">' . "\n";
                        
                        $xmlScript .= '<scripts>' . "\n";
                            $xmlScript .= '<![CDATA[' . "\n";
                                $xmlScript .= $script;
                            $xmlScript .= ']]>' . "\n";
                        $xmlScript .= '</scripts>' . "\n";
                        
                        $xmlScript .= $createTableScript;
        
                        $xmlScript .= $clobBlobScript;
                        $xmlScript .= self::fileAppendXml();
                        
                    
                    $xmlScript .= '</meta>' . "\n";
                }

                $result = array('status' => 'success', 'result' => $xmlScript);

            } catch (Exception $ex) {
                $result = array('status' => 'error', 'message' => $ex->getMessage());
            }
            
        } else {
            $result = array('status' => 'success', 'result' => '');
        }
        
        return $result;
    }
    
    public function exportCreateTableAppendXml() {
        
        $xml = null;
        
        if (self::$isCreateRollback == false && self::$exportCreateTables) {
            
            $kpiDbSchemaName = Config::getFromCache('kpiDbSchemaName');
            $rowXml = null;
            
            foreach (self::$exportCreateTables as $row) {
                
                $createTableName = $row['tableName'];
                
                try {
                    
                    if ($columns = $this->db->MetaColumns($createTableName)) {
                        
                        $tmpCreateTableName = $createTableName;
                        $createTableName    = str_replace($kpiDbSchemaName . '.', '', $createTableName);
                        $dbTableName        = "[kpiDbSchemaName].$createTableName";
                        
                        $fields = $createColumns = '';

                        foreach ($columns as $column) {

                            $name          = $column->name;
                            $max_length    = $column->max_length;
                            $type          = $column->type;
                            $scale         = $column->scale;
                            $not_null      = $column->not_null ? ' NOT NULL ENABLE' : '';
                            $default_value = $column->default_value;

                            if ($type == 'INT' || $type == 'NUMBER') {
                                
                                $fields .= "$name NUMBER($max_length, $scale)$not_null, ";
                                $createColumns .= "ALTER TABLE $dbTableName ADD ($name NUMBER($max_length, $scale))". Mdcommon::$separator . "\n";
                                
                            } elseif ($type == 'VARCHAR2') {
                                
                                $fields .= "$name VARCHAR2($max_length CHAR)$not_null, ";
                                $createColumns .= "ALTER TABLE $dbTableName ADD ($name VARCHAR2($max_length CHAR))". Mdcommon::$separator . "\n";
                                
                            } elseif ($type == 'DATE') {
                                
                                $fields .= "$name DATE$not_null, ";
                                $createColumns .= "ALTER TABLE $dbTableName ADD ($name DATE)". Mdcommon::$separator . "\n";
                                
                            } elseif ($type == 'CLOB') {
                                
                                $fields .= "$name CLOB$not_null, ";
                                $createColumns .= "ALTER TABLE $dbTableName ADD ($name CLOB)". Mdcommon::$separator . "\n";
                            }
                        }

                        $fields             = rtrim(trim($fields), ',');
                        $createTableScripts = "CREATE TABLE $dbTableName ($fields)". Mdcommon::$separator . "\n";

                        $tableOwner = $deleteScripts = $indexScripts = '';

                        if (strpos($tmpCreateTableName, '.') !== false) {
                            $tmpCreateTableNameArr = explode('.', $tmpCreateTableName);
                            $tableOwner = $tmpCreateTableNameArr[0];
                        }

                        $indexes = $this->db->MetaIndexes($createTableName, true, $tableOwner);

                        if ($indexes) {

                            $primaryKeys = $this->db->MetaPrimaryKeys($createTableName, $tableOwner);

                            global $ADODB_FETCH_MODE;

                            $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

                            foreach ($indexes as $indexName => $indexRow) {

                                if ($primaryKeys && isset($indexRow['columns'][0]) == $primaryKeys[0]) {

                                    $indexScripts .= "ALTER TABLE $dbTableName ADD CONSTRAINT $indexName PRIMARY KEY (ID) ENABLE" . Mdcommon::$separator . "\n";

                                } else {

                                    $unique  = $indexRow['unique'] ? 'UNIQUE ' : '';
                                    $columns = Arr::implode_r(',', $indexRow['columns'], true);

                                    $indexScripts .= "CREATE ".$unique."INDEX [kpiDbSchemaName].$indexName ON $dbTableName ($columns)" . Mdcommon::$separator . "\n";
                                }
                            }
                        }
                        
                        if (self::$isInsertData) {
                            $insertScripts = self::generateInsertQuery($tmpCreateTableName, '1=1', Mdcommon::$separator);
                        } elseif (self::$insertDataFilter) {
                            $insertScripts = self::generateInsertQuery($tmpCreateTableName, self::$insertDataFilter, Mdcommon::$separator);
                        }
                        
                        $insertScripts = str_replace('INTO '.$kpiDbSchemaName . '.', 'INTO [kpiDbSchemaName].', $insertScripts);
                        $columnName    = $row['columnName'];

                        if ($columnName == 'TEMPLATE_TABLE_NAME') {
                            $deleteScripts = "DELETE FROM $dbTableName" . Mdcommon::$separator . "\n";
                        }

                        $rowXml .= '<createTable tblName="'.$createTableName.'" skipError="1">' . "\n";
                            $rowXml .= '<![CDATA[' . "\n";
                                $rowXml .= $createTableScripts;
                                $rowXml .= $deleteScripts;
                                $rowXml .= $createColumns;
                                $rowXml .= $indexScripts;
                                $rowXml .= $insertScripts;
                            $rowXml .= ']]>' . "\n";
                        $rowXml .= '</createTable>' . "\n";
                    }
                    
                } catch (Exception $ex) { }
            }
            
            if ($rowXml) {
                $xml = '<createTables>' . "\n";
                    $xml .= $rowXml;
                $xml .= '</createTables>' . "\n";
            }
        }
        
        return $xml;
    }
    
    public function generateCreateInsertQuery($createTableName, $separator) {
        
        try {
            
            $fields = '';
            $columns = $this->db->MetaColumns($createTableName);
            
            foreach ($columns as $column) {
                
                $name          = $column->name;
                $max_length    = $column->max_length;
                $type          = $column->type;
                $scale         = $column->scale;
                $not_null      = $column->not_null;
                $default_value = $column->default_value;
                
                $not_null      = $not_null ? ' NOT NULL ENABLE' : '';
                
                if ($type == 'INT' || $type == 'NUMBER') {
                    $fields .= "$name NUMBER($max_length, $scale)$not_null, ";
                } elseif ($type == 'VARCHAR2') {
                    $fields .= "$name VARCHAR2($max_length CHAR)$not_null, ";
                } elseif ($type == 'DATE') {
                    $fields .= "$name DATE$not_null, ";
                } 
            }
            
            $scripts = "CREATE TABLE $createTableName ()". $separator;
            
            $result = array('status' => 'success');
            
        } catch (Exception $ex) {
            $result = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $result;
    }
    
    public function generateObjectDeleteScript($separator) {
        
        $deleteScript = null;
        
        if (self::$ignoreDeleteScript) {
            return $deleteScript;
        }
        
        if (self::$deleteIds) {
            
            $deleteIds = array_reverse(self::$deleteIds);
            
            foreach ($deleteIds as $tableName => $columns) {
                
                if (in_array($tableName, self::$ignoreDeleteScriptTables)) {
                    continue;
                }
                
                foreach ($columns as $columnName => $ids) {
                    
                    $where = '';
                    
                    if (is_array($ids)) {
                        
                        $idsSplit = array_chunk($ids, 500); 
                            
                        foreach ($idsSplit as $idsArr) {
                            $where .= " $columnName IN (" . implode(',', $idsArr) . ") OR";
                        }

                        $where = rtrim($where, ' OR');
                        
                    } else {
                        
                        parse_str($ids, $idsArr);
                        
                        foreach ($idsArr as $columnName => $val) {
                            if ($val != '') {
                                $where .= " $columnName = $val AND";
                            } else {
                                $where .= " $columnName IS NULL AND";
                            }
                        }
                        
                        $where = mb_substr($where, 0, -4);
                    }
                    
                    $deleteScript .= "DELETE FROM $tableName WHERE$where" . $separator . "\n";
                }
            }
        }
        
        return $deleteScript;
    }
    
    public function generateDeleteScript($separator) {
        
        $deleteScript = null;
        
        if (self::$deleteIds) {
            
            foreach (self::$deleteIds as $tableName => $columns) {
                
                foreach ($columns as $columnName => $ids) {
                    
                    $where = '';
                    
                    if (is_array($ids)) {
                        
                        $idsSplit = array_chunk($ids, 500); 
                            
                        foreach ($idsSplit as $idsArr) {
                            $where .= " $columnName IN (" . implode(',', $idsArr) . ") OR";
                        }

                        $where = rtrim($where, ' OR');
                        
                    } else {
                        
                        parse_str($ids, $idsArr);
                        
                        foreach ($idsArr as $columnName => $val) {
                            if ($val != '') {
                                $where .= " $columnName = $val AND";
                            } else {
                                $where .= " $columnName IS NULL AND";
                            }
                        }
                        
                        $where = mb_substr($where, 0, -4);
                    }
                    
                    $deleteScript .= "DELETE FROM $tableName WHERE$where" . $separator . "\n";
                }
            }
        }
        
        return $deleteScript;
    }
    
    public function generateDeleteInsertQuery($tblName, $recordId, $separator, $child = array()) {
        
        $script = null;
        
        if (self::$isIgnoreMetaFolder && $tblName == 'META_DATA_FOLDER_MAP') {    
            return $script;
        }
        
        if (!isset(self::$deleteIds[$tblName][$recordId]) && $recordId) {
            
            if (!in_array($tblName, self::$ignoreDeleteScriptTables)) {
                self::$deleteIds[$tblName][$recordId] = $recordId;
            }
        
            $insert = self::generateInsertQuery($tblName, $recordId, $separator, array(), $child);

            if ($insert) {

                $script .= $insert;

                if ($tblName == 'META_DATA_FOLDER_MAP') {
                    $script .= self::generateDeleteInsertFolderQuery(self::$metaFolderId);
                }
            }
        }
        
        return $script;
    }
    
    public function generateInsertQuery($tblName, $recordId, $separator, $data = array(), $childs = array()) {
         
        $sql = null;
        
        if (!$data && $recordId) {
            
            parse_str($recordId, $recordIdArr);
            $where = '';
            $bindParams = array();
            
            foreach ($recordIdArr as $fieldName => $fieldVal) {
                
                if (strpos($fieldVal, ',') !== false) {
                    $where .= "$fieldName IN ($fieldVal) AND ";
                } else {
                    $where .= "$fieldName = ".$this->db->Param($fieldName).' AND ';
                    $bindParams = array($fieldName => $fieldVal) + $bindParams;
                }
            }

            $where = mb_substr($where, 0, -5);
            
            if ($bindParams) {
                $data = $this->db->GetAll("SELECT * FROM $tblName WHERE $where", $bindParams);
            } else {
                $data = $this->db->GetAll("SELECT * FROM $tblName WHERE $where");
            }
        }
        
        if ($data) {
            
            $fields        = self::getObjectFields($tblName);
            $primaryColumn = self::$executedTablesPrimaryColumn[$tblName];
            
            $kpiDbSchemaName = Config::getFromCache('kpiDbSchemaName');
            
            foreach ($data as $row) {
                
                if ($tblName == 'META_PROCESS_PARAM_LINK' || $tblName == 'KPI_INDICATOR_MAP_CRITERIA') {
                    
                    if (isset(self::$exportedRecordIds[$tblName][$row[$primaryColumn]])) {
                        continue;
                    }
                    
                    self::$exportedRecordIds[$tblName][$row[$primaryColumn]] = 1;
                }
                
                if (self::$childCreateTable) {
                    
                    foreach (self::$childCreateTable as $tblRow) {
                        
                        $tableColumnName = $tblRow['tableColumnName'];
                        $dataTableName   = issetParam($row[$tableColumnName]);
                        
                        if ($dataTableName) {
                            
                            if ($tblRow['isCreateTable']) {
                                self::$exportCreateTables[strtolower($dataTableName)] = array('mainTableName' => $tblName, 'columnName' => $tableColumnName, 'tableName' => $dataTableName);
                            }
                            
                            $row[$tableColumnName] = '[kpiDbSchemaName].' . str_replace($kpiDbSchemaName.'.', '', $dataTableName);
                        }
                    }
                }
                
                $columns = $values = '';
                $sql     .= 'INSERT INTO ' . $tblName . ' (';

                foreach ($fields as $col) {

                    if (!in_array($col['name'], self::$exportIgnoreColumns) && !isset(self::$exportIgnoreTableColumns[$tblName][$col['name']])) {

                        if ($col['type'] == 'INT' || $col['type'] == 'NUMBER') {
                            
                            if ($row[$col['name']] != '') {
                                
                                $sId = $row[$col['name']];
                                
                                $columns .= $col['name'] . ', ';
                                
                                $values .= $sId . ', ';
                                
                                if (self::$isIdReplace && $primaryColumn == $col['name']) {
                                    array_push(self::$replaceIds, $sId);
                                }
                            }

                        } elseif ($col['type'] == 'DATE') {
                            
                            if ($row[$col['name']] != '') {
                                
                                $columns .= $col['name'] . ', ';
                                
                                $values .= $this->db->SQLDate('Y-m-d H:i:s', "'" . $row[$col['name']] . "'", 'TO_DATE') . ', ';
                            } 

                        } elseif ($col['type'] == 'CLOB') {

                            if ($row[$col['name']] != '') {
                                
                                self::$clobColumns[] = array(
                                    'tblName'    => $tblName, 
                                    'colName'    => $col['name'], 
                                    'equalField' => $primaryColumn, 
                                    'recordId'   => $row[$primaryColumn], 
                                    'content'    => $row[$col['name']]
                                );  
                            }

                        } elseif ($col['type'] == 'BLOB') {

                            if ($row[$col['name']] != '') {
                                
                                self::$blobColumns[] = array(
                                    'tblName'    => $tblName, 
                                    'colName'    => $col['name'], 
                                    'equalField' => $primaryColumn, 
                                    'recordId'   => $row[$primaryColumn], 
                                    'content'    => $row[$col['name']]
                                );  
                            } 

                        } else {
                            
                            if (isset(self::$fileColumn[$col['name']])) {
                                
                                self::$fileColumns[] = array(
                                    'tblName'    => $tblName, 
                                    'colName'    => $col['name'], 
                                    'equalField' => $primaryColumn, 
                                    'recordId'   => $row[$primaryColumn], 
                                    'content'    => $row[$col['name']], 
                                    'fileType'   => self::$fileColumn[$col['name']]
                                );
                            } 
                            
                            if ($row[$col['name']] != '') {
                                
                                $columns .= $col['name'] . ', ';
                                
                                $values .= "'" . str_replace("'", "''", $row[$col['name']]) . "', ";
                            } 
                        }
                    }
                }
                
                $sql .= rtrim($columns, ', ') . ') VALUES (' . rtrim($values, ', ') . ')' . $separator . "\n";

                if ($tblName == 'META_DATA_FOLDER_MAP' && $row['FOLDER_ID']) {
                    
                    self::$metaFolderId = $row['FOLDER_ID'];
                }
                
                if ($childs) {
                    
                    foreach ($childs as $child) {
                        
                        $qryStr = '';
                        
                        foreach ($child['link'] as $link) { 
                            $qryStr .= $link['trg'].'='.$row[$link['src']].'&';
                        }
                        
                        $qryStr = rtrim($qryStr, '&');
                        
                        $sql .= self::generateDeleteInsertQuery($child['table'], $qryStr, $separator, issetParamArray($child['child']));
                    }
                }
            }
        }
        
        return $sql;
    }
    
    public function generateDeleteInsertFolderQuery($metaFolderId) {
        
        $script = '';
        
        if (!isset(self::$executedFolderIds[$metaFolderId])) {
            
            if (DB_DRIVER == 'oci8') {
                
                $data = $this->db->GetAll("
                    SELECT 
                        * 
                    FROM FVM_FOLDER 
                        CONNECT BY 
                        NOCYCLE 
                        PRIOR PARENT_FOLDER_ID = FOLDER_ID 
                        START WITH FOLDER_ID = ".$this->db->Param(0)."  
                    ORDER BY LEVEL DESC", 
                    array($metaFolderId)
                );
                
            } elseif (DB_DRIVER == 'postgres9') {
                
                $data = $this->db->GetAll("
                    WITH RECURSIVE TMP_FOLDER AS 
                    (
                        SELECT 
                            U1.*, 
                            1 AS LEVEL 
                        FROM FVM_FOLDER U1 
                        WHERE FOLDER_ID = ".$this->db->Param(0)."  

                        UNION ALL 

                        SELECT 
                            U2.*, 
                            TMP_FOLDER.LEVEL + 1 
                        FROM FVM_FOLDER U2 
                            JOIN TMP_FOLDER ON TMP_FOLDER.PARENT_FOLDER_ID = U2.FOLDER_ID 
                    ) SELECT * FROM TMP_FOLDER ORDER BY LEVEL DESC", 
                    array($metaFolderId)
                );
            }

            if ($data) {

                $separator = Mdcommon::$separator;

                foreach ($data as $row) {

                    $folderId = $row['FOLDER_ID'];
                    
                    if (!isset(self::$scriptFolderIds[$folderId])) {
                        
                        self::$deleteIds['FVM_FOLDER']['FOLDER_ID'][] = $folderId;

                        $script .= self::generateInsertQuery('FVM_FOLDER', 'FOLDER_ID='.$folderId, $separator, array($row));
                        
                        self::$scriptFolderIds[$folderId] = 1;
                    }
                }
            }
            
            self::$executedFolderIds[$metaFolderId] = 1;
        }
        
        return $script;
    }
    
    public function postgreSqlColumnsConvert($sql) {

        $data = $this->db->GetAll($sql);
        
        if ($data) {
            
            $arr = array();
            
            foreach ($data as $row) {
                
                $typeName = 'varchar';
                
                if ($row['TYPNAME'] == 'numeric') {
                    $typeName = 'NUMBER';
                } elseif ($row['TYPNAME'] == 'text') {
                    $typeName = 'CLOB';
                } elseif ($row['TYPNAME'] == 'timestamp') {
                    $typeName = 'DATE'; 
                }
                
                $arr[] = array(
                    'name'       => strtoupper($row['ATTNAME']), 
                    'max_length' => 4000, 
                    'type'       => $typeName, 
                    'scale'      => 1
                );
            }
            
            return $arr;
        }
        
        return null;
    }
    
    public function postgreArrayColumnsConvert($data) {

        $arr = array();
            
        foreach ($data as $row) {

            $typeName = 'varchar';

            if ($row->type == 'numeric') {
                $typeName = 'NUMBER';
            } elseif ($row->type == 'text') {
                $typeName = 'CLOB';
            } elseif ($row->type == 'timestamp') {
                $typeName = 'DATE'; 
            }

            $arr[] = array(
                'name'       => strtoupper($row->name), 
                'max_length' => 4000, 
                'type'       => $typeName, 
                'scale'      => 1
            );
        }

        return $arr;
    }
    
    public function getObjectFields($objectName) {
        
        if (isset(self::$executedTables[$objectName])) {
            
            $fieldObjs = self::$executedTables[$objectName];
            
        } else {
            
            try {
                
                if (DB_DRIVER == 'oci8') {
                
                    $rs = $this->db->Execute("SELECT * FROM $objectName WHERE 1 = 0");
                    $fieldObjs = Arr::objectToArray($rs->_fieldobjs);

                    $getPrimaryColumn = $this->db->MetaPrimaryKeys($objectName);

                } elseif (DB_DRIVER == 'postgres9') {

                    $rs = $this->db->MetaColumns('public.' . $objectName);
                    
                    if (is_array($rs)) {
                        $fieldObjs = self::postgreArrayColumnsConvert($rs);
                    } else {
                        $fieldObjs = self::postgreSqlColumnsConvert($rs->sql);
                    }

                    $keyRow = $this->db->GetRow(sprintf($this->db->metaKeySQL1, strtolower($objectName)));

                    if (isset($keyRow['COLUMN_NAME'])) {
                        $getPrimaryColumn[0] = $keyRow['COLUMN_NAME'];
                    }
                }

                self::$executedTables[$objectName] = $fieldObjs;

                if (isset($getPrimaryColumn[0])) {

                    self::$executedTablesPrimaryColumn[$objectName] = strtoupper($getPrimaryColumn[0]);

                } else {

                    if ($objectName == 'META_DATA') {
                        self::$executedTablesPrimaryColumn[$objectName] = 'META_DATA_ID';
                    } else {
                        self::$executedTablesPrimaryColumn[$objectName] = 'ID';
                    }
                }
            
            } catch (Exception $ex) {
                
                $fieldObjs = array();
            }
        }
        
        return $fieldObjs;
    }
    
    public function clobBlobAppendXml() {
        
        $xml = null;
        
        if (!empty(self::$clobColumns) || !empty(self::$blobColumns)) {
            
            $xml .= '<cloblobs>' . "\n";
            
            if (!empty(self::$clobColumns)) {
                
                foreach (self::$clobColumns as $clobCol) {
                    $xml .= '<cloblob type="clob" tblName="'.$clobCol['tblName'].'" colName="'.$clobCol['colName'].'" equalField="'.$clobCol['equalField'].'" recordId="'.$clobCol['recordId'].'">' . "\n";
                        $xml .= '<![CDATA[' . "\n";
                            $xml .= $clobCol['content'] . "\n";
                        $xml .= ']]>' . "\n";
                    $xml .= '</cloblob>' . "\n";
                }
            }
            
            if (!empty(self::$blobColumns)) {
                
                foreach (self::$blobColumns as $blobCol) {
                    $xml .= '<cloblob type="blob" tblName="'.$blobCol['tblName'].'" colName="'.$blobCol['colName'].'" equalField="'.$blobCol['equalField'].'" recordId="'.$blobCol['recordId'].'">' . "\n";
                        $xml .= '<![CDATA[' . "\n";
                            $xml .= $blobCol['content'] . "\n";
                        $xml .= ']]>' . "\n";
                    $xml .= '</cloblob>' . "\n";
                }
            }
            
            $xml .= '</cloblobs>' . "\n";
        }
        
        return $xml;
    }
    
    public function fileAppendXml() {
        
        $xml = null;
        
        if (!empty(self::$fileColumns)) {
            
            $xml .= '<filecontents>' . "\n";
            
            foreach (self::$fileColumns as $fileCol) {
                
                if (file_exists($fileCol['content'])) {
                    
                    $fileType = $fileCol['fileType'];
                    
                    if ($fileType == 'imageFile') {
                        
                        $fileContent = base64_encode(file_get_contents($fileCol['content']));
                        
                    } elseif ($fileType == 'contentFile') {
                        
                        $fileContent = file_get_contents($fileCol['content']);
                    }
                    
                    $xml .= '<filecontent fileType="'.$fileType.'" fileUrl="'.$fileCol['content'].'">' . "\n";
                        $xml .= '<![CDATA[' . "\n";
                            $xml .= $fileContent . "\n";
                        $xml .= ']]>' . "\n";
                    $xml .= '</filecontent>' . "\n";
                }
            }
            
            $xml .= '</filecontents>' . "\n";
        }
        
        return $xml;
    }
    
    public function translateAppendXml($metaId, $metaTypeId) {
        
        $xml = null;
        
        if (self::$isIgnoreTranslate) {
            return $xml;
        }
        
        $data = array();
        $metaDataIdPh = $this->db->Param(0);
        
        if ($metaTypeId == Mdmetadata::$businessProcessMetaTypeId) {
            
            $sql = "
                SELECT 
                    GD.* 
                FROM ( 
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID    
                    FROM META_BUSINESS_PROCESS_LINK PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.PROCESS_NAME) 
                    WHERE PL.META_DATA_ID = $metaDataIdPh 
                    
                    UNION ALL 
                    
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID    
                    FROM META_PROCESS_PARAM_ATTR_LINK PL
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.TAB_NAME) 
                    WHERE PL.PROCESS_META_DATA_ID = $metaDataIdPh 
                        AND PL.IS_INPUT = 1 
                        AND PL.IS_SHOW = 1 
                        AND PL.TAB_NAME IS NOT NULL    
                    
                    UNION ALL 
                    
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID   
                    FROM META_PROCESS_PARAM_ATTR_LINK PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.SIDEBAR_NAME) 
                    WHERE PL.PROCESS_META_DATA_ID = $metaDataIdPh 
                        AND PL.IS_INPUT = 1 
                        AND PL.IS_SHOW = 1 
                        AND PL.SIDEBAR_NAME IS NOT NULL    
                    
                    UNION ALL 

                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID  
                    FROM META_PROCESS_PARAM_ATTR_LINK PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LABEL_NAME) 
                    WHERE PL.PROCESS_META_DATA_ID = $metaDataIdPh 
                        AND PL.IS_INPUT = 1 
                        AND PL.IS_SHOW = 1 
                        AND PL.LABEL_NAME IS NOT NULL 
                    
                    UNION ALL 

                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID  
                    FROM META_PROCESS_RULE PR 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PR.ERROR_MESSAGE) 
                    WHERE PR.MAIN_PROCESS_ID = $metaDataIdPh 
                ) GD 
                GROUP BY 
                    GD.CODE, 
                    GD.TYPE_ID, 
                    GD.GROUP_ID, 
                    GD.MONGOLIAN, 
                    GD.ENGLISH, 
                    GD.RUSSIAN, 
                    GD.KOREAN, 
                    GD.CHINESE, 
                    GD.JAPANESE, 
                    GD.TURKISH, 
                    GD.GERMAN, 
                    GD.ID, 
                    GD.ARABIC, 
                    GD.META_DATA_ID"; 
            
        } elseif ($metaTypeId == Mdmetadata::$metaGroupMetaTypeId) {
            
            $sql = "
                SELECT 
                    GD.* 
                FROM ( 
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID    
                    FROM META_GROUP_LINK PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LIST_NAME) 
                    WHERE PL.META_DATA_ID = $metaDataIdPh 
                    
                    UNION ALL 
                    
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID    
                    FROM META_GROUP_LINK PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LIST_MENU_NAME) 
                    WHERE PL.META_DATA_ID = $metaDataIdPh 
                    
                    UNION ALL
                    
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID    
                    FROM META_GROUP_CONFIG PL
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.SIDEBAR_NAME) 
                    WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                        AND PL.IS_SELECT = 1 
                        AND (PL.IS_SHOW = 1 OR PL.IS_SHOW_BASKET = 1)  
                        AND PL.PARENT_ID IS NULL  
                        AND PL.SIDEBAR_NAME IS NOT NULL    
                    
                    UNION ALL 
                    
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID   
                    FROM META_GROUP_CONFIG PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.SEARCH_GROUPING_NAME) 
                    WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                        AND (PL.IS_CRITERIA = 1 OR PL.IS_UM_CRITERIA = 1) 
                        AND PL.PARENT_ID IS NULL 
                        AND PL.SEARCH_GROUPING_NAME IS NOT NULL 
                    
                    UNION ALL
                    
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID   
                    FROM META_DM_PROCESS_BATCH PL 
                        INNER JOIN META_DM_PROCESS_DTL DTL ON DTL.MAIN_META_DATA_ID = PL.MAIN_META_DATA_ID 
                            AND DTL.BATCH_NUMBER = PL.BATCH_NUMBER  
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.BATCH_NAME) 
                    WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                        AND PL.BATCH_NAME IS NOT NULL
                        
                    UNION ALL
                    
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID   
                    FROM META_DM_PROCESS_DTL PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.PROCESS_NAME) 
                    WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                        AND PL.PROCESS_NAME IS NOT NULL 
                    
                    UNION ALL 

                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID  
                    FROM META_GROUP_CONFIG PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LABEL_NAME) 
                    WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                        AND PL.IS_SELECT = 1 
                        AND (PL.IS_SHOW = 1 OR PL.IS_SHOW_BASKET = 1)  
                        AND PL.PARENT_ID IS NULL 
                        AND PL.LABEL_NAME IS NOT NULL 
                ) GD 
                GROUP BY 
                    GD.CODE, 
                    GD.TYPE_ID, 
                    GD.GROUP_ID, 
                    GD.MONGOLIAN, 
                    GD.ENGLISH, 
                    GD.RUSSIAN, 
                    GD.KOREAN, 
                    GD.CHINESE, 
                    GD.JAPANESE, 
                    GD.TURKISH, 
                    GD.GERMAN, 
                    GD.ID, 
                    GD.ARABIC, 
                    GD.META_DATA_ID"; 
            
        } elseif ($metaTypeId == Mdmetadata::$menuMetaTypeId) {
            
            $sql = "
                SELECT 
                    GD.* 
                FROM ( 
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID    
                    FROM META_MENU_LINK PL 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.GLOBE_CODE) 
                    WHERE PL.META_DATA_ID = $metaDataIdPh 
                ) GD 
                GROUP BY 
                    GD.CODE, 
                    GD.TYPE_ID, 
                    GD.GROUP_ID, 
                    GD.MONGOLIAN, 
                    GD.ENGLISH, 
                    GD.RUSSIAN, 
                    GD.KOREAN, 
                    GD.CHINESE, 
                    GD.JAPANESE, 
                    GD.TURKISH, 
                    GD.GERMAN, 
                    GD.ID, 
                    GD.ARABIC, 
                    GD.META_DATA_ID"; 
            
        } elseif ($metaTypeId == Mdmetadata::$statementMetaTypeId) {
            
            $sql = "
                SELECT 
                    GD.* 
                FROM ( 
                    SELECT 
                        GL.CODE, 
                        GL.TYPE_ID, 
                        GL.GROUP_ID, 
                        GL.MONGOLIAN, 
                        GL.ENGLISH, 
                        GL.RUSSIAN, 
                        GL.KOREAN, 
                        GL.CHINESE, 
                        GL.JAPANESE, 
                        GL.TURKISH, 
                        GL.GERMAN, 
                        GL.ID, 
                        GL.ARABIC, 
                        GL.META_DATA_ID 
                    FROM META_TAG PL 
                        INNER JOIN META_TAG_MAP TM ON TM.TAG_ID = PL.ID 
                        INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.NAME) 
                    WHERE TM.META_DATA_ID = $metaDataIdPh 
                ) GD 
                GROUP BY 
                    GD.CODE, 
                    GD.TYPE_ID, 
                    GD.GROUP_ID, 
                    GD.MONGOLIAN, 
                    GD.ENGLISH, 
                    GD.RUSSIAN, 
                    GD.KOREAN, 
                    GD.CHINESE, 
                    GD.JAPANESE, 
                    GD.TURKISH, 
                    GD.GERMAN, 
                    GD.ID, 
                    GD.ARABIC, 
                    GD.META_DATA_ID"; 
        }
        
        if (isset($sql)) {
            $data = $this->db->GetAll($sql, array($metaId));
        }
        
        if ($data) {
            
            $xml .= '<translatelist>' . "\n";
                $xml .= '<![CDATA[' . "\n";
                    $xml .= Str::remove_doublewhitespace(Str::removeNL(var_export($data, true))) . "\n"; 
                $xml .= ']]>' . "\n";
            $xml .= '</translatelist>' . "\n";
        }
        
        return $xml;
    }
    
    public function exportObjectModel() {
        
        includeLib('Compress/Compression');
        $this->load->model('mdmetadata', 'middleware/models/');
        
        $id           = strtolower(Input::post('id'));
        $objectCode   = strtolower(Input::post('objectCode'));
        $selectedRows = Input::post('selectedRows');
        
        if (is_array($selectedRows) && array_key_exists($id, $selectedRows[0])) {
            
            $ids = Arr::implode_key(',', $selectedRows, $id, true);
            
            try {
                
                if ($objectCode == 'kpi') {
                
                    $objects = $this->db->GetAll("
                        SELECT 
                            ID AS META_DATA_ID, 
                            'kpi' AS META_TYPE_ID, 
                            CODE AS META_DATA_CODE 
                        FROM KPI_TEMPLATE 
                        WHERE ID IN ($ids) 

                        UNION 

                        SELECT 
                            KP.ID AS META_DATA_ID, 
                            'kpi' AS META_TYPE_ID, 
                            KP.CODE AS META_DATA_CODE 
                        FROM 
                            (
                                SELECT 
                                    TRG_TEMPLATE_ID 
                                FROM KPI_TEMPLATE_MAP 
                                START WITH SRC_TEMPLATE_ID IN ($ids) 
                                CONNECT BY NOCYCLE SRC_TEMPLATE_ID = PRIOR TRG_TEMPLATE_ID 
                            ) KM 
                            INNER JOIN KPI_TEMPLATE KP ON KP.ID = KM.TRG_TEMPLATE_ID"
                    );

                } elseif ($objectCode == 'ntf') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            NOTIFICATION_ID AS META_DATA_ID, 
                            'ntf' AS META_TYPE_ID, 
                            NOTIFICATION_TYPE_ID AS META_DATA_CODE 
                        FROM NTF_NOTIFICATION 
                        WHERE NOTIFICATION_ID IN ($ids)"
                    );

                } elseif ($objectCode == 'testcase') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            TC.ID AS META_DATA_ID, 
                            'testcase' AS META_TYPE_ID, 
                            TC.TEST_MODE AS META_DATA_CODE, 
                            MD.META_DATA_ID AS PROCESS_META_DATA_ID, 
                            MD.META_TYPE_ID AS PROCESS_META_TYPE_ID, 
                            MD.META_DATA_CODE AS PROCESS_META_DATA_CODE 
                        FROM TEST_CASE TC
                            INNER JOIN META_DATA MD ON MD.META_DATA_ID = TC.PROCESS_META_DATA_ID  
                        WHERE TC.ID IN ($ids)" 
                    );

                } elseif ($objectCode == 'umobject') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            OBJECT_ID AS META_DATA_ID, 
                            'umobject' AS META_TYPE_ID, 
                            OBJECT_CODE_ID AS META_DATA_CODE 
                        FROM UM_OBJECT_CODE 
                        WHERE OBJECT_CODE_ID IN ($ids)"
                    );

                } elseif ($objectCode == 'bugfix') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            ID AS META_DATA_ID, 
                            'bugfix' AS META_TYPE_ID, 
                            ID AS META_DATA_CODE 
                        FROM META_BUG_FIXING 
                        WHERE ID IN ($ids)"
                    );

                } elseif ($objectCode == 'kpiindicator') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            TMP.* 
                        FROM (
                            SELECT
                                ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                CODE AS META_DATA_CODE, 
                                NULL AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR
                            START WITH 
                                ID IN ($ids) 
                            CONNECT BY NOCYCLE PRIOR PARENT_ID = ID 

                            UNION 

                            SELECT
                                ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                CODE AS META_DATA_CODE, 
                                NULL AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR
                                START WITH ID IN (
                                    SELECT 
                                        CATEGORY_ID 
                                    FROM KPI_INDICATOR_CATEGORY
                                    WHERE INDICATOR_ID IN ($ids) 
                                )
                                CONNECT BY NOCYCLE PRIOR PARENT_ID = ID 

                            UNION 

                            SELECT 
                                KI.ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                KI.CODE AS META_DATA_CODE, 
                                NULL AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR_INDICATOR_MAP M 
                                INNER JOIN KPI_INDICATOR KI ON M.TRG_INDICATOR_ID = KI.ID 
                            WHERE M.SEMANTIC_TYPE_ID = 10000009 
                                AND M.SRC_INDICATOR_ID IN ($ids) 
                            
                            UNION 
    
                            SELECT 
                                T2.ID AS META_DATA_ID, 
                                'kpiindicatorbydata' AS META_TYPE_ID, 
                                T2.CODE AS META_DATA_CODE, 
                                T0.ID AS SRC_RECORD_ID 
                            FROM KPI_INDICATOR T0 
                                INNER JOIN KPI_TYPE T1 ON T1.ID = T0.KPI_TYPE_ID 
                                INNER JOIN KPI_INDICATOR T2 ON T2.ID = T1.RELATED_INDICATOR_ID 
                            WHERE T0.ID IN ($ids) 
                        ) TMP 
                        GROUP BY TMP.META_DATA_ID, TMP.META_TYPE_ID, TMP.META_DATA_CODE, TMP.SRC_RECORD_ID  
                        ORDER BY TMP.META_TYPE_ID DESC"
                    );

                } elseif ($objectCode == 'kpiindicatorall') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            TMP.* 
                        FROM (
                            WITH TMP_IND AS (

                                SELECT
                                    ID AS META_DATA_ID, 
                                    'kpiindicator' AS META_TYPE_ID, 
                                    CODE AS META_DATA_CODE 
                                FROM KPI_INDICATOR
                                START WITH 
                                    ID IN ($ids) 
                                CONNECT BY NOCYCLE PRIOR PARENT_ID = ID 

                                UNION 

                                SELECT
                                    ID AS META_DATA_ID, 
                                    'kpiindicator' AS META_TYPE_ID, 
                                    CODE AS META_DATA_CODE 
                                FROM KPI_INDICATOR
                                    START WITH ID IN (
                                        SELECT 
                                            CATEGORY_ID 
                                        FROM KPI_INDICATOR_CATEGORY
                                        WHERE INDICATOR_ID IN ($ids) 
                                    )
                                    CONNECT BY NOCYCLE PRIOR PARENT_ID = ID 

                                UNION 

                                SELECT
                                    ID AS META_DATA_ID, 
                                    'kpiindicator' AS META_TYPE_ID, 
                                    CODE AS META_DATA_CODE 
                                FROM KPI_INDICATOR
                                START WITH 
                                    ID IN ($ids) 
                                CONNECT BY NOCYCLE PRIOR ID = PARENT_ID  

                                UNION 

                                SELECT
                                    ID AS META_DATA_ID, 
                                    'kpiindicator' AS META_TYPE_ID, 
                                    CODE AS META_DATA_CODE 
                                FROM KPI_INDICATOR
                                    START WITH ID IN (
                                        SELECT 
                                            CATEGORY_ID 
                                        FROM KPI_INDICATOR_CATEGORY
                                        WHERE INDICATOR_ID IN ($ids) 
                                    )
                                    CONNECT BY NOCYCLE PRIOR ID = PARENT_ID
                            ) 

                            SELECT 
                                KI.ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                KI.CODE AS META_DATA_CODE 
                            FROM KPI_INDICATOR_INDICATOR_MAP M 
                                INNER JOIN KPI_INDICATOR KI ON M.TRG_INDICATOR_ID = KI.ID 
                                INNER JOIN TMP_IND TI ON M.SRC_INDICATOR_ID = TI.META_DATA_ID 
                            WHERE M.SEMANTIC_TYPE_ID = 10000009 

                            UNION 

                            SELECT 
                                META_DATA_ID, 
                                META_TYPE_ID, 
                                META_DATA_CODE
                            FROM TMP_IND 
                        ) TMP 
                        GROUP BY TMP.META_DATA_ID, TMP.META_TYPE_ID, TMP.META_DATA_CODE"
                    );

                } elseif ($objectCode == 'impexcel') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            ID AS META_DATA_ID, 
                            'impexcel' AS META_TYPE_ID, 
                            CODE AS META_DATA_CODE 
                        FROM IMP_EXCEL_TEMPLATE 
                        WHERE ID IN ($ids)"
                    );

                } elseif ($objectCode == 'kpitype') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            T0.* 
                        FROM (   
                            SELECT 
                                KT.ID AS META_DATA_ID, 
                                'kpitype' AS META_TYPE_ID, 
                                KT.CODE AS META_DATA_CODE 
                            FROM 
                                (
                                    SELECT 
                                        ID 
                                    FROM KPI_TYPE 
                                    START WITH ID IN ($ids) 
                                    CONNECT BY NOCYCLE ID = PRIOR PARENT_ID
                                ) KM 
                                INNER JOIN KPI_TYPE KT ON KT.ID = KM.ID 

                            UNION 

                            SELECT 
                                KI.ID AS META_DATA_ID, 
                                'kpiindicator' AS META_TYPE_ID, 
                                KI.CODE AS META_DATA_CODE 
                            FROM (
                                SELECT 
                                    RELATED_INDICATOR_ID 
                                FROM KPI_TYPE 
                                START WITH ID IN ($ids) 
                                CONNECT BY NOCYCLE ID = PRIOR PARENT_ID 
                            ) KT 
                            INNER JOIN KPI_INDICATOR KI ON KI.ID = KT.RELATED_INDICATOR_ID 
                        ) T0 
                        ORDER BY T0.META_TYPE_ID DESC"
                    );

                } elseif ($objectCode == 'metawidget') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            ID AS META_DATA_ID, 
                            'metawidget' AS META_TYPE_ID, 
                            CODE AS META_DATA_CODE 
                        FROM META_WIDGET 
                        WHERE ID IN ($ids)"
                    );

                } elseif ($objectCode == 'processrule') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            ID AS META_DATA_ID, 
                            'processrule' AS META_TYPE_ID, 
                            ID AS META_DATA_CODE 
                        FROM META_PROCESS_RULE 
                        WHERE ID IN ($ids)"
                    );

                } elseif ($objectCode == 'globedictionary') {

                    $objects = $this->db->GetAll("
                        SELECT 
                            ID AS META_DATA_ID, 
                            'globedictionary' AS META_TYPE_ID, 
                            CODE AS META_DATA_CODE 
                        FROM GLOBE_DICTIONARY 
                        WHERE ID IN ($ids)"
                    );

                }
            
            } catch (Exception $ex) { 
                return array('status' => 'error', 'message' => $ex->getMessage());
            }
        }
        
        $objectXml = null;
        
        if (isset($objects) && $objects) {
            
            foreach ($objects as $object) {
                
                self::$isCreateTable = false;
                self::$isInsertData = true;
                self::$insertDataFilter = null;
                
                if ($object['META_TYPE_ID'] == 'kpiindicatorbydata') { 
                    
                    self::$isCreateTable = true;
                    self::$isInsertData = false;
                    self::$insertDataFilter = 'SRC_RECORD_ID='.$object['SRC_RECORD_ID'];
                    
                    $object['META_TYPE_ID'] = 'kpiindicator';
                } 

                $objectResult = self::oneObjectModel($object['META_DATA_ID'], $object['META_TYPE_ID'], $object['META_DATA_CODE']);

                if ($objectResult['status'] == 'success') {
                    
                    $objectXml .= $objectResult['result'];
                    
                    if (isset($object['PROCESS_META_DATA_ID']) && $object['PROCESS_META_DATA_ID']) {
                        
                        $meta = array(
                            'META_TYPE_ID' => $object['PROCESS_META_TYPE_ID'], 
                            'META_DATA_ID' => $object['PROCESS_META_DATA_ID'], 
                            'META_DATA_CODE' => $object['PROCESS_META_DATA_CODE'], 
                            'USER_NAME' => '', 
                            'MODIFIED_DATE' => ''
                        );
                        
                        $metaResult = self::oneMetaModel($meta);

                        if ($metaResult['status'] == 'success') {
                            $objectXml .= $metaResult['result'];
                        }
                    }
                    
                } else {
                    return $objectResult;
                }
            }
            
        } else {
            return array('status' => 'error', 'message' => 'Татах өгөгдөл олдсонгүй!');
        }

        if ($objectXml) {

            $xml = self::upgradeXmlHeader();

            $xml .= '<metas>' . "\n";
                $xml .= $objectXml;
            $xml .= '</metas>' . "\n";

            $xml .= self::upgradeXmlFooter();

            $script = Compression::gzdeflate($xml);

            $result = array('status' => 'success', 'typeId' => $object['META_TYPE_ID'], 'result' => $script);
            
            if (count($selectedRows) == 1) {
                $result['objectId'] = $ids;
            }
        } 
        
        return isset($result) ? $result : array('status' => 'error', 'message' => 'Татах өгөгдөл олдсонгүй!');
    }
    
    public function importMetaFileModel() {
        
        if (!Mdmeta::isAccessMetaImport()) {
            return array('status' => 'error', 'message' => 'Импорт хийх боломжгүй байна.');
        }
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        includeLib('Compress/Compression');
        
        $totalFile   = count($_FILES['import_file']['name']);
        $fileSources = array();
        
        for ($i = 0; $i < $totalFile; $i++) {
            
            if ($_FILES['import_file']['error'][$i] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['import_file']['tmp_name'][$i])) {
                
                $fileContent = Compression::gzinflate(file_get_contents($_FILES['import_file']['tmp_name'][$i]));
                
                if ($fileContent && strpos($fileContent, '<meta id="') !== false) {
                    $fileSources[] = $fileContent;
                } else {
                    return array('status' => 'error', 'message' => 'PHP export хийсэн файл уншуулна уу!');
                }
            }
        }
        
        if ($fileSources) {
            
            if (Input::postCheck('isPreviewUpdateMeta')) {
                
                if (Input::postCheck('updateMeta')) {
                    
                    $updateMetas = Input::post('updateMeta');
                
                    foreach ($updateMetas as $updateMetaId) {
                        self::$updateMetaIds[$updateMetaId] = 1;
                    }

                    self::$isPreviewUpdateMeta = true;
                    
                } else {
                    return array('status' => 'error', 'message' => 'Шинэчлэлтийн үзүүлэлт олдсонгүй!');
                }
            }
            
            $response = self::executeUpgradeScript($fileSources);
            
        } else {
            $response = array('status' => 'error', 'message' => 'Файлаас шинэчлэлтийн дата олдсонгүй!');
        }
        
        return $response;
    }
    
    public function executeUpgradeScript($fileSources) {
        
        $isCustomerServer = Config::getFromCache('isCustomerServer');
        $isIgnoreCheckLock = Config::getFromCache('CONFIG_IGNORE_CHECK_LOCK');
        $kpiDbSchemaName = Config::getFromCache('kpiDbSchemaName');
        
        $sessionUserId = Ue::sessionUserId();
        $kpiDbSchemaName = $kpiDbSchemaName ? $kpiDbSchemaName . '.' : '';
        $metaCount = $metaLockedCount = 0;
        $successMetas = $translateList = array();
        $lockedMetaMessage = $logs = '';

        $this->db->BeginTrans(); 

        foreach ($fileSources as $fileSource) {
            
            if (DB_DRIVER != 'oci8') {
                
                $fileSource = str_replace('TO_CHAR(', '', $fileSource);
                $fileSource = str_replace('TO_DATE(', '', $fileSource);
                $fileSource = str_replace(",'YYYY-MM-DD HH24:MI:SS')", '', $fileSource);
            }
            
            $fileSource = str_replace('[kpiDbSchemaName].', $kpiDbSchemaName, $fileSource);

            $decryptedArray = Xml::createArray($fileSource);
            
            if (isset($decryptedArray['documents']['scripts']['@cdata']) && $decryptedArray['documents']['scripts']['@cdata']) {
                
                $scripts = trim($decryptedArray['documents']['scripts']['@cdata']);
                
                if ($scripts) {
                    
                    $scripts = str_replace(Mdcommon::$separator . Mdcommon::$separator, Mdcommon::$separator, $scripts);
                    $scripts = str_replace(';' . Mdcommon::$separator, Mdcommon::$separator, $scripts);

                    $scriptsArr = explode(Mdcommon::$separator, $scripts);
                    $isScriptRun = false;
                    
                    foreach ($scriptsArr as $scrpt) {
                        
                        $scrpt = trim($scrpt);
                        
                        if ($scrpt) {
                            
                            $isScriptRun = true;
                            
                            if (DB_DRIVER == 'postgres9') {
                                
                                $lastChar = substr($scrpt, -1);
                                
                                if ($lastChar == ';') {
                                    $scrpt = 'DO $$ BEGIN '.$scrpt.' EXCEPTION WHEN others THEN END; $$;';
                                } else {
                                    $scrpt = 'DO $$ BEGIN '.$scrpt.'; EXCEPTION WHEN others THEN END; $$;';
                                }
                            }
                            
                            try {
                                
                                $this->db->Execute($scrpt);
                                
                            } catch (Exception $ex) {
                                
                                $logs .= 'SQL:<br />';
                                $logs .= $scrpt.'<br />';
                                $logs .= 'Error:<br />';
                                $logs .= $ex->msg. '<br />';
                                $logs .= '=====================================================================<br />';
                            }
                        }
                    }
                    
                    if ($isScriptRun) {
                        $this->db->CommitTrans();
                        $this->db->BeginTrans(); 
                    }
                }
            }

            if (isset($decryptedArray['documents']['metas']['meta'])) {

                $metas = $decryptedArray['documents']['metas']['meta'];

                if (!array_key_exists(0, $metas)) {
                    $metas = array($metas);
                }

                foreach ($metas as $meta) {

                    $metaAttributes = $meta['@attributes'];

                    $metaDataId = $metaAttributes['id'];
                    $metaTypeId = $metaAttributes['typeId'];
                    
                    if (self::$isPreviewUpdateMeta && !isset(self::$updateMetaIds[$metaDataId])) {
                        continue;
                    }
                    
                    if (isset($successMetas[$metaTypeId . '_' . $metaDataId])) {
                        continue;
                    }
                    
                    $metaCode = $metaAttributes['code'];
                    
                    ++$metaCount;

                    if (!$isIgnoreCheckLock) {

                        $response = $this->ws->getJsonByCurl(Mdmeta::getLockServerAddr().'checkLock/'.$metaDataId.'/'.$sessionUserId);

                        $json = json_decode($response, true);

                        if ($json && ($json['isLocked'] == true || $json['isLocked'] == 'true')) {

                            ++$metaLockedCount;
                            $lockedMetaMessage .= $metaCode . ', ';

                            continue;
                        }
                    }

                    self::previousTranslateList($isCustomerServer, $metaDataId, $metaTypeId);
                    
                    $skipError    = issetParam($metaAttributes['skipError']);
                    $skipErrorTbl = array();
                    
                    $scripts     = $meta['scripts']['@cdata'];
                    $scriptsList = explode(Mdcommon::$separator, $scripts);
                    
                    foreach ($scriptsList as $script) {

                        $script = trim($script);

                        if ($script) {
                            
                            if (DB_DRIVER == 'postgres9') {
                                
                                preg_match_all('/INSERT INTO(.*?)\(/', $script, $parseTblName);
                                
                                if ($skipError == '1' || (isset($parseTblName[1][0]) && $parseTblName[1][0] && in_array(trim($parseTblName[1][0]), self::$ignoreDeleteScriptTables))) {
                                    $script = 'DO $$ BEGIN '.$script.'; EXCEPTION WHEN others THEN END; $$;';
                                }
                            }

                            try {
                                
                                $this->db->Execute($script);

                            } catch (Exception $ex) {
                                
                                preg_match_all('/INSERT INTO(.*?)\(/', $script, $parseTblName);
                                
                                if ($skipError == '1') {
                                    
                                    $skipErrorTbl[trim($parseTblName[1][0])] = 1;
                                    
                                } else {
                                
                                    $message = $ex->getMessage();

                                    $logs = 'META SQL:<br />';
                                    $logs .= $script.'<br />';
                                    $logs .= 'Error:<br />';
                                    $logs .= $message. '<br />';
                                    $logs .= '=====================================================================<br />';

                                    if (!isset($parseTblName[1][0]) || (isset($parseTblName[1][0]) && $parseTblName[1][0] && !in_array(trim($parseTblName[1][0]), self::$ignoreDeleteScriptTables))) {

                                        $this->db->RollbackTrans();
                                        return array('status' => 'error', 'message' => $message, 'logs' => $logs);
                                    }
                                }
                                
                            }
                        }
                    } 
                    
                    if (isset($meta['createTables']['createTable'])) {
                        
                        $createTables = $meta['createTables']['createTable'];
                        
                        if (!array_key_exists(0, $createTables)) {
                            $createTables = array($createTables);
                        }
                        
                        foreach ($createTables as $createTable) {
                            
                            $content = $createTable['@cdata'];
                            $createTableScripts = explode(Mdcommon::$separator, $content);
                            
                            foreach ($createTableScripts as $createTableScript) {
                                
                                $createTableScript = trim($createTableScript);

                                if ($createTableScript) {
                                    
                                    if (DB_DRIVER == 'postgres9') {
                                        
                                        $createTableScript = str_replace(' CHAR)', ')', $createTableScript);
                                        
                                        if (strpos($createTableScript, 'ALTER TABLE') !== false) {
                                            
                                            $createTableScript = str_replace(' ADD (', ' ADD COLUMN ', $createTableScript);
                                            $createTableScript = str_replace('))', ')', $createTableScript);
                                            $createTableScript = str_replace('CLOB)', 'CLOB', $createTableScript);
                                            $createTableScript = str_replace('DATE)', 'DATE', $createTableScript);
                                        }
                                        
                                        $createTableScript = 'DO $$ BEGIN '.$createTableScript.'; EXCEPTION WHEN others THEN END; $$;';
                                    }
                                    
                                    try {

                                        $this->db->Execute($createTableScript);

                                    } catch (Exception $ex) { 
                                    }
                                }
                            }
                        }
                    }

                    if (isset($meta['cloblobs']['cloblob'])) {

                        $cloblobs = $meta['cloblobs']['cloblob'];

                        if (!array_key_exists(0, $cloblobs)) {
                            $cloblobs = array($cloblobs);
                        }

                        foreach ($cloblobs as $cloblob) {

                            $content    = $cloblob['@cdata'];
                            $attributes = $cloblob['@attributes'];

                            $type       = $attributes['type'];
                            $tblName    = $attributes['tblName'];
                            $colName    = $attributes['colName'];
                            $equalField = $attributes['equalField'];
                            $recordId   = $attributes['recordId'];
                            
                            if ($skipError == '1' && isset($skipErrorTbl[$tblName])) {
                                continue;
                            }

                            try {

                                if ($type == 'clob') {
                                    $this->db->UpdateClob($tblName, $colName, $content, $equalField . ' = '.$recordId);
                                } else {
                                    $this->db->UpdateBlob($tblName, $colName, $content, $equalField . ' = '.$recordId);
                                }

                            } catch (Exception $ex) {
                                
                                if ($skipError != '1') {
                                    
                                    $message = $ex->getMessage();
                                    $this->db->RollbackTrans();

                                    $logs .= 'META CLOB:<br />';
                                    $logs .= $tblName.'<br />';
                                    $logs .= 'Error:<br />';
                                    $logs .= $message. '<br />';
                                    $logs .= '=====================================================================<br />';

                                    return array('status' => 'error', 'message' => $message, 'logs' => $logs);
                                }
                            }
                        }
                    }

                    if (isset($meta['filecontents']['filecontent'])) {

                        $fileContents = $meta['filecontents']['filecontent'];

                        if (!array_key_exists(0, $fileContents)) {
                            $fileContents = array($fileContents);
                        }

                        foreach ($fileContents as $fileContent) {

                            $content    = $fileContent['@cdata'];
                            $attributes = $fileContent['@attributes'];

                            $fileType   = $attributes['fileType'];
                            $fileUrl    = $attributes['fileUrl'];

                            try {

                                if ($fileType == 'imageFile') {

                                    self::file_force_contents($fileUrl, base64_decode($content));

                                } elseif ($fileType == 'contentFile') {

                                    self::file_force_contents($fileUrl, $content);
                                }

                            } catch (Exception $ex) {

                                $message = $ex->getMessage();
                                $this->db->RollbackTrans();
                                
                                $logs .= 'META FILE:<br />';
                                $logs .= $fileUrl.'<br />';
                                $logs .= 'Error:<br />';
                                $logs .= $message. '<br />';
                                $logs .= '=====================================================================<br />';

                                return array('status' => 'error', 'message' => $message, 'logs' => $logs);
                            }
                        }
                    }

                    if (isset($meta['translatelist']['@cdata'])) {
                        $translateList[$metaDataId] = $meta['translatelist']['@cdata'];
                    }
                    
                    $successMetas[$metaTypeId . '_' . $metaDataId] = array(
                        'metaTypeId' => $metaTypeId, 
                        'metaDataId' => $metaDataId, 
                        'metaCode'   => $metaCode
                    );
                }
            } 
        }
        
        if (self::$ignoreDbCommitTrans == false) {
            
            $this->db->CommitTrans();
            
            if ($successMetas) {
                
                self::upgradeGlobeDictionary($translateList);

                foreach ($successMetas as $keyMeta) {

                    if ($keyMeta['metaTypeId'] == Mdmetadata::$businessProcessMetaTypeId) {

                        (new Mdmeta())->bpParamsClearCache($keyMeta['metaDataId'], $keyMeta['metaCode'], true);

                    } elseif ($keyMeta['metaTypeId'] == Mdmetadata::$metaGroupMetaTypeId) {

                        (new Mdmeta())->dvCacheClearByMetaId($keyMeta['metaDataId'], true);

                    } elseif ($keyMeta['metaTypeId'] == 'kpi' || $keyMeta['metaTypeId'] == 'kpiindicator') {

                        (new Mdmeta())->clearCacheKpiTemplateById($keyMeta['metaDataId']);
                    }
                }
            }

            if ($metaCount == $metaLockedCount) {

                if ($metaCount == 1) {
                    $response = array('status' => 'error', 'message' => rtrim($lockedMetaMessage, ', ') . ' кодтой уг үзүүлэлт түгжээтэй байгаа учир шинэчлэх боломжгүй байна!', 'logs' => $logs);
                } else {
                    $response = array('status' => 'error', 'message' => "Нийт $metaCount үзүүлэлтээс түгжигдсэн үзүүлэлт: " . rtrim($lockedMetaMessage, ', '), 'logs' => $logs);
                }

            } else {

                if ($metaLockedCount > 0) {
                    $doneMetaCount = $metaCount - $metaLockedCount;
                    $response = array('status' => 'success', 'message' => "Нийт $metaCount үзүүлэлтээс $doneMetaCount шинэчлэгдлээ. Түгжигдсэн үзүүлэлт: " . rtrim($lockedMetaMessage, ', '), 'logs' => $logs);
                } else {
                    $response = array('status' => 'success', 'message' => 'Амжилттай', 'logs' => $logs);
                }
            }
            
        } else {
            $response = array('status' => 'success', 'metaDataId' => $metaDataId);
        }

        return $response;
    }
    
    public function upgradeGlobeDictionary($translateList) {
        
        if ($translateList) {
            
            foreach ($translateList as $metaId => $arrayStr) {
                
                eval('$array = '.$arrayStr.';'); 
                
                foreach ($array as $row) {
                    
                    $globeCode   = Str::lower($row['CODE']);
                    $updateArray = $row;

                    unset($updateArray['CODE']);

                    $cols = array();

                    foreach ($updateArray as $key => $val) {
                        if ($val != '') { 
                            $cols[] = "$key = '$val'"; 
                        } else {
                            $cols[] = "$key = null";
                        }
                    }

                    $affectedCount = self::executeQueryStr("UPDATE GLOBE_DICTIONARY SET ".implode(', ', $cols)." WHERE LOWER(CODE) = '$globeCode' AND IS_CUSTOM = 0");

                    if (!$affectedCount) {
                        
                        self::insertExecute($row);
                    }
                }
            }
        }
        
        if (self::$previousTranslateList) {
            
            $this->db->BeginTrans(); 
            
            foreach (self::$previousTranslateList as $metaId => $row) {
                
                $list = $row['list'];
                
                foreach ($list as $val) {
                    
                    try {
                        
                        if ($val['UPDATE_TBL'] == 'META_BUSINESS_PROCESS_LINK') {
                            
                            $updateQry = "UPDATE META_BUSINESS_PROCESS_LINK SET PROCESS_NAME = '".$val['CODE']."' WHERE META_DATA_ID = $metaId";
                            
                        } elseif ($val['UPDATE_COL'] == 'TAB_NAME' || $val['UPDATE_COL'] == 'SIDEBAR_NAME' || $val['UPDATE_COL'] == 'LABEL_NAME') {
                            
                            $updateQry = "UPDATE META_PROCESS_PARAM_ATTR_LINK SET ".$val['UPDATE_COL']." = '".$val['CODE']."' WHERE PROCESS_META_DATA_ID = $metaId AND IS_INPUT = 1 AND IS_SHOW = 1 AND PARAM_REAL_PATH = '".$val['REAL_PATH']."'";
                            
                        } elseif ($val['UPDATE_TBL'] == 'META_MENU_LINK') {
                            
                            $updateQry = "UPDATE META_MENU_LINK SET GLOBE_CODE = '".$val['CODE']."' WHERE META_DATA_ID = $metaId";
                            
                        } elseif ($val['UPDATE_COL'] == 'LIST_NAME') {
                            
                            $updateQry = "UPDATE META_GROUP_LINK SET LIST_NAME = '".$val['CODE']."' WHERE META_DATA_ID = $metaId";
                            
                        } elseif ($val['UPDATE_COL'] == 'LIST_MENU_NAME') {
                            
                            $updateQry = "UPDATE META_GROUP_LINK SET LIST_MENU_NAME = '".$val['CODE']."' WHERE META_DATA_ID = $metaId";
                            
                        } elseif ($val['UPDATE_COL'] == 'DV_SIDEBAR_NAME') {
                            
                            $updateQry = "UPDATE META_GROUP_CONFIG SET SIDEBAR_NAME = '".$val['CODE']."' WHERE MAIN_META_DATA_ID = $metaId AND FIELD_PATH = '".$val['REAL_PATH']."'";
                            
                        } elseif ($val['UPDATE_COL'] == 'SEARCH_GROUPING_NAME') {
                            
                            $updateQry = "UPDATE META_GROUP_CONFIG SET SEARCH_GROUPING_NAME = '".$val['CODE']."' WHERE MAIN_META_DATA_ID = $metaId AND FIELD_PATH = '".$val['REAL_PATH']."'";
                            
                        } elseif ($val['UPDATE_COL'] == 'BATCH_NAME') {
                            
                            $updateQry = "UPDATE META_DM_PROCESS_BATCH SET BATCH_NAME = '".$val['CODE']."' WHERE ID = ".$val['REAL_PATH'];
                            
                        } elseif ($val['UPDATE_COL'] == 'PROCESS_NAME') {
                            
                            $updateQry = "UPDATE META_DM_PROCESS_DTL SET PROCESS_NAME = '".$val['CODE']."' WHERE ID = ".$val['REAL_PATH'];
                            
                        } elseif ($val['UPDATE_COL'] == 'DV_LABEL_NAME') {
                            
                            $updateQry = "UPDATE META_GROUP_CONFIG SET LABEL_NAME = '".$val['CODE']."' WHERE MAIN_META_DATA_ID = $metaId AND FIELD_PATH = '".$val['REAL_PATH']."'";
                        }
                        
                        $this->db->Execute($updateQry);

                    } catch (Exception $ex) {
                        
                        $message = $ex->getMessage();
                    }
                }
            }
            
            $this->db->CommitTrans();
        }
        
        return true;
    }
    
    public function previousTranslateList($isCustomerServer, $metaDataId, $metaTypeId) {
        
        if ($isCustomerServer == '1') {
            
            if (isset(self::$previousTranslateList[$metaDataId])) {
                return null;
            }

            $row = $this->db->GetRow("SELECT ID FROM CUSTOMER_META_TRANSLATE WHERE META_DATA_ID = " . $this->db->Param(0), array($metaDataId));

            if (isset($row['ID'])) {
                
                $metaDataIdPh = $this->db->Param(0);
                
                if ($metaTypeId == Mdmetadata::$businessProcessMetaTypeId) {
            
                    $sql = "
                        SELECT 
                            GD.* 
                        FROM ( 
                            SELECT 
                                GL.CODE, 
                                null AS REAL_PATH, 
                                'PROCESS_NAME' AS UPDATE_COL, 
                                'META_BUSINESS_PROCESS_LINK' AS UPDATE_TBL   
                            FROM META_BUSINESS_PROCESS_LINK PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.PROCESS_NAME) 
                            WHERE PL.META_DATA_ID = $metaDataIdPh 

                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                PL.PARAM_REAL_PATH AS REAL_PATH, 
                                'TAB_NAME' AS UPDATE_COL, 
                                'META_PROCESS_PARAM_ATTR_LINK' AS UPDATE_TBL       
                            FROM META_PROCESS_PARAM_ATTR_LINK PL
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.TAB_NAME) 
                            WHERE PL.PROCESS_META_DATA_ID = $metaDataIdPh 
                                AND PL.IS_INPUT = 1 
                                AND PL.IS_SHOW = 1 
                                AND PL.TAB_NAME IS NOT NULL    

                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                PL.PARAM_REAL_PATH AS REAL_PATH, 
                                'SIDEBAR_NAME' AS UPDATE_COL, 
                                'META_PROCESS_PARAM_ATTR_LINK' AS UPDATE_TBL          
                            FROM META_PROCESS_PARAM_ATTR_LINK PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.SIDEBAR_NAME) 
                            WHERE PL.PROCESS_META_DATA_ID = $metaDataIdPh 
                                AND PL.IS_INPUT = 1 
                                AND PL.IS_SHOW = 1 
                                AND PL.SIDEBAR_NAME IS NOT NULL    

                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                PL.PARAM_REAL_PATH AS REAL_PATH, 
                                'LABEL_NAME' AS UPDATE_COL, 
                                'META_PROCESS_PARAM_ATTR_LINK' AS UPDATE_TBL           
                            FROM META_PROCESS_PARAM_ATTR_LINK PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LABEL_NAME) 
                            WHERE PL.PROCESS_META_DATA_ID = $metaDataIdPh 
                                AND PL.IS_INPUT = 1 
                                AND PL.IS_SHOW = 1 
                                AND PL.LABEL_NAME IS NOT NULL 
                        ) GD 
                        GROUP BY 
                            GD.CODE, 
                            GD.REAL_PATH, 
                            GD.UPDATE_COL, 
                            GD.UPDATE_TBL"; 
                    
                } elseif ($metaTypeId == Mdmetadata::$metaGroupMetaTypeId) {
                    
                    $sql = "
                        SELECT 
                            GD.* 
                        FROM ( 
                            SELECT 
                                GL.CODE, 
                                null AS REAL_PATH, 
                                'LIST_NAME' AS UPDATE_COL, 
                                'META_GROUP_LINK' AS UPDATE_TBL   
                            FROM META_GROUP_LINK PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LIST_NAME) 
                            WHERE PL.META_DATA_ID = $metaDataIdPh 

                            UNION ALL 
                            
                            SELECT 
                                GL.CODE, 
                                null AS REAL_PATH, 
                                'LIST_MENU_NAME' AS UPDATE_COL, 
                                'META_GROUP_LINK' AS UPDATE_TBL   
                            FROM META_GROUP_LINK PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LIST_MENU_NAME) 
                            WHERE PL.META_DATA_ID = $metaDataIdPh 

                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                PL.FIELD_PATH AS REAL_PATH, 
                                'DV_SIDEBAR_NAME' AS UPDATE_COL, 
                                'META_GROUP_CONFIG' AS UPDATE_TBL       
                            FROM META_GROUP_CONFIG PL
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.SIDEBAR_NAME) 
                            WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                                AND PL.IS_SELECT = 1 
                                AND (PL.IS_SHOW = 1 OR PL.IS_SHOW_BASKET = 1)  
                                AND PL.PARENT_ID IS NULL  
                                AND PL.SIDEBAR_NAME IS NOT NULL    

                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                PL.FIELD_PATH AS REAL_PATH, 
                                'SEARCH_GROUPING_NAME' AS UPDATE_COL, 
                                'META_GROUP_CONFIG' AS UPDATE_TBL          
                            FROM META_GROUP_CONFIG PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.SEARCH_GROUPING_NAME) 
                            WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                                AND (PL.IS_CRITERIA = 1 OR PL.IS_UM_CRITERIA = 1) 
                                AND PL.PARENT_ID IS NULL 
                                AND PL.SEARCH_GROUPING_NAME IS NOT NULL 
                            
                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                TO_CHAR(PL.ID) AS REAL_PATH, 
                                'BATCH_NAME' AS UPDATE_COL, 
                                'META_DM_PROCESS_BATCH' AS UPDATE_TBL          
                            FROM META_DM_PROCESS_BATCH PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.BATCH_NAME) 
                            WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                                AND PL.BATCH_NAME IS NOT NULL 
                            
                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                TO_CHAR(PL.ID) AS REAL_PATH, 
                                'PROCESS_NAME' AS UPDATE_COL, 
                                'META_DM_PROCESS_DTL' AS UPDATE_TBL          
                            FROM META_DM_PROCESS_DTL PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.PROCESS_NAME) 
                            WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                                AND PL.PROCESS_NAME IS NOT NULL 

                            UNION ALL 

                            SELECT 
                                GL.CODE, 
                                PL.FIELD_PATH AS REAL_PATH, 
                                'DV_LABEL_NAME' AS UPDATE_COL, 
                                'META_GROUP_CONFIG' AS UPDATE_TBL           
                            FROM META_GROUP_CONFIG PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.LABEL_NAME) 
                            WHERE PL.MAIN_META_DATA_ID = $metaDataIdPh 
                                AND PL.IS_SELECT = 1 
                                AND (PL.IS_SHOW = 1 OR PL.IS_SHOW_BASKET = 1)  
                                AND PL.PARENT_ID IS NULL 
                                AND PL.LABEL_NAME IS NOT NULL 
                        ) GD 
                        GROUP BY 
                            GD.CODE, 
                            GD.REAL_PATH, 
                            GD.UPDATE_COL, 
                            GD.UPDATE_TBL"; 
                    
                } elseif ($metaTypeId == Mdmetadata::$menuMetaTypeId) {
                    
                    $sql = "
                        SELECT 
                            GD.* 
                        FROM ( 
                            SELECT 
                                GL.CODE, 
                                null AS REAL_PATH, 
                                'GLOBE_CODE' AS UPDATE_COL, 
                                'META_MENU_LINK' AS UPDATE_TBL   
                            FROM META_MENU_LINK PL 
                                INNER JOIN GLOBE_DICTIONARY GL ON LOWER(GL.CODE) = LOWER(PL.GLOBE_CODE) 
                            WHERE PL.META_DATA_ID = $metaDataIdPh 
                        ) GD 
                        GROUP BY 
                            GD.CODE, 
                            GD.REAL_PATH, 
                            GD.UPDATE_COL, 
                            GD.UPDATE_TBL"; 
                }
                
                if (isset($sql)) {
                    
                    $data = $this->db->GetAll($sql, array($metaDataId));
                    
                    if ($data) {
                        self::$previousTranslateList[$metaDataId] = array(
                            'metaTypeId' => $metaTypeId, 
                            'list'       => $data
                        );
                    }
                    
                    return true;
                }

            } else {
                return null;
            }
        }
        
        return null;
    }
    
    public function executeQueryStr($str) {
        
        try {
            
            $this->db->Execute($str);
            
            return $this->db->affected_rows();
            
        } catch (Exception $ex) {
            return 0;
        }
    }
    
    public function insertExecute($row) {
        
        try {
            
            $this->db->AutoExecute('GLOBE_DICTIONARY', $row);
            return true;
            
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public function importAnotherServerModel() {
        
        $url = Config::getFromCache('PRODUCTION_HTTP_URL');
        $metaId = Input::post('metaId');
        
        if ($url) {
            
            set_time_limit(0);
            ini_set('memory_limit', '-1');
        
            $result = self::getOneMetaToMultiMetaModel($metaId);

            if ($result['status'] == 'success') {

                try {

                    $params = array('encryptedSource' => $result['result']); 

                    $curl_handle = curl_init();

                    curl_setopt($curl_handle, CURLOPT_URL, $url . 'mdupgrade/encryptedFileImport');
                    curl_setopt($curl_handle, CURLOPT_TIMEOUT, 30000);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_handle, CURLOPT_POST, true);
                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($params));
                    curl_setopt($curl_handle, CURLOPT_HEADER, false);
                    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                        'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',
                        'Cache-Control: no-cache', 
                        'Content-Type: application/json'
                    ));

                    $buffer = curl_exec($curl_handle);
                    curl_close($curl_handle);

                    $response = json_decode(remove_utf8_bom($buffer), true);

                } catch (Exception $ex) {
                    $response = array('status' => 'error', 'message' => $ex->getMessage());
                }

            } else {
                $response = array('status' => 'error', 'message' => 'Амжилтгүй!');
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'Тохиргоо дутуу байна!');
        }
        
        if ($response['status'] == 'success') {
            
            $logParam = array(
                'ID'              => getUID(), 
                'META_DATA_ID'    => $metaId, 
                'CREATED_USER_ID' => Ue::sessionUserId(), 
                'CREATED_DATE'    => Date::currentDate()
            );
            
            $this->db->AutoExecute('UM_META_UPGRADE_LOG', $logParam);
        }
        
        return $response;
    }
    
    public function encryptedFileImportModel($param = array()) {
        
        try {
            
            if (!$param) {
                $jsonBody = file_get_contents('php://input');
                $param = json_decode($jsonBody, true);
            }
            
            if (isset($param['bugFixEncryptedSource'])) {
                
                $bugFixEncryptedSource = $param['bugFixEncryptedSource'];
                unset($param['bugFixEncryptedSource']);
                
                $bugfixParam = $param;
                $bugfixParam['encryptedSource'] = $bugFixEncryptedSource;
                
                $result = $this->model->encryptedFileImportModel($bugfixParam);
                
                if ($result['status'] != 'success') {
                    
                    return $result;
                    
                } elseif ($bugFixId = issetParam($param['bugFixId'])) { 
                    
                    $result = self::downloadBugFixingModel($bugFixId);

                    if ($result['status'] == 'success') {
                        $param['bugfixOldPatch'] = $result['result'];
                    }
                }
            }
            
            if (isset($param['encryptedSource'])) {

                includeLib('Compress/Compression');

                $encryptedXml = $param['encryptedSource'];
                $decryptedXml = Compression::gzinflate($encryptedXml);

                $decryptedArray = Xml::createArray($decryptedXml);

                if (isset($decryptedArray['documents']['metas']['meta'])) {

                    $metas = $decryptedArray['documents']['metas']['meta'];

                    if (!array_key_exists(0, $metas)) {
                        $metas = array($metas);
                    }
                    
                    $isCustomerServer = Config::getFromCache('isCustomerServer');
                    $successMetas = $translateList = array();
                    
                    $this->db->BeginTrans(); 

                    foreach ($metas as $meta) {

                        $metaAttributes = $meta['@attributes'];

                        $metaDataId = $metaAttributes['id'];
                        $metaTypeId = $metaAttributes['typeId'];
                        $metaCode   = $metaAttributes['code'];
                        
                        self::previousTranslateList($isCustomerServer, $metaDataId, $metaTypeId);
                        
                        $skipError    = issetParam($metaAttributes['skipError']);
                        $skipErrorTbl = array();
                    
                        $scripts     = $meta['scripts']['@cdata'];
                        $scriptsList = explode(Mdcommon::$separator, $scripts);

                        foreach ($scriptsList as $script) {
                            
                            $script = trim($script);
                            
                            if ($script) {
                                
                                try {

                                    $this->db->Execute($script);

                                } catch (ADODB_Exception $ex) { 
                                    
                                    preg_match_all('/INSERT INTO(.*?)\(/', $script, $parseTblName);
                                
                                    if ($skipError == '1') {

                                        $skipErrorTbl[trim($parseTblName[1][0])] = 1;

                                    } else {

                                        $message = $ex->getMessage();

                                        if (!isset($parseTblName[1][0]) || (isset($parseTblName[1][0]) && $parseTblName[1][0] && !in_array(trim($parseTblName[1][0]), self::$ignoreDeleteScriptTables))) {

                                            $this->db->RollbackTrans();
                                            return array('status' => 'error', 'message' => $message);
                                        }
                                    }
                                }
                            }
                        } 

                        if (isset($meta['cloblobs']['cloblob'])) {

                            $cloblobs = $meta['cloblobs']['cloblob'];

                            if (!array_key_exists(0, $cloblobs)) {
                                $cloblobs = array($cloblobs);
                            }

                            foreach ($cloblobs as $cloblob) {

                                $content    = $cloblob['@cdata'];
                                $attributes = $cloblob['@attributes'];

                                $type       = $attributes['type'];
                                $tblName    = $attributes['tblName'];
                                $colName    = $attributes['colName'];
                                $equalField = $attributes['equalField'];
                                $recordId   = $attributes['recordId'];
                                
                                if ($skipError == '1' && isset($skipErrorTbl[$tblName])) {
                                    continue;
                                }

                                try {

                                    if ($type == 'clob') {
                                        $this->db->UpdateClob($tblName, $colName, $content, $equalField . ' = '.$recordId);
                                    } else {
                                        $this->db->UpdateBlob($tblName, $colName, $content, $equalField . ' = '.$recordId);
                                    }

                                } catch (ADODB_Exception $ex) {
                                    
                                    if ($skipError != '1') {
                                        
                                        $message = $ex->getMessage();
                                        $this->db->RollbackTrans();

                                        return array('status' => 'error', 'message' => $message);
                                    }
                                }
                            }
                        }

                        if (isset($meta['filecontents']['filecontent'])) {

                            $fileContents = $meta['filecontents']['filecontent'];

                            if (!array_key_exists(0, $fileContents)) {
                                $fileContents = array($fileContents);
                            }

                            foreach ($fileContents as $fileContent) {

                                $content    = $fileContent['@cdata'];
                                $attributes = $fileContent['@attributes'];

                                $fileType   = $attributes['fileType'];
                                $fileUrl    = $attributes['fileUrl'];

                                try {

                                    if ($fileType == 'imageFile') {

                                        self::file_force_contents($fileUrl, base64_decode($content));

                                    } elseif ($fileType == 'contentFile') {

                                        self::file_force_contents($fileUrl, $content);
                                    }

                                } catch (Exception $ex) {

                                    $message = $ex->getMessage();
                                    $this->db->RollbackTrans();

                                    return array('status' => 'error', 'message' => $message);
                                }
                            }
                        }
                        
                        if (isset($meta['translatelist']['@cdata'])) {
                            $translateList[$metaDataId] = $meta['translatelist']['@cdata'];
                        }
                        
                        $successMetas[$metaDataId] = array('metaTypeId' => $metaTypeId, 'metaCode' => $metaCode);
                    }

                    $this->db->CommitTrans();
                    
                    if ($successMetas) {
                        
                        Session::init();
                        $logged = Session::isCheck(SESSION_PREFIX.'LoggedIn');

                        if ($logged == false) {
                            Session::set(SESSION_PREFIX . 'LoggedIn', true);
                            Session::set(SESSION_PREFIX . 'lastTime', time());
                        }

                        $_POST['nult'] = true;
                        
                        self::upgradeGlobeDictionary($translateList);
                        
                        foreach ($successMetas as $keyMetaId => $keyMeta) {

                            if ($keyMeta['metaTypeId'] == Mdmetadata::$businessProcessMetaTypeId) {
                                
                                (new Mdmeta())->bpParamsClearCache($keyMetaId, $keyMeta['metaCode'], true);
                                
                            } elseif ($keyMeta['metaTypeId'] == Mdmetadata::$metaGroupMetaTypeId) {
                                
                                (new Mdmeta())->dvCacheClearByMetaId($keyMetaId, true);
                                
                            } elseif ($keyMeta['metaTypeId'] == 'kpi' || $keyMeta['metaTypeId'] == 'kpiindicator') {

                                (new Mdmeta())->clearCacheKpiTemplateById($keyMetaId);
                            }
                        }
                    }
                    
                    if ($bugFixId = issetParam($param['bugFixId'])) {
                        
                        $currentDate = Date::currentDate('Y-m-d H:i:s');

                        $fixedData = array(
                            'ID'                 => getUID(), 
                            'META_BUG_FIXING_ID' => $bugFixId, 
                            'CREATED_USER_ID'    => Ue::sessionUserKeyId(), 
                            'CREATED_DATE'       => $currentDate, 
                            'STATUS_ID'          => 1, 
                            'MODIFIED_DATE'      => $currentDate
                        );

                        $customerBugFixedResult = $this->db->AutoExecute('CUSTOMER_BUG_FIXED', $fixedData);
                        
                        if ($customerBugFixedResult && isset($param['bugfixOldPatch'])) {
                            $this->db->UpdateClob('CUSTOMER_BUG_FIXED', 'OLD_PATCH', $param['bugfixOldPatch'], 'ID = '.$fixedData['ID']);
                        }
                    }

                    return array('status' => 'success', 'message' => 'Амжилттай');
                }    
            } 

            return array('status' => 'error', 'message' => 'Амжилтгүй!');
            
        } catch (Exception $ex) {
            return array('status' => 'error', 'message' => $ex->getMessage());
        }
    }
    
    public function getOneMetaToMultiMetaModel($metaId) {
        
        $this->load->model('mdmetadata', 'middleware/models/');
        
        $result     = array('status' => 'error', 'message' => 'Үзүүлэлт олдсонгүй!');
        $metaTypeId = $this->model->getMetaTypeById($metaId);
        
        $metaIdPh   = $this->db->Param(0);
        
        if ($metaTypeId == Mdmetadata::$businessProcessMetaTypeId) {
            
            $sql = "
                SELECT 
                    TT.META_DATA_ID, TT.META_TYPE_ID, TT.META_DATA_CODE  
                FROM (
                    SELECT 
                        META_DATA_ID, META_TYPE_ID, META_DATA_CODE  
                    FROM META_DATA 
                    WHERE META_DATA_ID = $metaIdPh 
                    
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE  
                    FROM META_BUSINESS_PROCESS_LINK BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.GETDATA_PROCESS_ID 
                    WHERE BP.META_DATA_ID = $metaIdPh 
                    
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_BUSINESS_PROCESS_LINK BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.SYSTEM_META_GROUP_ID  
                    WHERE BP.META_DATA_ID = $metaIdPh 
                    
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_BUSINESS_PROCESS_LINK BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.REF_META_GROUP_ID   
                    WHERE BP.META_DATA_ID = $metaIdPh 
                    
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_PROCESS_PARAM_ATTR_LINK BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.GET_PROCESS_META_DATA_ID    
                    WHERE BP.PROCESS_META_DATA_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_PROCESS_PARAM_ATTR_LINK BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.LOOKUP_META_DATA_ID     
                    WHERE BP.PROCESS_META_DATA_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_BP_EXPRESSION_PROCESS BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.USE_META_DATA_ID      
                    WHERE BP.PROCESS_META_DATA_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_BP_EXPRESSION_PROCESS BP 
                        INNER JOIN META_BUSINESS_PROCESS_LINK PL ON PL.META_DATA_ID = BP.USE_META_DATA_ID   
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = PL.META_DATA_ID       
                    WHERE BP.PROCESS_META_DATA_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_BP_EXPRESSION_PROCESS BP 
                        INNER JOIN META_BUSINESS_PROCESS_LINK PL ON PL.META_DATA_ID = BP.USE_META_DATA_ID   
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = PL.SYSTEM_META_GROUP_ID        
                    WHERE BP.PROCESS_META_DATA_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_BP_EXPRESSION_PROCESS BP 
                        INNER JOIN META_BUSINESS_PROCESS_LINK PL ON PL.META_DATA_ID = BP.USE_META_DATA_ID   
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = PL.REF_META_GROUP_ID         
                    WHERE BP.PROCESS_META_DATA_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_PROCESS_PARAM_ATTR_LINK BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.LOOKUP_KEY_META_DATA_ID      
                    WHERE BP.PROCESS_META_DATA_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_PROCESS_WORKFLOW BP 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = BP.DO_BP_ID       
                    WHERE BP.MAIN_BP_ID = $metaIdPh 
                        
                    UNION  
                
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_PROCESS_WORKFLOW BP 
                        INNER JOIN META_BUSINESS_PROCESS_LINK PL ON PL.META_DATA_ID = BP.DO_BP_ID  
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = PL.SYSTEM_META_GROUP_ID  
                    WHERE BP.MAIN_BP_ID = $metaIdPh 
                        
                    UNION  
                    
                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_PROCESS_WORKFLOW BP 
                        INNER JOIN META_BUSINESS_PROCESS_LINK PL ON PL.META_DATA_ID = BP.DO_BP_ID  
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = PL.REF_META_GROUP_ID   
                    WHERE BP.MAIN_BP_ID = $metaIdPh 
                ) TT 
                GROUP BY TT.META_DATA_ID, TT.META_TYPE_ID, TT.META_DATA_CODE";
            
        } elseif ($metaTypeId == Mdmetadata::$metaGroupMetaTypeId) {
            
            $sql = "
                SELECT 
                    TT.META_DATA_ID, TT.META_TYPE_ID, TT.META_DATA_CODE 
                FROM (
                    SELECT 
                        META_DATA_ID, META_TYPE_ID, META_DATA_CODE 
                    FROM META_DATA 
                    WHERE META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_LINK GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.REF_META_GROUP_ID 
                    WHERE GL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_LINK GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.REF_STRUCTURE_ID  
                    WHERE GL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_LINK GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.LAYOUT_META_DATA_ID   
                    WHERE GL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_LINK GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.CALCULATE_PROCESS_ID   
                    WHERE GL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_LINK GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.QS_META_DATA_ID    
                    WHERE GL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_LINK GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.QS_META_DATA_ID    
                    WHERE GL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_CONFIG GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.LOOKUP_META_DATA_ID     
                    WHERE GL.MAIN_META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_CONFIG GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.LOOKUP_KEY_META_DATA_ID     
                    WHERE GL.MAIN_META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_CONFIG GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.PROCESS_META_DATA_ID      
                    WHERE GL.MAIN_META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_CONFIG GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.REF_STRUCTURE_ID       
                    WHERE GL.MAIN_META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_GROUP_CONFIG GL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = GL.REF_STRUCTURE_ID 
                    WHERE GL.MAIN_META_DATA_ID = $metaIdPh 
                ) TT 
                GROUP BY TT.META_DATA_ID, TT.META_TYPE_ID, TT.META_DATA_CODE";
            
        } elseif ($metaTypeId && Mdmetadata::$statementMetaTypeId) {
            
            $sql = "
                SELECT 
                    TT.META_DATA_ID, TT.META_TYPE_ID, TT.META_DATA_CODE 
                FROM ( 
                    SELECT 
                        META_DATA_ID, META_TYPE_ID, META_DATA_CODE 
                    FROM META_DATA 
                    WHERE META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_STATEMENT_LINK SL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = SL.DATA_VIEW_ID  
                    WHERE SL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_STATEMENT_LINK SL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = SL.DASHBOARD_META_ID   
                    WHERE SL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_STATEMENT_LINK SL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = SL.GROUP_DATA_VIEW_ID    
                    WHERE SL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_STATEMENT_LINK SL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = SL.PROCESS_META_DATA_ID     
                    WHERE SL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_STATEMENT_LINK SL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = SL.ROW_DATA_VIEW_ID      
                    WHERE SL.META_DATA_ID = $metaIdPh 

                    UNION  

                    SELECT 
                        MD.META_DATA_ID, MD.META_TYPE_ID, MD.META_DATA_CODE 
                    FROM META_STATEMENT_TEMPLATE SL 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = SL.TRG_META_DATA_ID       
                    WHERE SL.SRC_META_DATA_ID = $metaIdPh 
                ) TT 
                GROUP BY TT.META_DATA_ID, TT.META_TYPE_ID, TT.META_DATA_CODE";
        }
        
        if (isset($sql)) {
            
            $metas = $this->db->GetAll(
                "SELECT 
                    TMP.*
                FROM ( 
                    SELECT 
                        MD.META_TYPE_ID, 
                        MD.META_DATA_ID, 
                        MD.META_DATA_CODE, 
                        ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                        ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE 
                    FROM ($sql) SMD 
                        INNER JOIN META_DATA MD ON MD.META_DATA_ID = SMD.META_DATA_ID 
                        LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                        LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                ) TMP 
                GROUP BY 
                    TMP.META_TYPE_ID, 
                    TMP.META_DATA_ID, 
                    TMP.META_DATA_CODE, 
                    TMP.USER_NAME, 
                    TMP.MODIFIED_DATE 
                ORDER BY TMP.META_TYPE_ID ASC", 
                array($metaId)
            );
        }
        
        if (isset($metas) && $metas) {
            
            includeLib('Compress/Compression');
            
            $isUseMetaUserId = Config::getFromCache('IS_USE_META_CREATED_MODIFIED_USER_ID');
            
            if ($isUseMetaUserId == '1') {
                self::$exportIgnoreColumns = array('EXPORT_SCRIPT', 'COPY_COUNT');
            }
            
            $metaXml = null;
            
            foreach ($metas as $meta) {
                
                $metaResult = self::oneMetaModel($meta);
                
                if ($metaResult['status'] == 'success') {
                    $metaXml .= $metaResult['result'];
                }
            }
            
            if ($metaXml) {
                
                $xml = self::upgradeXmlHeader();

                $xml .= '<metas>' . "\n";
                    $xml .= $metaXml;
                $xml .= '</metas>' . "\n";

                $xml .= self::upgradeXmlFooter();

                $script = Compression::gzdeflate($xml);

                $result = array('status' => 'success', 'result' => $script);
            }
        } 
        
        return isset($result) ? $result : array('status' => 'error', 'message' => 'Татах өгөгдөл олдсонгүй!');
    }
    
    public function getMetaUpgradeInfoModel($metaId) {
        
        $metaIdPh = $this->db->Param(0);
        
        $row = $this->db->GetRow("
            SELECT 
                MD.META_DATA_CODE, 
                MD.META_DATA_NAME, 
                UM.USERNAME, 
                UP.CREATED_DATE AS LAST_UPGRADE_DATE 
            FROM META_DATA MD 
                LEFT JOIN (
                    SELECT 
                        MAX(CREATED_DATE) AS CREATED_DATE, 
                        CREATED_USER_ID, 
                        META_DATA_ID 
                    FROM UM_META_UPGRADE_LOG 
                    WHERE META_DATA_ID = $metaIdPh 
                    GROUP BY CREATED_USER_ID, META_DATA_ID  
                ) UP ON UP.META_DATA_ID = MD.META_DATA_ID 
                LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = UP.CREATED_USER_ID 
            WHERE MD.META_DATA_ID = $metaIdPh", 
            array($metaId) 
        );
        
        return $row;
    }
    
    public function sysUpdateAccessByPassModel() {
        
        $password = Input::post('ps');
        
        if (Hash::createMD5reverse($password) == '67a4ae7ee4b7f12fd2c9a4a8f4bc0ee1') {
            return true;
        }
        
        return false;
    }
    
    public function sysUpdateModel() {
        
        $codeNames    = Input::post('codeNames');
        $codeNamesArr = explode(',', $codeNames);
        
        if (is_countable($codeNamesArr) && count($codeNamesArr) > 0) {
            
            try {
                
                $isPhpUpdate = false;
                
                foreach ($codeNamesArr as $codeName) {
                
                    if ($codeName == 'frontend-full') {
                        
                        $isPhpUpdate = true;
                        shell_exec('sh /home/update/php.sh full');
                        
                    } elseif ($codeName == 'frontend-assets') {
                        
                        $isPhpUpdate = true;
                        shell_exec('sh /home/update/php.sh assets');
                        
                    } elseif ($codeName == 'frontend-helper') {
                        
                        $isPhpUpdate = true;
                        shell_exec('sh /home/update/php.sh helper');
                        
                    } elseif ($codeName == 'frontend-libs') {
                        
                        $isPhpUpdate = true;
                        shell_exec('sh /home/update/php.sh libs');
                        
                    } elseif ($codeName == 'frontend-middleware') {
                        
                        $isPhpUpdate = true;
                        shell_exec('sh /home/update/php.sh middleware');
                        
                    } elseif ($codeName == 'backend') {
                        
                        shell_exec('sh /home/update/java.sh');
                    }
                }
                
                if ($isPhpUpdate && function_exists('opcache_reset')) {
                    opcache_reset();
                }
                
                $response = array('status' => 'success', 'message' => 'Амжилттай шинэчлэлээ');
            
            } catch (Exception $ex) {
                $response = array('status' => 'error', 'message' => $ex->getMessage());
            } 
            
        } else {
            $response = array('status' => 'error', 'message' => 'Empty');
        }
        
        return $response;
    }
    
    public function metaConfigReplaceModel() {
        
        $sourceId = Input::numeric('sourceId');
        $targetId = Input::numeric('targetId');
        
        if ($sourceId && $targetId) {
            
            $metaIdPh = $this->db->Param(0);
            
            $metas = $this->db->GetAll("
                SELECT 
                    MD.META_DATA_ID, 
                    MD.META_TYPE_ID, 
                    MD.META_DATA_CODE, 
                    ".$this->db->IfNull('UD.USERNAME', 'UM.USERNAME')." AS USER_NAME, 
                    ".$this->db->IfNull('MD.MODIFIED_DATE', 'MD.CREATED_DATE')." AS MODIFIED_DATE 
                FROM META_DATA MD 
                    LEFT JOIN UM_USER US ON US.USER_ID = MD.CREATED_USER_ID 
                    LEFT JOIN UM_SYSTEM_USER UM ON UM.USER_ID = US.SYSTEM_USER_ID 
                    LEFT JOIN UM_SYSTEM_USER UD ON UD.USER_ID = MD.MODIFIED_USER_ID 
                WHERE MD.META_DATA_ID = $metaIdPh", 
                array($targetId) 
            );
            
            if ($metas) {
                
                $isUseMetaUserId = Config::getFromCache('IS_USE_META_CREATED_MODIFIED_USER_ID');
            
                if ($isUseMetaUserId == '1') {
                    self::$exportIgnoreColumns = array('EXPORT_SCRIPT', 'COPY_COUNT');
                }
            
                self::$isIgnoreMetaFolder = true;
                self::$isIgnoreTranslate = true;
                self::$isIdReplace = true;
                
                $meta = $metas[0];
                
                $metaResult = self::oneMetaModel($meta);

                if ($metaResult['status'] == 'success') {
                    
                    $result = $metaResult['result'];
                    
                    $sourceMetaRow = self::getMetaColumnDatas($sourceId);
                    $targetMetaRow = self::getMetaColumnDatas($targetId);
                    
                    $sourceMetaCode = $sourceMetaRow['META_DATA_CODE'];
                    $targetMetaCode = $targetMetaRow['META_DATA_CODE'];
                    $sourceMetaName = $sourceMetaRow['META_DATA_NAME'];
                    $targetMetaName = $targetMetaRow['META_DATA_NAME'];
                    
                    $result = str_replace(
                        array(
                            ' '. $targetId . ')', '('. $targetId . ',', ' '. $targetId . ',', '('. $targetId . ')',
                            ' = '. $targetId . Mdcommon::$separator, '"'.$targetId . '"', '"'.$targetMetaCode.'"', "'".$targetMetaCode."'", 
                            '"'.$targetMetaName.'"', "'".$targetMetaName."'"
                        ), 
                        array(
                            ' '. $sourceId . ')', '('. $sourceId . ',', ' '. $sourceId . ',', '('. $sourceId . ')', 
                            ' = '. $sourceId . Mdcommon::$separator, '"'.$sourceId . '"', '"'.$sourceMetaCode.'"', "'".$sourceMetaCode."'", 
                            '"'.$sourceMetaName.'"', "'".$sourceMetaName."'"
                        ),
                        $result
                    );
                    
                    if ($meta['META_TYPE_ID'] == Mdmetadata::$metaGroupMetaTypeId) {
                        
                        $sourceMetaGroupLinkId = $sourceMetaRow['META_GROUP_LINK_ID'] ? $sourceMetaRow['META_GROUP_LINK_ID'] : getUID();
                        $targetMetaGroupLinkId = $targetMetaRow['META_GROUP_LINK_ID'] ? $targetMetaRow['META_GROUP_LINK_ID'] : getUID();
                    
                        $result = str_replace(
                            array(
                                '"'.$targetMetaGroupLinkId.'"', 
                                '('. $targetMetaGroupLinkId . ',', 
                                ' '. $targetMetaGroupLinkId . ')', 
                                ' '. $targetMetaGroupLinkId . ',', 
                                ' = '. $targetMetaGroupLinkId . Mdcommon::$separator
                            ), 
                            array(
                                '"'.$sourceMetaGroupLinkId.'"', 
                                '('. $sourceMetaGroupLinkId . ',', 
                                ' '. $sourceMetaGroupLinkId . ')', 
                                ' '. $sourceMetaGroupLinkId . ',', 
                                ' = '. $sourceMetaGroupLinkId . Mdcommon::$separator
                            ),
                            $result
                        );
                    }
                    
                    if (self::$replaceIds) {
                        
                        $sourceReplaceIds = array();
                        $targetReplaceIds = array();
                        
                        foreach (self::$replaceIds as $k => $oldId) {
                            
                            $newId = getUIDAdd($k);
                            
                            $targetReplaceIds[] = ' '. $oldId . ')';
                            $targetReplaceIds[] = '('. $oldId . ')';
                            $targetReplaceIds[] = ' '. $oldId . ',';
                            $targetReplaceIds[] = '('. $oldId . ',';
                            $targetReplaceIds[] = '"'. $oldId . '"';
                            $targetReplaceIds[] = ' = '. $oldId . Mdcommon::$separator;
                            
                            $sourceReplaceIds[] = ' '. $newId . ')';
                            $sourceReplaceIds[] = '('. $newId . ')';
                            $sourceReplaceIds[] = ' '. $newId . ',';
                            $sourceReplaceIds[] = '('. $newId . ',';
                            $sourceReplaceIds[] = '"'. $newId . '"';
                            $sourceReplaceIds[] = ' = '. $newId . Mdcommon::$separator;
                        }
                        
                        $result = str_replace($targetReplaceIds, $sourceReplaceIds, $result);
                    }
                    
                    $xml = self::upgradeXmlHeader();

                        $xml .= '<metas>' . "\n";
                            $xml .= $result;
                        $xml .= '</metas>' . "\n";

                    $xml .= self::upgradeXmlFooter();
                    
                    if (self::$isMetaImportCopy == false) {
                        
                        $response = self::executeUpgradeScript(array($xml));
                        
                    } else {
                        $response = array('status' => 'success', 'xmlData' => $xml);
                    }
                    
                } else {
                    $response = array('status' => 'error', 'message' => 'Unsuccessfully');
                }
                
            } else {
                $response = array('status' => 'error', 'message' => 'Not found: '.$targetId);
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'Invalid id!');
        }
        
        return $response;
    }
    
    public function getMetaColumnDatas($metaId) {
        
        $row = $this->db->GetRow("
            SELECT 
                MD.META_DATA_CODE, 
                MD.META_DATA_NAME, 
                GL.ID AS META_GROUP_LINK_ID 
            FROM META_DATA MD 
                LEFT JOIN META_GROUP_LINK GL ON GL.META_DATA_ID = MD.META_DATA_ID
            WHERE MD.META_DATA_ID = ".$this->db->Param(0), 
            array($metaId)
        );
        
        return $row;
    }
    
    public function knowMetasInFileModel() {
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        includeLib('Compress/Compression');
        
        $totalFile   = count($_FILES['import_file']['name']);
        $fileSources = array();
        $isMetaImportCopy = Input::numeric('isMetaImportCopy');
        
        for ($i = 0; $i < $totalFile; $i++) {
            
            if ($_FILES['import_file']['error'][$i] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['import_file']['tmp_name'][$i])) {
                
                $fileContent = Compression::gzinflate(file_get_contents($_FILES['import_file']['tmp_name'][$i]));
                
                if ($fileContent && strpos($fileContent, '<meta id="') !== false) {
                    
                    if ($isMetaImportCopy) {
                        
                        if (strpos($fileContent, 'typeId="'.Mdmetadata::$businessProcessMetaTypeId.'"') !== false 
                            || strpos($fileContent, 'typeId="'.Mdmetadata::$metaGroupMetaTypeId.'"') !== false 
                            || strpos($fileContent, 'typeId="'.Mdmetadata::$statementMetaTypeId.'"') !== false) {

                            $fileSources[$_FILES['import_file']['name'][$i]] = $fileContent;
                        }
                    
                    } else {
                        $fileSources[$_FILES['import_file']['name'][$i]] = $fileContent;
                    }
                } 
            }
        }
        
        if ($fileSources) {
            
            $metaTypes = array();
            $dbMetaTypes = $this->db->GetAll("SELECT META_TYPE_ID, META_TYPE_NAME FROM META_TYPE");
            
            foreach ($dbMetaTypes as $metaType) {
                $metaTypes[$metaType['META_TYPE_ID']] = $metaType['META_TYPE_NAME'];
            }
            
            $idPh = $this->db->Param(0);
            $data = array();
            
            foreach ($fileSources as $fileName => $fileSource) {

                $decryptedArray = Xml::createArray($fileSource);

                if (isset($decryptedArray['documents']['metas']['meta'])) {

                    $metas = $decryptedArray['documents']['metas']['meta'];

                    if (!array_key_exists(0, $metas)) {
                        $metas = array($metas);
                    }
                    
                    if ($isMetaImportCopy && count($metas) > 1) {
                        return array('status' => 'error', 'message' => 'Зөвхөн нэг метаны хувьд ажиллана!');
                    }

                    foreach ($metas as $meta) {

                        $metaAttributes = $meta['@attributes'];

                        $metaDataId   = $metaAttributes['id'];
                        $metaTypeId   = $metaAttributes['typeId'];
                        $metaCode     = $metaAttributes['code'];
                        $userName     = issetParam($metaAttributes['userName']);
                        $modifiedDate = issetParam($metaAttributes['modifiedDate']);
                        $metaName = $folderId = $folderCode = $folderName = '';
                        
                        if ($isMetaImportCopy) {
                            
                            $metaRow = $this->db->GetRow("
                                SELECT 
                                    MD.META_DATA_ID, 
                                    MD.META_DATA_NAME, 
                                    FF.FOLDER_ID, 
                                    FF.FOLDER_CODE, 
                                    FF.FOLDER_NAME 
                                FROM META_DATA MD 
                                    LEFT JOIN META_DATA_FOLDER_MAP FM ON FM.META_DATA_ID = MD.META_DATA_ID 
                                    LEFT JOIN FVM_FOLDER FF ON FF.FOLDER_ID = FM.FOLDER_ID 
                                WHERE MD.META_DATA_ID = ".$idPh, array($metaDataId));
                            
                            if ($metaRow) {
                                $isMetaCreated = true;
                                $metaName = $metaRow['META_DATA_NAME'];
                                $folderId = $metaRow['FOLDER_ID'];
                                $folderCode = $metaRow['FOLDER_NAME'];
                                $folderName = $metaRow['FOLDER_NAME'];
                            } else {
                                $isMetaCreated = false;
                            }
                            
                        } else {
                            $isMetaCreated = $this->db->GetOne("SELECT META_DATA_ID FROM META_DATA WHERE META_DATA_ID = ".$idPh, array($metaDataId));
                        }
                        
                        $data[] = array(
                            'fileName'      => $fileName, 
                            'metaId'        => $metaDataId, 
                            'metaCode'      => $metaCode, 
                            'metaName'      => $metaName, 
                            'metaType'      => issetDefaultVal($metaTypes[$metaTypeId], $metaTypeId), 
                            'folderId'      => $folderId, 
                            'folderCode'    => $folderCode, 
                            'folderName'    => $folderName, 
                            'userName'      => $userName, 
                            'modifiedDate'  => $modifiedDate, 
                            'isMetaCreated' => $isMetaCreated ? true : false
                        );
                    }
                }
            }
            
            $response = array('status' => 'success', 'metaList' => $data);
            
        } else {
            $response = array('status' => 'error', 'message' => 'Файлаас шинэчлэлтийн дата олдсонгүй!');
        }
        
        return $response;
    }
    
    public function updatePatchMetaBugFixModel($rows) {
        
        if (isset($rows[0]['id'])) {
            
            $alreadyBugFix = $selectedRows = array();
            
            foreach ($rows as $row) {
                
                $bugFixId = $row['id'];
                
                if (!isset($alreadyBugFix[$bugFixId])) {
                    
                    $result = self::downloadBugFixingModel($bugFixId);

                    if ($result['status'] == 'success') {
                        $this->db->UpdateClob('META_BUG_FIXING', 'PATCH', $result['result'], 'ID = '.$bugFixId);
                    }
                    
                    $alreadyBugFix[$bugFixId] = 1;
                    $selectedRows[] = array('id' => $bugFixId);
                }
            }
            
            if ($alreadyBugFix) {
                
                $_POST['id'] = 'id';
                $_POST['objectCode'] = 'bugfix';
                $_POST['selectedRows'] = $selectedRows;
                
                self::$ignoreDeleteScript = false;
                $bugFixExport = self::exportObjectModel();
                
                if ($bugFixExport['status'] == 'success') {
                    
                    $url = 'https://qa.veritech.mn/mdupgrade/encryptedFileImport';
                    $response = $this->ws->curlRequest($url, array('encryptedSource' => $bugFixExport['result']));
                    
                } else {
                    $response = $bugFixExport;
                }
                
            } else {
                $response = array('status' => 'error', 'message' => 'Шинэчлэх өгөгдөл олдсонгүй! /005/');
            }
        }
        
        return $response;
    }
    
    public function importPatchMetaBugFixModel($rows) {
        
        $isDev = Config::getFromCache('is_dev');
        
        if (!$isDev && isset($rows[0]['id'])) {
            
            set_time_limit(0);
            ini_set('memory_limit', '-1');
        
            $n = 1;
            $alreadyBugFix = $customerBugFixedIds = array();
            
            foreach ($rows as $row) {
                
                $bugFixId = $row['id'];
                
                if (!isset($alreadyBugFix[$bugFixId])) {
                    
                    $result = self::downloadBugFixingModel($bugFixId);

                    if ($result['status'] == 'success') {
                        
                        $currentDate = Date::currentDate('Y-m-d H:i:s');

                        $fixedData = array(
                            'ID'                 => getUIDAdd($n), 
                            'META_BUG_FIXING_ID' => $bugFixId, 
                            'CREATED_USER_ID'    => Ue::sessionUserKeyId(), 
                            'CREATED_DATE'       => $currentDate, 
                            'MODIFIED_DATE'      => $currentDate, 
                            'STATUS_ID'          => 1, 
                            'DESCRIPTION'        => issetParam($row['newWfmDescription'])
                        );

                        $this->db->AutoExecute('CUSTOMER_BUG_FIXED', $fixedData);
                        $this->db->UpdateClob('CUSTOMER_BUG_FIXED', 'OLD_PATCH', $result['result'], 'ID = '.$fixedData['ID']);
                        
                        $customerBugFixedIds[] = $fixedData['ID'];
                    }
                    
                    $alreadyBugFix[$bugFixId] = $bugFixId;
                    
                    $n ++;
                }
            }
            
            if ($alreadyBugFix) {
                
                includeLib('Compress/Compression');
                
                $updateSources = array();
                $ids = Arr::implode_r(',', $alreadyBugFix, true);
                
                $patchData = $this->db->GetAll("SELECT PATCH FROM META_BUG_FIXING WHERE ID IN ($ids) AND PATCH IS NOT NULL");
                
                foreach ($patchData as $patchRow) {
                    
                    $updateContent = Compression::gzinflate($patchRow['PATCH']);
                    
                    if ($updateContent && strpos($updateContent, '<meta id="') !== false) {
                        $updateSources[] = $updateContent;
                    }
                }
                
                if ($updateSources) {
                    $response = self::executeUpgradeScript($updateSources);
                } else {
                    $response = array('status' => 'error', 'message' => 'Шинэчлэх өгөгдөл олдсонгүй! /006/');
                }
            }
            
        } else {
            $response = array('status' => 'error', 'message' => 'Шинэчлэх тохиргоо олдсонгүй! /007/');
        }
        
        if ($customerBugFixedIds && $response['status'] != 'success') {
            $this->db->Execute("DELETE FROM CUSTOMER_BUG_FIXED WHERE ID IN (".Arr::implode_r(',', $customerBugFixedIds, true).")");
        }
        
        return $response;
    }
    
    public function metaPatchRollbackModel() {
        
        try {
            
            $id = Input::numeric('id');
            
            if (!$id) {
                
                throw new Exception('Invalid id!');
                
            } else {
                
                $row = $this->db->GetRow("SELECT OLD_PATCH FROM CUSTOMER_BUG_FIXED WHERE ID = ".$this->db->Param(0), array($id));
                $oldPatch = issetParam($row['OLD_PATCH']);
                
                if ($oldPatch) {
                    
                    includeLib('Compress/Compression');
                    
                    $updateContent = Compression::gzinflate($oldPatch);
                    
                    if ($updateContent && strpos($updateContent, '<meta id="') !== false) {
                        
                        $response = self::executeUpgradeScript(array($updateContent));
                        
                    } else {
                        throw new Exception('Зөв Old patch олдсонгүй!');
                    }
                
                } else {
                    throw new Exception('Old patch олдсонгүй!');
                }
            }
            
        } catch (Exception $ex) {
            $response = array('status' => 'error', 'message' => $ex->getMessage());
        }
        
        return $response;
    }
    
    public function sendPatchMetaBugFixModel($rows) {
        
        $isDev = Config::getFromCache('is_dev');
        
        if (!$isDev && isset($rows[0]['id'])) {
            
            $domains = Config::getFromCache('metaPatchSendToDomains');
            
            if ($domains) {
                
                set_time_limit(0);
                ini_set('memory_limit', '-1');
                
                $domains = explode(',', $domains);
                $alreadyBugFix = array();

                foreach ($rows as $row) {

                    $bugFixId = $row['id'];

                    if (!isset($alreadyBugFix[$bugFixId])) {

                        $row = $this->db->GetRow("SELECT PATCH FROM META_BUG_FIXING WHERE ID = ".$this->db->Param(0), array($bugFixId));
                        $patch = issetParam($row['PATCH']);

                        if ($patch) {
                            
                            $_POST['id'] = 'id';
                            $_POST['objectCode'] = 'bugfix';
                            $_POST['selectedRows'] = array(array('id' => $bugFixId));

                            self::$ignoreDeleteScript = false;
                            $bugFixExport = self::exportObjectModel();

                            if ($bugFixExport['status'] == 'success') {
                                
                                foreach ($domains as $domain) {
                                
                                    $domain = rtrim($domain, '/');
                                    $url = $domain. '/mdupgrade/encryptedFileImport';

                                    $this->ws->curlQueue($url, array(
                                            'bugFixId' => $bugFixId, 
                                            'encryptedSource' => $patch, 
                                            'bugFixEncryptedSource' => $bugFixExport['result']
                                        )
                                    );
                                }
                                
                                $response = array('status' => 'success');
                            } else {
                                $response = $bugFixExport;
                            }
                        }

                        $alreadyBugFix[$bugFixId] = $bugFixId;
                    }
                }
                
                if (!$alreadyBugFix) {
                    $response = array('status' => 'error', 'message' => 'Шинэчлэх өгөгдөл олдсонгүй! /002/');
                }
                
            } else {
                $response = array('status' => 'error', 'message' => 'Шинэчлэх тохиргоо олдсонгүй! /003/');
            }
        } else {
            $response = array('status' => 'error', 'message' => 'Шинэчлэх тохиргоо олдсонгүй! /004/');
        }
        
        return true;
    }
    
    public function metaPatchImportModel() {
        
        try {
            
            $bugFixId = Input::numeric('id');
        
            $exportData = Mdupgrade::getBugfixDataByCommand('downloadObject', array('objectCode' => 'bugfix', 'ids' => $bugFixId));

            if ($exportData['status'] == 'success' && isset($exportData['result'])) {

                includeLib('Compress/Compression');

                $fileContent = Compression::gzinflate($exportData['result']);

                if ($fileContent && strpos($fileContent, '<meta id="') === false) {
                    return array('status' => 'error', 'message' => 'PHP export хийсэн файл уншуулна уу!', 'logs' => '');
                } 

                $response = self::executeUpgradeScript(array($fileContent));

                if ($response['status'] == 'success') {

                    $currentDate = Date::currentDate('Y-m-d H:i:s');

                    $fixedData = array(
                        'ID'                 => getUID(), 
                        'META_BUG_FIXING_ID' => $bugFixId, 
                        'CREATED_USER_ID'    => Ue::sessionUserKeyId(), 
                        'STATUS_ID'          => 1, 
                        'CREATED_DATE'       => $currentDate, 
                        'MODIFIED_DATE'      => $currentDate
                    );

                    $dbResult = $this->db->AutoExecute('CUSTOMER_BUG_FIXED', $fixedData);

                    if ($dbResult) {
                        
                        self::$isCreateRollback = true;
                        
                        $result = self::downloadBugFixingModel($bugFixId);
                        $oldPatch = $result['result'];
                        
                        if ($oldPatch) {
                            $clobResult = $this->db->UpdateClob('CUSTOMER_BUG_FIXED', 'OLD_PATCH', $oldPatch, 'ID = '.$fixedData['ID']);
                        } else {
                            $clobResult = true;
                        }

                        if ($clobResult) {

                            $exportData = Mdupgrade::getBugfixDataByCommand('download', array('ids' => $bugFixId));

                            if ($exportData['status'] == 'success' && isset($exportData['result'])) {

                                $fileContent = Compression::gzinflate($exportData['result']);

                                if ($fileContent && strpos($fileContent, '<meta id="') === false) {
                                    return array('status' => 'error', 'message' => 'PHP export хийсэн файл уншуулна уу!', 'logs' => '');
                                } 

                                $response = self::executeUpgradeScript(array($fileContent));
                                
                                $status = issetDefaultVal($response['status'], 'success');
                                $message = issetDefaultVal($response['message'], 'Амжилттай');

                                return array('status' => $status, 'message' => $message, 'logs' => issetParam($response['logs']));
                            } else {
                                return $exportData;
                            }
                        }
                    }
                    
                } else {
                    return array('status' => 'error', 'message' => $response['message']);
                }
                
            } else {
                return array('status' => 'error', 'message' => $exportData['message']);
            }
            
            return array('status' => 'error', 'message' => 'Error!');
        
        } catch (Exception $ex) {
            return array('status' => 'error', 'message' => $ex->getMessage());
        }
    }
    
    public function metaImportCopyFileModel() {
        
        if (!Mdmeta::isAccessMetaImport()) {
            return array('status' => 'error', 'message' => 'Импорт хийх боломжгүй байна.');
        }
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        includeLib('Compress/Compression');
        
        $totalFile   = count($_FILES['import_file']['name']);
        $fileSources = array();
        
        for ($i = 0; $i < $totalFile; $i++) {
            
            if ($_FILES['import_file']['error'][$i] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['import_file']['tmp_name'][$i])) {
                
                $fileContent = Compression::gzinflate(file_get_contents($_FILES['import_file']['tmp_name'][$i]));
                
                if ($fileContent && strpos($fileContent, '<meta id="') !== false) {
                    
                    if (strpos($fileContent, 'typeId="'.Mdmetadata::$businessProcessMetaTypeId.'"') !== false 
                        || strpos($fileContent, 'typeId="'.Mdmetadata::$metaGroupMetaTypeId.'"') !== false 
                        || strpos($fileContent, 'typeId="'.Mdmetadata::$statementMetaTypeId.'"') !== false) {
                        
                        $fileSources[] = $fileContent;
                    }
                    
                } else {
                    return array('status' => 'error', 'message' => 'PHP export хийсэн файл уншуулна уу!');
                }
            }
        }
        
        if ($fileSources) {
            
            self::$ignoreDbCommitTrans = true;
            self::$isMetaImportCopy = true;
            
            $response = self::executeUpgradeScript($fileSources);
            
            if ($response['status'] == 'success') {
                
                $copyMetaDataId = $response['metaDataId'];
                
                $newMetaId      = getUID();
                $newMetaCode    = Input::post('newMetaCode');
                $newMetaName    = Input::post('newMetaName');
                
                $this->db->Execute("INSERT INTO META_DATA (META_DATA_ID, META_DATA_CODE, META_DATA_NAME) VALUES ($newMetaId, '$newMetaCode', '$newMetaName')");
                
                $_POST['sourceId'] = $newMetaId;
                $_POST['targetId'] = $copyMetaDataId;
                
                $copyResult = self::metaConfigReplaceModel();
                
                if ($copyResult['status'] == 'success') {
                    $xmlData = $copyResult['xmlData'];
                } else {
                    $response = $copyResult;
                }
                
                $this->db->RollbackTrans();
                
                if (isset($xmlData)) {
                    
                    self::$ignoreDbCommitTrans = false;
                    self::$isMetaImportCopy = false;
            
                    $response = self::executeUpgradeScript(array($xmlData));
                    
                    if ($response['status'] != 'success') {
                        
                        $response['message'] = 'new copy: ' . $response['message'];
                        
                    } else {
                        
                        $folderId = Input::numeric('folderId');
                        
                        if ($folderId) {
                            
                            $metaDataFolderMap = array(
                                'ID'           => getUID(), 
                                'FOLDER_ID'    => $folderId, 
                                'META_DATA_ID' => $newMetaId,
                                'CREATED_DATE' => Date::currentDate('Y-m-d H:i:s')
                            );
                            $this->db->AutoExecute('META_DATA_FOLDER_MAP', $metaDataFolderMap);

                            $response['folderId'] = $folderId;
                        }
                    }
                }
            } 
            
        } else {
            $response = array('status' => 'error', 'message' => 'Файлаас шинэчлэлтийн дата олдсонгүй! /Only Process, Metagroup/');
        }
        
        return $response;
    }
    
    public function getBugFixingScriptModel($id) {
        
        try {
            
            $row = $this->db->GetRow("SELECT SCRIPT FROM META_BUG_FIXING WHERE ID = ".$this->db->Param(0), array($id));
            
            return array('status' => 'success', 'script' => issetParam($row['SCRIPT']));
            
        } catch (Exception $ex) {
            return array('status' => 'error', 'message' => $ex->getMessage());
        }        
    }
    
    public function getBugFixingKnowledgeModel($id) {
        
        try {
            
            $row = $this->db->GetRow("SELECT KNOWLEDGE FROM META_BUG_FIXING WHERE ID = ".$this->db->Param(0), array($id));
            
            return array('status' => 'success', 'knowledge' => issetParam($row['KNOWLEDGE']));
            
        } catch (Exception $ex) {
            return array('status' => 'error', 'message' => $ex->getMessage());
        }        
    }
    
    public function file_force_contents($filename, $data, $flags = 0) {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename).'/', 0777, true);
        }
        return file_put_contents($filename, $data, $flags);
    }
    
    public function isTableDataKpiIndicatorModel() {
        
        $id           = strtolower(Input::post('id'));
        $selectedRows = Input::post('selectedRows');
        
        if (is_array($selectedRows) && array_key_exists($id, $selectedRows[0])) {
            
            $ids = Arr::implode_key(',', $selectedRows, $id, true);
            
            $row = $this->db->GetRow("
                SELECT 
                    TABLE_NAME  
                FROM KPI_INDICATOR 
                WHERE ID IN ($ids) 
                    AND TABLE_NAME IS NOT NULL");
            
            if (isset($row['TABLE_NAME'])) {
                
                $tableName = substr($row['TABLE_NAME'], 0, 2);
                
                if ($tableName == 'V_' || strpos($row['TABLE_NAME'], '.V_') !== false) {
                    
                    try {
                        
                        $rs = $this->db->SelectLimit("
                            SELECT 
                                ID  
                            FROM ".$row['TABLE_NAME']." 
                            WHERE DELETED_USER_ID IS NULL 
                            ORDER BY ID ASC", 100, 0);
                        
                        if (isset($rs->_array)) {
                            return array('status' => 'success', 'count' => count($rs->_array));
                        } 
                        
                    } catch (Exception $ex) {
                        return null;
                    }
                }
            } 
            
            return null;
            
        } else {
            return array('status' => 'error', 'message' => 'Татах өгөгдөл олдсонгүй!');
        }
    }
    
}